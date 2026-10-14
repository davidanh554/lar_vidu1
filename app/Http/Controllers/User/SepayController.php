<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Services\GHNOrderService;
use App\Services\GHNService;
use App\Services\SepayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SepayController extends Controller
{
    /**
     * Hiển thị trang quét mã VietQR SePay để khách thanh toán
     */
    public function pay(Order $order, SepayService $sepay)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền truy cập đơn hàng này.');
        }

        if ($order->status === 'cancelled' || $order->shipping_status === 'cancelled') {
            return redirect()->route('orders.index')->with('error', 'Đơn hàng này đã bị hủy, không thể tiếp tục thanh toán.');
        }

        if ($order->status === 'paid') {
            return redirect()->route('orders.show', $order->id)->with('success', 'Đơn hàng đã được thanh toán thành công!');
        }

        // Đảm bảo có bản ghi PaymentTransaction cho SePay
        $transaction = PaymentTransaction::firstOrCreate(
            [
                'order_id' => $order->id,
                'gateway'  => 'sepay',
            ],
            [
                'gateway_order_id' => 'DH' . $order->id,
                'amount'           => $order->total_price,
                'status'           => 'pending',
                'message'          => 'Chờ chuyển khoản VietQR (SePay)',
            ]
        );

        $paymentDetails = $sepay->getPaymentDetails($order);

        return view('user.payment.sepay', compact('order', 'paymentDetails'));
    }

    /**
     * Endpoint API cho JavaScript tự động kiểm tra xem đơn hàng đã được thanh toán chưa (Polling real-time)
     */
    public function checkStatus(Order $order)
    {
        if (Auth::check() && $order->user_id !== Auth::id() && (!Auth::user()->isAdmin())) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $order->refresh();

        return response()->json([
            'order_id'        => $order->id,
            'is_paid'         => $order->status === 'paid',
            'status'          => $order->status,
            'shipping_status' => $order->shipping_status,
            'ghn_order_code'  => $order->ghn_order_code,
            'redirect_url'    => route('orders.show', $order->id),
        ]);
    }

    /**
     * Khách hàng bấm hủy giao dịch ngay tại trang quét mã QR (hoàn lại kho, xu và voucher)
     */
    public function cancelPayment(Order $order, GHNService $ghn)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        if ($order->status === 'paid') {
            return back()->with('error', 'Đơn hàng đã thanh toán thành công, không thể hủy thanh toán.');
        }

        DB::transaction(function () use ($order, $ghn) {
            $order->update([
                'status'          => 'cancelled',
                'shipping_status' => 'cancelled',
            ]);

            // Phục hồi tồn kho theo từng màu sắc
            foreach ($order->items as $item) {
                $product = $item->product;
                if ($product && method_exists($product, 'restoreColorStock')) {
                    $product->restoreColorStock($item->color ?? null, (int) ($item->quantity ?? 1));
                }
            }

            // Hoàn lại Xu cho người dùng nếu có dùng
            if ($order->coins_used > 0) {
                $user = $order->user;
                if ($user) {
                    $user->increment('coins', $order->coins_used);
                }
            }

            // Hoàn lại voucher nếu có dùng
            if ($order->coupon_code) {
                $coupon = \App\Models\Coupon::where('code', $order->coupon_code)->first();
                if ($coupon) {
                    $coupon->decrement('used_count');
                    \App\Models\UserCoupon::where('user_id', $order->user_id)
                        ->where('coupon_id', $coupon->id)
                        ->update(['is_used' => false, 'used_at' => null]);
                }
            }

            // Khôi phục lại toàn bộ sản phẩm của đơn hàng vào giỏ hàng
            $user = $order->user;
            if ($user) {
                $cart = $user->cart ?? [];
                if (!is_array($cart)) {
                    $cart = [];
                }
                foreach ($order->items as $item) {
                    $product = $item->product;
                    if ($product) {
                        $key = $item->product_id . ($item->color ? ('_' . $item->color) : '');
                        $cart[$key] = [
                            'product_id' => $item->product_id,
                            'name'       => $product->name,
                            'price'      => (int) $item->price,
                            'quantity'   => (int) $item->quantity,
                            'color'      => $item->color,
                            'image'      => $product->image ?? '',
                        ];
                    }
                }
                $user->cart = $cart;
                $user->save();
                session(['cart' => $cart]);
            }

            // Cập nhật trạng thái transaction
            PaymentTransaction::where('order_id', $order->id)
                ->where('gateway', 'sepay')
                ->update(['status' => 'cancelled', 'message' => 'Người dùng hủy thanh toán VietQR để sửa lại thông tin']);
        });

        session()->forget('last_order_id');

        return redirect()->route('checkout.index')->with('info', 'Đã hủy đơn hàng #' . $order->id . ' và khôi phục giỏ hàng thành công. Bạn có thể thay đổi thông tin và tiến hành đặt lại!');
    }

    /**
     * Nút mô phỏng thanh toán thành công (Dành riêng cho môi trường Test / Localhost)
     * Giúp giảng viên hoặc người phát triển test toàn bộ luồng tạo vận đơn GHN mà không cần nạp tiền thật
     */
    public function mockSuccess(Order $order, SepayService $sepay, GHNOrderService $ghnOrders)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        if ($order->status === 'paid') {
            return redirect()->route('orders.show', $order->id)->with('info', 'Đơn hàng này đã được thanh toán trước đó.');
        }

        $mockPayload = [
            'id'              => 'MOCK_' . time(),
            'gateway'         => config('services.sepay.bank', 'MBBank'),
            'transactionDate' => now()->toDateTimeString(),
            'accountNumber'   => config('services.sepay.account_no', '0987654321'),
            'content'         => 'DH' . $order->id . ' DEMO TEST',
            'transferType'    => 'in',
            'transferAmount'  => (int) $order->total_price,
            'referenceCode'   => 'FT_DEMO_' . strtoupper(bin2hex(random_bytes(4))),
        ];

        $result = $sepay->processPaymentSuccess($order, $mockPayload, $ghnOrders);

        return redirect()->route('orders.show', $order->id)
            ->with('success', 'Mô phỏng thanh toán VietQR SePay thành công! Vận đơn GHN đã được tự động khởi tạo.');
    }

    /**
     * Nhận Webhook IPN từ SePay khi có biến động số dư tài khoản ngân hàng
     */
    public function webhook(Request $request, SepayService $sepay, GHNOrderService $ghnOrders)
    {
        Log::info('SePay Webhook Received:', $request->all());

        // 1. Kiểm tra xác thực token (nếu cấu hình)
        if (!$sepay->verifyWebhook($request)) {
            Log::warning('SePay Webhook Unauthorized token');
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $payload = $request->all();

        // 2. Chỉ xử lý tiền vào (transferType = 'in')
        $transferType = strtolower($payload['transferType'] ?? 'in');
        if ($transferType !== 'in') {
            return response()->json(['success' => true, 'message' => 'Ignored outgoing transaction']);
        }

        // 3. Trích xuất mã đơn hàng từ nội dung chuyển khoản
        $content = (string) ($payload['content'] ?? $payload['description'] ?? '');
        $code = (string) ($payload['code'] ?? '');

        $orderId = null;

        // Ưu tiên regex tìm cú pháp "DH" + mã đơn (ví dụ: DH47, DH 47)
        if (preg_match('/DH\s*(\d+)/i', $content, $matches)) {
            $orderId = (int) $matches[1];
        } elseif (!empty($code) && is_numeric($code)) {
            $orderId = (int) $code;
        } elseif (preg_match('/\b(\d{1,8})\b/', $content, $matches)) {
            // Dự phòng tìm số trong nội dung nếu có
            $orderId = (int) $matches[1];
        }

        if (!$orderId) {
            Log::warning('SePay Webhook: Cannot detect order ID from content: ' . $content);
            return response()->json(['success' => false, 'message' => 'Order ID not found in content'], 422);
        }

        $order = Order::find($orderId);
        if (!$order) {
            Log::warning("SePay Webhook: Order #{$orderId} not found in database");
            return response()->json(['success' => false, 'message' => "Order #{$orderId} not found"], 404);
        }

        // 4. Đối chiếu số tiền chuyển khoản với tổng giá trị đơn hàng
        $transferAmount = (int) ($payload['transferAmount'] ?? 0);
        if ($transferAmount < (int) $order->total_price) {
            Log::warning("SePay Webhook: Amount mismatch for Order #{$orderId}. Expected {$order->total_price}, received {$transferAmount}");
            return response()->json([
                'success' => false, 
                'message' => 'Transfer amount is less than order total price'
            ], 400);
        }

        // 5. Cập nhật thanh toán thành công và khởi tạo vận đơn GHN
        $result = $sepay->processPaymentSuccess($order, $payload, $ghnOrders);

        Log::info("SePay Webhook Processed successfully for Order #{$orderId}");

        return response()->json([
            'success' => true,
            'message' => 'Payment processed successfully',
            'order_id' => $orderId,
            'result' => $result['status'] ?? 'success'
        ]);
    }
}
