@extends('admin.layouts.master')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="fw-bold">Quản lý Sản phẩm (Admin)</h2>
    <a href="{{ route('admin.products.create') }}" class="btn btn-success rounded-pill px-3">
        Thêm sản phẩm mới
    </a>
</div>

<!-- Bộ lọc sản phẩm -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
    <div class="btn-group btn-group-sm shadow-sm" role="group">
        <a href="{{ route('admin.products.index') }}" class="btn {{ (!request('sort') || request('sort') === 'newest') ? 'btn-primary' : 'btn-outline-secondary' }}">
            Tất cả sản phẩm
        </a>
        <a href="{{ route('admin.products.index', ['sort' => 'best_sellers']) }}" class="btn {{ request('sort') === 'best_sellers' ? 'btn-primary' : 'btn-outline-secondary' }}">
            Bán chạy nhất
        </a>
    </div>
    <div class="text-muted small">
        Đang hiển thị <strong>{{ $products->total() }}</strong> sản phẩm
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th width="40px" class="text-center"><input type="checkbox" id="check-all-products" class="form-check-input"></th>
                    <th width="60px" class="text-center">ID</th>
                    <th width="90px">Hình ảnh</th>
                    <th>Tên sản phẩm & Màu sắc / Tồn kho</th>
                    <th>Danh mục</th>
                    <th>Giá bán</th>
                    <th width="90px" class="text-center">Đã bán</th>
                    <th width="120px" class="text-center">Tổng kho</th>
                    <th width="180px" class="text-center">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr>
                        <td class="text-center"><input type="checkbox" class="form-check-input product-check-item" value="{{ $product->id }}"></td>
                        <td class="text-center fw-bold text-muted">{{ $product->id }}</td>
                        <td>
                            @if($product->image)
                                <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" style="width: 60px; height: 60px; object-fit: contain;" class="rounded border p-1 bg-white">
                            @else
                                <div class="bg-light rounded d-flex align-items-center justify-content-center text-muted" style="width: 60px; height: 60px;">
                                    <i class="fa-solid fa-tablet-screen-button"></i>
                                </div>
                            @endif
                        </td>
                        <td>
                            <div class="fw-bold text-dark fs-6">{{ $product->name }}</div>
                            @if(!empty($product->color_variants))
                                <div class="mt-1 d-flex flex-wrap gap-1">
                                    @foreach($product->color_variants as $v)
                                        <span class="badge {{ $v['quantity'] > 0 ? 'bg-light text-dark border' : 'bg-danger-subtle text-danger border border-danger-subtle' }} py-1 px-2 small">
                                            <i class="fa-solid fa-circle-dot me-1 small"></i>{{ $v['name'] }}: <strong>{{ $v['quantity'] }}</strong>
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-secondary-subtle text-secondary fs-7">
                                {{ $product->category->name ?? 'N/A' }}
                            </span>
                        </td>
                        <td>
                            @if($product->sale_price && $product->sale_price < $product->price)
                                <span class="fw-bold text-danger">{{ number_format($product->sale_price, 0, ',', '.') }}đ</span>
                                <div class="text-muted text-decoration-line-through small">{{ number_format($product->price, 0, ',', '.') }}đ</div>
                            @else
                                <span class="fw-bold text-primary">{{ number_format($product->price, 0, ',', '.') }}đ</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if((int)($product->total_sold ?? 0) > 0)
                                <span class="badge bg-danger-subtle text-danger fw-bold rounded-pill px-2 py-1" style="font-size: 0.8rem;">
                                    {{ (int)$product->total_sold }}
                                </span>
                            @else
                                <span class="text-muted small">0</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($product->stock_quantity > 0)
                                <span class="badge bg-success-subtle text-success fs-6 py-2 px-3 rounded-pill border border-success-subtle">
                                    {{ $product->stock_quantity }}
                                </span>
                            @else
                                <span class="badge bg-danger-subtle text-danger fs-6 py-2 px-3 rounded-pill border border-danger-subtle">
                                    Hết hàng
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.products.show', $product->id) }}" class="btn btn-outline-info" title="Xem chi tiết">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-outline-warning" title="Chỉnh sửa">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Xóa">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">Chưa có sản phẩm nào trong hệ thống.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex justify-content-center mt-4">
    {{ $products->links() }}
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const checkAll = document.getElementById('check-all-products');
    const itemChecks = document.querySelectorAll('.product-check-item');

    if (checkAll) {
        checkAll.addEventListener('change', function () {
            itemChecks.forEach(cb => cb.checked = checkAll.checked);
        });

        itemChecks.forEach(cb => {
            cb.addEventListener('change', function () {
                checkAll.checked = Array.from(itemChecks).every(i => i.checked);
            });
        });
    }
});
</script>
@endsection