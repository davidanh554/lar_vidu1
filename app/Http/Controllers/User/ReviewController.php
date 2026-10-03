<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Lưu hoặc cập nhật đánh giá sản phẩm từ người dùng đã mua
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id'        => 'required|exists:products,id',
            'order_id'          => 'nullable|exists:orders,id',
            'rating'            => 'required|integer|min:1|max:5',
            'color'             => 'nullable|string|max:100',
            'match_description' => 'nullable|string|max:100',
            'quality_rating'    => 'nullable|string|max:100',
            'comment'           => 'nullable|string|max:2000',
        ]);

        $user = Auth::user();
        if (!$user) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Vui lòng đăng nhập để đánh giá.'], 401);
            }
            return redirect()->route('login')->with('warning', 'Vui lòng đăng nhập để đánh giá.');
        }

        $product = Product::findOrFail($request->product_id);

        // Lấy danh sách ID các bản ghi sản phẩm cùng model (nếu có duplicate)
        $sameProductIds = Product::where('name', $product->name)->pluck('id')->toArray();
        if (empty($sameProductIds)) {
            $sameProductIds = [$product->id];
        }

        // BẮT BUỘC: Kiểm tra đơn hàng của user đã mua và NHẬN HÀNG THÀNH CÔNG (shipping_status === 'delivered')
        $orderQuery = Order::where('user_id', $user->id)
            ->where('shipping_status', 'delivered')
            ->whereHas('items', function($q) use ($sameProductIds) {
                $q->whereIn('product_id', $sameProductIds);
            });

        if ($request->filled('order_id')) {
            $order = (clone $orderQuery)->where('id', $request->order_id)->first();
        } else {
            $order = (clone $orderQuery)->latest()->first();
        }

        // Nếu không có đơn hàng nào đã nhận hàng thành công chứa sản phẩm này -> Chặn không cho đánh giá
        if (!$order) {
            $msg = 'Bạn chỉ có thể viết đánh giá sau khi đã mua và nhận hàng thành công sản phẩm này.';
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 403);
            }
            return back()->with('error', $msg);
        }

        $review = Review::updateOrCreate(
            [
                'user_id'    => $user->id,
                'product_id' => $product->id,
                'order_id'   => $order->id,
            ],
            [
                'rating'            => (int) $request->rating,
                'color'             => $request->color,
                'match_description' => $request->match_description ?: 'đúng',
                'quality_rating'    => $request->quality_rating ?: 'Tốt',
                'comment'           => $request->comment ?: 'Sản phẩm rất tốt, giao hàng nhanh!',
            ]
        );

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Cảm ơn bạn đã đánh giá sản phẩm!',
                'review'  => $review,
            ]);
        }

        return back()->with('success', 'Cảm ơn bạn đã đánh giá sản phẩm!');
    }

    /**
     * Thả tim / Bấm hữu ích cho đánh giá
     */
    public function helpful(Review $review)
    {
        $review->increment('helpful_count');

        return response()->json([
            'success' => true,
            'helpful_count' => $review->helpful_count,
        ]);
    }
}
