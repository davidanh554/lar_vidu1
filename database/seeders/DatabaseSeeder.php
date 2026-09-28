<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            // Xóa các sản phẩm mẫu ví dụ quần áo (nếu có từ file mẫu)
            Product::whereIn('name', [
                'Quần jean nam', 'Áo thun nữ', 'Phụ kiện thời trang', 'Áo sơ mi nam',
                'Quần short nữ', 'Balo mini', 'Áo khoác jean', 'Vòng tay đá',
                'Quần tây nam', 'Áo hoodie'
            ])->delete();
            Category::whereIn('name', ['Quần', 'Áo'])->delete();

            // 1. Tạo tài khoản Admin bảo mật từ config
            $this->call(AdminUserSeeder::class);

            // 2. Tạo các thương hiệu máy tính bảng
            $apple = Brand::firstOrCreate(['slug' => 'apple'], ['name' => 'Apple', 'description' => 'Apple Inc.']);
            $samsung = Brand::firstOrCreate(['slug' => 'samsung'], ['name' => 'Samsung', 'description' => 'Samsung Electronics']);
            $xiaomi = Brand::firstOrCreate(['slug' => 'xiaomi'], ['name' => 'Xiaomi', 'description' => 'Xiaomi Corporation']);
            $lenovo = Brand::firstOrCreate(['slug' => 'lenovo'], ['name' => 'Lenovo', 'description' => 'Lenovo Group']);

            // 3. Tạo các danh mục máy tính bảng
            $catIpad = Category::firstOrCreate(['slug' => 'ipad'], ['name' => 'iPad (Apple)', 'description' => 'Máy tính bảng Apple iPad']);
            $catSamsung = Category::firstOrCreate(['slug' => 'samsung-tab'], ['name' => 'Samsung Galaxy Tab', 'description' => 'Máy tính bảng Samsung']);
            $catXiaomi = Category::firstOrCreate(['slug' => 'xiaomi-pad'], ['name' => 'Xiaomi Pad', 'description' => 'Máy tính bảng Xiaomi']);
            $catLenovo = Category::firstOrCreate(['slug' => 'lenovo-tab'], ['name' => 'Lenovo Tab', 'description' => 'Máy tính bảng Lenovo']);

            // 4. Seed sản phẩm Samsung & Xiaomi
            Product::updateOrCreate(
                ['slug' => 'samsung-galaxy-tab-s9-ultra'],
                [
                    'category_id' => $catSamsung->id,
                    'brand_id' => $samsung->id,
                    'name' => 'Samsung Galaxy Tab S9 Ultra 5G',
                    'image' => 'uploads/products/1786957692_ty.jfif',
                    'price' => 31990000,
                    'sale_price' => 29990000,
                    'stock_quantity' => 8,
                    'screen_size' => '14.6 inch Dynamic AMOLED 2X',
                    'chip' => 'Snapdragon 8 Gen 2 for Galaxy',
                    'ram' => '12GB',
                    'storage' => '256GB',
                    'description' => 'Siêu máy tính bảng màn hình lớn 14.6 inch cực sắc nét kèm bút S-Pen chuyên nghiệp chống nước IP68.',
                ]
            );

            Product::updateOrCreate(
                ['slug' => 'xiaomi-pad-6-pro'],
                [
                    'category_id' => $catXiaomi->id,
                    'brand_id' => $xiaomi->id,
                    'name' => 'Xiaomi Pad 6 Pro 8GB/128GB',
                    'image' => 'uploads/products/1786957712_idafadfa.jfif',
                    'price' => 8990000,
                    'sale_price' => 8290000,
                    'stock_quantity' => 20,
                    'screen_size' => '11 inch 144Hz 2.8K',
                    'chip' => 'Snapdragon 8+ Gen 1',
                    'ram' => '8GB',
                    'storage' => '128GB',
                    'description' => 'Hiệu năng mạnh mẽ với màn hình 144Hz siêu mượt và pin 8600mAh sạc nhanh 67W.',
                ]
            );

            // 5. Seed iPad, Phụ kiện iPad và Voucher
            $this->call(IpadProductSeeder::class);
            $this->call(AccessorySeeder::class);
            $this->call(CouponSeeder::class);
        });
    }
}
