@extends('admin.layouts.master')
@section('title', 'Báo cáo & Quản lý Đánh giá sản phẩm')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <h2 class="fw-bold mb-1">Báo cáo & Quản lý Đánh giá</h2>
            <p class="text-muted small mb-0">Theo dõi mức độ hài lòng của khách hàng và kiểm duyệt phản hồi đánh giá sản phẩm</p>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <nav class="nav nav-pills my-3" aria-label="Báo cáo">
        <a class="nav-link" href="{{ route('admin.reports.index') }}">Bảng số liệu doanh thu</a>
        <a class="nav-link" href="{{ route('admin.reports.charts') }}">Biểu đồ doanh thu</a>
        <a class="nav-link active" aria-current="page" href="{{ route('admin.reviews.index') }}">Báo cáo Đánh giá</a>
    </nav>

    <!-- Thống kê tổng quan dạng Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
                <span class="text-muted small fw-medium">Tổng lượt đánh giá</span>
                <h3 class="fw-bold mb-0 mt-1 text-dark">{{ number_format($totalReviews) }}</h3>
                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill mt-2 align-self-start">Toàn bộ sản phẩm</span>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
                <span class="text-muted small fw-medium">Điểm đánh giá trung bình</span>
                <div class="d-flex align-items-baseline gap-2 mt-1">
                    <h3 class="fw-bold mb-0 text-warning">{{ number_format($avgRating, 1) }}</h3>
                    <span class="text-muted fw-bold">/ 5.0</span>
                </div>
                <div class="text-warning small mt-2">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="fa-solid fa-star {{ $i <= round($avgRating) ? 'text-warning' : 'text-secondary opacity-25' }}"></i>
                    @endfor
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
                <span class="text-muted small fw-medium">Tỷ lệ hài lòng (4-5★)</span>
                <h3 class="fw-bold mb-0 mt-1 text-success">{{ $satisfactionRate }}%</h3>
                <span class="text-muted small mt-2 d-block">Dựa trên {{ ($starCounts[5] + $starCounts[4]) }} lượt tích cực</span>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
                <span class="text-muted small fw-medium">Phản hồi cần chú ý (1-2★)</span>
                <h3 class="fw-bold mb-0 mt-1 {{ ($starCounts[1] + $starCounts[2]) > 0 ? 'text-danger' : 'text-secondary' }}">
                    {{ $starCounts[1] + $starCounts[2] }}
                </h3>
                <span class="text-muted small mt-2 d-block">{{ ($starCounts[1] + $starCounts[2]) > 0 ? 'Cần hỗ trợ khách' : 'Chưa có khiếu nại' }}</span>
            </div>
        </div>
    </div>

    <!-- Hàng thống kê phân bổ sao & Top sản phẩm -->
    <div class="row g-3 mb-4">
        <!-- Phân bổ số sao -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                <h5 class="fw-bold mb-3">
                    Phân bổ xếp hạng sao
                </h5>
                <div class="d-flex flex-column gap-2 mt-2">
                    @for($s = 5; $s >= 1; $s--)
                        @php
                            $cnt = $starCounts[$s] ?? 0;
                            $pct = $totalReviews > 0 ? round(($cnt / $totalReviews) * 100, 1) : 0;
                        @endphp
                        <div class="d-flex align-items-center gap-3">
                            <span class="fw-bold small text-nowrap" style="width: 55px;">
                                {{ $s }} <i class="fa-solid fa-star text-warning"></i>
                            </span>
                            <div class="progress flex-fill" style="height: 10px; border-radius: 999px;">
                                <div class="progress-bar {{ $s >= 4 ? 'bg-success' : ($s == 3 ? 'bg-warning' : 'bg-danger') }}" 
                                     role="progressbar" 
                                     style="width: {{ $pct }}%;" 
                                     aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100">
                                </div>
                            </div>
                            <span class="text-muted small text-nowrap" style="width: 75px; text-align: right;">
                                {{ $cnt }} ({{ $pct }}%)
                            </span>
                        </div>
                    @endfor
                </div>
            </div>
        </div>

        <!-- Top sản phẩm nhiều đánh giá -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                <h5 class="fw-bold mb-3">
                    Sản phẩm được đánh giá nhiều nhất
                </h5>
                <div class="table-responsive">
                    <table class="table table-hover table-sm align-middle mb-0">
                        <thead class="table-light">
                            <tr class="small text-muted">
                                <th>Sản phẩm</th>
                                <th class="text-center">Số lượt</th>
                                <th class="text-end">Điểm TB</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topReviewedProducts as $tp)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            @if($tp->image)
                                                <img src="{{ asset($tp->image) }}" class="rounded border p-1" style="width: 34px; height: 34px; object-fit: contain;">
                                            @else
                                                <div class="rounded bg-light border d-flex align-items-center justify-content-center text-muted" style="width: 34px; height: 34px;">
                                                    <i class="fa-solid fa-tablet small"></i>
                                                </div>
                                            @endif
                                            <a href="{{ route('products.show', $tp->slug) }}" target="_blank" class="text-decoration-none text-dark fw-bold small text-truncate" style="max-width: 240px;">
                                                {{ $tp->name }}
                                            </a>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-2">
                                            {{ $tp->reviews_count }} lượt
                                        </span>
                                    </td>
                                    <td class="text-end fw-bold text-warning small">
                                        {{ number_format($tp->reviews_avg_rating ?? 5, 1) }} <i class="fa-solid fa-star"></i>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-3 text-muted small">Chưa có dữ liệu đánh giá sản phẩm.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Bộ lọc tìm kiếm danh sách đánh giá -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.reviews.index') }}" class="row g-2 align-items-center">
                <div class="col-md-4 col-lg-3">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Tìm theo tên khách, email, nhận xét..." value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-md-3 col-lg-2">
                    <select name="rating" class="form-select form-select-sm">
                        <option value="">-- Tất cả số sao --</option>
                        <option value="5" {{ request('rating') == '5' ? 'selected' : '' }}>5 Sao ★★★★★</option>
                        <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>4 Sao ★★★★☆</option>
                        <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>3 Sao ★★★☆☆</option>
                        <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>2 Sao ★★☆☆☆</option>
                        <option value="1" {{ request('rating') == '1' ? 'selected' : '' }}>1 Sao ★☆☆☆☆</option>
                    </select>
                </div>

                <div class="col-md-3 col-lg-3">
                    <select name="product_id" class="form-select form-select-sm">
                        <option value="">-- Tất cả sản phẩm --</option>
                        @foreach($products as $prod)
                            <option value="{{ $prod->id }}" {{ request('product_id') == $prod->id ? 'selected' : '' }}>
                                {{ $prod->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3 fw-medium">
                        <i class="fa-solid fa-filter me-1"></i> Lọc
                    </button>
                    @if(request()->hasAny(['search', 'rating', 'product_id']))
                        <a href="{{ route('admin.reviews.index') }}" class="btn btn-light btn-sm rounded-pill px-3" title="Đặt lại bộ lọc">
                            <i class="fa-solid fa-rotate-left"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Bảng danh sách chi tiết các đánh giá -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">Danh sách đánh giá từ khách hàng</h5>
            <span class="text-muted small">Hiển thị {{ $reviews->count() }} / {{ $reviews->total() }} phản hồi</span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-muted">
                    <tr>
                        <th class="ps-4" style="width: 50px;">#ID</th>
                        <th style="min-width: 220px;">Sản phẩm</th>
                        <th style="min-width: 170px;">Khách hàng</th>
                        <th style="min-width: 130px;">Xếp hạng</th>
                        <th style="min-width: 140px;">Tiêu chí</th>
                        <th style="min-width: 280px;">Nội dung nhận xét</th>
                        <th class="text-center" style="width: 100px;">Hữu ích</th>
                        <th style="min-width: 130px;">Ngày tạo</th>
                        <th class="text-end pe-4" style="width: 80px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reviews as $rev)
                        <tr>
                            <td class="ps-4 fw-bold text-muted small">#{{ $rev->id }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if(!empty($rev->product->image))
                                        <img src="{{ asset($rev->product->image) }}" class="rounded border p-1" style="width: 44px; height: 44px; object-fit: contain;">
                                    @else
                                        <div class="rounded bg-light border d-flex align-items-center justify-content-center text-muted" style="width: 44px; height: 44px;">
                                            <i class="fa-solid fa-tablet fs-5"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <a href="{{ route('products.show', $rev->product->slug ?? $rev->product_id) }}" target="_blank" class="fw-bold text-dark text-decoration-none small d-block">
                                            {{ $rev->product->name ?? 'Sản phẩm #' . $rev->product_id }}
                                        </a>
                                        @if($rev->color)
                                            <span class="badge bg-light text-secondary border small">Màu: {{ $rev->color }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark small">{{ $rev->user->name ?? 'Khách hàng' }}</div>
                                <small class="text-muted d-block">{{ $rev->user->email ?? 'Không có email' }}</small>
                                @if($rev->order_id)
                                    <span class="badge bg-info bg-opacity-10 text-info rounded-pill font-monospace small">Đơn #{{ $rev->order_id }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="text-warning small mb-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fa-solid fa-star {{ $i <= $rev->rating ? 'text-warning' : 'text-secondary opacity-25' }}"></i>
                                    @endfor
                                </div>
                                <span class="badge {{ $rev->rating >= 4 ? 'bg-success-subtle text-success border border-success-subtle' : ($rev->rating == 3 ? 'bg-warning-subtle text-warning border border-warning-subtle' : 'bg-danger-subtle text-danger border border-danger-subtle') }} rounded-pill px-2">
                                    {{ $rev->rating }} sao
                                </span>
                            </td>
                            <td>
                                <div class="small text-muted mb-1">Mô tả: <strong class="text-dark">{{ $rev->match_description ?: 'đúng' }}</strong></div>
                                <div class="small text-muted">Chất lượng: <strong class="text-dark">{{ $rev->quality_rating ?: 'Tốt' }}</strong></div>
                            </td>
                            <td>
                                <div class="text-dark small lh-base" style="white-space: pre-line;">
                                    {{ $rev->comment ?: 'Khách hàng không để lại bình luận chi tiết.' }}
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-muted border rounded-pill px-2">
                                    <i class="fa-regular fa-thumbs-up text-primary me-1"></i>{{ $rev->helpful_count }}
                                </span>
                            </td>
                            <td>
                                <span class="text-secondary small">{{ $rev->created_at->format('d/m/Y H:i') }}</span>
                            </td>
                            <td class="text-end pe-4">
                                <form action="{{ route('admin.reviews.destroy', $rev->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa phản hồi đánh giá này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle p-2" title="Xóa đánh giá vi phạm">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="fa-regular fa-comment-dots fs-1 mb-2 d-block opacity-50"></i>
                                Không tìm thấy đánh giá nào phù hợp với điều kiện lọc.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($reviews->hasPages())
            <div class="card-footer bg-white border-top py-3 d-flex justify-content-center">
                {{ $reviews->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
