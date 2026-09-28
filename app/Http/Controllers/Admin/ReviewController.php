<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Báo cáo và danh sách quản lý đánh giá sản phẩm
     */
    public function index(Request $request)
    {
        $query = Review::with(['user', 'product', 'order'])->latest();

        // Lọc theo số sao
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        // Lọc theo sản phẩm
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        // Tìm kiếm theo từ khóa (tên khách, email, bình luận)
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('comment', 'LIKE', "%{$search}%")
                  ->orWhere('match_description', 'LIKE', "%{$search}%")
                  ->orWhere('quality_rating', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'LIKE', "%{$search}%")
                         ->orWhere('email', 'LIKE', "%{$search}%");
                  })
                  ->orWhereHas('product', function ($pq) use ($search) {
                      $pq->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        $reviews = $query->paginate(15)->withQueryString();

        // Thống kê tổng quan báo cáo đánh giá
        $allReviews = Review::all();
        $totalReviews = $allReviews->count();
        $avgRating = $totalReviews > 0 ? round($allReviews->avg('rating'), 1) : 5.0;

        $starCounts = [
            5 => $allReviews->where('rating', 5)->count(),
            4 => $allReviews->where('rating', 4)->count(),
            3 => $allReviews->where('rating', 3)->count(),
            2 => $allReviews->where('rating', 2)->count(),
            1 => $allReviews->where('rating', 1)->count(),
        ];

        // Tỷ lệ hài lòng (4 và 5 sao)
        $satisfiedCount = $starCounts[5] + $starCounts[4];
        $satisfactionRate = $totalReviews > 0 ? round(($satisfiedCount / $totalReviews) * 100, 1) : 100;

        // Top sản phẩm nhận được nhiều đánh giá nhất
        $topReviewedProducts = Product::has('reviews')
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->orderByDesc('reviews_count')
            ->take(5)
            ->get();

        $products = Product::select('id', 'name')->orderBy('name')->get();

        return view('admin.reviews.index', compact(
            'reviews',
            'totalReviews',
            'avgRating',
            'starCounts',
            'satisfactionRate',
            'topReviewedProducts',
            'products'
        ));
    }

    /**
     * Xóa đánh giá (khi vi phạm hoặc spam)
     */
    public function destroy(Review $review)
    {
        $review->delete();

        return back()->with('success', 'Đã xóa đánh giá thành công.');
    }
}
