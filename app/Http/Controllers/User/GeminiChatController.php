<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\AiChatMessage;

class GeminiChatController extends Controller
{
    /**
     * Lấy lịch sử chat của tài khoản hiện tại
     */
    public function history(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'messages' => [],
            ], 401);
        }

        $messages = AiChatMessage::where('user_id', Auth::id())
            ->orderBy('created_at', 'asc')
            ->take(50)
            ->get(['id', 'role', 'content', 'created_at']);

        return response()->json([
            'success' => true,
            'messages' => $messages,
        ]);
    }

    /**
     * Xóa toàn bộ lịch sử chat của tài khoản hiện tại
     */
    public function clear(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Chưa đăng nhập',
            ], 401);
        }

        AiChatMessage::where('user_id', Auth::id())->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa toàn bộ lịch sử trò chuyện AI.',
        ]);
    }

    /**
     * Xử lý tin nhắn hỏi đáp tự động với Google Gemini AI
     */
    public function chat(Request $request)
    {
        // 0. Bắt buộc đăng nhập để nhận tư vấn từ AI
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'require_login' => true,
                'reply' => "Vui lòng đăng nhập tài khoản để nhận tư vấn từ Trợ lý AI!",
                'redirect' => route('login'),
            ], 401);
        }

        $request->validate([
            'message' => 'required|string|max:1500',
        ]);

        $userMessage = trim($request->input('message'));
        $userId = Auth::id();

        // 1. Lưu câu hỏi của người dùng vào Database
        $userMsgRecord = AiChatMessage::create([
            'user_id' => $userId,
            'role'    => 'user',
            'content' => $userMessage,
        ]);

        // 1.1. GUARDRAIL TẦNG 1: Kiểm tra câu hỏi ngoài lề (Toán học, 1+1, giải bài tập, làm thơ, code, thời tiết...)
        // Lọc trực tiếp tại Backend: Từ chối lịch sự ngay lập tức, tiết kiệm 100% token Gemini API, tốc độ phản hồi tức thì
        if ($this->isOffTopicMessage($userMessage)) {
            $refusalReply = $this->getOffTopicRefusalMessage();

            AiChatMessage::create([
                'user_id' => $userId,
                'role'    => 'model',
                'content' => $refusalReply,
            ]);

            return response()->json([
                'success' => true,
                'reply'   => $refusalReply,
            ]);
        }

        // 2. Kiểm tra API Key
        $apiKey = config('services.gemini.api_key');
        $model = config('services.gemini.model', 'gemini-1.5-flash');
        $verifySsl = (bool) config('services.gemini.verify_ssl', false);

        if (empty($apiKey)) {
            $botReply = "**Chào bạn! Tôi là Trợ lý AI VUA TABLET.**\n\nHiện tại hệ thống đang được Quản trị viên cập nhật cấu hình kết nối.\n\n**Bạn cần hỗ trợ ngay?** Vui lòng bấm vào nút **Hỗ trợ khách hàng** ở góc dưới màn hình để chat trực tiếp với nhân viên tư vấn của shop nhé!";
            
            // Lưu câu trả lời mặc định vào DB
            AiChatMessage::create([
                'user_id' => $userId,
                'role'    => 'model',
                'content' => $botReply,
            ]);

            return response()->json([
                'success'  => true,
                'need_key' => true,
                'reply'    => $botReply,
            ]);
        }

        // 3. Chuẩn bị Context dữ liệu sản phẩm & thông tin cửa hàng
        $systemInstruction = $this->buildSystemInstruction();

        // 4. Chuẩn bị Payload cho Gemini API dựa trên lịch sử trong CSDL
        $contents = [];

        // Lấy tối đa 8 tin nhắn gần nhất trước tin nhắn này từ CSDL để giữ ngữ cảnh hội thoại
        $dbHistory = AiChatMessage::where('user_id', $userId)
            ->where('id', '<', $userMsgRecord->id)
            ->orderBy('id', 'desc')
            ->take(8)
            ->get()
            ->reverse();

        foreach ($dbHistory as $item) {
            $contents[] = [
                'role'  => $item->role === 'model' ? 'model' : 'user',
                'parts' => [['text' => $item->content]],
            ];
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
                'temperature'     => 0.2, // Nhiệt độ thấp để AI bám sát quy tắc nghiệp vụ, không tùy tiện trả lời ngoài lề
                'topK'            => 40,
                'topP'            => 0.9,
                'maxOutputTokens' => 700, // Tối ưu số token sinh ra để tiết kiệm chi phí
            ],
        ];

        // 5. Gọi API Google Gemini với cơ chế tự động chuyển model dự phòng nếu bị lỗi 404/503
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
                        // Lưu câu trả lời của AI vào Database
                        AiChatMessage::create([
                            'user_id' => $userId,
                            'role'    => 'model',
                            'content' => $replyText,
                        ]);

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

        // Nếu thất bại hoàn toàn, xóa câu hỏi vừa tạo để không làm rác CSDL
        $userMsgRecord->delete();

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

