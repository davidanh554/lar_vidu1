<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentTransaction;
use App\Models\Coupon;
use App\Models\UserCoupon;
use App\Services\GHNService;
use App\Services\GHNOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('warning', 'Vui lòng đăng nhập để thanh toán!');
        }

        $user = auth()->user();
        $userCart = $user->cart ?? [];

        if (empty($userCart)) {
            $recentOrder = Order::where('user_id', auth()->id())
                ->where('created_at', '>=', now()->subSeconds(60))
                ->latest()
                ->first();
            if ($recentOrder) {
                return redirect()->route('orders.index')
                    ->with('success', 'Đơn hàng #' . $recentOrder->id . ' của bạn đã được đặt thành công!');
            }
            return redirect()->route('cart.index')->with('warning', 'Giỏ hàng của bạn đang trống!');
        }

        $selectedItemKeys = $request->query('selected_items', []);

        $cart = [];
        $totalPrice = 0;

        if (!empty($selectedItemKeys) && is_array($selectedItemKeys)) {
            foreach ($selectedItemKeys as $key) {
                if (isset($userCart[$key])) {
                    $cart[$key] = $userCart[$key];
                    $totalPrice += $userCart[$key]['price'] * $userCart[$key]['quantity'];
                }
            }
        } else {
            $cart = $userCart;
            foreach ($cart as $item) {
                $totalPrice += $item['price'] * $item['quantity'];
            }
        }

        if (empty($cart)) {
            $recentOrder = Order::where('user_id', auth()->id())
                ->where('created_at', '>=', now()->subSeconds(60))
                ->latest()
                ->first();
            if ($recentOrder) {
                return redirect()->route('orders.index')
                    ->with('success', 'Đơn hàng #' . $recentOrder->id . ' của bạn đã được đặt thành công!');
            }
            return redirect()->route('cart.index')->with('warning', 'Vui lòng chọn ít nhất một sản phẩm để thanh toán!');
        }

        $userCoupons = UserCoupon::with('coupon')
            ->where('user_id', auth()->id())
            ->where('is_used', false)
            ->latest()
            ->get();

        return view('products.checkout', compact('cart', 'totalPrice', 'userCoupons'));
    }

    public function process(Request $request, GHNOrderService $ghnOrderService, GHNService $ghnService)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $request->validate([
            'fullname'       => 'required|string|max:255',
            'phone'          => ['required', 'regex:/^(03|05|07|08|09)[0-9]{8}$/'],
            'address'        => 'required|string|max:500',
            'to_district_id' => 'required|integer',
            'to_ward_code'   => 'required|string',
        ], [
            'fullname.required'       => 'Vui lòng nhập họ và tên',
            'phone.required'          => 'Vui lòng nhập số điện thoại',
            'phone.regex'             => 'Số điện thoại không hợp lệ (phải gồm đúng 10 chữ số thuộc các đầu số nhà mạng 03, 05, 07, 08, 09. Ví dụ: 0987654321)',
            'address.required'        => 'Vui lòng nhập địa chỉ chi tiết',
            'to_district_id.required' => 'Vui lòng chọn Quận / Huyện',
            'to_ward_code.required'   => 'Vui lòng chọn Phường / Xã',
        ]);

        $user = auth()->user();
        $cart = $user->cart ?? [];
        $selectedKeys = $request->input('selected_items', []);

        $itemsToDeduct = [];
        $remainingCart = $cart;
        if (!empty($selectedKeys) && is_array($selectedKeys)) {
            foreach ($selectedKeys as $key) {
                if (isset($cart[$key])) {
                    $itemsToDeduct[$key] = $cart[$key];
                    unset($remainingCart[$key]);
                }
            }
        } else {
            $itemsToDeduct = $cart;
            $remainingCart = [];
        }

        if (empty($itemsToDeduct)) {
            $recentOrder = Order::where('user_id', auth()->id())
                ->where('created_at', '>=', now()->subSeconds(60))
                ->latest()
                ->first();
            if ($recentOrder) {
                return redirect()->route('orders.index')
                    ->with('success', 'Đơn hàng #' . $recentOrder->id . ' của bạn đã được đặt thành công!');
            }
            return redirect()->route('cart.index')->with('warning', 'Không có sản phẩm nào để thanh toán.');
        }

        $subtotal = collect($itemsToDeduct)->sum(fn($i) => $i['price'] * $i['quantity']);
        $shippingFee = (int) $request->input('shipping_fee', 0);

        // Tính cước tự động trên server nếu frontend chưa kịp gửi
        if ($shippingFee <= 0 && $request->to_district_id && $request->to_ward_code) {
            try {
                $feeRes = $ghnService->calculateFee([
                    'from_district_id' => (int) config('services.ghn.from_district_id', 3440),
                    'from_ward_code'   => (string) config('services.ghn.from_ward_code', '13010'),
                    'to_district_id'   => (int) $request->to_district_id,
                    'to_ward_code'     => (string) $request->to_ward_code,
                    'service_type_id'  => 2,
                    'weight'           => 300,
                    'length'           => 15,
                    'width'            => 15,
                    'height'           => 10,
                ]);
                if (!empty($feeRes['data']['total'])) {
                    $shippingFee = (int) $feeRes['data']['total'];
                }
            } catch (\Throwable $e) {
                Log::warning('Calculate fee fallback failed: ' . $e->getMessage());
            }
        }

        // Xử lý mã giảm giá (nếu có)
        $couponCode = strtoupper(trim($request->input('coupon_code', '')));
        $discountAmount = 0;
        $appliedCoupon = null;

        if (!empty($couponCode)) {
            $coupon = Coupon::where('code', $couponCode)->where('is_active', true)->first();
            if ($coupon) {
                $validCheck = $coupon->isValidForOrder($subtotal);
                if ($validCheck['valid']) {
                    $hasUsed = Order::where('user_id', $user->id)->where('coupon_code', $couponCode)->exists();
                    if (!$hasUsed) {
                        $discountAmount = $coupon->calculateDiscount($subtotal);
                        $appliedCoupon = $coupon;
                    }
                }
            }
        }

        // Xử lý trừ tiền từ Xu Vua Tablet (1 Xu = 500₫)
        $useCoins = $request->boolean('use_coins');
        $coinsUsed = 0;
        $coinsDiscount = 0;
        $payableBeforeCoins = max(0, $subtotal + $shippingFee - $discountAmount);

        $coinRate = (int)\App\Models\Setting::get('coin_rate', 500);
        if ($useCoins && ($user->coins ?? 0) > 0) {
            $maxCoinsCash = $user->coins * $coinRate;
            if ($maxCoinsCash >= $payableBeforeCoins) {
                $coinsDiscount = $payableBeforeCoins;
                $coinsUsed = (int) ceil($coinsDiscount / $coinRate);
            } else {
                $coinsDiscount = $maxCoinsCash;
                $coinsUsed = $user->coins;
            }
        }

        $totalPrice = max(0, $payableBeforeCoins - $coinsDiscount);

        // Ghép địa chỉ đầy đủ (Số nhà + Phường/Xã + Quận/Huyện + Tỉnh/Thành)
        $fullAddress = trim($request->address);
        if ($request->filled('ward_name')) {
            $fullAddress .= ', ' . $request->ward_name;
        }
        if ($request->filled('district_name')) {
            $fullAddress .= ', ' . $request->district_name;
        }
        if ($request->filled('province_name')) {
            $fullAddress .= ', ' . $request->province_name;
        }

        try {
            // Thực hiện giao dịch: Nếu GHN từ chối hoặc lỗi => Rollback hoàn toàn (Không tạo đơn trong DB, không trừ tồn kho, không xóa giỏ hàng)
            $order = DB::transaction(function () use ($request, $user, $itemsToDeduct, $totalPrice, $shippingFee, $discountAmount, $appliedCoupon, $fullAddress, $coinsUsed, $coinsDiscount, $ghnOrderService) {
                $order = Order::create([
                    'user_id'         => $user->id,
                    'name'            => $request->fullname,
                    'phone'           => $request->phone,
                    'address'         => $fullAddress,
                    'to_district_id'  => $request->to_district_id,
                    'to_ward_code'    => $request->to_ward_code,
                    'ghn_total_fee'   => $shippingFee,
                    'coupon_code'     => $appliedCoupon ? $appliedCoupon->code : null,
                    'discount_amount' => $discountAmount,
                    'coins_used'      => $coinsUsed,
                    'coins_discount'  => $coinsDiscount,
                    'total_price'     => $totalPrice,
                    'status'          => 'cod_ordered',
                    'shipping_status' => 'not_shipped',
                ]);

                PaymentTransaction::create([
                    'order_id' => $order->id,
                    'gateway'  => 'cod',
                    'amount'   => $order->total_price,
                    'status'   => 'pending',
                    'message'  => 'Thanh toán khi nhận hàng',
                ]);

                foreach ($itemsToDeduct as $key => $item) {
                    $productId = $item['id'] ?? (is_numeric($key) ? $key : null);
                    if (!$productId) {
                        $productId = Product::where('name', $item['name'] ?? '')->value('id') ?? 1;
                    }

                    OrderItem::create([
                        'order_id'   => $order->id,
                        'product_id' => $productId,
                        'quantity'   => $item['quantity'],
                        'price'      => $item['price'],
                    ]);
                }

                // Gửi thông tin sang Giao Hàng Nhanh để tạo vận đơn
                $order->load('items.product');
                $ghnResult = $ghnOrderService->create($order);

                // KIỂM TRA BẮT BUỘC: Nếu GHN không chấp nhận hoặc trả về lỗi => Hủy giao dịch ngay
                if (empty($ghnResult['data']['order_code'])) {
                    $rawMsg = $ghnResult['message_display'] 
                        ?? $ghnResult['code_message_value'] 
                        ?? $ghnResult['message'] 
                        ?? 'Không xác định';

                    if (stripos($rawMsg, 'PHONE_INVALID') !== false) {
                        $errorMsg = 'Số điện thoại không hợp lệ hoặc bị hệ thống GHN từ chối. Vui lòng kiểm tra lại số điện thoại!';
                    } elseif (stripos($rawMsg, 'EXCEED_LIMIT') !== false) {
                        $errorMsg = 'Tài khoản GHN thử nghiệm đã vượt quá hạn mức (tối đa 3 đơn đang hoạt động). Vui lòng hủy các đơn cũ trước khi đặt đơn mới!';
                    } elseif (stripos($rawMsg, 'COD_IS_OVER_LIMIT') !== false) {
                        $errorMsg = 'Tiền thu hộ COD vượt hạn mức tối đa của tài khoản GHN.';
                    } elseif (stripos($rawMsg, 'FROM_ADDRESS') !== false || stripos($rawMsg, 'SHOP_INFO') !== false) {
                        $errorMsg = 'Địa chỉ người gửi của Shop chưa hợp lệ trên GHN.';
                    } else {
                        $errorMsg = 'Giao Hàng Nhanh báo lỗi: ' . $rawMsg;
                    }

                    throw new \Exception($errorMsg);
                }

                // GHN tạo thành công: Cập nhật mã vận đơn và trạng thái
                $order->update([
                    'status'          => 'cod_ordered',
                    'ghn_order_code'  => $ghnResult['data']['order_code'],
                    'shipping_status' => 'ready_to_pick',
                ]);

                // Trừ số lượng tồn kho sản phẩm theo màu
                foreach ($itemsToDeduct as $key => $item) {
                    $productId = $item['id'] ?? (is_numeric($key) ? $key : null);
                    if (!$productId) {
                        $productId = Product::where('name', $item['name'] ?? '')->value('id') ?? 1;
                    }
                    $product = Product::find($productId);
                    if ($product) {
                        $color = $item['color'] ?? null;
                        $quantity = (int)($item['quantity'] ?? 1);
                        $product->reduceColorStock($color, $quantity);
                    }
                }

                // Đánh dấu mã giảm giá đã dùng và tăng lượt dùng
                if ($appliedCoupon) {
                    $appliedCoupon->increment('used_count');
                    UserCoupon::where('user_id', $user->id)
                        ->where('coupon_id', $appliedCoupon->id)
                        ->where('is_used', false)
                        ->first()
                        ?->update([
                            'is_used'  => true,
                            'used_at'  => now(),
                            'order_id' => $order->id,
                        ]);
                }

                // Trừ Xu của người dùng nếu có áp dụng trừ tiền từ Xu
                if ($coinsUsed > 0) {
                    $user->decrement('coins', $coinsUsed);
                }

                return $order;
            });

            // Cập nhật giỏ hàng người dùng khi đơn hàng đã tạo thành công cả ở DB và GHN
            $user->cart = $remainingCart;
            $user->save();

            return redirect()->route('orders.index')->with('success', 'Đặt hàng thành công! Mã đơn: #' . $order->id . ' (Mã vận đơn GHN: ' . $order->ghn_order_code . ')');

        } catch (\Throwable $e) {
            Log::error('Checkout blocked due to GHN failure: ' . $e->getMessage());
            // Chặn đặt hàng, giữ nguyên giỏ hàng, quay lại trang thanh toán bắt người dùng kiểm tra lại thông tin
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * API Kiểm tra & Áp dụng mã giảm giá khi thanh toán
     */
    public function applyCoupon(Request $request)
    {
        $code = strtoupper(trim($request->input('code', '')));
        $subtotal = (float)$request->input('subtotal', 0);

        if (empty($code)) {
            return response()->json(['valid' => false, 'message' => 'Vui lòng nhập mã giảm giá!']);
        }

        $coupon = Coupon::where('code', $code)->first();
        if (!$coupon) {
            return response()->json(['valid' => false, 'message' => 'Mã giảm giá không tồn tại hoặc đã hết hạn!']);
        }

        $check = $coupon->isValidForOrder($subtotal);
        if (!$check['valid']) {
            return response()->json(['valid' => false, 'message' => $check['message']]);
        }

        // Kiểm tra xem khách hàng này đã dùng mã này cho đơn nào chưa
        $userId = auth()->id();
        $usedCount = Order::where('user_id', $userId)->where('coupon_code', $code)->count();
        if ($usedCount >= 1) {
            return response()->json(['valid' => false, 'message' => 'Bạn đã sử dụng mã giảm giá này cho một đơn hàng trước đó rồi!']);
        }

        $discount = $coupon->calculateDiscount($subtotal);

        return response()->json([
            'valid'              => true,
            'message'            => 'Áp dụng mã giảm giá thành công!',
            'code'               => $coupon->code,
            'title'              => $coupon->title,
            'discount'           => $discount,
            'discount_formatted' => '-' . number_format($discount) . '₫',
        ]);
    }

    /**
     * API Hủy áp dụng mã giảm giá
     */
    public function removeCoupon(Request $request)
    {
        return response()->json(['success' => true, 'message' => 'Đã gỡ mã giảm giá thành công!']);
    }
}
