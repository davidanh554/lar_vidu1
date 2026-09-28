<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Video;

class VideoShoppingController extends Controller
{
    /**
     * Hiển thị giao diện Lướt Video Mua Sắm & Tích Xu Trừ Tiền Mặt
     */
    public function index()
    {
        $videos = Video::with('product')->where('is_active', true)->latest()->get();
        $user = auth()->user();
        
        $coinRate = (int)\App\Models\Setting::get('coin_rate', 500);
        $watchSeconds = (int)\App\Models\Setting::get('coin_watch_seconds', 30);
        $coinPerReward = (int)\App\Models\Setting::get('coin_per_reward', 1);
        $globalDailyLimit = (int)\App\Models\Setting::get('daily_coins_limit', 10);

        $userCoins = $user ? $user->coins : 0;
        $coinsRemainingToday = $user ? $user->getCoinsRemainingToday() : $globalDailyLimit;
        $dailyLimit = $user ? ($user->daily_coins_limit ?? $globalDailyLimit) : $globalDailyLimit;

        return view('products.videos', compact(
            'videos', 
            'userCoins', 
            'coinsRemainingToday', 
            'dailyLimit', 
            'coinRate', 
            'watchSeconds', 
            'coinPerReward'
        ));
    }

    /**
     * Nhận Xu thưởng khi xem video đủ chu kỳ (Admin kiểm soát hạn mức mỗi ngày)
     */
    public function claimReward(Request $request)
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'need_login' => true,
                'message' => 'Vui lòng đăng nhập để tích luỹ Xu trừ tiền khi mua hàng!'
            ], 401);
        }

        $user = auth()->user();
        $remainingToday = $user->getCoinsRemainingToday();
        $coinRate = (int)\App\Models\Setting::get('coin_rate', 500);
        $coinPerReward = (int)\App\Models\Setting::get('coin_per_reward', 1);
        $globalDailyLimit = (int)\App\Models\Setting::get('daily_coins_limit', 10);
        $limit = $user->daily_coins_limit ?? $globalDailyLimit;

        // Kiểm tra xem đã đạt hạn mức nhận xu trong ngày chưa (do Admin cài đặt)
        if ($remainingToday <= 0) {
            $maxCash = number_format($limit * $coinRate);
            return response()->json([
                'success' => false,
                'limit_reached' => true,
                'message' => "Hôm nay bạn đã nhận đủ giới hạn {$limit} Xu ({$maxCash}₫) từ video! Hãy quay lại sau 24h hoặc mua hàng để sử dụng số Xu hiện có trừ tiền nhé."
            ]);
        }

        // Mỗi lần xem xong chu kỳ: Cộng số Xu theo Admin cài đặt
        $earnedCoins = min($coinPerReward, $remainingToday);
        $user->increment('coins', $earnedCoins);
        $user->increment('coins_earned_today', $earnedCoins);
        $user->refresh();

        $newRemaining = $user->getCoinsRemainingToday();

        return response()->json([
            'success' => true,
            'earned_coins' => $earnedCoins,
            'total_coins' => $user->coins,
            'money_value' => number_format($user->coins * $coinRate) . '₫',
            'coins_remaining_today' => $newRemaining,
            'daily_limit' => $limit,
            'message' => "Chúc mừng bạn nhận được +{$earnedCoins} Xu (tương đương giảm " . number_format($earnedCoins * $coinRate) . "₫)! Bạn có thể tiếp tục xem để nhận thêm xu."
        ]);
    }

    /**
     * Thả tim video
     */
    public function toggleLike(Request $request, Video $video)
    {
        $video->increment('likes_count');
        return response()->json([
            'success' => true,
            'likes' => $video->likes_count
        ]);
    }
}
