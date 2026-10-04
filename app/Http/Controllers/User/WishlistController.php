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
        if (Auth::check()) {
            $products = Auth::user()->wishlistProducts()->latest('wishlists.created_at')->paginate(12);
        } else {
            $wishlistIds = session('wishlist', []);
            $products = Product::whereIn('id', $wishlistIds)->latest()->paginate(12);
        }

        return view('products.wishlist', compact('products'));
    }

    /**
     * Thêm / Xóa nhanh sản phẩm khỏi danh sách yêu thích bằng AJAX
     */
    public function toggle(Request $request, Product $product)
    {
        $inWishlist = false;

        if (Auth::check()) {
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
        } else {
            $wishlist = session('wishlist', []);
            if (in_array($product->id, $wishlist)) {
                $wishlist = array_values(array_diff($wishlist, [$product->id]));
                $inWishlist = false;
                $msg = "Đã bỏ \"{$product->name}\" khỏi danh sách yêu thích!";
            } else {
                $wishlist[] = $product->id;
                $inWishlist = true;
                $msg = "Đã thêm \"{$product->name}\" vào danh sách yêu thích ❤️!";
            }
            session(['wishlist' => $wishlist]);
            $count = count($wishlist);
        }

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
        if (Auth::check()) {
            Wishlist::where('user_id', Auth::id())->where('product_id', $productId)->delete();
        } else {
            $wishlist = session('wishlist', []);
            $wishlist = array_values(array_diff($wishlist, [$productId]));
            session(['wishlist' => $wishlist]);
        }

        return redirect()->route('wishlist.index')->with('success', 'Đã xóa sản phẩm khỏi danh sách yêu thích.');
    }
}
