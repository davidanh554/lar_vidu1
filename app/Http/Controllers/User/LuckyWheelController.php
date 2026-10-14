<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Coupon;
use App\Models\UserCoupon;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;

class LuckyWheelController extends Controller
{
    /**
     * Danh sách 8 lát cắt mặc định của Vòng quay
     */
    public static function getDefaultSlices(): array
    {
        return [
            0 => [
                'type'        => 'coupon',
                'code'        => 'VUA20K',
                'name'        => 'Voucher 20.000đ',
                'weight'      => 25,
                'color'       => '#4f46e5', // Indigo primary
                'textColor'   => '#ffffff',
                'icon'        => 'fa-ticket',
            ],
            1 => [
                'type'        => 'empty',
                'code'        => null,
                'name'        => 'Chúc bạn may mắn lần sau',
                'weight'      => 20,
                'color'       => '#1e293b', // Slate dark
                'textColor'   => '#94a3b8',
                'icon'        => 'fa-clover',
            ],
            2 => [
                'type'        => 'coupon',
                'code'        => 'VUA50K',
                'name'        => 'Voucher 50.000đ',
                'weight'      => 12,
                'color'       => '#0284c7', // Sky Blue
                'textColor'   => '#ffffff',
                'icon'        => 'fa-gift',
            ],
            3 => [
                'type'        => 'coupon',
                'code'        => 'VUA10VIP',
                'name'        => 'Voucher VIP Giảm 10%',
                'weight'      => 10,
                'color'       => '#6366f1', // Indigo bright
                'textColor'   => '#ffffff',
                'icon'        => 'fa-crown',
            ],
            4 => [
                'type'        => 'empty',
                'code'        => null,
                'name'        => 'Chúc bạn may mắn lần sau',
                'weight'      => 18,
                'color'       => '#334155', // Slate mid
                'textColor'   => '#94a3b8',
                'icon'        => 'fa-clover',
            ],
            5 => [
                'type'        => 'coupon',
                'code'        => 'FREESHIP30K',
                'name'        => 'Freeship 30.000đ',
                'weight'      => 10,
                'color'       => '#10b981', // Emerald green
                'textColor'   => '#ffffff',
                'icon'        => 'fa-truck-fast',
            ],
            6 => [
                'type'        => 'coupon',
                'code'        => 'VUA30K',
                'name'        => 'Voucher 30.000đ',
                'weight'      => 4,
                'color'       => '#7c3aed', // Purple Violet
                'textColor'   => '#ffffff',
                'icon'        => 'fa-coins',
            ],
            7 => [
                'type'        => 'coupon',
                'code'        => 'VUASUPER100K',
                'name'        => 'Siêu Cấp 100.000đ',
                'weight'      => 1,
                'color'       => '#f59e0b', // Gold Amber
                'textColor'   => '#ffffff',
                'icon'        => 'fa-star',
            ],
        ];
    }

    /**
     * Lấy 8 lát cắt vòng quay từ cấu hình động trong Setting (nếu có)
     */
    public static function getSlices(): array
    {
        $savedJson = Setting::get('lucky_wheel_slices');
        if ($savedJson) {
            $decoded = json_decode($savedJson, true);
            if (is_array($decoded) && count($decoded) === 8) {
                return array_values($decoded);
            }
        }

        $defaults = self::getDefaultSlices();

        // Tự động kiểm tra nếu có mã FREESHIP50K thay cho FREESHIP30K
        if (Coupon::where('code', 'FREESHIP50K')->exists() && !Coupon::where('code', 'FREESHIP30K')->exists()) {
            if (isset($defaults[5])) {
                $defaults[5]['code'] = 'FREESHIP50K';
                $defaults[5]['name'] = 'Freeship 50.000đ';
            }
        }

        return $defaults;
    }

    /**
     * Lấy trạng thái vòng quay của khách hàng
     */
    public function getStatus(Request $request)
    {
        $enabled = (bool)Setting::get('lucky_wheel_enabled', 1);
        $slices = self::getSlices();
        $globalDailyLimit = (int)Setting::get('daily_spins_limit', 1);

        if (!$enabled) {
            return response()->json([
                'enabled'   => false,
                'logged_in' => auth()->check(),
                'slices'    => $slices,
                'message'   => 'Chương trình Vòng quay may mắn hiện đang tạm bảo trì.',
            ]);
        }

        if (!auth()->check()) {
            return response()->json([
                'enabled'     => true,
                'logged_in'   => false,
                'daily_limit' => $globalDailyLimit,
                'slices'      => $slices,
                'message'     => 'Vui lòng đăng nhập để tham gia Vòng quay may mắn!'
            ]);
        }

        $user = auth()->user();
        $spinsLeft = $user->refreshAndGetSpins();
        $secondsUntilReset = $user->secondsUntilNextSpinReset();

        // Lấy các voucher khả dụng của user
        $myCoupons = UserCoupon::with('coupon')
            ->where('user_id', $user->id)
            ->where('is_used', false)
            ->latest()
            ->get()
            ->map(function ($uc) {
                return [
                    'id'              => $uc->id,
                    'code'            => $uc->coupon ? $uc->coupon->code : '',
                    'title'           => $uc->coupon ? $uc->coupon->title : 'Mã giảm giá',
                    'type'            => $uc->coupon ? $uc->coupon->type : 'fixed',
                    'value'           => $uc->coupon ? (float)$uc->coupon->value : 0,
                    'max_discount'    => $uc->coupon ? (float)$uc->coupon->max_discount : 0,
                    'min_order_value' => $uc->coupon ? (float)$uc->coupon->min_order_value : 0,
                    'created_at'      => $uc->created_at->format('H:i d/m/Y'),
                ];
            });

        return response()->json([
            'enabled'             => true,
            'logged_in'           => true,
            'daily_limit'         => $user->daily_spins_limit ?? $globalDailyLimit,
            'spins_left'          => $spinsLeft,
            'seconds_until_reset' => $secondsUntilReset,
            'slices'              => $slices,
            'my_coupons'          => $myCoupons,
        ]);
    }

