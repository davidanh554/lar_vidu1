@extends('admin.layouts.master')

@section('content')
<div class="bg-white rounded-4 p-4 p-md-5 shadow-sm">
    <div class="mb-4 pb-3 border-bottom d-flex justify-content-between align-items-center">
        <a href="{{ route('admin.products.index') }}" class="text-decoration-none text-muted fw-semibold">
            <i class="fa-solid fa-arrow-left me-1"></i> Quay lại danh sách
        </a>
        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-2">
            Tổng tồn kho: {{ $product->stock_quantity }} máy
        </span>
    </div>

    <div class="row g-5 align-items-center">
        <!-- Image Preview Column -->
        <div class="col-md-5 text-center">
            @php
                $imagePath = $product->image ? asset($product->image) : 'https://vsp.vn/vsonline/uploads/2024/05/ipad-air-m4-11-inch-wifi-128gb.jpg';
            @endphp
            <div class="p-4 rounded-4 bg-light d-inline-block w-100">
                <img src="{{ $imagePath }}" class="img-fluid" style="max-height: 350px; object-fit: contain;" alt="{{ $product->name }}">
            </div>
        </div>

        <!-- Info Column -->
        <div class="col-md-7">
            <h1 class="fw-bold fs-2 mb-2">{{ $product->name }}</h1>
            
            <!-- Specs Grid Pills -->
            <div class="d-flex flex-wrap gap-2 mb-3">
                @if($product->chip) <span class="badge bg-dark rounded-pill px-3 py-2">{{ $product->chip }}</span> @endif
                @if($product->screen_size) <span class="badge bg-secondary rounded-pill px-3 py-2">{{ $product->screen_size }}</span> @endif
                @if($product->ram) <span class="badge bg-secondary rounded-pill px-3 py-2">{{ $product->ram }}</span> @endif
            </div>

            <!-- Color Variants & Stock -->
            <div class="mb-4">
                <h6 class="fw-bold text-muted small text-uppercase mb-2">Tồn kho theo màu sắc</h6>
                <div class="d-flex flex-wrap gap-2">
                    @foreach($product->color_variants as $v)
                        <div class="border rounded-3 p-2 px-3 bg-light d-flex align-items-center gap-2">
                            <i class="fa-solid fa-circle-dot text-primary"></i>
                            <div>
                                <strong class="d-block">{{ $v['name'] }}</strong>
                                <span class="badge {{ $v['quantity'] > 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} rounded-pill font-monospace">
                                    {{ $v['quantity'] > 0 ? 'Còn ' . $v['quantity'] . ' máy' : 'Hết hàng' }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Price Block -->
            <div class="p-3 bg-light rounded-4 my-4 d-flex align-items-baseline gap-3">
                @if($product->sale_price && $product->sale_price < $product->price)
                    <span class="fs-2 fw-extrabold text-primary">{{ number_format($product->sale_price, 0, ',', '.') }}đ</span>
                    <span class="fs-5 text-muted text-decoration-line-through">{{ number_format($product->price, 0, ',', '.') }}đ</span>
                    <span class="badge bg-danger rounded-pill">Khuyến mãi</span>
                @else
                    <span class="fs-2 fw-extrabold text-primary">{{ number_format($product->price, 0, ',', '.') }}đ</span>
                @endif
            </div>

            <!-- Description -->
            <div class="mb-4">
                <h6 class="fw-bold text-uppercase text-muted small">Mô tả sản phẩm</h6>
                <p class="text-secondary leading-relaxed">
                    {{ $product->description ?? 'Chưa có thông tin mô tả chi tiết cho sản phẩm này.' }}
                </p>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex gap-3 pt-3 border-top">
                <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-warning rounded-pill px-4 fw-semibold">
                    <i class="fa-solid fa-pen-to-square me-1"></i> Chỉnh sửa
                </a>
                <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Xóa sản phẩm này?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger rounded-pill px-4 fw-semibold">
                        <i class="fa-solid fa-trash me-1"></i> Xóa
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection