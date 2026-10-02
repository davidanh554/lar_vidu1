<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;

class GeminiChatController extends Controller
{
    /**
     * Xử lý tin nhắn hỏi đáp tự động với Google Gemini AI
     */
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1500',
            'history' => 'nullable|array',
        ]);

        $userMessage = trim($request->input('message'));
        $history = $request->input('history', []);

        // 1. Kiểm tra API Key
        $apiKey = config('services.gemini.api_key');
        $model = config('services.gemini.model', 'gemini-1.5-flash');
        $verifySsl = (bool) config('services.gemini.verify_ssl', false);

        if (empty($apiKey)) {
            return response()->json([
                'success' => true,
                'need_key' => true,
                'reply' => "👋 **Chào bạn! Tôi là Trợ lý AI VUA TABLET.**\n\nHiện tại hệ thống đang chờ Quản trị viên cập nhật `GEMINI_API_KEY` trong tệp cấu hình `.env` để kích hoạt tính năng trò chuyện trực tiếp với Google Gemini.\n\n👉 **Bạn cần hỗ trợ ngay?** Vui lòng bấm vào nút **🎧 Hỗ trợ khách hàng** ở góc dưới màn hình để chat trực tiếp với nhân viên tư vấn của shop nhé!",
            ]);
        }

        // 2. Chuẩn bị Context dữ liệu sản phẩm & thông tin cửa hàng
        $systemInstruction = $this->buildSystemInstruction();

        // 3. Chuẩn bị Payload cho Gemini API
        $contents = [];

        // Lấy tối đa 8 tin nhắn gần nhất để giữ ngữ cảnh hội thoại
        if (is_array($history)) {
            $recentHistory = array_slice($history, -8);
            foreach ($recentHistory as $msg) {
                $role = ($msg['role'] ?? '') === 'model' ? 'model' : 'user';
                $text = trim($msg['text'] ?? '');
                if (!empty($text)) {
                    $contents[] = [
                        'role'  => $role,
                        'parts' => [['text' => $text]],
                    ];
                }
            }
        }

        // Thêm câu hỏi hiện tại của người dùng
        $contents[] = [
            'role'  => 'user',
            'parts' => [['text' => $userMessage]],
        ];

        $payload = [
            'system_instruction' => [
                'parts' => [['text' => $systemInstruction]],
            ],
            'contents' => $contents,
            'generationConfig' => [
                'temperature'     => 0.7,
                'topK'            => 40,
                'topP'            => 0.95,
                'maxOutputTokens' => 1024,
            ],
        ];

        // 4. Gọi API Google Gemini với cơ chế tự động chuyển model dự phòng nếu bị lỗi 404/503
        $candidateModels = array_unique(array_filter([
            $model,
            'gemini-flash-latest',
            'gemini-3.1-flash-lite',
            'gemini-3.5-flash',
        ]));

        $lastError = null;

        foreach ($candidateModels as $currentModel) {
            try {
                $url = "https://generativelanguage.googleapis.com/v1beta/models/{$currentModel}:generateContent?key={$apiKey}";

                $response = Http::withOptions(['verify' => $verifySsl])
                    ->withHeaders([
                        'Content-Type' => 'application/json',
                    ])
                    ->timeout(20)
                    ->post($url, $payload);

                if ($response->successful()) {
                    $data = $response->json();
                    $replyText = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

                    if (!empty($replyText)) {
                        return response()->json([
                            'success' => true,
                            'reply'   => $replyText,
                        ]);
                    }
                }

                $status = $response->status();
                $lastError = [
                    'model'  => $currentModel,
                    'status' => $status,
                    'body'   => $response->json() ?? $response->body(),
                ];

                // Nếu lỗi 400/403 (Key sai/không có quyền) thì không cần thử model khác
                if ($status === 400 || $status === 403) {
                    break;
                }

                // Nếu lỗi 404 (model không hỗ trợ) hoặc 503 (quá tải tạm thời), tiếp tục thử model tiếp theo
                Log::warning("Gemini model {$currentModel} returned {$status}, attempting fallback...");

            } catch (\Throwable $e) {
                Log::warning("Gemini model {$currentModel} exception: " . $e->getMessage() . ", attempting fallback...");
            }
        }

        if ($lastError) {
            Log::error('Gemini All Models Failed', $lastError);
        }

        $errMsg = "Dạ, hiện tại em chưa kết nối được tới máy chủ AI hoặc API Key không hợp lệ. Quý khách vui lòng thử lại hoặc bấm vào nút **🎧 Hỗ trợ khách hàng** bên dưới để được nhân viên tư vấn hỗ trợ tức thì nhé!";
        return response()->json([
            'success' => false,
            'reply'   => $errMsg,
        ]);
    }

    /**
     * Xây dựng ngữ cảnh chuyên sâu về cửa hàng VUA TABLET & danh mục máy tính bảng
     */
    private function buildSystemInstruction(): string
    {
        // Cache danh mục sản phẩm 5 phút để tối ưu hiệu năng
        $productsContext = Cache::remember('gemini_ai_product_catalog', 300, function () {
            $products = Product::where('is_active', true)
                ->select('id', 'name', 'slug', 'price', 'sale_price', 'chip', 'screen_size', 'storage', 'ram', 'stock_quantity')
                ->take(35)
                ->get();

            $lines = [];
            foreach ($products as $p) {
                $priceCurrent = number_format($p->sale_price ?? $p->price) . 'đ';
                $priceOld = $p->sale_price ? (' (Gốc: ' . number_format($p->price) . 'đ)') : '';
                $stock = $p->stock_quantity > 0 ? 'Còn hàng' : 'Hết hàng tạm thời';
                $url = route('products.show', $p->slug ?? $p->id);

                $lines[] = "- **{$p->name}**: Giá {$priceCurrent}{$priceOld} | Chip: {$p->chip} | Màn: {$p->screen_size} | Bộ nhớ: {$p->storage} | {$stock} | Link: {$url}";
            }
            return implode("\n", $lines);
        });

        $customerName = Auth::check() ? Auth::user()->name : 'Quý khách';

        return <<<EOT
Bạn là "Trợ lý AI VUA TABLET" - chuyên viên tư vấn bán hàng và chuyên gia kỹ thuật về máy tính bảng (iPad, tablet Android) của hệ sinh thái cửa hàng "VUA TABLET" tại Việt Nam.
Người đang trò chuyện với bạn: {$customerName}.

NGUYÊN TẮC GIAO TIẾP:
1. Xưng hô: "Em" hoặc "Shop", gọi khách là "Anh/Chị" hoặc "Bạn" một cách lịch sự, thân thiện, hào hứng và tận tâm.
2. Phong cách trả lời: Ngắn gọn, súc tích, dễ đọc, sử dụng Markdown (in đậm thông số chính, gạch đầu dòng, danh sách). Tránh viết đoạn văn quá dài gây mỏi mắt.
3. Khi tư vấn máy: Dựa vào nhu cầu (học tập, vẽ vời, đồ họa, chơi game, giải trí, làm việc) và ngân sách của khách để chọn máy phù hợp nhất trong danh sách sản phẩm của shop.
4. LUÔN CHÈN LINK SẢN PHẨM: Khi gợi ý bất kỳ sản phẩm nào có trong danh mục, hãy tạo link Markdown dạng [Xem chi tiết Tên Máy](URL_tương_ứng) để khách dễ bấm vào xem ngay.
5. Về chính sách shop VUA TABLET:
   - Bảo hành: 12 tháng chính hãng, 1 đổi 1 trong 30 ngày đầu tiên nếu có lỗi phần cứng do nhà sản xuất.
   - Giao hàng: Giao hàng toàn quốc qua Giao Hàng Nhanh (GHN), thời gian 1-3 ngày, hỗ trợ đồng kiểm khi nhận hàng.
   - Thanh toán: Hỗ trợ tiền mặt khi nhận hàng (COD), Chuyển khoản, hoặc Ví điện tử MoMo.
   - Ưu đãi: Vòng quay may mắn (Lucky Wheel) trúng Voucher giảm giá mỗi ngày; Tích lũy Xu mua sắm khi xem video shopping & mua hàng.
6. HỖ TRỢ KHÁCH HÀNG (QUAN TRỌNG):
   - Nếu khách hàng cần khiếu nại, hủy/sửa đơn hàng gấp, xử lý bảo hành người thật, hoặc muốn gặp trực tiếp nhân viên / Quản trị viên: Hãy lịch sự hướng dẫn khách bấm vào nút **"🎧 Hỗ trợ khách hàng"** (màu xanh ở góc dưới màn hình) để nhắn tin trực tiếp với nhân viên hỗ trợ VUA TABLET.

DANH SÁCH MÁY TÍNH BẢNG ĐANG KINH DOANH TẠI VUA TABLET:
{$productsContext}
EOT;
    }
}
