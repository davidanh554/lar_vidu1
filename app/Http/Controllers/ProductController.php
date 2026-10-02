<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // 1. Đảm bảo đầy đủ các thương hiệu máy tính bảng phổ biến trong hệ thống
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

        $brands = Brand::orderByRaw("FIELD(slug, 'apple', 'samsung', 'xiaomi', 'lenovo', 'huawei') ASC, name ASC")->get();

        // 2. Tự động đồng bộ brand_id cho các sản phẩm chưa được gán thương hiệu
        $samsungBrand = $brands->firstWhere('slug', 'samsung');
        if ($samsungBrand) {
            Product::where(function($q) {
                $q->where('name', 'LIKE', '%Samsung%')->orWhere('name', 'LIKE', '%Galaxy%');
            })->where(function($q) use ($samsungBrand) {
                $q->whereNull('brand_id')->orWhere('brand_id', '!=', $samsungBrand->id);
            })->update(['brand_id' => $samsungBrand->id]);
        }

        $xiaomiBrand = $brands->firstWhere('slug', 'xiaomi');
        if ($xiaomiBrand) {
            Product::where(function($q) {
                $q->where('name', 'LIKE', '%Xiaomi%')->orWhere('name', 'LIKE', '%Redmi%');
            })->where(function($q) use ($xiaomiBrand) {
                $q->whereNull('brand_id')->orWhere('brand_id', '!=', $xiaomiBrand->id);
            })->update(['brand_id' => $xiaomiBrand->id]);
        }

        $lenovoBrand = $brands->firstWhere('slug', 'lenovo');
        if ($lenovoBrand) {
            Product::where('name', 'LIKE', '%Lenovo%')
                ->where(function($q) use ($lenovoBrand) {
                    $q->whereNull('brand_id')->orWhere('brand_id', '!=', $lenovoBrand->id);
                })->update(['brand_id' => $lenovoBrand->id]);
        }

        $appleBrand = $brands->firstWhere('slug', 'apple');
        if ($appleBrand) {
            Product::where(function($q) {
                $q->where('name', 'LIKE', '%iPad%')->orWhere('name', 'LIKE', '%Apple%');
            })->where(function($q) use ($appleBrand) {
                $q->whereNull('brand_id')->orWhere('brand_id', '!=', $appleBrand->id);
            })->update(['brand_id' => $appleBrand->id]);
        }

        $query = Product::with(['category', 'brand']);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('chip', 'LIKE', "%{$search}%")
                  ->orWhere('ram', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        $products = $query->latest()->paginate(12)->withQueryString();
        $categories = Category::all();

        // Lấy sản phẩm thực tế gán cho các Banner quảng cáo lớn
        $bannerIpad = Product::where('name', 'like', '%iPad Pro%')->orWhere('name', 'like', '%iPad%')->first() ?? $products->first();
        $bannerGalaxy = Product::where('name', 'like', '%Galaxy Tab%')->orWhere('name', 'like', '%Samsung%')->first() ?? $products->skip(1)->first() ?? $bannerIpad;
        $bannerXiaomi = Product::where('name', 'like', '%Xiaomi%')->first() ?? $products->skip(2)->first() ?? $bannerIpad;

        if ($request->ajax()) {
            return response()->json([
                'html' => view('products._product_list', compact('products'))->render(),
                'total' => $products->total(),
            ]);
        }

        return view('products.index', compact('products', 'categories', 'brands', 'bannerIpad', 'bannerGalaxy', 'bannerXiaomi'));
    }

    public function show($product)
    {
        if (!$product instanceof Product) {
            $productModel = Product::where('id', $product)->orWhere('slug', $product)->first();
            if (!$productModel) {
                // Tự động tìm sản phẩm nổi bật thay thế nếu ID không tồn tại (tránh lỗi 404)
                $fallback = Product::where('name', 'like', '%iPad%')->first() ?? Product::first();
                if ($fallback) {
                    return redirect()->route('products.show', $fallback->id)
                        ->with('info', 'Sản phẩm bạn tìm kiếm hiện không còn, chúng tôi đã chuyển hướng bạn đến mẫu máy bán chạy nhất.');
                }
                return redirect()->route('home')->with('warning', 'Không tìm thấy sản phẩm.');
            }
            $product = $productModel;
        }

        $product->load(['category', 'brand', 'reviews.user']);

        $reviews = $product->reviews;
        $totalReviews = $reviews->count();
        $avgRating = $totalReviews > 0 ? round($reviews->avg('rating'), 1) : 5.0;

        $starCounts = [
            5 => $reviews->where('rating', 5)->count(),
            4 => $reviews->where('rating', 4)->count(),
            3 => $reviews->where('rating', 3)->count(),
            2 => $reviews->where('rating', 2)->count(),
            1 => $reviews->where('rating', 1)->count(),
        ];
        $withCommentCount = $reviews->filter(fn($r) => !empty(trim($r->comment ?? '')))->count();

        $userEligibleOrder = null;
        if (\Illuminate\Support\Facades\Auth::check()) {
            $userEligibleOrder = \App\Models\Order::where('user_id', \Illuminate\Support\Facades\Auth::id())
                ->where('shipping_status', 'delivered')
                ->whereHas('items', fn($q) => $q->where('product_id', $product->id))
                ->latest()
                ->first();
        }

        return view('products.show', compact('product', 'reviews', 'totalReviews', 'avgRating', 'starCounts', 'withCommentCount', 'userEligibleOrder'));
    }

    public function show_normal(Product $product)
    {
        return view('products.show', compact('product'));
    }
}
