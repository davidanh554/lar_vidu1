<?php

namespace App\Services;

use App\Models\Order;
use App\Models\PaymentTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SepayService
{
    /**
     * Lấy thông tin cấu hình SePay / VietQR
     */
    public function getConfig(): array
    {
        return [
            'account_no'   => config('services.sepay.account_no', env('SEPAY_ACCOUNT_NO', '0987654321')),
            'bank'         => config('services.sepay.bank', env('SEPAY_BANK', 'MBBank')),
            'account_name' => config('services.sepay.account_name', env('SEPAY_ACCOUNT_NAME', 'VUA TABLET')),
            'api_key'      => config('services.sepay.api_key', env('SEPAY_API_KEY', '')),
            'webhook_token'=> config('services.sepay.webhook_token', env('SEPAY_WEBHOOK_TOKEN', '')),
        ];
    }

    /**
     * Cú pháp nội dung chuyển khoản cho đơn hàng (SePay tự động nhận diện)
     */
    public function getTransferContent(Order $order): string
    {
        return 'DH' . $order->id;
    }

    /**
     * Tạo đường dẫn mã QR VietQR chuẩn SePay
     */
    public function generateQrUrl(Order $order): string
    {
        $cfg = $this->getConfig();
        $accountNo = urlencode($cfg['account_no']);
        $bank = urlencode($cfg['bank']);
        $amount = (int) $order->total_price;
        $content = urlencode($this->getTransferContent($order));

        // Đường dẫn tạo VietQR trực tiếp từ SePay
        return "https://qr.sepay.vn/img?acc={$accountNo}&bank={$bank}&amount={$amount}&des={$content}&template=compact";
    }

    /**
     * Đường dẫn dự phòng chuẩn VietQR.io (Napas 247)
     */
    public function generateVietQrUrl(Order $order): string
    {
        $cfg = $this->getConfig();
        $accountNo = $cfg['account_no'];
        $bank = $cfg['bank'];
        $amount = (int) $order->total_price;
        $content = urlencode($this->getTransferContent($order));
        $name = urlencode($cfg['account_name']);

        return "https://api.vietqr.io/image/{$bank}-{$accountNo}-compact2.jpg?amount={$amount}&addInfo={$content}&accountName={$name}";
    }

    /**
     * Lấy đầy đủ thông tin thanh toán phục vụ hiển thị
     */
    public function getPaymentDetails(Order $order): array
    {
        $cfg = $this->getConfig();
        $transferContent = $this->getTransferContent($order);

        return [
            'order_id'         => $order->id,
            'amount'           => (int) $order->total_price,
            'amount_formatted' => number_format($order->total_price, 0, ',', '.') . '₫',
            'bank'             => $cfg['bank'],
            'account_no'       => $cfg['account_no'],
            'account_name'     => $cfg['account_name'],
            'transfer_content' => $transferContent,
            'qr_url'           => $this->generateQrUrl($order),
            'vietqr_url'       => $this->generateVietQrUrl($order),
        ];
    }

    /**
     * Xác thực Webhook gửi từ SePay (Header Authorization hoặc Webhook Token)
     */
    public function verifyWebhook(Request $request): bool
    {
        $cfg = $this->getConfig();
        $configuredToken = $cfg['webhook_token'] ?: $cfg['api_key'];

        // Nếu chưa cấu hình token trên .env (chế độ dev / test) thì bỏ qua kiểm tra token
        if (empty($configuredToken)) {
            return true;
        }

        // 1. Kiểm tra chữ ký HMAC-SHA256 nếu SePay dùng phương thức HMAC-SHA256
        $signature = $request->header('X-Sepay-Signature') ?: $request->header('x-sepay-signature');
        if ($signature) {
            $computedSignature = hash_hmac('sha256', $request->getContent(), $configuredToken);
            if (hash_equals($signature, $computedSignature)) {
                return true;
            }
        }

        // 2. SePay gửi Header: "Apikey <TOKEN>" hoặc "Bearer <TOKEN>"
        $authHeader = $request->header('Authorization', '');
        if (str_starts_with($authHeader, 'Apikey ') || str_starts_with($authHeader, 'Bearer ')) {
            $receivedToken = trim(substr($authHeader, 7));
            return hash_equals($configuredToken, $receivedToken);
        }

        // 3. Hoặc kiểm tra qua query param / body nếu có
        $paramToken = $request->input('token') ?: $request->input('api_key');
        if (!empty($paramToken)) {
            return hash_equals($configuredToken, (string) $paramToken);
        }

        return false;
    }

    /**
     * Xử lý xác nhận thanh toán đơn hàng thành công và tạo vận đơn GHN
     */
    public function processPaymentSuccess(Order $order, array $transactionData, GHNOrderService $ghnOrders): array
    {
        $result = DB::transaction(function () use ($order, $transactionData) {
            $lockedOrder = Order::lockForUpdate()->find($order->id);

            if (!$lockedOrder) {
                return ['status' => 'order_not_found'];
            }

            if ($lockedOrder->status === 'paid') {
                return ['status' => 'already_paid', 'order' => $lockedOrder];
            }

            // Cập nhật trạng thái Order
            $lockedOrder->update([
                'status'          => 'paid',
                'shipping_status' => 'processing',
                'has_unread_update' => true,
            ]);

            // Tìm hoặc cập nhật bản ghi Transaction
            $transaction = PaymentTransaction::where('order_id', $lockedOrder->id)
                ->where('gateway', 'sepay')
                ->latest()
                ->first();

            $transId = (string) ($transactionData['id'] ?? $transactionData['referenceCode'] ?? ('SEPAY_' . time()));

            if ($transaction) {
                $transaction->update([
                    'status'            => 'paid',
                    'transaction_id'    => $transId,
                    'result_code'       => 0,
                    'message'           => 'Thanh toán thành công qua SePay VietQR',
                    'response_payload'  => $transactionData,
                    'paid_at'           => Carbon::now(),
                ]);
            } else {
                PaymentTransaction::create([
                    'order_id'          => $lockedOrder->id,
                    'gateway'           => 'sepay',
                    'gateway_order_id'  => 'DH' . $lockedOrder->id,
                    'transaction_id'    => $transId,
                    'amount'            => $lockedOrder->total_price,
                    'status'            => 'paid',
                    'result_code'       => 0,
                    'message'           => 'Thanh toán thành công qua SePay VietQR',
                    'response_payload'  => $transactionData,
                    'paid_at'           => Carbon::now(),
                ]);
            }

            return ['status' => 'success', 'order' => $lockedOrder];
        });

        if ($result['status'] === 'success') {
            // Tự động gọi sang GHN để tạo vận đơn
            $freshOrder = Order::with('items.product')->find($order->id);
            if ($freshOrder && empty($freshOrder->ghn_order_code)) {
                try {
                    $response = $ghnOrders->create($freshOrder, true);
                    if (isset($response['code']) && $response['code'] === 200) {
                        $freshOrder->update([
                            'ghn_order_code'  => $response['data']['order_code'],
                            'shipping_status' => 'ready_to_pick',
                        ]);
                        Log::info("GHN order created successfully for Order #{$freshOrder->id}: {$response['data']['order_code']}");
                    } else {
                        Log::warning("GHN order creation failed for Order #{$freshOrder->id}:", $response ?? []);
                    }
                } catch (\Throwable $e) {
                    Log::error("GHN order creation exception for Order #{$freshOrder->id}: " . $e->getMessage());
                }
            }
        }

        return $result;
    }
}
