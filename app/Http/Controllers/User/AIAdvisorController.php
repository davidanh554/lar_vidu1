<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class AIAdvisorController extends Controller
{
    /**
     * Hiển thị trang Quiz trắc nghiệm thông minh "AI Chọn iPad Hộ Tôi"
     */
    public function index()
    {
        return view('products.ai_advisor');
    }

    /**
     * Thuật toán Khuyến nghị & Đánh giá mức độ phù hợp (Weighted Scoring Recommendation)
     */
    public function recommend(Request $request)
    {
        $request->validate([
            'purpose'  => 'required|string',
            'budget'   => 'required|string',
            'pencil'   => 'required|string',
            'size'     => 'required|string',
            'priority' => 'required|string',
        ]);

        $purpose  = $request->purpose;
        $budget   = $request->budget;
        $pencil   = $request->pencil;
        $size     = $request->size;
        $priority = $request->priority;

        // Lấy danh sách các iPad / Máy tính bảng đang kích hoạt
        $products = Product::where('is_active', true)->get();

        if ($products->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Hiện chưa có sản phẩm nào trong hệ thống!']);
        }

        $scoredProducts = [];

        foreach ($products as $product) {
            $score = 50; // Điểm cơ sở
            $reasons = [];
            $price = $product->sale_price ?? $product->price;
            $chip = strtoupper($product->chip ?? '');
            $screen = strtoupper($product->screen_size ?? '');
            $name = strtoupper($product->name ?? '');

            // 1. Phân tích Ngân sách
            if ($budget === 'under_8m') {
                if ($price <= 8500000) {
                    $score += 35;
                    $reasons[] = 'Mức giá cực kỳ êm ví, nằm trọn vẹn trong khoảng ngân sách tiết kiệm của bạn';
                } else {
                    $score -= 30;
                }
            } elseif ($budget === '8m_15m') {
                if ($price >= 8000000 && $price <= 15500000) {
                    $score += 35;
                    $reasons[] = 'Mức giá tối ưu ngân sách tầm trung, đáng tiền nhất trong phân khúc';
                } elseif ($price < 8000000) {
                    $score += 15;
                    $reasons[] = 'Giúp bạn tiết kiệm được một khoản tiền đáng kể so với ngân sách ban đầu';
                } else {
                    $score -= 25;
                }
            } elseif ($budget === '15m_25m') {
                if ($price >= 15000000 && $price <= 25500000) {
                    $score += 35;
                    $reasons[] = 'Phù hợp ngân sách cận cao cấp, sở hữu phần cứng thế hệ mới vượt trội';
                } elseif ($price < 15000000) {
                    $score += 15;
                } else {
                    $score -= 15;
                }
            } elseif ($budget === 'above_25m') {
                if ($price >= 25000000) {
                    $score += 35;
                    $reasons[] = 'Thuộc phân khúc Flagship cao cấp nhất, hoàn hảo cho ngân sách đầu tư không giới hạn';
                } else {
                    $score += 20;
                }
            } else {
                $score += 20;
            }

            // 2. Phân tích Nhu cầu chính (Purpose)
            if ($purpose === 'draw') {
                // Vẽ & Đồ họa: Cần chip M hoặc hỗ trợ Pencil Pro
                if (str_contains($chip, 'M2') || str_contains($chip, 'M4')) {
                    $score += 40;
                    $reasons[] = 'Hỗ trợ Apple Pencil Pro với tính năng Bóp (Squeeze) và Xoay thân bút (Barrel roll) cực đỉnh cho vẽ';
                    $reasons[] = 'Màn hình dải màu rộng DCI-P3 chuẩn màu đồ họa chuyên nghiệp';
                } elseif (str_contains($name, 'MINI') || str_contains($name, 'GEN 10')) {
                    $score += 20;
                    $reasons[] = 'Đáp ứng tốt vẽ phác thảo, minh họa truyện tranh và ghi chú thường nhật';
                }
            } elseif ($purpose === 'study') {
                // Học tập & Ghi chép
                if (str_contains($name, 'GEN 10') || str_contains($name, 'AIR')) {
                    $score += 40;
                    $reasons[] = 'Kích thước 11 inch tiêu chuẩn, mở song song 2 ứng dụng tài liệu và ghi chú cực kỳ tiện lợi';
                    $reasons[] = 'Độ bền phần cứng cao, thời lượng pin thoải mái cho cả ngày học tập ở trường';
                } elseif (str_contains($name, 'GEN 9')) {
                    $score += 35;
                    $reasons[] = 'Lựa chọn tiết kiệm chi phí tối đa cho học sinh - sinh viên học online';
                } else {
                    $score += 20;
                }
            } elseif ($purpose === 'gaming') {
                // Chơi game đồ họa cao
                if (str_contains($name, 'MINI')) {
                    $score += 45;
                    $reasons[] = 'Thiết kế 8.3 inch cầm 2 tay chiến game cực kỳ chắc chắn, không bị mỏi cổ tay khi chơi lâu';
                    $reasons[] = 'Chip vi xử lý đồ họa mạnh mẽ cân mượt mà mức cấu hình cao nhất';
                } elseif (str_contains($chip, 'M4') || str_contains($chip, 'M2') || str_contains($chip, 'A17') || str_contains($chip, 'SNAPDRAGON 8')) {
                    $score += 40;
                    $reasons[] = 'Hiệu năng đồ họa khủng, FPS ổn định tuyệt đối trong các pha combat nảy lửa';
                }
            } elseif ($purpose === 'work') {
                // Công việc & Đa nhiệm
                if (str_contains($chip, 'M4') || str_contains($chip, 'M2')) {
                    $score += 40;
                    $reasons[] = 'Hỗ trợ tính năng Stage Manager đa nhiệm chia nhiều cửa sổ như Macbook';
                    $reasons[] = 'Hiệu năng vi xử lý mạnh mẽ xử lý mượt mà file Excel nặng, dựng video 4K';
                } else {
                    $score += 15;
                }
            } elseif ($purpose === 'entertainment') {
                // Giải trí, xem phim
                if (str_contains($name, 'GEN 9') || str_contains($name, 'GEN 10') || str_contains($name, 'XIAOMI')) {
                    $score += 35;
                    $reasons[] = 'Hệ thống loa kép sống động kết hợp màn hình lớn giải trí xem phim Netflix, Youtube cực đã';
                } else {
                    $score += 20;
                }
            }

            // 3. Phân tích Bút cảm ứng (Pencil)
            if ($pencil === 'frequent') {
                if (str_contains($chip, 'M2') || str_contains($chip, 'M4') || str_contains($name, 'MINI 7')) {
                    $score += 25;
                    $reasons[] = 'Hỗ trợ sạc không dây từ tính gắn liền cạnh máy cho bút, không lo hết pin giữa chừng';
                } elseif (str_contains($name, 'GEN 10')) {
                    $score += 15;
                    $reasons[] = 'Tương thích hoàn hảo với Apple Pencil USB-C cho trải nghiệm viết liền mạch';
                }
            } elseif ($pencil === 'none') {
                // Không dùng bút -> Tối ưu giá tiền
                if (str_contains($name, 'GEN 9') || str_contains($name, 'GEN 10') || str_contains($name, 'XIAOMI')) {
                    $score += 15;
                }
            }

            // 4. Phân tích Kích thước (Size)
            if ($size === 'compact') {
                if (str_contains($name, 'MINI') || str_contains($screen, '8.')) {
                    $score += 35;
                    $reasons[] = 'Kích thước siêu nhỏ gọn, dễ dàng nhét vừa túi xách hoặc túi áo khoác mang đi mọi nơi';
                } else {
                    $score -= 20;
                }
            } elseif ($size === 'standard') {
                if (str_contains($screen, '10.') || str_contains($screen, '11')) {
                    $score += 30;
                    $reasons[] = 'Kích thước 11 inch tiêu chuẩn cân đối hoàn hảo giữa không gian hiển thị và độ di động';
                }
            } elseif ($size === 'large') {
                if (str_contains($screen, '13') || str_contains($screen, '14.')) {
                    $score += 35;
                    $reasons[] = 'Màn hình cỡ lớn cung cấp không gian thao tác bao la cho công việc chuyên nghiệp';
                }
            }

            // 5. Yếu tố Ưu tiên (Priority)
            if ($priority === 'performance') {
                if (str_contains($chip, 'M4')) {
                    $score += 30;
                    $reasons[] = 'Sở hữu chip Apple M4 đỉnh cao nhất của Apple với hiệu năng dẫn đầu thị trường';
                } elseif (str_contains($chip, 'M2') || str_contains($chip, 'A17')) {
                    $score += 20;
                    $reasons[] = 'Cấu hình mạnh mẽ đảm bảo sử dụng mượt mà không lo lỗi thời trong 4-5 năm tới';
                }
            } elseif ($priority === 'screen') {
                if (str_contains($screen, '120HZ') || str_contains($screen, 'OLED') || str_contains($screen, 'XDR')) {
                    $score += 30;
                    $reasons[] = 'Màn hình công nghệ cao cấp nhất với tần số quét siêu mượt và độ tương phản tuyệt đối';
                }
            } elseif ($priority === 'budget') {
                if ($price <= 10000000) {
                    $score += 30;
                    $reasons[] = 'Tối ưu hóa từng đồng chi phí, là món hời công nghệ trong tầm giá';
                }
            }

            // Chuẩn hóa điểm số thành Match % (70% - 98%)
            $matchPercentage = min(98, max(68, round(70 + ($score - 50) * 0.4)));

            $scoredProducts[] = [
                'product' => $product,
                'raw_score' => $score,
                'match_percentage' => $matchPercentage,
                'reasons' => array_unique(array_slice($reasons, 0, 4)),
            ];
        }

        // Sắp xếp sản phẩm theo điểm số giảm dần
        usort($scoredProducts, function ($a, $b) {
            return $b['raw_score'] <=> $a['raw_score'];
        });

        // 1. Quán quân (Top 1)
        $top1 = $scoredProducts[0];

        // 2. Lựa chọn Tiết kiệm hơn (Alternative Budget)
        $savingChoice = null;
        $top1Price = $top1['product']->sale_price ?? $top1['product']->price;
        foreach ($scoredProducts as $item) {
            $itemPrice = $item['product']->sale_price ?? $item['product']->price;
            if ($item['product']->id !== $top1['product']->id && $itemPrice < $top1Price && ($top1Price - $itemPrice) >= 3000000) {
                $savingChoice = $item;
                break;
            }
        }

        // 3. Lựa chọn Nâng cấp cao hơn (Alternative Upgrade)
        $upgradeChoice = null;
        foreach ($scoredProducts as $item) {
            $itemPrice = $item['product']->sale_price ?? $item['product']->price;
            if ($item['product']->id !== $top1['product']->id && $itemPrice > $top1Price) {
                $upgradeChoice = $item;
                break;
            }
        }

        return response()->json([
            'success' => true,
            'top1' => [
                'id' => $top1['product']->id,
                'name' => $top1['product']->name,
                'price' => $top1['product']->price,
                'sale_price' => $top1['product']->sale_price,
                'price_formatted' => number_format($top1['product']->sale_price ?? $top1['product']->price) . '₫',
                'original_price_formatted' => $top1['product']->sale_price ? number_format($top1['product']->price) . '₫' : null,
                'image' => asset($top1['product']->image ?? 'images/placeholder-ipad.png'),
                'screen_size' => $top1['product']->screen_size,
                'chip' => $top1['product']->chip,
                'storage' => $top1['product']->storage,
                'match_percentage' => $top1['match_percentage'],
                'reasons' => $top1['reasons'],
                'url' => route('products.show', $top1['product']->slug ?? $top1['product']->id),
            ],
            'saving_choice' => $savingChoice ? [
                'id' => $savingChoice['product']->id,
                'name' => $savingChoice['product']->name,
                'price_formatted' => number_format($savingChoice['product']->sale_price ?? $savingChoice['product']->price) . '₫',
                'image' => asset($savingChoice['product']->image ?? 'images/placeholder-ipad.png'),
                'match_percentage' => $savingChoice['match_percentage'],
                'chip' => $savingChoice['product']->chip,
                'url' => route('products.show', $savingChoice['product']->slug ?? $savingChoice['product']->id),
            ] : null,
            'upgrade_choice' => $upgradeChoice ? [
                'id' => $upgradeChoice['product']->id,
                'name' => $upgradeChoice['product']->name,
                'price_formatted' => number_format($upgradeChoice['product']->sale_price ?? $upgradeChoice['product']->price) . '₫',
                'image' => asset($upgradeChoice['product']->image ?? 'images/placeholder-ipad.png'),
                'match_percentage' => $upgradeChoice['match_percentage'],
                'chip' => $upgradeChoice['product']->chip,
                'url' => route('products.show', $upgradeChoice['product']->slug ?? $upgradeChoice['product']->id),
            ] : null,
        ]);
    }
}
