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

        // Kiểm tra xem đơn hàng có thuộc về user này không
        if ($request->order_id) {
            $order = Order::where('id', $request->order_id)
                ->where('user_id', $user->id)
                ->first();

            if (!$order) {
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Đơn hàng không hợp lệ.'], 403);
                }
                return back()->with('error', 'Đơn hàng không hợp lệ.');
            }
        }

        $review = Review::updateOrCreate(
            [
                'user_id'    => $user->id,
                'product_id' => $request->product_id,
                'order_id'   => $request->order_id,
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
