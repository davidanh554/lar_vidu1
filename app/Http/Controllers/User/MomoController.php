<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Services\GHNOrderService;
use App\Services\MomoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MomoController extends Controller
{
    /**
     * Bắt đầu yêu cầu thanh toán MoMo cho đơn hàng mới
     */
    public function start(Order $order, MomoService $momo)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        if ($order->status === 'cancelled' || $order->shipping_status === 'cancelled') {
            return redirect()->route('orders.index')->with('error', 'Đơn hàng này đã bị hủy, không thể tiếp tục thanh toán.');
        }

        if ($order->status === 'paid') {
            return redirect()->route('orders.index')->with('warning', 'Đơn hàng này đã được thanh toán thành công trước đó.');
        }

        return $this->redirectToMomo($order, $this->newTransaction($order), $momo);
    }

    /**
     * Cho phép khách bấm thanh toán lại từ trang lịch sử đơn hàng
     */
    public function payAgain(Order $order, MomoService $momo)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        if ($order->status === 'cancelled' || $order->shipping_status === 'cancelled') {
            return redirect()->route('orders.index')->with('error', 'Đơn hàng này đã bị hủy, không thể tiếp tục thanh toán.');
        }

        if ($order->status === 'paid') {
            return redirect()->route('orders.index')->with('warning', 'Đơn hàng này đã được thanh toán thành công trước đó.');
        }

        return $this->redirectToMomo($order, $this->newTransaction($order), $momo);
    }

    /**
     * Xử lý khi khách hàng hoàn tất thanh toán trên MoMo và được chuyển hướng về website
     */
    public function callback(Request $request, GHNOrderService $ghnOrders, MomoService $momo)
    {
        Log::info('MoMo callback received', [
            'payload'       => $request->except('signature'),
            'has_signature' => $request->has('signature'),
        ]);

        // Nếu kiểm tra chữ ký hoặc kết quả không thành công
        if (!$momo->isValidSuccessfulResponse($request->all())) {
            Log::warning('MoMo callback rejected', [
                'result_code'     => $request->input('resultCode'),
                'order_id'        => $request->input('orderId'),
                'signature_valid' => $momo->isValidResponse($request->all()),
            ]);

            if ($momo->isValidResponse($request->all())) {
                $this->markFailed($request->all(), $momo);
            }

            return redirect()->route('orders.index')->with('error', 'Giao dịch MoMo thất bại.');
        }

        // Thanh toán thành công -> hoàn tất đơn hàng và gọi GHN tạo vận đơn
        $result = $this->completePayment($request->all(), $ghnOrders, $momo);
        $message = in_array($result, ['created', 'already_created'], true)
            ? 'Thanh toán MoMo thành công! Vận đơn GHN đã được khởi tạo.'
            : 'Thanh toán thành công! Đơn hàng đang chờ tạo vận đơn GHN.';

        return redirect()->route('orders.index')->with('success', $message);
    }

    /**
     * Nhận IPN Webhook trực tiếp từ Server MoMo bắn sang (server-to-server)
     */
    public function ipn(Request $request, GHNOrderService $ghnOrders, MomoService $momo)
    {
        Log::info('MoMo IPN received', [
            'payload'       => $request->except('signature'),
            'has_signature' => $request->has('signature'),
        ]);

        if ($momo->isValidSuccessfulResponse($request->all())) {
            $this->completePayment($request->all(), $ghnOrders, $momo);
        } elseif ($momo->isValidResponse($request->all())) {
            $this->markFailed($request->all(), $momo);
        }

        return response()->json(['message' => 'Received']);
    }

    /**
     * Tạo một bản ghi giao dịch mới ở trạng thái pending
     */
    private function newTransaction(Order $order): PaymentTransaction
    {
        return PaymentTransaction::create([
            'order_id' => $order->id,
            'gateway'  => 'momo',
            'amount'   => $order->total_price,
            'status'   => 'pending',
        ]);
    }

    /**
     * Gọi MomoService tạo link thanh toán rồi redirect người dùng sang trang MoMo
     */
    private function redirectToMomo(Order $order, PaymentTransaction $transaction, MomoService $momo)
    {
        $result = $momo->createPayment($order, $transaction);

        return isset($result['payUrl'])
            ? redirect($result['payUrl'])
            : redirect()->route('orders.index')->with('error', $result['message'] ?? 'Không thể kết nối tới MoMo.');
    }

    /**
     * Xử lý nghiệp vụ khi MoMo báo thanh toán thành công: Cập nhật Transaction, Order và tạo đơn GHN
     */
    private function completePayment(array $payload, GHNOrderService $ghnOrders, MomoService $momo): string
    {
        $result = DB::transaction(function () use ($payload, $momo) {
            $transaction = PaymentTransaction::where('gateway', 'momo')
                ->where('gateway_order_id', $payload['orderId'] ?? '')
                ->lockForUpdate()
                ->first();

            if (!$transaction) {
                return 'invalid';
            }

            $order = Order::lockForUpdate()->find($transaction->order_id);
            if (!$order) {
                return 'invalid';
            }

            if ($order->ghn_order_code) {
                return 'already_created';
            }

            if ($order->shipping_status === 'processing') {
                return 'processing';
            }

            // Đối chiếu số tiền thực tế với số tiền giao dịch
            if ((int) $transaction->amount !== (int) ($payload['amount'] ?? 0)) {
                $momo->markFailed($transaction, $payload);
                return 'invalid';
            }

            $order->update(['status' => 'paid', 'shipping_status' => 'processing']);
            $momo->markPaid($transaction, $payload);

            return ['create', $order->id];
        });

        if (!is_array($result)) {
            return (string) $result;
        }

        // Gọi sang GHN để tạo vận đơn giao hàng
        $order = Order::with('items.product')->find($result[1]);
        $response = $ghnOrders->create($order, true);

        if (isset($response['code']) && $response['code'] === 200) {
            $order->update([
                'ghn_order_code'  => $response['data']['order_code'],
                'shipping_status' => 'ready_to_pick',
            ]);
            return 'created';
        }

        Log::error('GHN order failed after MoMo payment', [
            'order_id' => $order->id,
            'response' => $response,
        ]);

        $order->update(['shipping_status' => 'pending']);
        return 'failed';
    }

    /**
     * Đánh dấu giao dịch thất bại
     */
    private function markFailed(array $payload, MomoService $momo): void
    {
        $transaction = PaymentTransaction::where('gateway', 'momo')
            ->where('gateway_order_id', $payload['orderId'] ?? '')
            ->first();

        if ($transaction && $transaction->status !== 'paid') {
            $momo->markFailed($transaction, $payload);
        }
    }
}

