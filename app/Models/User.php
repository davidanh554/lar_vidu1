<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
   
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role', 
        'cart',
        'daily_spins_limit',
        'spins_left',
        'last_spin_reset_at',
        'coins',
        'daily_coins_limit',
        'coins_earned_today',
        'last_coin_reset_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'cart' => 'array',
            'daily_spins_limit' => 'integer',
            'spins_left' => 'integer',
            'last_spin_reset_at' => 'datetime',
            'coins' => 'integer',
            'daily_coins_limit' => 'integer',
            'coins_earned_today' => 'integer',
            'last_coin_reset_at' => 'datetime',
        ];
    }

    /**
     * Lấy số xu còn lại có thể nhận trong ngày (Tự động reset sau 24h hoặc qua ngày mới)
     */
    public function getCoinsRemainingToday(): int
    {
        if (!$this->last_coin_reset_at || !\Carbon\Carbon::parse($this->last_coin_reset_at)->isToday()) {
            $this->coins_earned_today = 0;
            $this->last_coin_reset_at = now();
            $this->save();
        }

        $globalLimit = (int)\App\Models\Setting::get('daily_coins_limit', 10);
        $limit = $this->daily_coins_limit ?? $globalLimit;
        return max(0, $limit - ($this->coins_earned_today ?? 0));
    }

    /**
     * Kiểm tra người dùng có phải là Admin hay không[cite: 1]
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function userCoupons()
    {
        return $this->hasMany(UserCoupon::class);
    }

    /**
     * Lấy số lượt quay khả dụng và tự động reset sau 24h (Lazy Reset)
     */
    public function refreshAndGetSpins(): int
    {
        $now = now();
        $limit = $this->daily_spins_limit ?? 1;

        // Nếu chưa từng reset hoặc đã quá 24h kể từ lần reset trước
        if (!$this->last_spin_reset_at || $this->last_spin_reset_at->diffInHours($now) >= 24) {
            $this->spins_left = $limit;
            $this->last_spin_reset_at = $now;
            $this->save();
        }

        return max(0, (int)$this->spins_left);
    }

    /**
     * Tính thời gian còn lại (giây) đến lần reset tiếp theo
     */
    public function secondsUntilNextSpinReset(): int
    {
        if (!$this->last_spin_reset_at) {
            return 0;
        }

        $nextReset = $this->last_spin_reset_at->copy()->addHours(24);
        $diff = now()->diffInSeconds($nextReset, false);

        return max(0, (int)$diff);
    }

    /**
     * Lịch sử tin nhắn Chatbot AI của người dùng
     */
    public function aiChatMessages()
    {
        return $this->hasMany(\App\Models\AiChatMessage::class);
    }

    /**
     * Danh sách sản phẩm yêu thích (Wishlist)
     */
    public function wishlists()
    {
        return $this->hasMany(\App\Models\Wishlist::class);
    }

    public function wishlistProducts()
    {
        return $this->belongsToMany(\App\Models\Product::class, 'wishlists')->withTimestamps();
    }


    /**
     * Tổng số tiền đã chi tiêu tích lũy từ các đơn hàng thành công
     */
    public function getTotalSpentAttribute(): float
    {
        return (float) $this->orders()
            ->where(function($q) {
                $q->whereIn('status', ['paid', 'paid_momo', 'cod_paid'])
                  ->orWhere('shipping_status', 'delivered');
            })
            ->whereNotIn('status', ['cancelled'])
            ->where('shipping_status', '!=', 'cancelled')
            ->sum('total_price');
    }

    /**
     * Xếp hạng thành viên thân thiết (Loyalty Tiers: Đồng, Bạc, Vàng, Kim Cương)
     */
    public function getMembershipTierAttribute(): array
    {
        $spent = $this->total_spent;

        if ($spent >= 50000000) {
            return [
                'name' => 'Kim Cương',
                'badge' => 'Kim Cương VIP',
                'color' => '#0891b2',
                'bg' => '#ecfeff',
                'border' => '#a5f3fc',
                'icon' => 'fa-gem',
                'spent' => $spent,
                'next_tier' => null,
                'target' => 50000000,
                'progress' => 100,
                'remaining' => 0,
                'perk' => 'Nhân đôi x2.0 Xu thưởng, Miễn phí vận chuyển trọn đời, Quà sinh nhật VIP',
            ];
        }

        if ($spent >= 25000000) {
            $target = 50000000;
            return [
                'name' => 'Vàng',
                'badge' => 'Vàng VIP',
                'color' => '#ca8a04',
                'bg' => '#fefce8',
                'border' => '#fef08a',
                'icon' => 'fa-crown',
                'spent' => $spent,
                'next_tier' => 'Kim Cương',
                'target' => $target,
                'progress' => min(99, round(($spent / $target) * 100)),
                'remaining' => max(0, $target - $spent),
                'perk' => 'Nhân x1.5 Xu thưởng khi lướt video, Ưu tiên xử lý đơn hàng hỏa tốc',
            ];
        }

        if ($spent >= 10000000) {
            $target = 25000000;
            return [
                'name' => 'Bạc',
                'badge' => 'Bạc',
                'color' => '#475569',
                'bg' => '#f8fafc',
                'border' => '#e2e8f0',
                'icon' => 'fa-medal',
                'spent' => $spent,
                'next_tier' => 'Vàng',
                'target' => $target,
                'progress' => min(99, round(($spent / $target) * 100)),
                'remaining' => max(0, $target - $spent),
                'perk' => 'Nhân x1.2 Xu thưởng, Voucher giảm 5% vào ngày hội viên',
            ];
        }

        $target = 10000000;
        return [
            'name' => 'Đồng',
            'badge' => 'Đồng',
            'color' => '#b45309',
            'bg' => '#fffbeb',
            'border' => '#fde68a',
            'icon' => 'fa-award',
            'spent' => $spent,
            'next_tier' => 'Bạc',
            'target' => $target,
            'progress' => min(99, round(($spent / $target) * 100)),
            'remaining' => max(0, $target - $spent),
            'perk' => 'Tích Xu mua hàng, Tham gia quay thưởng hàng ngày',
        ];
    }
}