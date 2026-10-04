@extends('layouts.store')

@section('title', $product->name . ' - VUA TABLET')

@section('content')
<div class="container my-5">
    <div class="mb-4">
        <a href="{{ route('home') }}" class="btn btn-modern-outline btn-sm rounded-pill">
            Quay lại trang chủ
        </a>
    </div>

    <div class="card card-modern p-4 p-lg-5 bg-white">
        <div class="row g-5">
            <!-- Ảnh sản phẩm -->
            <div class="col-lg-5 text-center">
                <div class="bg-light p-4 rounded-4 d-flex align-items-center justify-content-center border" style="min-height: 380px;">
                    @if($product->image)
                        <img src="{{ asset($product->image) }}" class="img-fluid rounded-4" style="max-height: 360px; object-fit: contain;" alt="{{ $product->name }}">
                    @else
                        <div class="text-muted">
                            <i class="fa-solid fa-tablet-screen-button fs-1 mb-2"></i>
                            <p class="mb-0">Chưa có hình ảnh</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Thông tin & Tùy chọn mua hàng -->
            <div class="col-lg-7">
                <div class="d-flex align-items-center gap-2 mb-2">
                    @if($product->category)
                        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-1 rounded-pill fw-semibold">{{ $product->category->name }}</span>
                    @endif
                    @if($product->brand)
                        <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-1 rounded-pill fw-semibold">{{ $product->brand->name }}</span>
                    @endif
                </div>

                <h1 class="h2 fw-bold text-dark mb-3">{{ $product->name }}</h1>

                @php
                    $variants = $product->color_variants;
                    $totalStock = (int)$product->stock_quantity;
                    $isOutOfStock = ($totalStock <= 0);
                @endphp

                <!-- Cấu hình nhanh -->
                <div class="d-flex flex-wrap gap-2 mb-3">
                    @if($product->chip) <span class="badge bg-light text-secondary border px-3 py-2 rounded-pill">{{ $product->chip }}</span> @endif
                    @if($product->ram) <span class="badge bg-light text-secondary border px-3 py-2 rounded-pill">{{ $product->ram }}</span> @endif
                    @if($product->screen_size) <span class="badge bg-light text-secondary border px-3 py-2 rounded-pill">{{ $product->screen_size }}</span> @endif
                </div>

                <!-- Tồn kho theo màu đã chọn -->
                <div class="mb-4" id="color-stock-badge-container">
                    @if($isOutOfStock)
                        <span class="badge-soft badge-soft-danger py-2 px-3">
                            Tạm hết hàng
                        </span>
                    @else
                        <span class="badge-soft badge-soft-success py-2 px-3" id="stock-status-badge">
                            Tổng kho: {{ $totalStock }} sản phẩm
                        </span>
                    @endif
                </div>

                <!-- Giá bán -->
                <div class="mb-4 p-3 bg-light rounded-4 d-flex align-items-baseline">
                    @if($product->sale_price && $product->sale_price < $product->price)
                        <span class="fs-2 fw-bold text-primary">{{ number_format($product->sale_price, 0, ',', '.') }}đ</span>
                        <span class="fs-5 text-muted text-decoration-line-through ms-3">{{ number_format($product->price, 0, ',', '.') }}đ</span>
                        <span class="badge bg-danger rounded-pill ms-3 px-3 py-1">Tiết kiệm {{ number_format($product->price - $product->sale_price, 0, ',', '.') }}đ</span>
                    @else
                        <span class="fs-2 fw-bold text-primary">{{ number_format($product->price, 0, ',', '.') }}đ</span>
                    @endif
                </div>

                <!-- Form Mua hàng -->
                <form action="{{ route('cart.add', $product->id) }}" method="POST" id="purchase-form">
                    @csrf
                    
                    <!-- Chọn màu sắc -->
                    <div class="mb-4">
                        <label class="form-label fw-bold d-flex justify-content-between">
                            <span>Chọn màu sắc:</span>
                            <span class="text-muted small" id="selected-color-info"></span>
                        </label>
                        <div class="d-flex flex-wrap gap-2">
                            @php
                                $firstAvailableFound = false;
                            @endphp
                            @foreach($variants as $index => $variant)
                                @php
                                    $vQty = (int)$variant['quantity'];
                                    $isAvailable = $vQty > 0;
                                    $shouldCheck = false;
                                    if ($isAvailable && !$firstAvailableFound) {
                                        $shouldCheck = true;
                                        $firstAvailableFound = true;
                                    }
                                @endphp
                                <input type="radio" 
                                       class="btn-check color-radio-input" 
                                       name="color" 
                                       id="color{{ $index }}" 
                                       value="{{ $variant['name'] }}" 
                                       data-name="{{ $variant['name'] }}" 
                                       data-stock="{{ $vQty }}" 
                                       {{ $shouldCheck ? 'checked' : '' }} 
                                       {{ (!$isAvailable && $isOutOfStock) ? 'disabled' : '' }}>
                                
                                <label class="btn btn-outline-secondary btn-sm px-3 py-2 rounded-pill d-flex align-items-center gap-2 {{ !$isAvailable ? 'opacity-50' : '' }}" for="color{{ $index }}">
                                    <span>{{ $variant['name'] }}</span>
                                    @if($vQty > 0)
                                        <span class="badge bg-white text-dark border ms-1 font-monospace">Còn {{ $vQty }}</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle ms-1">Hết</span>
                                    @endif
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Chọn số lượng -->
                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark mb-2">Số lượng đặt mua:</label>
                        <div class="d-flex align-items-center gap-3">
                            <div class="quantity-stepper">
                                <button class="stepper-btn" type="button" id="btn-qty-minus" aria-label="Giảm số lượng">
                                    <i class="fa-solid fa-minus"></i>
                                </button>
                                <input type="number" name="quantity" id="input-quantity" value="1" min="1" max="1" class="stepper-input fw-bold" required>
                                <button class="stepper-btn" type="button" id="btn-qty-plus" aria-label="Tăng số lượng">
                                    <i class="fa-solid fa-plus"></i>
                                </button>
                            </div>
                            <span class="text-muted small" id="color-stock-note"></span>
                        </div>
                    </div>

                    <!-- Bộ nút hành động -->
                    <div class="d-flex gap-2 gap-sm-3 mb-4 align-items-center">
                        <button type="submit" name="action" value="add" id="btn-add-cart" class="btn btn-modern-outline btn-lg flex-fill rounded-pill fs-6 py-3" {{ $isOutOfStock ? 'disabled style=cursor:not-allowed;' : '' }}>
                            {{ $isOutOfStock ? 'Hết hàng' : 'Thêm vào giỏ' }}
                        </button>
                        <button type="submit" name="action" value="buy_now" id="btn-buy-now" class="btn btn-modern-primary btn-lg flex-fill rounded-pill fs-6 fw-bold py-3" {{ $isOutOfStock ? 'disabled style=cursor:not-allowed;' : '' }}>
                            {{ $isOutOfStock ? 'Hết hàng' : 'Mua ngay' }}
                        </button>
                        @php
                            $isWishlisted = auth()->check()
                                ? auth()->user()->wishlists()->where('product_id', $product->id)->exists()
                                : in_array($product->id, session('wishlist', []));
                        @endphp
                        <button type="button" 
                                class="btn-wishlist-toggle btn-wishlist-toggle-detail {{ $isWishlisted ? 'active' : '' }}" 
                                data-product-id="{{ $product->id }}" 
                                data-url="{{ route('wishlist.toggle', $product->id) }}"
                                style="width: 52px; height: 52px; flex-shrink: 0; font-size: 1.35rem;"
                                title="{{ $isWishlisted ? 'Bỏ thích' : 'Thêm vào yêu thích' }}">
                            <i class="{{ $isWishlisted ? 'fa-solid text-danger' : 'fa-regular' }} fa-heart"></i>
                        </button>
                    </div>
                </form>

                <!-- Box Chính sách & Bảo hành -->
                <div class="card bg-light border-0 rounded-4 p-4">
                    <div class="mb-2 text-primary fw-bold">
                        Chính sách ưu đãi & Cam kết tại VUA TABLET
                    </div>
                    <ul class="mb-0 text-muted small lh-lg ps-3">
                        <li>Sản phẩm chính hãng 100%, bảo hành 12 tháng tại các TTBH ủy quyền.</li>
                        <li>Đổi mới trong 30 ngày đầu tiên nếu máy phát sinh lỗi phần cứng NSX.</li>
                        <li>Giao hàng hỏa tốc toàn quốc qua GHN Express, kiểm tra hàng trước khi nhận.</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Mô tả chi tiết -->
        <div class="border-top mt-5 pt-4">
            <h4 class="fw-bold mb-3 text-dark">Mô tả chi tiết sản phẩm</h4>
            <div class="text-secondary lh-lg fs-6">
                {{ $product->description ?? 'Chưa có thông tin mô tả chi tiết cho sản phẩm này.' }}
            </div>
        </div>

        <!-- PHẦN ĐÁNH GIÁ SẢN PHẨM (CHUẨN GIAO DIỆN SHOPEE) -->
        <div class="shopee-reviews-card mt-5">
            <h4 class="shopee-review-title">ĐÁNH GIÁ SẢN PHẨM</h4>

            <!-- Tổng quan điểm & bộ lọc sao -->
            <div class="shopee-rating-overview d-flex flex-wrap align-items-center gap-4">
                <div class="text-center pe-md-4 border-md-end">
                    <div class="shopee-score-text mb-1">
                        <span class="shopee-score-number">{{ number_format($avgRating, 1) }}</span> trên 5
                    </div>
                    <div class="shopee-stars-rating">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= round($avgRating))
                                <i class="fa-solid fa-star"></i>
                            @else
                                <i class="fa-regular fa-star"></i>
                            @endif
                        @endfor
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2 align-items-center flex-fill">
                    <button type="button" class="shopee-filter-btn active" data-filter="all">
                        Tất Cả
                    </button>
                    <button type="button" class="shopee-filter-btn" data-filter="5">
                        5 Sao ({{ $starCounts[5] ?? 0 }})
                    </button>
                    <button type="button" class="shopee-filter-btn" data-filter="4">
                        4 Sao ({{ $starCounts[4] ?? 0 }})
                    </button>
                    <button type="button" class="shopee-filter-btn" data-filter="3">
                        3 Sao ({{ $starCounts[3] ?? 0 }})
                    </button>
                    <button type="button" class="shopee-filter-btn" data-filter="2">
                        2 Sao ({{ $starCounts[2] ?? 0 }})
                    </button>
                    <button type="button" class="shopee-filter-btn" data-filter="1">
                        1 Sao ({{ $starCounts[1] ?? 0 }})
                    </button>
                    <button type="button" class="shopee-filter-btn" data-filter="comment">
                        Có Bình Luận ({{ $withCommentCount ?? 0 }})
                    </button>

                    @if($canReview && $userEligibleOrder)
                        <button type="button" class="btn btn-sm btn-dark rounded-pill px-3 ms-auto fw-bold shadow-sm" style="background-color: #0f172a; border-color: #0f172a;" data-bs-toggle="modal" data-bs-target="#productDetailReviewModal">
                            Viết đánh giá
                        </button>
                    @else
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 ms-auto fw-semibold" id="btn-blocked-review" data-msg="{{ $reviewBlockedMessage }}" title="{{ $reviewBlockedMessage }}">
                            Viết đánh giá
                        </button>
                    @endif
                </div>
            </div>

            <!-- Danh sách các lượt đánh giá -->
            <div id="shopee-reviews-container">
                @forelse($reviews as $rev)
                    <div class="shopee-review-item" data-rating="{{ $rev->rating }}" data-has-comment="{{ !empty(trim($rev->comment ?? '')) ? '1' : '0' }}">
                        <div class="d-flex gap-3">
                            <div class="shopee-user-avatar">
                                <i class="fa-solid fa-user"></i>
                            </div>
                            <div class="flex-fill">
                                <div class="shopee-user-name mb-1 d-flex align-items-center flex-wrap gap-2">
                                    <span>{{ $rev->user->name ?? 'Người dùng' }}</span>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0" style="font-size: 0.68rem;">
                                        Đã mua & nhận hàng
                                    </span>
                                </div>
                                <div class="shopee-stars-rating small mb-1">
                                    @for($s = 1; $s <= 5; $s++)
                                        @if($s <= $rev->rating)
                                            <i class="fa-solid fa-star"></i>
                                        @else
                                            <i class="fa-regular fa-star"></i>
                                        @endif
                                    @endfor
                                </div>
                                <div class="shopee-item-meta mb-2">
                                    {{ $rev->created_at->format('Y-m-d H:i') }} | Phân loại hàng: {{ $rev->color ?: 'Mặc định' }}
                                </div>

                                <div class="shopee-criteria-row">
                                    <span class="shopee-criteria-label">Đúng với mô tả:</span>
                                    <span class="shopee-criteria-val ms-1">{{ $rev->match_description ?: 'đúng' }}</span>
                                </div>
                                <div class="shopee-criteria-row mb-2">
                                    <span class="shopee-criteria-label">Chất lượng sản phẩm:</span>
                                    <span class="shopee-criteria-val ms-1">{{ $rev->quality_rating ?: 'Tốt' }}</span>
                                </div>

                                @if(!empty($rev->comment))
                                    <div class="shopee-review-text">{{ $rev->comment }}</div>
                                @endif

                                <div class="d-flex align-items-center gap-3 mt-2">
                                    <button type="button" class="shopee-helpful-btn" data-review-id="{{ $rev->id }}">
                                        <i class="fa-regular fa-thumbs-up"></i> Hữu Ích? <span class="helpful-num">({{ $rev->helpful_count }})</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 text-muted">
                        <i class="fa-regular fa-comment-dots fs-1 mb-2 d-block opacity-50"></i>
                        Sản phẩm này chưa có đánh giá nào.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- PHẦN SẢN PHẨM TƯƠNG TỰ (RELATED PRODUCTS) -->
    @if(isset($relatedProducts) && $relatedProducts->isNotEmpty())
        <div class="related-products-section mt-5">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h3 class="h4 fw-bold text-dark mb-1">
                        <i class="fa-solid fa-layer-group text-primary me-2"></i>Sản phẩm tương tự
                    </h3>
                    <p class="text-muted small mb-0">Các mẫu máy tính bảng & phụ kiện cùng phân khúc bạn có thể quan tâm</p>
                </div>
            </div>

            <div class="row g-4">
                @foreach($relatedProducts as $rel)
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="product-card h-100 p-3 bg-white rounded-4 border shadow-sm d-flex flex-column" style="transition: all 0.25s ease;">
                            <!-- Ảnh -->
                            <a href="{{ route('products.show', $rel->id) }}" class="text-decoration-none mb-3">
                                <div class="d-flex align-items-center justify-content-center p-2 rounded-3 bg-light" style="height: 160px;">
                                    @if($rel->image)
                                        <img src="{{ asset($rel->image) }}" alt="{{ $rel->name }}" style="max-height: 140px; max-width: 100%; object-fit: contain;">
                                    @else
                                        <span class="text-muted small">{{ $rel->name }}</span>
                                    @endif
                                </div>
                            </a>

                            <!-- Cấu hình capsule -->
                            <div class="d-flex gap-1 flex-wrap mb-2">
                                @if($rel->storage)
                                    <span class="badge bg-light text-secondary border rounded-pill px-2 py-1" style="font-size: 0.7rem;">{{ $rel->storage }}</span>
                                @endif
                                @if($rel->ram)
                                    <span class="badge bg-light text-secondary border rounded-pill px-2 py-1" style="font-size: 0.7rem;">{{ $rel->ram }}</span>
                                @endif
                            </div>

                            <!-- Tên -->
                            <h6 class="product-title mb-2 text-truncate" title="{{ $rel->name }}">
                                <a href="{{ route('products.show', $rel->id) }}" class="text-dark text-decoration-none fw-bold" style="font-size: 0.92rem;">
                                    {{ $rel->name }}
                                </a>
                            </h6>

                            <!-- Giá -->
                            <div class="mt-auto pt-2 border-top d-flex align-items-center justify-content-between">
                                <div>
                                    @if($rel->sale_price && $rel->sale_price < $rel->price)
                                        <span class="fw-bold" style="color: #4f46e5; font-size: 1rem;">{{ number_format($rel->sale_price, 0, ',', '.') }}₫</span>
                                        <span class="small text-muted text-decoration-line-through d-block" style="font-size: 0.75rem;">{{ number_format($rel->price, 0, ',', '.') }}₫</span>
                                    @else
                                        <span class="fw-bold" style="color: #4f46e5; font-size: 1rem;">{{ number_format($rel->price, 0, ',', '.') }}₫</span>
                                    @endif
                                </div>
                                <a href="{{ route('products.show', $rel->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1">
                                    Xem
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