QUY TẮC PHẠM VI NGHIỆP VỤ & TỪ CHỐI CÂU HỎI NGOÀI LỀ (QUAN TRỌNG NHẤT):
1. PHẠM VI HOẠT ĐỘNG DUY NHẤT:
   - Bạn CHỈ ĐƯỢC PHÉP hỗ trợ các vấn đề liên quan trực tiếp đến:
     * Máy tính bảng (iPad, tablet Android, tablet vẽ/học tập/làm việc/gaming) và phụ kiện (Apple Pencil, bàn phím, bao da, sạc cáp).
     * Tư vấn chọn máy phù hợp nhu cầu và ngân sách, so sánh thông số kỹ thuật (chip, màn hình, RAM, pin, bộ nhớ).
     * Bảng giá, khuyến mãi, voucher, chính sách bảo hành 12 tháng, đổi trả 30 ngày, giao hàng GHN, thanh toán COD/MoMo tại shop VUA TABLET.
     * Chào hỏi lịch sự và hướng dẫn bấm nút "Hỗ trợ khách hàng" khi cần gặp nhân viên.

2. TUYỆT ĐỐI KHÔNG TRẢ LỜI CÂU HỎI NGOÀI LỀ (ĐỂ TRÁNH LÃNG PHÍ TOKEN VÀ DƯ THỪA CHỨC NĂNG):
   - NGHIÊM CẤM giải toán hoặc tính toán số học (ví dụ: "1+1 bằng mấy", tính toán đại số, giải phương trình, hình học...).
   - NGHIÊM CẤM viết code/lập trình, không làm thơ, không kể chuyện, không giải bài tập hộ, không trả lời kiến thức văn hóa, xã hội, lịch sử, thời tiết, giải đố.
   - Khi gặp bất kỳ câu hỏi nào ngoài phạm vi bán hàng máy tính bảng, bạn BẮT BUỘC TỪ CHỐI LỊCH SỰ theo mẫu chuẩn sau:
     "Dạ, em là Trợ lý AI của VUA TABLET, em chỉ hỗ trợ tư vấn về máy tính bảng, giá bán và dịch vụ của shop thôi ạ. Em không hỗ trợ giải đáp các câu hỏi ngoài phạm vi cửa hàng. Anh/Chị có cần em tư vấn mẫu tablet nào phù hợp với nhu cầu không ạ?"
   - TUYỆT ĐỐI KHÔNG trả lời đáp án trước rồi mới từ chối (Ví dụ: CẤM nói "1+1 = 2 nhưng em chỉ là trợ lý..."). Hãy từ chối trực tiếp và lịch sự!

