<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;

class AccessorySeeder extends Seeder
{
    public function run(): void
    {
        $apple = Brand::firstOrCreate(['slug' => 'apple'], ['name' => 'Apple', 'description' => 'Apple Inc.']);
        $catAcc = Category::firstOrCreate(['slug' => 'phu-kien-ipad'], ['name' => 'Phụ Kiện iPad', 'description' => 'Bút, Bàn phím, Bao da, Củ sạc chính hãng']);

        $accessories = [
            [
                'slug' => 'but-apple-pencil-pro',
                'name' => 'Bút Cảm Ứng Apple Pencil Pro (Chính hãng)',
                'image' => 'uploads/products/1786416578_cvb.png',
                'category_id' => $catAcc->id,
                'brand_id' => $apple->id,
                'price' => 3790000,
                'sale_price' => 3490000,
                'stock_quantity' => 30,
                'screen_size' => 'Tương thích iPad Air M2 & Pro M4',
                'chip' => 'Cảm ứng lực & Con quay hồi chuyển',
                'ram' => 'Sạc không dây nam châm',
                'storage' => 'Trắng',
                'color' => 'Trắng',
                'description' => 'Apple Pencil Pro đỉnh cao với cảm ứng bóp (Squeeze) đổi cọ nhanh, cảm biến xoay thân bút (Barrel roll) và rung phản hồi xúc giác Haptic.',
            ],
            [
                'slug' => 'ban-phim-magic-keyboard-ipad',
                'name' => 'Bàn Phím Magic Keyboard iPad Air/Pro 11 inch',
                'image' => 'uploads/products/1786416194_chuky.jpg',
                'category_id' => $catAcc->id,
                'brand_id' => $apple->id,
                'price' => 8490000,
                'sale_price' => 7890000,
                'stock_quantity' => 15,
                'screen_size' => '11 inch Thiết kế lơ lửng',
                'chip' => 'Đèn nền bàn phím & Cổng sạc Type-C',
                'ram' => 'Trackpad cảm ứng đa điểm',
                'storage' => 'Đen,Trắng',
                'color' => 'Đen,Trắng',
                'description' => 'Biến chiếc iPad thành chiếc máy tính xách tay thực thụ với cơ cấu phím cắt kéo 1mm êm ái, cổng USB-C sạc pass-through và trackpad mượt mà.',
            ],
            [
                'slug' => 'bao-da-smart-folio-ipad',
                'name' => 'Bao Da Thông Minh Smart Folio Nam Châm iPad',
                'image' => 'uploads/products/1786417480_images.jfif',
                'category_id' => $catAcc->id,
                'brand_id' => $apple->id,
                'price' => 990000,
                'sale_price' => 790000,
                'stock_quantity' => 50,
                'screen_size' => '10.9 - 11 inch',
                'chip' => 'Hít nam châm mặt lưng',
                'ram' => 'Gập 3 khúc dựng máy',
                'storage' => 'Xanh Rêu,Đen,Xanh Dương',
                'color' => 'Xanh Rêu,Đen,Xanh Dương',
                'description' => 'Thiết kế nam châm hít chặt mặt sau cực kỳ mỏng nhẹ, hỗ trợ tự động đánh thức/tắt màn hình khi mở gập nắp bao da.',
            ],
            [
                'slug' => 'cu-sac-nhanh-apple-20w-usb-c',
                'name' => 'Củ Sạc Nhanh Apple 20W Type-C Power Adapter',
                'image' => 'uploads/products/1786415776_698360027_981607227711513_7297394100153227655_n.jpg',
                'category_id' => $catAcc->id,
                'brand_id' => $apple->id,
                'price' => 590000,
                'sale_price' => 490000,
                'stock_quantity' => 100,
                'screen_size' => 'Chuẩn sạc nhanh Power Delivery',
                'chip' => 'Công suất 20W an toàn',
                'ram' => 'Cổng Type-C',
                'storage' => 'Trắng',
                'color' => 'Trắng',
                'description' => 'Củ sạc chính hãng Apple 20W sạc nhanh từ 0% lên 50% chỉ trong 30 phút cho mọi dòng iPad, kiểm soát nhiệt độ an toàn tuyệt đối.',
            ],
            [
                'slug' => 'dan-man-hinh-paperlike-ve-viet',
                'name' => 'Miếng Dán Màn Hình Paperlike Chống Vân Tay Cho iPad',
                'image' => 'uploads/products/1786417368_ipad2.webp',
                'category_id' => $catAcc->id,
                'brand_id' => $apple->id,
                'price' => 390000,
                'sale_price' => 290000,
                'stock_quantity' => 80,
                'screen_size' => 'Độ nhám Micro-matte mô phỏng giấy',
                'chip' => 'Chống lóa ánh sáng & chống vân tay',
                'ram' => 'Tương thích mọi bút stylus',
                'storage' => 'Trong mờ',
                'color' => 'Trong mờ',
                'description' => 'Bề mặt phủ hạt nano nhám giúp bút Apple Pencil di chuyển có độ bám và phát ra tiếng sột soạt chân thực hệt như viết vẽ trên giấy thật.',
            ],
        ];

        foreach ($accessories as $data) {
            Product::updateOrCreate(['slug' => $data['slug']], $data);
        }
    }
}
