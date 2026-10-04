<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    /**
     * Hiển thị trang Danh sách sản phẩm yêu thích (Wishlist)
     */
    public function index(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('warning', 'Vui lòng đăng nhập để xem danh sách sản phẩm yêu thích của bạn!');
        }

        $products = Auth::user()->wishlistProducts()->latest('wishlists.created_at')->paginate(12);

        return view('products.wishlist', compact('products'));
    }

    /**
     * Thêm / Xóa nhanh sản phẩm khỏi danh sách yêu thích bằng AJAX
     */
    public function toggle(Request $request, Product $product)
    {
        if (!Auth::check()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'redirect' => route('login'),
                    'message' => 'Vui lòng đăng nhập để lưu sản phẩm yêu thích!',
                ], 401);
            }
            return redirect()->route('login')->with('warning', 'Vui lòng đăng nhập để thêm sản phẩm vào yêu thích!');
        }

        $userId = Auth::id();
        $existing = Wishlist::where('user_id', $userId)->where('product_id', $product->id)->first();

        if ($existing) {
            $existing->delete();
            $inWishlist = false;
            $msg = "Đã bỏ \"{$product->name}\" khỏi danh sách yêu thích!";
        } else {
            Wishlist::create([
                'user_id' => $userId,
                'product_id' => $product->id,
            ]);
            $inWishlist = true;
            $msg = "Đã thêm \"{$product->name}\" vào danh sách yêu thích ❤️!";
        }

        $count = Wishlist::where('user_id', $userId)->count();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'in_wishlist' => $inWishlist,
                'count' => $count,
                'message' => $msg,
            ]);
        }

        return back()->with('success', $msg);
    }

    /**
     * Xóa sản phẩm khỏi danh sách yêu thích
     */
    public function destroy(Request $request, $productId)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('warning', 'Vui lòng đăng nhập để quản lý danh sách yêu thích!');
        }

        Wishlist::where('user_id', Auth::id())->where('product_id', $productId)->delete();

        return redirect()->route('wishlist.index')->with('success', 'Đã xóa sản phẩm khỏi danh sách yêu thích.');
    }
}