NGUYÊN TẮC GIAO TIẾP & BÁN HÀNG:
1. Xưng hô: "Em" hoặc "Shop", gọi khách là "Anh/Chị" hoặc "Bạn" một cách lịch sự, thân thiện, hào hứng và tận tâm.
2. Phong cách trả lời: Ngắn gọn, súc tích, dễ đọc, sử dụng Markdown (in đậm thông số chính, gạch đầu dòng, danh sách). Tránh viết đoạn văn quá dài gây mỏi mắt và tốn token.
3. Khi tư vấn máy: Dựa vào nhu cầu và ngân sách của khách để chọn máy phù hợp nhất trong danh sách sản phẩm của shop.
4. LUÔN CHÈN LINK SẢN PHẨM: Khi gợi ý bất kỳ sản phẩm nào có trong danh mục, hãy tạo link Markdown dạng [Xem chi tiết Tên Máy](URL_tương_ứng) để khách dễ bấm vào xem ngay.
5. Về chính sách shop VUA TABLET:
   - Bảo hành: 12 tháng chính hãng, 1 đổi 1 trong 30 ngày đầu tiên nếu có lỗi phần cứng do nhà sản xuất.
   - Giao hàng: Giao hàng toàn quốc qua Giao Hàng Nhanh (GHN), thời gian 1-3 ngày, hỗ trợ đồng kiểm khi nhận hàng.
   - Thanh toán: Hỗ trợ tiền mặt khi nhận hàng (COD), Chuyển khoản, hoặc Ví điện tử MoMo.
   - Ưu đãi: Vòng quay may mắn (Lucky Wheel) trúng Voucher giảm giá mỗi ngày; Tích lũy Xu mua sắm khi xem video shopping & mua hàng.
6. HỖ TRỢ KHÁCH HÀNG:
   - Nếu khách cần khiếu nại, hủy/sửa đơn hàng, gặp người thật: Hướng dẫn khách bấm vào nút **"Hỗ trợ khách hàng"** (màu xanh ở góc dưới màn hình).
7. TUYỆT ĐỐI KHÔNG giới thiệu hoặc đề cập đến tên 'Google Gemini', 'Google AI' hoặc bất kỳ công nghệ nền tảng thứ ba nào. Chỉ xưng là 'Trợ lý AI VUA TABLET' hoặc 'Em'. Không dùng biểu tượng robot.

