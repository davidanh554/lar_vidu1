<!-- Products Grid -->
<div class="row g-4">
    @forelse($products as $index => $product)
        <div class="col-12 col-sm-6 col-md-4 col-lg-3 product-grid-item" style="--item-idx: {{ $index }};">
            <div class="product-card">
                
                <!-- Click vào ảnh để xem chi tiết -->
                <a href="{{ route('products.show', $product->id) }}" class="text-decoration-none">
                    <div class="card-img-wrapper">
                        @if($product->category)
                            <span class="badge bg-white text-dark position-absolute top-0 start-0 m-3 border shadow-sm small py-1 px-2 rounded-pill">
                                {{ $product->category->name }}
                            </span>
                        @endif
                        @if($product->image)
                            <img src="{{ asset($product->image) }}" class="img-fluid" alt="{{ $product->name }}">
                        @else
                            <div class="d-flex align-items-center justify-content-center h-100 text-muted small fw-medium">
                                <span>Vua Tablet</span>
                            </div>
                        @endif
                    </div>
                </a>

                <div class="card-body">
                    <div>
                        <h5 class="product-title">
                            <a href="{{ route('products.show', $product->id) }}" class="text-dark text-decoration-none">
                                {{ $product->name }}
                            </a>
                        </h5>

                        <div class="d-flex gap-1 mb-2 flex-wrap">
                            @if($product->chip) <span class="badge bg-light text-secondary border small">{{ $product->chip }}</span> @endif
                            @if($product->ram) <span class="badge bg-light text-secondary border small">{{ $product->ram }}</span> @endif
                        </div>
                        <p class="card-text text-muted small text-truncate">{{ $product->description ?? 'Chưa có mô tả' }}</p>
                    </div>

                    <div class="mt-3 pt-2 border-top">
                        <div class="mb-3 d-flex align-items-baseline">
                            @if($product->sale_price && $product->sale_price < $product->price)
                                <span class="product-price-current">{{ number_format($product->sale_price, 0, ',', '.') }}đ</span>
                                <span class="product-price-old">{{ number_format($product->price, 0, ',', '.') }}đ</span>
                            @else
                                <span class="product-price-current">{{ number_format($product->price, 0, ',', '.') }}đ</span>
                            @endif
                        </div>

                        <!-- Cặp nút: Thêm giỏ hàng & Mua ngay -->
                        @if($product->stock_quantity <= 0)
                            <button class="btn btn-secondary btn-sm w-100 rounded-pill" disabled>
                                Tạm hết hàng
                            </button>
                        @else
                            <div class="d-flex gap-2">
                                <form action="{{ route('cart.add', $product->id) }}" method="POST" class="flex-grow-1 ajax-add-cart-form">
                                    @csrf
                                    <button type="submit" class="btn btn-modern-outline btn-sm w-100 btn-ajax-add">
                                        Thêm vào giỏ
                                    </button>
                                </form>

                                <form action="{{ route('cart.add', $product->id) }}" method="POST" class="flex-grow-1">
                                    @csrf
                                    <input type="hidden" name="action" value="buy_now">
                                    <button type="submit" class="btn btn-modern-primary btn-sm w-100">
                                        Mua ngay
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    @empty
        <div class="col-12 text-center py-5">
            <div class="card card-modern py-5">
                <h4 class="fw-bold mb-2">Không tìm thấy sản phẩm nào phù hợp</h4>
                <p class="text-muted mb-3">Vui lòng kiểm tra lại từ khóa tìm kiếm hoặc chọn danh mục khác.</p>
                <div>
                    <button type="button" class="btn btn-modern-primary btn-sm rounded-pill px-4 js-btn-reset-filter">
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
