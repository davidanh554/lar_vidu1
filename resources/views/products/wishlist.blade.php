@extends('layouts.store')

@section('title', 'Sản Phẩm Yêu Thích - VUA TABLET')

@section('content')
<div class="container my-4 my-lg-5" style="max-width: 1200px;">
    <!-- Breadcrumb & Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 pb-2 border-bottom gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb small mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">Trang chủ</a></li>
                    <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Sản phẩm yêu thích</li>
                </ol>
            </nav>
            <h1 class="h3 fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <i class="fa-solid fa-heart text-danger"></i> Danh Sách Yêu Thích
            </h1>
        </div>
        <a href="{{ route('home') }}" class="btn btn-modern-outline btn-sm rounded-pill px-3">
            <i class="fa-solid fa-arrow-left me-1"></i> Tiếp tục mua sắm
        </a>
    </div>

    @if($products->isEmpty())
        <!-- Trạng thái trống -->
        <div class="card card-modern p-5 text-center my-4 border-0 shadow-sm rounded-4">
            <div class="mb-3 text-danger opacity-75">
                <i class="fa-regular fa-heart display-1"></i>
            </div>
            <h4 class="fw-bold text-dark mb-2">Danh sách yêu thích của bạn đang trống</h4>
            <p class="text-muted mb-4 mx-auto" style="max-width: 480px;">
                Hãy bấm biểu tượng trái tim ❤️ trên các sản phẩm máy tính bảng & phụ kiện bạn quan tâm để lưu lại và theo dõi giá bất cứ lúc nào.
            </p>
            <div>
                <a href="{{ route('home') }}" class="btn btn-modern-primary rounded-pill px-4 py-2 fw-bold">
                    <i class="fa-solid fa-bag-shopping me-2"></i>Khám phá sản phẩm ngay
                </a>
            </div>
        </div>
    @else
        <!-- Lưới sản phẩm yêu thích -->
        <div class="row g-4">
            @foreach($products as $product)
                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                    <div class="product-card h-100 p-3 bg-white rounded-4 border shadow-sm position-relative d-flex flex-column" style="transition: all 0.25s ease;">
                        
                        <!-- Nút xóa nhanh khỏi wishlist -->
                        <form action="{{ route('wishlist.destroy', $product->id) }}" method="POST" class="position-absolute top-0 end-0 m-2" style="z-index: 5;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-light rounded-circle shadow-sm border text-danger" title="Xóa khỏi yêu thích" style="width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center;">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </form>

                        <!-- Ảnh sản phẩm -->
                        <a href="{{ route('products.show', $product->id) }}" class="text-decoration-none mb-3">
                            <div class="card-img-wrapper d-flex align-items-center justify-content-center p-2 rounded-3 bg-light" style="height: 170px;">
                                @if($product->image)
                                    <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" style="max-height: 150px; max-width: 100%; object-fit: contain;">
                                @else
                                    <span class="text-muted small">{{ $product->name }}</span>
                                @endif
                            </div>
                        </a>

                        <!-- Cấu hình capsule -->
                        <div class="d-flex gap-1 flex-wrap mb-2">
                            @if($product->storage)
                                <span class="badge bg-light text-secondary border rounded-pill px-2 py-1" style="font-size: 0.72rem;">{{ $product->storage }}</span>
                            @endif
                            @if($product->ram)
                                <span class="badge bg-light text-secondary border rounded-pill px-2 py-1" style="font-size: 0.72rem;">{{ $product->ram }}</span>
                            @endif
                        </div>

                        <!-- Tên sản phẩm -->
                        <h6 class="product-title mb-2 text-truncate" title="{{ $product->name }}">
                            <a href="{{ route('products.show', $product->id) }}" class="text-dark text-decoration-none fw-bold" style="font-size: 0.95rem;">
                                {{ $product->name }}
                            </a>
                        </h6>

                        <!-- Tình trạng hàng & Giá bán -->
                        <div class="mt-auto">
                            <div class="mb-2">
                                @if($product->stock_quantity <= 0)
                                    <span class="badge bg-secondary text-white rounded-pill px-2 py-1" style="font-size: 0.72rem;">Hết hàng</span>
                                @else
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1" style="font-size: 0.72rem;">Còn hàng</span>
                                @endif
                            </div>

                            <div class="product-pricing-wrap mb-3">
                                @if($product->sale_price && $product->sale_price < $product->price)
                                    <span class="fw-bold" style="color: #4f46e5; font-size: 1.1rem;">{{ number_format($product->sale_price, 0, ',', '.') }}₫</span>
                                    <span class="small text-muted text-decoration-line-through ms-1">{{ number_format($product->price, 0, ',', '.') }}₫</span>
                                @else
                                    <span class="fw-bold" style="color: #4f46e5; font-size: 1.1rem;">{{ number_format($product->price, 0, ',', '.') }}₫</span>
                                @endif
                            </div>

                            <!-- Nút hành động -->
                            <div class="d-flex gap-2">
                                <a href="{{ route('products.show', $product->id) }}" class="btn btn-modern-primary btn-sm flex-grow-1 rounded-pill py-2 fw-semibold">
                                    Xem chi tiết
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $products->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
