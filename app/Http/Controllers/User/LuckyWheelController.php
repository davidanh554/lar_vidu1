<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Coupon;
use App\Models\UserCoupon;
use Illuminate\Support\Facades\DB;

class LuckyWheelController extends Controller
{
    /**
     * Danh sách 8 lát cắt trên Vòng quay
     */
    protected $wheelSlices = [
        0 => [
            'type'        => 'coupon',
            'code'        => 'VUA20K',
            'name'        => 'Voucher 20.000đ',
            'weight'      => 25,
            'color'       => '#059669', // Emerald dark
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
            'color'       => '#10b981', // Emerald primary
            'textColor'   => '#064e3b',
            'icon'        => 'fa-gift',
        ],
        3 => [
            'type'        => 'coupon',
            'code'        => 'VUA10VIP',
            'name'        => 'Voucher VIP Giảm 10%',
            'weight'      => 10,
            'color'       => '#0f766e', // Deep Teal
            'textColor'   => '#00ff87',
            'icon'        => 'fa-crown',
        ],
        4 => [
            'type'        => 'empty',
            'code'        => null,
            'name'        => 'Chúc bạn may mắn lần sau',
            'weight'      => 18,
            'color'       => '#1e293b', // Slate dark
            'textColor'   => '#94a3b8',
            'icon'        => 'fa-clover',
        ],
        5 => [
            'type'        => 'coupon',
            'code'        => 'FREESHIP30K',
            'name'        => 'Freeship 30.000đ',
            'weight'      => 10,
            'color'       => '#00ff87', // Neon laser green
            'textColor'   => '#064e3b',
            'icon'        => 'fa-truck-fast',
        ],
        6 => [
            'type'        => 'coupon',
            'code'        => 'VUA30K',
            'name'        => 'Voucher 30.000đ',
            'weight'      => 4,
            'color'       => '#047857', // Emerald mid
            'textColor'   => '#ffffff',
            'icon'        => 'fa-coins',
        ],
        7 => [
            'type'        => 'coupon',
            'code'        => 'VUASUPER100K',
            'name'        => 'Siêu Cấp 100.000đ',
            'weight'      => 1,
            'color'       => '#fbbf24', // Gold Star
            'textColor'   => '#78350f',
            'icon'        => 'fa-star',
        ],
    ];

    /**
     * Lấy trạng thái vòng quay của khách hàng
     */
    public function getStatus(Request $request)
    {
        if (!auth()->check()) {
            return response()->json([
                'logged_in' => false,
                'slices' => $this->wheelSlices,
                'message' => 'Vui lòng đăng nhập để tham gia Vòng quay may mắn!'
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
                    'id' => $uc->id,
                    'code' => $uc->coupon->code,
                    'title' => $uc->coupon->title,
                    'type' => $uc->coupon->type,
                    'value' => (float)$uc->coupon->value,
                    'max_discount' => (float)$uc->coupon->max_discount,
                    'min_order_value' => (float)$uc->coupon->min_order_value,
                    'created_at' => $uc->created_at->format('H:i d/m/Y'),
                ];
            });

        return response()->json([
            'logged_in' => true,
            'daily_limit' => $user->daily_spins_limit ?? 1,
            'spins_left' => $spinsLeft,
            'seconds_until_reset' => $secondsUntilReset,
            'slices' => $this->wheelSlices,
            'my_coupons' => $myCoupons,
        ]);
    }

    /**
     * Thực hiện quay thưởng
     */
    public function spin(Request $request)
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'need_login' => true,
                'message' => 'Vui lòng đăng nhập tài khoản để quay thưởng nhận mã giảm giá!'
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
                    'success' => false,
                    'spins_left' => 0,
                    'seconds_until_reset' => $seconds,
                    'message' => "Bạn đã dùng hết lượt quay hôm nay. Vui lòng quay lại sau {$hours} giờ {$minutes} phút để nhận lượt mới!"
                ], 400);
            }

            // Trừ 1 lượt quay
            $lockedUser->decrement('spins_left');
            $newSpinsLeft = $lockedUser->spins_left;

            // Thuật toán chọn giải thưởng ngẫu nhiên theo trọng số (Weighted Random)
            $selectedSliceIndex = $this->pickPrizeIndex();
            $prize = $this->wheelSlices[$selectedSliceIndex];

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
                        'code' => $coupon->code,
                        'title' => $coupon->title,
                        'value' => (float)$coupon->value,
                        'type' => $coupon->type,
                        'min_order_value' => (float)$coupon->min_order_value,
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'slice_index' => $selectedSliceIndex,
                'prize' => $prize,
                'awarded_coupon' => $awardedCoupon,
                'spins_left' => $newSpinsLeft,
                'seconds_until_reset' => $lockedUser->secondsUntilNextSpinReset(),
            ]);
        });
    }

    /**
     * Thuật toán chọn ngẫu nhiên dựa trên xác suất (weight)
     */
    protected function pickPrizeIndex(): int
    {
        $totalWeight = array_sum(array_column($this->wheelSlices, 'weight'));
        $rand = mt_rand(1, $totalWeight);
        $current = 0;

        foreach ($this->wheelSlices as $index => $slice) {
            $current += $slice['weight'];
            if ($rand <= $current) {
                return $index;
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
                    'id' => $uc->id,
                    'code' => $uc->coupon->code,
                    'title' => $uc->coupon->title,
                    'type' => $uc->coupon->type,
                    'value' => (float)$uc->coupon->value,
                    'max_discount' => (float)$uc->coupon->max_discount,
                    'min_order_value' => (float)$uc->coupon->min_order_value,
                    'created_at' => $uc->created_at->format('H:i d/m/Y'),
                ];
            });

        return response()->json(['coupons' => $coupons]);
    }
}