DANH SÁCH MÁY TÍNH BẢNG ĐANG KINH DOANH TẠI VUA TABLET:
{$productsContext}
EOT;
    }

    /**
     * Kiểm tra xem tin nhắn người dùng có phải là câu hỏi ngoài lề (toán học, code, thơ ca, đố vui...) hay không.
     * Áp dụng Guardrail tầng 1 (Backend Local Pre-filter): Từ chối ngay lập tức, tiết kiệm 100% token gọi Gemini API.
     */
    private function isOffTopicMessage(string $message): bool
    {
        $raw = trim($message);
        $normalized = mb_strtolower($raw, 'UTF-8');

        // Danh sách từ khóa liên quan đến sản phẩm / dịch vụ shop VUA TABLET
        $tabletKeywords = [
            'ipad', 'tablet', 'máy tính bảng', 'tab', 'galaxy tab', 'xiaomi pad', 'lenovo', 'surface',
            'màn hình', 'pin', 'bộ nhớ', 'chip', 'ram', 'rom', 'inch', 'gb', 'tb', 'camera', 'loa',
            'apple pencil', 'bút', 'stylus', 'bàn phím', 'bao da', 'tai nghe', 'sạc',
            'giá', 'triệu', 'k', 'vnđ', 'vnd', 'đ', 'tiền', 'ngân sách', 'tầm', 'khoảng',
            'bảo hành', 'đổi trả', 'ship', 'giao hàng', 'vận chuyển', 'ghn', 'momo', 'cod', 'thanh toán',
            'voucher', 'khuyến mãi', 'vòng quay', 'xu', 'đơn hàng', 'mua', 'bán', 'đặt', 'shop', 'cửa hàng',
            'tư vấn', 'chính hãng', 'cũ', 'mới', 'likenew', 'giảm giá', 'sale', 'so sánh', 'học tập', 'vẽ', 'chơi game'
        ];

        $hasTabletContext = false;
        foreach ($tabletKeywords as $kw) {
            if (mb_strpos($normalized, $kw) !== false) {
                $hasTabletContext = true;
                break;
            }
        }

        // 1. Phép tính số học thuần túy (VD: "1+1", "1 + 1", "1+1=?", "1+1 bằng mấy", "2*3", "10/5", "100 - 30", "5^2")
        if (preg_match('/^\s*\d+[\s\+\-\*\/\^\%xX÷]+\d+[\s\+\-\*\/\^\%xX÷\d\=\?\s]*(bằng mấy|bằng bao nhiêu|là mấy|là bao nhiêu)?\s*$/u', $normalized)) {
            return true;
        }

        // Phép tính có dấu phép tính hoặc từ "cộng", "trừ", "nhân", "chia" kèm số
        // Ví dụ: "1 + 1 bằng mấy", "1 cộng 1 bằng mấy", "1 + 1 bằng mấy con", "tính 1+1", "1+1 là bao nhiêu"
        if (preg_match('/(\d+)\s*(\+|\-|x|\*|\/|÷|cộng|trừ|nhân|chia)\s*(\d+)/u', $normalized)) {
            if (!$hasTabletContext) {
                return true;
            }
        }

        // 2. Các dạng câu hỏi toán học & giải bài tập rõ ràng
        $mathPatterns = [
            '/\b(1\s*(\+|\bcộng\b)\s*1|2\s*(\+|\bcộng\b)\s*2)\b/u',
            '/\b(giải bài toán|giải toán|giải phương trình|hệ phương trình|tính tích phân|đạo hàm|căn bậc hai|tính diện tích|chu vi hình|bất đẳng thức|định lý|sin\s*\(|cos\s*\(|tan\s*\()\b/u',
            '/\b(tính nhẩm|phép tính)\b/u',
            '/\b(bằng mấy|bằng bao nhiêu|là mấy|là bao nhiêu)\b.*[\+\-\*\/]/u',
            '/[\+\-\*\/].*\b(bằng mấy|bằng bao nhiêu|là mấy|là bao nhiêu)\b/u',
        ];
        foreach ($mathPatterns as $pattern) {
            if (preg_match($pattern, $normalized)) {
                return true;
            }
        }

        // 3. Yêu cầu lập trình / Viết code / Script
        $programmingPatterns = [
            '/\b(viết code|lập trình|code python|code c\+\+|code java|code php|code javascript|code html|viết hàm|viết thuật toán|debug lỗi)\b/u',
        ];
        foreach ($programmingPatterns as $pattern) {
            if (preg_match($pattern, $normalized)) {
                return true;
            }
        }

        // 4. Yêu cầu văn học, thơ ca, kể chuyện, giải đố
        $creativePatterns = [
            '/\b(làm thơ|viết thơ|bài thơ|sáng tác thơ|kể chuyện|truyện cười|chuyện cười|chuyện ma|cổ tích|viết bài văn|bài văn mẫu|tả con|thuyết minh về|đố vui)\b/u',
        ];
        foreach ($creativePatterns as $pattern) {
            if (preg_match($pattern, $normalized)) {
                return true;
            }
        }

        // 5. Kiến thức đời sống / tổng quát hoàn toàn không liên quan (khi không có ngữ cảnh shop)
        $generalKnowledgePatterns = [
            '/\b(thời tiết hôm nay|dự báo thời tiết|thời tiết ngày mai)\b/u',
            '/\b(thủ đô của|dân số của nước|tổng thống nước nào|chiến tranh thế giới|ai phát minh ra)\b/u',
        ];
        if (!$hasTabletContext) {
            foreach ($generalKnowledgePatterns as $pattern) {
                if (preg_match($pattern, $normalized)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Mẫu phản hồi từ chối lịch sự và định hướng khách quay lại sản phẩm của cửa hàng
     */
    private function getOffTopicRefusalMessage(): string
    {
        return "Dạ, em là **Trợ lý AI chuyên trách tư vấn máy tính bảng & dịch vụ tại VUA TABLET** ạ! 😊\n\nEm chỉ hỗ trợ tư vấn các thông tin liên quan đến **sản phẩm máy tính bảng (iPad, tablet Android)**, cấu hình, báo giá, phụ kiện và chính sách của cửa hàng thôi ạ.\n\nEm không thể hỗ trợ giải toán hoặc trả lời các chủ đề ngoài lề được. Quý khách có thắc mắc gì về mẫu máy tính bảng nào cần em hỗ trợ tư vấn không ạ?";
    }
}
