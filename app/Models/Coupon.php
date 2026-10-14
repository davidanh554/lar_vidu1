<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'title',
        'type', // 'fixed' hoặc 'percent'
        'value',
        'max_discount',
        'min_order_value',
        'quantity',
        'used_count',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'value' => 'float',
        'max_discount' => 'float',
        'min_order_value' => 'float',
        'is_active' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function userCoupons()
    {
        return $this->hasMany(UserCoupon::class);
    }

    /**
     * Kiểm tra coupon có hợp lệ đối với giá trị đơn hàng hay không
     */
    public function isValidForOrder(float $orderAmount): array
    {
        if (!$this->is_active) {
            return ['valid' => false, 'message' => 'Mã giảm giá hiện đang bị vô hiệu hóa!'];
        }

        if ($this->quantity !== null && $this->used_count >= $this->quantity) {
            return ['valid' => false, 'message' => 'Mã giảm giá đã hết lượt sử dụng!'];
        }

        $now = now();
        if ($this->start_date && $now->lt($this->start_date)) {
            return ['valid' => false, 'message' => 'Mã giảm giá chưa đến ngày áp dụng!'];
        }

        if ($this->end_date && $now->gt($this->end_date)) {
            return ['valid' => false, 'message' => 'Mã giảm giá đã hết hạn sử dụng!'];
        }

        if ($orderAmount < $this->min_order_value) {
            return [
                'valid' => false,
                'message' => 'Đơn hàng tối thiểu ' . number_format($this->min_order_value) . 'đ mới có thể dùng mã này!'
            ];
        }

        return ['valid' => true, 'message' => 'Mã giảm giá hợp lệ!'];
    }

    /**
     * Tính số tiền được giảm
     */
    public function calculateDiscount(float $orderAmount, float $shippingFee = 0): float
    {
        $isFreeship = str_contains(strtoupper($this->code), 'FREESHIP') 
            || str_contains(mb_strtolower($this->title), 'vận chuyển') 
            || str_contains(mb_strtolower($this->title), 'freeship');

        if ($isFreeship) {
            // Mã Freeship: Trừ trực tiếp vào tiền cước vận chuyển (nếu có ship) hoặc tổng thanh toán
            $applicableFee = $shippingFee > 0 ? $shippingFee : ($orderAmount + $shippingFee);
            return min($this->value, $applicableFee);
        }

        $discount = 0;
        if ($this->type === 'percent') {
            $discount = ($orderAmount * $this->value) / 100;
            if ($this->max_discount !== null && $this->max_discount > 0) {
                $discount = min($discount, $this->max_discount);
            }
            return min($discount, $orderAmount);
        } else {
            $discount = $this->value;
            // Với mã tiền mặt cố định: cho phép trừ vào tổng đơn (tiền hàng + ship)
            return min($discount, $orderAmount + $shippingFee);
        }
    }
}
