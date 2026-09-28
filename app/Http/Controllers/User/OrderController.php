<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\GHNService;
use App\Services\GHNOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\PaymentTransaction;

class OrderController extends Controller
{
    // ==========================================
    // 1. CÁC VIEW HIỂN THỊ ĐƠN HÀNG & THANH TOÁN
    // ==========================================
    public function index()
    {
        $cart = session('cart', []);
        if (empty($cart) && Auth::check()) {
            $cart = Auth::user()->cart ?? []; 
        }
        
        if (empty($cart)) {
            $recentOrder = Auth::check() ? Order::where('user_id', Auth::id())
                ->where('created_at', '>=', now()->subSeconds(60))
                ->latest()
                ->first() : null;

            if ($recentOrder) {
                return redirect()->route('orders.index')
                    ->with('success', 'Đơn hàng #' . $recentOrder->id . ' của bạn đã được tiếp nhận thành công!');
            }

            return redirect()->route('cart.index')->with('error', 'Giỏ hàng đang trống.');
        }

        $totalPrice = collect($cart)->sum(function($item) {
            return $item['price'] * $item['quantity'];
        });
        
        return view('products.checkout', compact('cart', 'totalPrice'));
    }

    // đối tượng request để lấy toàn bộ tt vừa nhập
    public function processPayment(Request $request, GHNService $ghn, GHNOrderService $ghnOrders)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('warning', 'Vui lòng đăng nhập để hoàn tất đặt hàng!');
        }

        $request->validate([
            'name'           => 'required|string|max:100',
            'phone'          => ['required', 'regex:/^0\d{9}$/'],
            'address'        => 'required|string|max:100',
            'to_district_id' => 'required|integer',
            'to_ward_code'   => 'required|string',
            'payment_method' => 'required|in:cod,momo',
        ], [
            'name.required'           => 'Vui lòng nhập họ và tên người nhận',
            'phone.required'          => 'Vui lòng nhập số điện thoại người nhận',
            'phone.regex'             => 'Số điện thoại không hợp lệ (phải gồm đúng 10 chữ số, bắt đầu bằng số 0)',
            'address.required'        => 'Vui lòng nhập địa chỉ chi tiết',
            'to_district_id.required' => 'Vui lòng chọn Quận / Huyện',
            'to_ward_code.required'   => 'Vui lòng chọn Phường / Xã',
            'payment_method.required' => 'Vui lòng chọn phương thức thanh toán',
        ]);

        $user = Auth::user();
        $userCart = $user->cart ?? session('cart', []);

        // Lọc các món hàng được chọn nếu có truyền selected_items
        $selectedKeys = $request->input('selected_items', []);
        $cart = [];
        if (!empty($selectedKeys) && is_array($selectedKeys)) {
            foreach ($selectedKeys as $key) {
                if (isset($userCart[$key])) {
                    $cart[$key] = $userCart[$key];
                }
            }
        } else {
            $cart = $userCart;
        }

        // Chốt nếu cả 2 rỗng: kiểm tra xem có phải vừa submit tạo đơn xong không
        if (empty($cart)) {
            $recentOrder = Order::where('user_id', Auth::id())
                ->where('created_at', '>=', now()->subSeconds(60))
                ->latest()
                ->first();

            if ($recentOrder) {
                return redirect()->route('orders.index')
                    ->with('success', 'Đơn hàng #' . $recentOrder->id . ' của bạn đã được tiếp nhận thành công!');
            }

            return redirect()->route('cart.index')->with('error', 'Không thể thanh toán vì giỏ hàng trống.');
        }

        // 1. Tính tổng tiền hàng và tổng khối lượng sản phẩm
        $subtotal = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);
        $totalWeight = collect($cart)->sum(function ($item) {
            $singleWeight = (int) ($item['weight'] ?? 500);
            $quantity = (int) ($item['quantity'] ?? 1);
            return $singleWeight * $quantity;
        });

        // 2. Tính lại phí ship
        $addressData = [
            'from_district_id' => (int) config('services.ghn.from_district_id'),
            'to_district_id'   => (int) $request->to_district_id,
            'to_ward_code'     => (string) $request->to_ward_code,
        ];

        if (method_exists($ghn, 'packageParameters')) {
            $packageData = $ghn->packageParameters($totalWeight);
        } else {
            $packageData = ['weight' => $totalWeight];
        }

        $requestData = array_merge($addressData, $packageData);
        $feeResponse = $ghn->calculateFee($requestData);

        // 1. Xác định tiền ship
        if (isset($feeResponse['code']) && $feeResponse['code'] == 200) {
            $shippingFee = (int) ($feeResponse['data']['total'] ?? 0);
        } else {
            $shippingFee = (int) $request->input('shipping_fee', 0);
        }

        // 2. Xử lý mã giảm giá Coupon & Xu Vua Tablet
        $couponCode = strtoupper(trim($request->input('coupon_code', '')));
        $discountAmount = 0;
        $appliedCoupon = null;

        if (!empty($couponCode)) {
            $coupon = \App\Models\Coupon::where('code', $couponCode)->where('is_active', true)->first();
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

        // Xử lý trừ tiền từ Xu Vua Tablet (1 Xu = coin_rate)
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

        // Tổng tiền cuối cùng của đơn hàng sau khi trừ toàn bộ giảm giá
        $finalTotal = max(0, $payableBeforeCoins - $coinsDiscount);

        // Ghép địa chỉ đầy đủ (Số nhà + Phường/Xã + Quận/Huyện + Tỉnh/Thành)
        $fullAddress = trim($request->address);
        if ($request->filled('ward_name')) $fullAddress .= ', ' . $request->ward_name;
        if ($request->filled('district_name')) $fullAddress .= ', ' . $request->district_name;
        if ($request->filled('province_name')) $fullAddress .= ', ' . $request->province_name;

        // 3. Tạo đơn hàng và chi tiết đơn hàng trong Database
        $order = DB::transaction(function () use ($request, $user, $shippingFee, $finalTotal, $discountAmount, $appliedCoupon, $coinsUsed, $coinsDiscount, $cart, $fullAddress) {
            $order = Order::create([
                'user_id'         => $user->id,
                'name'            => $request->name,
                'address'         => $fullAddress,
                'phone'           => $request->phone,
                'total_price'     => $finalTotal,
                'coupon_code'     => $appliedCoupon ? $appliedCoupon->code : null,
                'discount_amount' => $discountAmount,
                'coins_used'      => $coinsUsed,
                'coins_discount'  => $coinsDiscount,
                'status'          => 'pending',
                'to_district_id'  => (int) $request->to_district_id,
                'to_ward_code'    => (string) $request->to_ward_code,
                'ghn_total_fee'   => $shippingFee,
                'shipping_status' => 'pending',
            ]);

            // Khấu trừ Xu trong ví khách hàng
            if ($coinsUsed > 0) {
                $user->decrement('coins', $coinsUsed);
            }

            // Đánh dấu mã giảm giá đã dùng
            if ($appliedCoupon) {
                $appliedCoupon->increment('used_count');
                \App\Models\UserCoupon::where('user_id', $user->id)
                    ->where('coupon_id', $appliedCoupon->id)
                    ->where('is_used', false)
                    ->update(['is_used' => true, 'used_at' => now()]);
            }

            foreach ($cart as $key => $item) {
                $productId = $item['id'] ?? $item['product_id'] ?? (is_numeric($key) ? $key : null);
                if (!$productId) {
                    $productId = Product::where('name', $item['name'] ?? '')->value('id') ?? 1;
                }

                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $productId,
                    'quantity'   => $item['quantity'],
                    'price'      => $item['price'],
                ]);

                // Giảm số lượng tồn kho theo màu sắc tương ứng
                $product = Product::find($productId);
                if ($product && method_exists($product, 'reduceColorStock')) {
                    $product->reduceColorStock($item['color'] ?? null, (int)($item['quantity'] ?? 1));
                }
            }

            return $order;
        });

        // Chỉ xóa các món đã đặt khỏi giỏ hàng
        $orderedKeys = array_keys($cart);
        session()->forget('cart');
        if (Auth::check()) {
            $user = Auth::user();
            $remainingCart = $user->cart ?? [];
            foreach ($orderedKeys as $k) {
                unset($remainingCart[$k]);
            }
            $user->cart = $remainingCart;
            $user->save();
        }

        // Lưu order ID vào session để bảo vệ double submit
        session(['last_order_id' => $order->id]);

        // 4. Phân luồng thanh toán
        if ($request->payment_method === 'momo') {
            PaymentTransaction::create([
                'order_id' => $order->id,
                'gateway'  => 'momo',
                'amount'   => $order->total_price,
                'status'   => 'pending',
            ]);

            return redirect()->route('orders.momo.start', $order);
        }

        // --- NHÁNH COD: TẠO VẬN ĐƠN GHN NGAY LẬP TỨC ---
        PaymentTransaction::create([
            'order_id' => $order->id,
            'gateway'  => 'cod',
            'amount'   => $order->total_price,
            'status'   => 'pending',
            'message'  => 'Thanh toán khi nhận hàng (COD)',
        ]);

        $order->load('items.product');
        $ghnOrderResponse = $ghnOrders->create($order);

        if (($ghnOrderResponse['code'] ?? null) == 200 && !empty($ghnOrderResponse['data']['order_code'])) {
            $order->update([
                'status'          => 'cod_ordered',
                'ghn_order_code'  => $ghnOrderResponse['data']['order_code'],
                'shipping_status' => 'ready_to_pick',
            ]);

            return redirect()->route('orders.index')
                ->with('success', 'Đặt hàng COD thành công! Mã đơn: #' . $order->id . ' - Mã vận đơn GHN: ' . $ghnOrderResponse['data']['order_code']);
        }

        Log::error('GHN COD Order Failed: ', $ghnOrderResponse ?? []);
        $order->update(['status' => 'cod_ordered']);

        return redirect()->route('orders.index')
            ->with('warning', 'Đặt hàng COD thành công! Mã đơn: #' . $order->id . '. Vận đơn GHN sẽ được cập nhật tự động khi nhân viên xử lý.');
    }


    public function orderHistory()
    {
        $newlyUpdatedOrderIds = Order::where('user_id', Auth::id())
            ->where('has_unread_update', true)
            ->pluck('id')
            ->toArray();

        if (!empty($newlyUpdatedOrderIds)) {
            Order::whereIn('id', $newlyUpdatedOrderIds)->update(['has_unread_update' => false]);
        }

        $ordersQuery = Order::where('user_id', Auth::id())
            ->with(['items.product']);

        if (method_exists(Order::class, 'paymentTransactions')) {
            $ordersQuery->with(['paymentTransactions' => function ($query) {
                $query->latest();
            }]);
        }

        $ordersQuery->with(['items.product', 'reviews']);

        $orders = $ordersQuery->orderByDesc('created_at')->paginate(10);

        if (view()->exists('user.payment.order')) {
            return view('user.payment.order', compact('orders', 'newlyUpdatedOrderIds'));
        }

        return view('orders.index', compact('orders', 'newlyUpdatedOrderIds'));
    }

    public function show(Order $order)
    {
        if ($order->user_id !== Auth::id() && (!Auth::user() || !Auth::user()->isAdmin())) {
            abort(403);
        }

        if ($order->user_id === Auth::id() && $order->has_unread_update) {
            $order->update(['has_unread_update' => false]);
        }

        $withRelations = ['items.product', 'reviews'];
        if (method_exists(Order::class, 'paymentTransactions')) {
            $withRelations['paymentTransactions'] = function ($query) {
                $query->latest();
            };
        }

        $order->load($withRelations);

        if (view()->exists('user.payment.show')) {
            return view('user.payment.show', compact('order'));
        }

        return view('orders.show', compact('order'));
    }

    public function confirmDelivery(Order $order)
    {
        abort_unless($order->user_id === Auth::id(), 403);

        if ($order->shipping_status === 'cancelled') {
            return back()->with('error', 'Đơn hàng này đã bị hủy, không thể xác nhận nhận hàng.');
        }

        if ($order->shipping_status === 'delivered') {
            return back()->with('info', 'Đơn hàng này đã được xác nhận giao thành công trước đó.');
        }

        DB::transaction(function () use ($order) {
            $data = [
                'shipping_status' => 'delivered',
            ];

            if ($order->status === 'cod_ordered' || $order->status === 'pending') {
                $data['status'] = 'paid';
            }

            $order->update($data);
        });

        return back()->with('success', 'Bạn đã xác nhận nhận hàng thành công! Hãy chia sẻ trải nghiệm đánh giá sản phẩm nhé.');
    }

    public function cancel(Order $order, GHNService $ghn)
    {
        abort_unless($order->user_id === Auth::id(), 403);

        if ($order->status === 'cancelled' || $order->shipping_status === 'cancelled') {
            return back()->with('warning', 'Đơn hàng này đã được hủy trước đó.');
        }

        // Không cho phép hủy khi đã chuyển sang trạng thái đang giao trở đi
        if (in_array($order->shipping_status, ['delivering', 'delivered', 'storing', 'return'], true)) {
            return back()->with('error', 'Đơn hàng đang trong quá trình giao hoặc đã giao thành công nên không thể hủy.');
        }

        // Chỉ cho phép hủy ở 2 nhóm trạng thái: Chờ xử lý (pending, not_shipped) hoặc Chờ lấy hàng (ready_to_pick, picking)
        $allowedStatuses = ['pending', 'ready_to_pick', 'picking', 'not_shipped'];

        if (!in_array($order->shipping_status, $allowedStatuses, true)) {
            return back()->with('error', 'Đơn hàng không còn ở trạng thái có thể hủy.');
        }

        if ($order->ghn_order_code) {
            try {
                $response = $ghn->cancelOrder([$order->ghn_order_code]);
                if (($response['code'] ?? null) !== 200) {
                    Log::warning("GHN cancelOrder response code not 200 for order {$order->id}: ", $response ?? []);
                }
            } catch (\Throwable $e) {
                Log::warning("GHN cancelOrder exception for order {$order->id}: " . $e->getMessage());
            }
        }

        DB::transaction(function () use ($order) {
            $order->update([
                'status' => 'cancelled',
                'shipping_status' => 'cancelled',
            ]);

            // Hoàn lại số lượng tồn kho theo từng màu sắc
            foreach ($order->items as $item) {
                $product = $item->product;
                if ($product && method_exists($product, 'restoreColorStock')) {
                    $product->restoreColorStock($item->color ?? null, (int)($item->quantity ?? 1));
                }
            }

            if (method_exists($order, 'paymentTransactions')) {
                $order->paymentTransactions()
                    ->whereIn('status', ['pending', 'initiated'])
                    ->update(['status' => 'cancelled']);

                $order->paymentTransactions()
                    ->where('status', 'paid')
                    ->update(['status' => 'refund_pending']);
            }
        });

        return back()->with('success', 'Đơn hàng #' . $order->id . ' đã được hủy thành công.');
    }
}
