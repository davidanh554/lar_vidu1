<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Coupon;
use App\Models\User;
use App\Models\Setting;
use App\Http\Controllers\User\LuckyWheelController as UserLuckyWheelController;

class LuckyWheelController extends Controller
{
    /**
     * Hiển thị trang Quản lý & Cấu hình Vòng Quay May Mắn
     */
    public function index()
    {
        // 1. Lấy danh sách coupons đang có
        $coupons = Coupon::orderBy('is_active', 'desc')->latest()->get();

        // 2. Lấy danh sách 8 lát cắt hiện tại (từ Setting hoặc mặc định)
        $slices = UserLuckyWheelController::getSlices();

        // 3. Cấu hình chung
        $enabled = (bool)Setting::get('lucky_wheel_enabled', 1);
        $dailyLimit = (int)Setting::get('daily_spins_limit', 1);

        // 4. Thống kê
        $totalWeight = array_sum(array_column($slices, 'weight'));
        $activeCouponsCount = Coupon::where('is_active', true)->count();
        $totalCouponsAwarded = \App\Models\UserCoupon::count();

        return view('admin.lucky_wheel.index', compact(
            'coupons',
            'slices',
            'enabled',
            'dailyLimit',
            'totalWeight',
            'activeCouponsCount',
            'totalCouponsAwarded'
        ));
    }

    /**
     * Cập nhật cấu hình 8 lát cắt và thông số vòng quay
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'daily_spins_limit' => 'required|integer|min:1|max:50',
            'slices'            => 'required|array|size:8',
            'slices.*.type'     => 'required|in:coupon,empty',
            'slices.*.name'     => 'required|string|max:100',
            'slices.*.code'     => 'nullable|string|max:50',
            'slices.*.weight'   => 'required|numeric|min:0|max:1000',
            'slices.*.color'    => 'required|string|max:20',
            'slices.*.textColor'=> 'required|string|max:20',
        ], [
            'slices.size'       => 'Vòng quay bắt buộc phải có đủ đúng 8 lát cắt.',
            'slices.*.name.required' => 'Vui lòng nhập tên hiển thị cho tất cả lát cắt.',
            'slices.*.weight.required' => 'Vui lòng nhập trọng số xác suất cho tất cả lát cắt.',
        ]);

        $rawSlices = $request->input('slices');
        $formattedSlices = [];

        foreach ($rawSlices as $index => $slice) {
            $type = $slice['type'] ?? 'empty';
            $code = ($type === 'coupon' && !empty($slice['code'])) ? trim($slice['code']) : null;

            // Tự gán icon phù hợp nếu chưa có
            $icon = $slice['icon'] ?? ($type === 'coupon' ? 'fa-ticket' : 'fa-clover');

            $formattedSlices[(int)$index] = [
                'type'      => $type,
                'code'      => $code,
                'name'      => trim($slice['name']),
                'weight'    => (float)$slice['weight'],
                'color'     => $slice['color'] ?: '#4f46e5',
                'textColor' => $slice['textColor'] ?: '#ffffff',
                'icon'      => $icon,
            ];
        }

        // Lưu cấu hình lát cắt vào bảng settings
        Setting::set(
            'lucky_wheel_slices',
            json_encode($formattedSlices, JSON_UNESCAPED_UNICODE),
            'Cấu hình 8 lát cắt Vòng quay may mắn'
        );

        // Lưu trạng thái bật/tắt
        $isEnabled = $request->has('lucky_wheel_enabled') ? '1' : '0';
        Setting::set('lucky_wheel_enabled', $isEnabled, 'Bật/Tắt tính năng Vòng quay may mắn');

        // Lưu lượt quay mỗi ngày
        Setting::set('daily_spins_limit', $request->daily_spins_limit, 'Số lượt quay miễn phí mỗi ngày cho user');

        // Nếu admin chọn đồng bộ cho toàn bộ user hiện có
        if ($request->has('sync_all_users')) {
            User::query()->update(['daily_spins_limit' => $request->daily_spins_limit]);
            $msg = 'Đã lưu cấu hình Vòng quay và đồng bộ ' . $request->daily_spins_limit . ' lượt quay/ngày cho toàn bộ người dùng!';
        } else {
            $msg = 'Đã cập nhật cấu hình Vòng quay may mắn thành công!';
        }

        return redirect()->route('admin.lucky-wheel.index')->with('success', $msg);
    }

    /**
     * Khôi phục cấu hình 8 lát cắt về mặc định
     */
    public function resetDefault()
    {
        $defaultSlices = UserLuckyWheelController::getDefaultSlices();

        // Kiểm tra xem có mã FREESHIP50K trong DB thay cho FREESHIP30K không để gắn tự động
        $hasFreeship50k = Coupon::where('code', 'FREESHIP50K')->exists();
        if ($hasFreeship50k && isset($defaultSlices[5])) {
            $defaultSlices[5]['code'] = 'FREESHIP50K';
            $defaultSlices[5]['name'] = 'Freeship 50.000đ';
        }

        Setting::set(
            'lucky_wheel_slices',
            json_encode($defaultSlices, JSON_UNESCAPED_UNICODE),
            'Cấu hình 8 lát cắt Vòng quay may mắn'
        );

        return redirect()->route('admin.lucky-wheel.index')->with('success', 'Đã khôi phục 8 lát cắt Vòng quay may mắn về cấu hình mặc định!');
    }
}
