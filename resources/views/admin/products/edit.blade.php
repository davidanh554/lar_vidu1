@extends('admin.layouts.master')

@section('content')
<div class="bg-white rounded-4 p-4 shadow-sm" style="max-width: 850px; margin: auto;">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
        <h3 class="fw-bold mb-0">Chỉnh sửa sản phẩm (Admin)</h3>
        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fa-solid fa-arrow-left me-1"></i> Quay lại
        </a>
    </div>

    <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Danh mục sản phẩm (*)</label>
                <select name="category_id" class="form-select" required>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $product->category_id == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Thương hiệu</label>
                <select name="brand_id" class="form-select">
                    <option value="">-- Chọn thương hiệu --</option>
                    @foreach($brands as $b)
                        <option value="{{ $b->id }}" {{ $product->brand_id == $b->id ? 'selected' : '' }}>
                            {{ $b->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Tên sản phẩm (*)</label>
                <input type="text" name="name" value="{{ $product->name }}" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Giá gốc (VNĐ) (*)</label>
                <input type="number" name="price" value="{{ $product->price }}" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Giá khuyến mãi (VNĐ)</label>
                <input type="number" name="sale_price" value="{{ $product->sale_price }}" class="form-control">
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Tổng tồn kho (*)</label>
                <input type="number" name="stock_quantity" id="total_stock_quantity" value="{{ $product->stock_quantity }}" class="form-control bg-light" required readonly>
                <div class="form-text text-muted small">Tự động tính từ tổng số lượng các màu bên dưới.</div>
            </div>

            <!-- Quản lý màu sắc và số lượng tồn kho từng màu -->
            <div class="col-12">
                <div class="card border border-primary-subtle bg-light-subtle p-3 rounded-3 shadow-xs">
                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                        <div>
                            <label class="form-label fw-bold mb-0 text-dark">
                                <i class="fa-solid fa-palette text-primary me-1"></i> Quản lý Màu sắc & Tồn kho theo từng màu
                            </label>
                            <div class="form-text text-muted small">Chỉnh sửa tên màu (ví dụ: Hồng, Đen, Bạc) và số lượng tồn kho tương ứng.</div>
                        </div>
                        <button type="button" class="btn btn-sm btn-primary rounded-pill px-3" id="btn-add-color">
                            <i class="fa-solid fa-plus me-1"></i> Thêm màu
                        </button>
                    </div>

                    <div id="color-rows-container" class="d-flex flex-column gap-2">
                        @php
                            $variants = $product->color_variants;
                        @endphp

                        @forelse($variants as $index => $variant)
                            <div class="row g-2 align-items-center color-row">
                                <div class="col-md-6">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-white"><i class="fa-solid fa-circle-dot text-secondary"></i></span>
                                        <input type="text" name="colors[{{ $index }}][name]" class="form-control color-name-input" value="{{ $variant['name'] }}" placeholder="Tên màu (VD: Xám Space, Hồng...)" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-white">SL kho</span>
                                        <input type="number" name="colors[{{ $index }}][quantity]" class="form-control color-qty-input text-center" min="0" value="{{ $variant['quantity'] }}" required>
                                    </div>
                                </div>
                                <div class="col-md-2 text-end">
                                    <button type="button" class="btn btn-sm btn-outline-danger btn-remove-color" title="Xóa màu này">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="row g-2 align-items-center color-row">
                                <div class="col-md-6">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-white"><i class="fa-solid fa-circle-dot text-secondary"></i></span>
                                        <input type="text" name="colors[0][name]" class="form-control color-name-input" value="Mặc định" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-white">SL kho</span>
                                        <input type="number" name="colors[0][quantity]" class="form-control color-qty-input text-center" min="0" value="{{ $product->stock_quantity }}" required>
                                    </div>
                                </div>
                                <div class="col-md-2 text-end">
                                    <button type="button" class="btn btn-sm btn-outline-danger btn-remove-color" title="Xóa màu này">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <label class="form-label fw-semibold">Vi xử lý (Chip)</label>
                <input type="text" name="chip" value="{{ $product->chip }}" class="form-control">
            </div>

            <div class="col-md-4">
                <label class="form-label fw-semibold">Màn hình</label>
                <input type="text" name="screen_size" value="{{ $product->screen_size }}" class="form-control">
            </div>

            <div class="col-md-4">
                <label class="form-label fw-semibold">RAM / Dung lượng</label>
                <input type="text" name="ram" value="{{ old('ram', $product->ram) }}" class="form-control">
            </div>

            <div class="col-12">
                <label class="form-label fw-semibold">Thay đổi ảnh sản phẩm</label>
                @if($product->image)
                    <div class="mb-2">
                        <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" style="max-height: 80px; border-radius: 8px;" class="border p-1">
                    </div>
                @endif
                <input type="file" name="image" class="form-control" accept="image/*">
            </div>

            <div class="col-12">
                <label class="form-label fw-semibold">Mô tả sản phẩm</label>
                <textarea name="description" class="form-control" rows="3">{{ $product->description }}</textarea>
            </div>

            <div class="col-12 text-end pt-3">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="fa-solid fa-check me-1"></i> Cập nhật thay đổi
                </button>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('color-rows-container');
    const btnAdd = document.getElementById('btn-add-color');
    const totalStockInput = document.getElementById('total_stock_quantity');

    function calculateTotalStock() {
        let total = 0;
        const qtyInputs = container.querySelectorAll('.color-qty-input');
        qtyInputs.forEach(input => {
            const val = parseInt(input.value) || 0;
            total += val;
        });
        totalStockInput.value = total;
    }

    let colorIndex = {{ count($variants ?? []) + 10 }};

    btnAdd.addEventListener('click', function() {
        const row = document.createElement('div');
        row.className = 'row g-2 align-items-center color-row';
        row.innerHTML = `
            <div class="col-md-6">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white"><i class="fa-solid fa-circle-dot text-secondary"></i></span>
                    <input type="text" name="colors[${colorIndex}][name]" class="form-control color-name-input" placeholder="Tên màu (VD: Xám Space, Hồng...)" required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white">SL kho</span>
                    <input type="number" name="colors[${colorIndex}][quantity]" class="form-control color-qty-input text-center" min="0" value="5" required>
                </div>
            </div>
            <div class="col-md-2 text-end">
                <button type="button" class="btn btn-sm btn-outline-danger btn-remove-color" title="Xóa màu này">
                    <i class="fa-solid fa-trash-can"></i>
                </button>
            </div>
        `;
        container.appendChild(row);
        colorIndex++;
        calculateTotalStock();
    });

    container.addEventListener('click', function(e) {
        if (e.target.closest('.btn-remove-color')) {
            const rows = container.querySelectorAll('.color-row');
            if (rows.length > 1) {
                e.target.closest('.color-row').remove();
                calculateTotalStock();
            } else {
                alert('Sản phẩm cần có ít nhất 1 màu sắc hoặc tùy chọn mặc định!');
            }
        }
    });

    container.addEventListener('input', function(e) {
        if (e.target.classList.contains('color-qty-input')) {
            calculateTotalStock();
        }
    });

    calculateTotalStock();
});
</script>
@endsection