    /**
     * Thực hiện quay thưởng
     */
    public function spin(Request $request)
    {
        $enabled = (bool)Setting::get('lucky_wheel_enabled', 1);
        if (!$enabled) {
            return response()->json([
                'success' => false,
                'message' => 'Chương trình Vòng quay may mắn hiện đang tạm đóng. Vui lòng quay lại sau!'
            ], 400);
        }

        if (!auth()->check()) {
            return response()->json([
                'success'    => false,
                'need_login' => true,
                'message'    => 'Vui lòng đăng nhập tài khoản để quay thưởng nhận mã giảm giá!'
            ], 401);
        }

        $user = auth()->user();

        return DB::transaction(function () use ($user) {
            // Khóa dòng user để tránh race condition quay 2 lần cùng lúc
            $lockedUser = \App\Models\User::where('id', $user->id)->lockForUpdate()->first();
            $spinsLeft = $lockedUser->refreshAndGetSpins();

            if ($spinsLeft <= 0) {
                $seconds = $lockedUser->secondsUntilNextSpinReset();
                $hours = floor($seconds / 3600);
                $minutes = floor(($seconds % 3600) / 60);

                return response()->json([
                    'success'             => false,
                    'spins_left'          => 0,
                    'seconds_until_reset' => $seconds,
                    'message'             => "Bạn đã dùng hết lượt quay hôm nay. Vui lòng quay lại sau {$hours} giờ {$minutes} phút để nhận lượt mới!"
                ], 400);
            }

            // Trừ 1 lượt quay
            $lockedUser->decrement('spins_left');
            $newSpinsLeft = $lockedUser->spins_left;

            // Lấy danh sách slices cấu hình hiện tại
            $slices = self::getSlices();

            // Thuật toán chọn giải thưởng ngẫu nhiên theo trọng số (Weighted Random)
            $selectedSliceIndex = $this->pickPrizeIndex($slices);
            $prize = $slices[$selectedSliceIndex];

            $awardedCoupon = null;

            if ($prize['type'] === 'coupon' && !empty($prize['code'])) {
                $coupon = Coupon::where('code', $prize['code'])->where('is_active', true)->first();
                if ($coupon) {
                    $userCoupon = UserCoupon::create([
                        'user_id'   => $lockedUser->id,
                        'coupon_id' => $coupon->id,
                        'is_used'   => false,
                    ]);

                    $awardedCoupon = [
                        'code'            => $coupon->code,
                        'title'           => $coupon->title,
                        'value'           => (float)$coupon->value,
                        'type'            => $coupon->type,
                        'min_order_value' => (float)$coupon->min_order_value,
                    ];
                }
            }

            return response()->json([
                'success'             => true,
                'slice_index'         => $selectedSliceIndex,
                'prize'               => $prize,
                'awarded_coupon'      => $awardedCoupon,
                'spins_left'          => $newSpinsLeft,
                'seconds_until_reset' => $lockedUser->secondsUntilNextSpinReset(),
            ]);
        });
    }

    /**
     * Thuật toán chọn ngẫu nhiên dựa trên xác suất (weight)
     */
    protected function pickPrizeIndex(array $slices): int
    {
        $totalWeight = array_sum(array_column($slices, 'weight'));
        if ($totalWeight <= 0) {
            return 1; // Fallback
        }

        $rand = mt_rand(1, (int)$totalWeight);
        $current = 0;

        foreach ($slices as $index => $slice) {
            $current += (float)$slice['weight'];
            if ($rand <= $current) {
                return (int)$index;
            }
        }

        return 1; // Default: May mắn lần sau
    }

    /**
     * Lấy danh sách voucher trong kho của tôi
     */
    public function myCoupons()
    {
        if (!auth()->check()) {
            return response()->json(['coupons' => []]);
        }

        $coupons = UserCoupon::with('coupon')
            ->where('user_id', auth()->id())
            ->where('is_used', false)
            ->latest()
            ->get()
            ->map(function ($uc) {
                return [
                    'id'              => $uc->id,
                    'code'            => $uc->coupon ? $uc->coupon->code : '',
                    'title'           => $uc->coupon ? $uc->coupon->title : 'Mã giảm giá',
                    'type'            => $uc->coupon ? $uc->coupon->type : 'fixed',
                    'value'           => $uc->coupon ? (float)$uc->coupon->value : 0,
                    'max_discount'    => $uc->coupon ? (float)$uc->coupon->max_discount : 0,
                    'min_order_value' => $uc->coupon ? (float)$uc->coupon->min_order_value : 0,
                    'created_at'      => $uc->created_at->format('H:i d/m/Y'),
                ];
            });

        return response()->json(['coupons' => $coupons]);
    }
}
