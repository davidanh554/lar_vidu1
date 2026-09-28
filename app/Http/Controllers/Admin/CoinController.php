<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Setting;

class CoinController extends Controller
{
    /**
     * Trang Quản lý & Cấu hình Hạn mức Xu
     */
    public function index(Request $request)
    {
        // 1. Cấu hình hệ thống hiện tại
        $coinRate = (int)Setting::get('coin_rate', 500);
        $dailyLimit = (int)Setting::get('daily_coins_limit', 10);
        $watchSeconds = (int)Setting::get('coin_watch_seconds', 30);
        $coinPerReward = (int)Setting::get('coin_per_reward', 1);

        // 2. Thống kê tổng quan
        $totalCoinsInCirculation = User::sum('coins');
        $totalMoneyValue = $totalCoinsInCirculation * $coinRate;
        $usersWithCoinsCount = User::where('coins', '>', 0)->count();
        $coinsEarnedToday = User::sum('coins_earned_today');

        // 3. Danh sách người dùng
        $query = User::query();

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
                if (is_numeric($search)) {
                    $q->orWhere('id', (int)$search);
                }
            });
        }

        $users = $query->orderByDesc('coins')->paginate(15);

        return view('admin.coins.index', compact(
            'coinRate',
            'dailyLimit',
            'watchSeconds',
            'coinPerReward',
            'totalCoinsInCirculation',
            'totalMoneyValue',
            'usersWithCoinsCount',
            'coinsEarnedToday',
            'users'
        ));
    }

    /**
     * Cập nhật cấu hình hạn mức Xu toàn hệ thống
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'coin_rate'          => 'required|numeric|min:1',
            'daily_coins_limit'  => 'required|integer|min:1|max:1000',
            'coin_watch_seconds' => 'required|integer|min:5|max:300',
            'coin_per_reward'    => 'required|integer|min:1|max:100',
        ], [
            'coin_rate.required'         => 'Vui lòng nhập tỷ lệ quy đổi 1 Xu.',
            'daily_coins_limit.required' => 'Vui lòng nhập hạn mức Xu tối đa mỗi ngày.',
            'coin_watch_seconds.min'     => 'Thời gian xem tối thiểu là 5 giây.',
        ]);

        Setting::set('coin_rate', $request->coin_rate, 'Giá trị quy đổi 1 Xu thành VNĐ');
        Setting::set('daily_coins_limit', $request->daily_coins_limit, 'Hạn mức Xu xem video tối đa 1 ngày');
        Setting::set('coin_watch_seconds', $request->coin_watch_seconds, 'Thời gian xem video để nhận 1 lượt xu (giây)');
        Setting::set('coin_per_reward', $request->coin_per_reward, 'Số Xu cộng cho mỗi lần xem');

        // Nếu admin chọn đồng bộ cho toàn bộ khách hàng hiện tại
        if ($request->has('sync_all_users')) {
            User::query()->update(['daily_coins_limit' => $request->daily_coins_limit]);
            $msg = 'Đã lưu cấu hình và đồng bộ hạn mức mới (' . $request->daily_coins_limit . ' Xu/ngày) cho toàn bộ khách hàng!';
        } else {
            $msg = 'Đã lưu cấu hình Xu thành công!';
        }

        return redirect()->route('admin.coins.index')->with('success', $msg);
    }

    /**
     * Điều chỉnh Xu hoặc Hạn mức cho từng người dùng cụ thể
     */
    public function adjustUserCoins(Request $request, User $user)
    {
        $request->validate([
            'action' => 'required|in:add,subtract,set,update_limit',
            'amount' => 'required|integer|min:0',
        ]);

        $action = $request->action;
        $amount = (int)$request->amount;

        if ($action === 'add') {
            $user->increment('coins', $amount);
            $msg = "Đã cộng thêm +{$amount} Xu cho {$user->name}.";
        } elseif ($action === 'subtract') {
            $user->coins = max(0, $user->coins - $amount);
            $user->save();
            $msg = "Đã trừ {$amount} Xu của {$user->name}.";
        } elseif ($action === 'set') {
            $user->coins = $amount;
            $user->save();
            $msg = "Đã đặt số Xu của {$user->name} thành {$amount} Xu.";
        } elseif ($action === 'update_limit') {
            $user->daily_coins_limit = $amount;
            $user->save();
            $msg = "Đã cập nhật hạn mức ngày của {$user->name} thành {$amount} Xu/ngày.";
        }

        return back()->with('success', $msg);
    }
}
