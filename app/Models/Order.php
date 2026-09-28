<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'address',
        'phone',
        'total_price',
        'coupon_code',
        'discount_amount',
        'coins_used',
        'coins_discount',
        'status',
        'shipping_status',
        'has_unread_update',
        // Thêm 4 trường bên dưới cho GHN:
        'ghn_order_code',
        'ghn_total_fee',
        'to_district_id',
        'to_ward_code',
    ];

    protected $casts = [
        'has_unread_update' => 'boolean',
    ];
    public function paymentTransactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }
    // Một đơn hàng thuộc về một người dùng
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Một đơn hàng có nhiều mục sản phẩm
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // Chuyển đổi trạng thái giao hàng sang tiếng Việt dễ hiểu
    public function getShippingStatusTextAttribute(): string
    {
        return match($this->shipping_status) {
            'not_shipped'   => 'Chưa gửi hàng',
            'pending'       => 'Chờ xử lý',
            'ready_to_pick' => 'Chờ lấy hàng',
            'picking'       => 'Đang lấy hàng',
            'storing'       => 'Đã nhập kho trung chuyển',
            'delivering'    => 'Đang giao hàng',
            'delivered'     => 'Giao thành công',
            'cancelled'     => 'Đã hủy',
            'return'        => 'Chuyển hoàn',
            default         => $this->shipping_status ?? 'Chưa xác định',
        };
    }

    // Màu sắc hiển thị tương ứng
    public function getShippingStatusBadgeAttribute(): string
    {
        return match($this->shipping_status) {
            'not_shipped'   => 'bg-secondary',
            'pending'       => 'bg-warning text-dark',
            'ready_to_pick' => 'bg-info text-dark',
            'picking'       => 'bg-primary',
            'storing'       => 'bg-secondary',
            'delivering'    => 'bg-primary',
            'delivered'     => 'bg-success',
            'cancelled'     => 'bg-danger',
            'return'        => 'bg-dark',
            default         => 'bg-secondary',
        };
    }
}
