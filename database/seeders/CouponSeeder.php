<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Coupon;

class CouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $coupons = [
            [
                'code' => 'VUA20K',
                'title' => 'Voucher May Mắn 20.000đ',
                'type' => 'fixed',
                'value' => 20000,
                'max_discount' => null,
                'min_order_value' => 100000,
                'quantity' => 1000,
                'is_active' => true,
                'start_date' => now()->subDay(),
                'end_date' => now()->addDays(90),
            ],
            [
                'code' => 'VUA30K',
                'title' => 'Voucher May Mắn 30.000đ',
                'type' => 'fixed',
                'value' => 30000,
                'max_discount' => null,
                'min_order_value' => 200000,
                'quantity' => 1000,
                'is_active' => true,
                'start_date' => now()->subDay(),
                'end_date' => now()->addDays(90),
            ],
            [
                'code' => 'VUA50K',
                'title' => 'Voucher Đặc Biệt 50.000đ',
                'type' => 'fixed',
                'value' => 50000,
                'max_discount' => null,
                'min_order_value' => 300000,
                'quantity' => 500,
                'is_active' => true,
                'start_date' => now()->subDay(),
                'end_date' => now()->addDays(90),
            ],
            [
                'code' => 'VUA10VIP',
                'title' => 'Voucher VIP Giảm 10%',
                'type' => 'percent',
                'value' => 10,
                'max_discount' => 100000,
                'min_order_value' => 500000,
                'quantity' => 300,
                'is_active' => true,
                'start_date' => now()->subDay(),
                'end_date' => now()->addDays(90),
            ],
            [
                'code' => 'FREESHIP30K',
                'title' => 'Voucher Hỗ Trợ Phí Vận Chuyển 30.000đ',
                'type' => 'fixed',
                'value' => 30000,
                'max_discount' => null,
                'min_order_value' => 150000,
                'quantity' => 1000,
                'is_active' => true,
                'start_date' => now()->subDay(),
                'end_date' => now()->addDays(90),
            ],
            [
                'code' => 'VUASUPER100K',
                'title' => 'Voucher Độc Quyền Siêu Cấp 100.000đ',
                'type' => 'fixed',
                'value' => 100000,
                'max_discount' => null,
                'min_order_value' => 800000,
                'quantity' => 100,
                'is_active' => true,
                'start_date' => now()->subDay(),
                'end_date' => now()->addDays(90),
            ],
        ];

        foreach ($coupons as $data) {
            Coupon::updateOrCreate(['code' => $data['code']], $data);
        }
    }
}