@if($userEligibleOrder)
<!-- Modal Viết Đánh Giá Từ Trang Chi Tiết Sản Phẩm -->
<div class="modal fade" id="productDetailReviewModal" tabindex="-1" aria-labelledby="productDetailReviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header border-bottom px-4 py-3 bg-light">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center" id="productDetailReviewModalLabel">
                    <i class="fa-solid fa-star text-warning me-2 fs-5"></i>Đánh giá sản phẩm
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('reviews.store') }}" method="POST" id="form-product-detail-review">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="order_id" value="{{ $userEligibleOrder->id }}">
                <input type="hidden" name="rating" id="detail-review-rating-val" value="5">
                <input type="hidden" name="match_description" id="detail-review-match-val" value="đúng">
                <input type="hidden" name="quality_rating" id="detail-review-quality-val" value="Tốt">

                <div class="modal-body p-4">
                    <div class="d-flex align-items-center gap-3 mb-3 border-bottom pb-3">
                        <img src="{{ !empty($product->image) ? asset($product->image) : asset('images/default.png') }}" class="rounded border p-1" style="width: 50px; height: 50px; object-fit: contain;">
                        <div>
                            <h6 class="fw-bold mb-1 text-dark">{{ $product->name }}</h6>
                            <small class="text-success"><i class="fa-solid fa-check-circle me-1"></i>Đã mua từ đơn hàng #{{ $userEligibleOrder->id }}</small>
                        </div>
                    </div>

                    <div class="mb-3 d-flex align-items-center gap-3">
                        <span class="text-dark small fw-bold">Chất lượng:</span>
                        <div class="star-rating-picker" id="detail-star-picker" data-rating="5">
                            <i class="fa-solid fa-star star-item active" data-value="1"></i>
                            <i class="fa-solid fa-star star-item active" data-value="2"></i>
                            <i class="fa-solid fa-star star-item active" data-value="3"></i>
                            <i class="fa-solid fa-star star-item active" data-value="4"></i>
                            <i class="fa-solid fa-star star-item active" data-value="5"></i>
                        </div>
                        <span class="star-feedback-label fw-bold text-primary small" id="detail-star-label">Tuyệt vời</span>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary mb-1">Đúng với mô tả:</label>
                        <div class="d-flex gap-2 flex-wrap" id="detail-desc-match-pills">
                            <button type="button" class="btn btn-sm btn-dark rounded-pill px-3" data-val="đúng">đúng</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" data-val="rất đúng">rất đúng</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" data-val="đúng một phần">đúng một phần</button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary mb-1">Chất lượng sản phẩm:</label>
                        <div class="d-flex gap-2 flex-wrap" id="detail-quality-pills">
                            <button type="button" class="btn btn-sm btn-dark rounded-pill px-3" data-val="Tốt">Tốt</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" data-val="Tuyệt vời">Tuyệt vời</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" data-val="Bình thường">Bình thường</button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary mb-1">Nhận xét chi tiết:</label>
                        <textarea name="comment" class="form-control rounded-3" rows="3" placeholder="Hãy chia sẻ cảm nhận về sản phẩm..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top px-4 py-3 bg-light">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-dark rounded-pill px-4 fw-bold" style="background-color: #0f172a; border-color: #0f172a;">Gửi đánh giá</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Toast thông báo giỏ hàng không reload -->
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 9999;">
    <div id="cartToast" class="toast toast-modern align-items-center border-0 shadow-lg rounded-4" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex p-2">
            <div class="toast-body d-flex align-items-center gap-2 fs-6">
                <i id="cartToastIcon" class="fa-solid fa-circle-check fs-5 text-indigo-light"></i>
                <span id="cartToastMsg">Đã thêm vào giỏ hàng thành công!</span>
            </div>
            <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const colorRadios = document.querySelectorAll('.color-radio-input');
    const qtyInput = document.getElementById('input-quantity');
    const btnMinus = document.getElementById('btn-qty-minus');
    const btnPlus = document.getElementById('btn-qty-plus');
    const btnAddCart = document.getElementById('btn-add-cart');
    const btnBuyNow = document.getElementById('btn-buy-now');
    const stockBadge = document.getElementById('stock-status-badge');
    const colorNote = document.getElementById('color-stock-note');
    const selectedColorInfo = document.getElementById('selected-color-info');
    const purchaseForm = document.getElementById('purchase-form');
    const cartToastEl = document.getElementById('cartToast');
    const cartToast = new bootstrap.Toast(cartToastEl, { delay: 3000 });
    const cartToastMsg = document.getElementById('cartToastMsg');
    const cartToastIcon = document.getElementById('cartToastIcon');
    const navCartCount = document.getElementById('nav-cart-count');

    function showNotification(message, isSuccess = true) {
        cartToastEl.className = `toast toast-modern align-items-center ${isSuccess ? 'toast-modern-success' : 'toast-modern-danger'} border-0 shadow-lg rounded-4`;
        cartToastIcon.className = isSuccess ? 'fa-solid fa-circle-check fs-5 text-indigo-light' : 'fa-solid fa-circle-exclamation fs-5 text-danger';
        cartToastMsg.textContent = message;
        cartToast.show();
    }

    function updateStockForSelectedColor() {
        const checkedRadio = document.querySelector('.color-radio-input:checked');
        if (!checkedRadio) {
            if (colorRadios.length > 0) {
                colorRadios[0].checked = true;
                return updateStockForSelectedColor();
            }
            return;
        }

        const colorName = checkedRadio.getAttribute('data-name');
        const stock = parseInt(checkedRadio.getAttribute('data-stock')) || 0;

        selectedColorInfo.innerHTML = `Đang chọn: <strong>${colorName}</strong>`;

        if (stock > 0) {
            stockBadge.className = 'badge-soft badge-soft-success py-2 px-3';
            stockBadge.innerHTML = `Màu <strong>${colorName}</strong>: Còn <strong>${stock}</strong> máy`;
            colorNote.textContent = `(Tối đa ${stock} sản phẩm cho màu ${colorName})`;
            
            qtyInput.disabled = false;
            qtyInput.max = stock;
            if (parseInt(qtyInput.value) > stock) {
                qtyInput.value = stock;
            }
            if (parseInt(qtyInput.value) < 1) {
                qtyInput.value = 1;
            }

            btnAddCart.disabled = false;
            btnBuyNow.disabled = false;
            btnAddCart.innerHTML = `Thêm vào giỏ`;
            btnBuyNow.innerHTML = `Mua ngay`;
        } else {
            stockBadge.className = 'badge-soft badge-soft-danger py-2 px-3';
            stockBadge.innerHTML = `Màu <strong>${colorName}</strong>: Đã hết hàng`;
            colorNote.textContent = `(Màu này tạm thời hết hàng)`;
            
            qtyInput.value = 0;
            qtyInput.max = 0;
            qtyInput.disabled = true;

            btnAddCart.disabled = true;
            btnBuyNow.disabled = true;
            btnAddCart.innerHTML = `Hết hàng`;
            btnBuyNow.innerHTML = `Hết hàng`;
        }
    }

    colorRadios.forEach(radio => {
        radio.addEventListener('change', updateStockForSelectedColor);
    });

    btnMinus.addEventListener('click', function() {
        let current = parseInt(qtyInput.value) || 1;
        if (current > 1) {
            qtyInput.value = current - 1;
        }
    });

    btnPlus.addEventListener('click', function() {
        let current = parseInt(qtyInput.value) || 1;
        let max = parseInt(qtyInput.max) || 1;
        if (current < max) {
            qtyInput.value = current + 1;
        }
    });

    qtyInput.addEventListener('input', function() {
        let current = parseInt(qtyInput.value) || 1;
        let max = parseInt(qtyInput.max) || 1;
        if (current > max) {
            qtyInput.value = max;
        }
        if (current < 1) {
            qtyInput.value = 1;
        }
    });

    // Xử lý Thêm giỏ hàng AJAX
    if (btnAddCart && purchaseForm) {
        btnAddCart.addEventListener('click', function(e) {
            e.preventDefault();

            const formData = new FormData(purchaseForm);
            formData.set('action', 'add');

            const originalHtml = btnAddCart.innerHTML;
            btnAddCart.disabled = true;
            btnAddCart.innerHTML = `<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Đang thêm...`;
            const actionUrl = purchaseForm.getAttribute('action') || "{{ route('cart.add', $product->id) }}";

            fetch(actionUrl, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(response => {
                return response.json().then(data => ({
                    status: response.status,
                    data: data
                }));
            })
            .then(({ status, data }) => {
                if (data.redirect) {
                    window.location.href = data.redirect;
                    return;
                }

                if (data.success) {
                    if (navCartCount && data.cart_count !== undefined) {
                        navCartCount.textContent = data.cart_count;
                        navCartCount.classList.remove('d-none');
                    }

                    showNotification(data.message || 'Đã thêm sản phẩm vào giỏ hàng thành công!', true);

                    btnAddCart.innerHTML = `Đã thêm!`;
                    setTimeout(() => {
                        btnAddCart.disabled = false;
                        btnAddCart.innerHTML = originalHtml;
                    }, 1200);
                } else {
                    showNotification(data.message || 'Có lỗi xảy ra, vui lòng thử lại!', false);
                    btnAddCart.disabled = false;
                    btnAddCart.innerHTML = originalHtml;
                }
            })
            .catch(error => {
                console.error('Lỗi thêm giỏ hàng:', error);
                showNotification('Không thể kết nối tới máy chủ. Vui lòng thử lại!', false);
                btnAddCart.disabled = false;
                btnAddCart.innerHTML = originalHtml;
            });
        });
    }

    // Filter Shopee reviews by star / comments
    const filterButtons = document.querySelectorAll('.shopee-filter-btn');
    const reviewItems = document.querySelectorAll('.shopee-review-item');

    filterButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            filterButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            const filter = this.dataset.filter;
            reviewItems.forEach(item => {
                const rating = item.dataset.rating;
                const hasComment = item.dataset.hasComment === '1';

                if (filter === 'all') {
                    item.style.display = '';
                } else if (filter === 'comment') {
                    item.style.display = hasComment ? '' : 'none';
                } else if (filter === rating) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });

    // Helpful button
    document.querySelectorAll('.shopee-helpful-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const reviewId = this.dataset.reviewId;
            const helpfulNum = this.querySelector('.helpful-num');
            const icon = this.querySelector('i');

            if (this.classList.contains('liked')) return;

            fetch(`/reviews/${reviewId}/helpful`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    if (helpfulNum) helpfulNum.textContent = `(${data.helpful_count})`;
                    this.classList.add('liked');
                    if (icon) {
                        icon.classList.remove('fa-regular');
                        icon.classList.add('fa-solid');
                    }
                }
            })
            .catch(err => console.error(err));
        });
    });

    // Detail Review Modal rating picker
    const detailStarPicker = document.getElementById('detail-star-picker');
    if (detailStarPicker) {
        const ratingVal = document.getElementById('detail-review-rating-val');
        const starLabel = document.getElementById('detail-star-label');
        const stars = detailStarPicker.querySelectorAll('.star-item');
        const starLabels = {
            5: 'Tuyệt vời',
            4: 'Hài lòng',
            3: 'Bình thường',
            2: 'Không hài lòng',
            1: 'Rất tệ'
        };

        stars.forEach(star => {
            star.addEventListener('click', function() {
                const val = parseInt(this.dataset.value);
                if (ratingVal) ratingVal.value = val;
                if (starLabel) starLabel.textContent = starLabels[val] || '';
                stars.forEach(s => {
                    if (parseInt(s.dataset.value) <= val) {
                        s.classList.add('active');
                    } else {
                        s.classList.remove('active');
                    }
                });
            });
        });
    }

    // Detail Modal desc pills
    const detailDescPills = document.getElementById('detail-desc-match-pills');
    if (detailDescPills) {
        const inputDesc = document.getElementById('detail-review-match-val');
        detailDescPills.querySelectorAll('button').forEach(btn => {
            btn.addEventListener('click', function() {
                detailDescPills.querySelectorAll('button').forEach(b => {
                    b.classList.remove('btn-dark');
                    b.classList.add('btn-outline-secondary');
                });
                this.classList.remove('btn-outline-secondary');
                this.classList.add('btn-dark');
                if (inputDesc) inputDesc.value = this.dataset.val;
            });
        });
    }

    // Detail Modal quality pills
    const detailQualityPills = document.getElementById('detail-quality-pills');
    if (detailQualityPills) {
        const inputQuality = document.getElementById('detail-review-quality-val');
        detailQualityPills.querySelectorAll('button').forEach(btn => {
            btn.addEventListener('click', function() {
                detailQualityPills.querySelectorAll('button').forEach(b => {
                    b.classList.remove('btn-dark');
                    b.classList.add('btn-outline-secondary');
                });
                this.classList.remove('btn-outline-secondary');
                this.classList.add('btn-dark');
                if (inputQuality) inputQuality.value = this.dataset.val;
            });
        });
    }

    // Detail Modal: Click listener for non-eligible review button
    const btnBlockedReview = document.getElementById('btn-blocked-review');
    if (btnBlockedReview) {
        btnBlockedReview.addEventListener('click', function() {
            const msg = this.dataset.msg || 'Bạn cần mua và nhận hàng thành công sản phẩm này mới có thể viết đánh giá.';
            if (typeof showNotification === 'function') {
                showNotification(msg, false);
            } else {
                alert(msg);
            }
        });
    }

    updateStockForSelectedColor();
});
</script>
@endpush