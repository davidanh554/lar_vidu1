<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;

class IpadProductSeeder extends Seeder
{
    public function run(): void
    {
        $apple = Brand::firstOrCreate(['slug' => 'apple'], ['name' => 'Apple', 'description' => 'Apple Inc.']);
        $catIpad = Category::firstOrCreate(['slug' => 'ipad'], ['name' => 'iPad (Apple)', 'description' => 'Máy tính bảng Apple iPad']);

        $ipads = [
            [
                'slug' => 'ipad-gen-9-64gb',
                'name' => 'iPad Gen 9 10.2 inch Wi-Fi 64GB',
                'image' => 'uploads/products/1786417309_ipad.jfif',
                'category_id' => $catIpad->id,
                'brand_id' => $apple->id,
                'price' => 7490000,
                'sale_price' => 6990000,
                'stock_quantity' => 25,
                'screen_size' => '10.2 inch Retina IPS',
                'chip' => 'Apple A13 Bionic',
                'ram' => '3GB',
                'storage' => '64GB',
                'color' => 'Bạc,Xám Không Gian',
                'description' => 'Mẫu iPad giá rẻ quốc dân, lựa chọn tuyệt vời cho nhu cầu giải trí, xem phim, Youtube và học tập online cơ bản với mức chi phí tiết kiệm nhất.',
            ],
            [
                'slug' => 'ipad-gen-10-64gb',
                'name' => 'iPad Gen 10 10.9 inch Wi-Fi 64GB',
                'image' => 'uploads/products/1786417368_ipad2.webp',
                'category_id' => $catIpad->id,
                'brand_id' => $apple->id,
                'price' => 10290000,
                'sale_price' => 9490000,
                'stock_quantity' => 20,
                'screen_size' => '10.9 inch Liquid Retina',
                'chip' => 'Apple A14 Bionic',
                'ram' => '4GB',
                'storage' => '64GB',
                'color' => 'Xanh Dương,Hồng,Vàng,Bạc',
                'description' => 'Thiết kế viền mỏng hiện đại, cổng USB-C tiện lợi, tương thích Apple Pencil (USB-C), tối ưu cho học sinh, sinh viên học tập, ghi chú và vẽ cơ bản.',
            ],
            [
                'slug' => 'ipad-air-m2-11-128gb',
                'name' => 'iPad Air M2 11 inch Wi-Fi 128GB',
                'image' => 'uploads/products/1786417412_ipad3.jpeg',
                'category_id' => $catIpad->id,
                'brand_id' => $apple->id,
                'price' => 16990000,
                'sale_price' => 16490000,
                'stock_quantity' => 18,
                'screen_size' => '11 inch Liquid Retina',
                'chip' => 'Apple M2',
                'ram' => '8GB',
                'storage' => '128GB',
                'color' => 'Xanh Không Gian,Ánh Sao,Tím,Xám',
                'description' => 'Trang bị vi xử lý Apple M2 cực mạnh mẽ, tương thích Apple Pencil Pro với cảm ứng bóp và xoay thân bút thần thánh. Lựa chọn số 1 cho vẽ đồ họa, thiết kế và ghi chú chuyên sâu.',
            ],
            [
                'slug' => 'ipad-mini-7-128gb',
                'name' => 'iPad Mini 7 8.3 inch Wi-Fi 128GB',
                'image' => 'uploads/products/1786957740_ipad.jfif',
                'category_id' => $catIpad->id,
                'brand_id' => $apple->id,
                'price' => 14490000,
                'sale_price' => 13990000,
                'stock_quantity' => 12,
                'screen_size' => '8.3 inch Liquid Retina',
                'chip' => 'Apple A17 Pro',
                'ram' => '8GB',
                'storage' => '128GB',
                'color' => 'Xám Không Gian,Xanh Dương,Tím,Ánh Sao',
                'description' => 'Thiết kế siêu nhỏ gọn chỉ 8.3 inch dễ dàng cầm bằng 1 tay, sở hữu chip Apple A17 Pro cân mượt mà mọi tựa game nặng nhất hiện nay. Hỗ trợ Apple Pencil Pro.',
            ],
            [
                'slug' => 'ipad-pro-m4-11',
                'name' => 'iPad Pro M4 11 inch Wi-Fi 256GB',
                'image' => 'uploads/products/1786957583_images.jpg',
                'category_id' => $catIpad->id,
                'brand_id' => $apple->id,
                'price' => 28990000,
                'sale_price' => 27490000,
                'stock_quantity' => 15,
                'screen_size' => '11 inch Ultra Retina XDR Tandem OLED (120Hz)',
                'chip' => 'Apple M4',
                'ram' => '8GB',
                'storage' => '256GB',
                'color' => 'Đen Không Gian,Bạc',
                'description' => 'Đỉnh cao công nghệ của Apple với màn hình Tandem OLED 120Hz ProMotion siêu mượt, chip Apple M4 xử lý AI đồ họa vượt trội, thân máy siêu mỏng nhẹ.',
            ],
        ];

        foreach ($ipads as $data) {
            Product::updateOrCreate(['slug' => $data['slug']], $data);
        }
    }
}
