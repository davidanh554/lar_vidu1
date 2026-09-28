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
        $brands = Brand::all();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('products._product_list', compact('products'))->render(),
                'total' => $products->total(),
            ]);
        }

        return view('products.index', compact('products', 'categories', 'brands'));
    }

    public function show($product)
    {
        if (!$product instanceof Product) {
            $product = Product::where('id', $product)->orWhere('slug', $product)->firstOrFail();
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
