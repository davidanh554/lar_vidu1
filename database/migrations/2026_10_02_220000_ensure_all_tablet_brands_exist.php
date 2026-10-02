<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Brand;
use App\Models\Product;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $standardBrands = [
            ['slug' => 'apple', 'name' => 'Apple', 'description' => 'Máy tính bảng Apple iPad & Phụ kiện'],
            ['slug' => 'samsung', 'name' => 'Samsung', 'description' => 'Máy tính bảng Samsung Galaxy Tab'],
            ['slug' => 'xiaomi', 'name' => 'Xiaomi', 'description' => 'Máy tính bảng Xiaomi Pad & Redmi Pad'],
            ['slug' => 'lenovo', 'name' => 'Lenovo', 'description' => 'Máy tính bảng Lenovo Tab'],
            ['slug' => 'huawei', 'name' => 'Huawei', 'description' => 'Máy tính bảng Huawei MatePad'],
        ];

        foreach ($standardBrands as $b) {
            Brand::firstOrCreate(['slug' => $b['slug']], $b);
        }

        // Tự động liên kết các sản phẩm hiện có về đúng thương hiệu tương ứng
        $samsungBrand = Brand::where('slug', 'samsung')->first();
        if ($samsungBrand) {
            Product::where(function($q) {
                $q->where('name', 'LIKE', '%Samsung%')->orWhere('name', 'LIKE', '%Galaxy%');
            })->update(['brand_id' => $samsungBrand->id]);
        }

        $xiaomiBrand = Brand::where('slug', 'xiaomi')->first();
        if ($xiaomiBrand) {
            Product::where(function($q) {
                $q->where('name', 'LIKE', '%Xiaomi%')->orWhere('name', 'LIKE', '%Redmi%');
            })->update(['brand_id' => $xiaomiBrand->id]);
        }

        $lenovoBrand = Brand::where('slug', 'lenovo')->first();
        if ($lenovoBrand) {
            Product::where('name', 'LIKE', '%Lenovo%')->update(['brand_id' => $lenovoBrand->id]);
        }

        $appleBrand = Brand::where('slug', 'apple')->first();
        if ($appleBrand) {
            Product::where(function($q) {
                $q->where('name', 'LIKE', '%iPad%')->orWhere('name', 'LIKE', '%Apple%');
            })->update(['brand_id' => $appleBrand->id]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Giữ nguyên dữ liệu, không xóa brands
    }
};
