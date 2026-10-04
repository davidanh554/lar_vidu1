<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $sort = $request->query('sort');
        $query = Product::with(['category', 'brand']);

        $paidOrderIds = \App\Models\Order::where(function($q) {
            $q->whereIn('status', ['paid', 'paid_momo', 'cod_paid'])
              ->orWhere('shipping_status', 'delivered');
        })->whereNotIn('status', ['cancelled'])->where('shipping_status', '!=', 'cancelled')->pluck('id');

        $query->withSum(['orderItems as total_sold' => function($q) use ($paidOrderIds) {
            $q->whereIn('order_id', $paidOrderIds);
        }], 'quantity');

        if ($sort === 'best_sellers') {
            $query->orderByDesc('total_sold')->orderByDesc('id');
        } else {
            $query->latest('id');
        }

        $products = $query->paginate(12)->withQueryString();
        return view('admin.products.index', compact('products', 'sort'));
    }

    public function create()
    {
        $categories = Category::all();
        $brands = Brand::all();
        return view('admin.products.create', compact('categories', 'brands'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'price' => 'required|numeric',
            'sale_price' => 'nullable|numeric',
            'stock_quantity' => 'nullable|integer',
            'colors' => 'nullable|array',
            'colors.*.name' => 'nullable|string|max:100',
            'colors.*.quantity' => 'nullable|integer|min:0',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = $request->except('image', 'colors');
        $data['slug'] = Str::slug($request->name) . '-' . time();

        // Xử lý biến thể màu sắc và số lượng tồn kho
        $colorVariants = [];
        $totalColorStock = 0;
        $hasColorVariants = false;

        if ($request->has('colors') && is_array($request->colors)) {
            foreach ($request->colors as $item) {
                if (!empty($item['name']) && trim($item['name']) !== '') {
                    $qty = max(0, (int)($item['quantity'] ?? 0));
                    $colorVariants[] = [
                        'name' => trim($item['name']),
                        'quantity' => $qty,
                    ];
                    $totalColorStock += $qty;
                    $hasColorVariants = true;
                }
            }
        }

        if ($hasColorVariants) {
            $data['colors'] = $colorVariants;
            $data['color'] = implode(', ', array_column($colorVariants, 'name'));
            $data['stock_quantity'] = $totalColorStock;
        } else {
            $data['colors'] = null;
            $data['stock_quantity'] = (int)$request->input('stock_quantity', 0);
        }

        if ($request->hasFile('image')) {
            $imageName = time() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path('uploads/products'), $imageName);
            $data['image'] = 'uploads/products/' . $imageName;
        }

        Product::create($data);

        return redirect()->route('admin.products.index')->with('success', 'Thêm sản phẩm thành công!');
    }

    public function show(Product $product)
    {
        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        $brands = Brand::all();
        return view('admin.products.edit', compact('product', 'categories', 'brands'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|max:255',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'price' => 'required|numeric',
            'sale_price' => 'nullable|numeric',
            'stock_quantity' => 'nullable|integer',
            'colors' => 'nullable|array',
            'colors.*.name' => 'nullable|string|max:100',
            'colors.*.quantity' => 'nullable|integer|min:0',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = $request->except('image', 'colors');
        $data['slug'] = Str::slug($request->name) . '-' . $product->id;

        // Xử lý biến thể màu sắc và số lượng tồn kho
        $colorVariants = [];
        $totalColorStock = 0;
        $hasColorVariants = false;

        if ($request->has('colors') && is_array($request->colors)) {
            foreach ($request->colors as $item) {
                if (!empty($item['name']) && trim($item['name']) !== '') {
                    $qty = max(0, (int)($item['quantity'] ?? 0));
                    $colorVariants[] = [
                        'name' => trim($item['name']),
                        'quantity' => $qty,
                    ];
                    $totalColorStock += $qty;
                    $hasColorVariants = true;
                }
            }
        }

        if ($hasColorVariants) {
            $data['colors'] = $colorVariants;
            $data['color'] = implode(', ', array_column($colorVariants, 'name'));
            $data['stock_quantity'] = $totalColorStock;
        } else {
            $data['colors'] = null;
            $data['stock_quantity'] = (int)$request->input('stock_quantity', 0);
        }

        if ($request->hasFile('image')) {
            $imageName = time() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path('uploads/products'), $imageName);
            $data['image'] = 'uploads/products/' . $imageName;
        }

        $product->update($data);

        return redirect()->route('admin.products.index')->with('success', 'Cập nhật sản phẩm thành công!');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Xóa sản phẩm thành công!');
    }
}