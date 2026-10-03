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
}