@php
    $favIds = auth()->check()
        ? auth()->user()->wishlists()->pluck('product_id')->toArray()
        : [];
@endphp

<!-- Products Grid (PhongMobile Clean Minimalist Style) -->
<div class="row g-4">
    @forelse($products as $index => $product)
        <div class="col-12 col-sm-6 col-md-4 col-lg-3 product-grid-item" style="--item-idx: {{ $index }};">
            <div class="product-card">
                
                <!-- Huy hiệu góc trên bên trái (HẾT HÀNG / TOP BÁN CHẠY / HOT / MỚI) -->
                <div class="product-badges-corner">
                    @if($product->stock_quantity <= 0)
                        <span class="badge bg-secondary text-white fw-bold px-2 py-1 rounded-2 shadow-sm" style="font-size: 0.72rem;">HẾT HÀNG</span>
                    @elseif(request('sort') === 'best_sellers' && (int)($product->total_sold ?? 0) > 0)
                        <span class="badge {{ $index === 0 ? 'bg-danger' : ($index === 1 ? 'bg-warning text-dark' : 'bg-primary') }} rounded-pill px-2 py-1 shadow-sm fw-bold" style="font-size: 0.72rem;">
                            <i class="fa-solid fa-trophy me-1"></i>Top #{{ $index + 1 }}
                        </span>
                    @else
                        @if($product->sale_price && $product->sale_price < $product->price)
                            <span class="badge-flag-hot">HOT</span>
                        @endif
                        <span class="badge-flag-new">MỚI</span>
                    @endif
                </div>

                <!-- Nút Yêu thích góc trên bên phải (Wishlist Heart Button) -->
                @php
                    $isFav = in_array($product->id, $favIds);
                @endphp
                <div class="product-wishlist-corner">
                    <button type="button" 
                            class="btn-wishlist-toggle {{ $isFav ? 'active' : '' }}" 
                            data-product-id="{{ $product->id }}" 
                            data-url="{{ route('wishlist.toggle', $product->id) }}"
                            title="{{ $isFav ? 'Bỏ thích' : 'Thêm vào yêu thích' }}">
                        <i class="{{ $isFav ? 'fa-solid text-danger' : 'fa-regular' }} fa-heart"></i>
                    </button>
                </div>

                <!-- Ảnh sản phẩm căn giữa sạch sẽ -->
                <a href="{{ route('products.show', $product->id) }}" class="text-decoration-none">
                    <div class="card-img-wrapper">
                        @if($product->image)
                            <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" loading="lazy">
                        @else
                            <div class="d-flex align-items-center justify-content-center h-100 text-muted small fw-medium">
                                <span>{{ $product->name }}</span>
                            </div>
                        @endif
                    </div>
                </a>

                <!-- Thông số cấu hình dạng Capsule -->
                <div class="product-specs-list">
                    @php
                        $storage = $product->storage ?? '256GB';
                        $ram = $product->ram ? $product->ram : '8GB';
                        if (is_numeric(trim($ram))) {
                            $ram = trim($ram) . 'GB';
                        }
                    @endphp
                    <span class="spec-capsule">{{ $storage }}</span>
                    <span class="spec-capsule">{{ $ram }}</span>
                    @if($product->screen_size)
                        <span class="spec-capsule d-none d-xl-inline-flex">{{ $product->screen_size }}</span>
                    @endif
                </div>

                <!-- Tên sản phẩm in đậm -->
                <h5 class="product-title mb-1">
                    <a href="{{ route('products.show', $product->id) }}">
                        {{ $product->name }}
                    </a>
                </h5>

                <!-- Hiển thị số lượng đã bán -->
                @if(request('sort') === 'best_sellers' || (int)($product->total_sold ?? 0) > 0)
                    <div class="product-sold-label mb-2">
                        <span class="badge bg-danger-subtle text-danger fw-semibold border border-danger-subtle px-2 py-1 rounded-pill" style="font-size: 0.74rem;">
                            <i class="fa-solid fa-fire me-1"></i>Đã bán {{ (int)($product->total_sold ?? 0) }} chiếc
                        </span>
                    </div>
                @endif

                <!-- Giá bán màu tím Indigo rực rỡ đặc trưng -->
                <div class="product-pricing-wrap">
                    @if($product->sale_price && $product->sale_price < $product->price)
                        <span class="product-price-current">{{ number_format($product->sale_price, 0, ',', '.') }}₫</span>
                        <span class="product-price-old">{{ number_format($product->price, 0, ',', '.') }}₫</span>
                    @else
                        <span class="product-price-current">{{ number_format($product->price, 0, ',', '.') }}₫</span>
                    @endif
                </div>

                <!-- Cặp nút Thêm vào giỏ hàng & Mua ngay -->
                <div class="product-actions-wrap">
                    @if($product->stock_quantity <= 0)
                        <button type="button" class="btn-card-detail opacity-50" disabled style="cursor: not-allowed;">
                            Hết hàng
                        </button>
                    @else
                        <form action="{{ route('cart.add', $product->id) }}" method="POST" class="ajax-add-cart-form flex-grow-1 mb-0 d-flex">
                            @csrf
                            <input type="hidden" name="action" value="add_to_cart">
                            <button type="submit" class="btn-card-detail btn-ajax-add w-100" title="Thêm vào giỏ hàng">
                                Thêm vào giỏ hàng
                            </button>
                        </form>
                    @endif

                    @if($product->stock_quantity <= 0)
                        <button type="button" class="btn-card-buy opacity-50" disabled style="cursor: not-allowed;">
                            Hết hàng
                        </button>
                    @else
                        <form action="{{ route('cart.add', $product->id) }}" method="POST" class="flex-grow-1 mb-0 d-flex">
                            @csrf
                            <input type="hidden" name="action" value="buy_now">
                            <button type="submit" class="btn-card-buy w-100">
                                Mua ngay
                            </button>
                        </form>
                    @endif
                </div>

            </div>
        </div>
    @empty
        <div class="col-12 text-center py-5">
            <div class="card card-modern py-5">
                <div class="mb-3 text-muted">
                    <i class="fa-solid fa-box-open fs-1"></i>
                </div>
                <h4 class="fw-bold mb-2">Không tìm thấy sản phẩm nào phù hợp</h4>
                <p class="text-muted mb-3">Vui lòng kiểm tra lại từ khóa tìm kiếm hoặc chọn danh mục khác.</p>
                <div>
                    <button type="button" class="btn btn-nav-dark btn-sm rounded-pill px-4 js-btn-reset-filter">
                        Xem tất cả sản phẩm
                    </button>
                </div>
            </div>
        </div>
    @endforelse
</div>

<!-- Pagination -->
<div class="d-flex justify-content-center mt-5 ajax-pagination-wrapper">
    {{ $products->links('pagination::bootstrap-5') }}
</div>
