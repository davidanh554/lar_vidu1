@extends('admin.layouts.master')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold mb-0">Chỉnh sửa mã giảm giá: #{{ $coupon->code }}</h3>
        <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Quay lại
        </a>
    </div>

    <div class="card shadow-sm border-0 col-md-9">
        <div class="card-body p-4">
            <form action="{{ route('admin.coupons.update', $coupon->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Mã code:</label>
                        <input type="text" name="code" class="form-control text-uppercase font-monospace fw-bold" value="{{ old('code', $coupon->code) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Tên / Tiêu đề khuyến mãi:</label>
                        <input type="text" name="title" class="form-control" value="{{ old('title', $coupon->title) }}" required>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Loại giảm giá:</label>
                        <select name="type" id="coupon_type" class="form-select" onchange="toggleDiscountType()">
                            <option value="fixed" @selected(old('type', $coupon->type) === 'fixed')>Số tiền cố định (VNĐ)</option>
                            <option value="percent" @selected(old('type', $coupon->type) === 'percent')>Theo phần trăm (%)</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold" id="value_label">Mức giảm:</label>
                        <input type="number" step="0.01" name="value" class="form-control" value="{{ old('value', $coupon->value) }}" required>
                    </div>
                    <div class="col-md-4" id="max_discount_col">
                        <label class="form-label fw-semibold">Giảm tối đa (VNĐ):</label>
                        <input type="number" step="0.01" name="max_discount" class="form-control" value="{{ old('max_discount', $coupon->max_discount) }}">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Đơn hàng tối thiểu (VNĐ):</label>
                        <input type="number" step="0.01" name="min_order_value" class="form-control" value="{{ old('min_order_value', $coupon->min_order_value) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Tổng số lượt dùng tối đa:</label>
                        <input type="number" name="quantity" class="form-control" value="{{ old('quantity', $coupon->quantity) }}">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Thời gian bắt đầu:</label>
                        <input type="datetime-local" name="start_date" class="form-control" value="{{ old('start_date', $coupon->start_date ? $coupon->start_date->format('Y-m-d\TH:i') : '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Thời gian kết thúc:</label>
                        <input type="datetime-local" name="end_date" class="form-control" value="{{ old('end_date', $coupon->end_date ? $coupon->end_date->format('Y-m-d\TH:i') : '') }}">
                    </div>
                </div>

                <div class="mb-4 form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" @checked($coupon->is_active)>
                    <label class="form-check-label fw-semibold" for="is_active">Kích hoạt mã giảm giá này</label>
                </div>

                <button type="submit" class="btn btn-primary px-4 rounded-pill">
                    <i class="fa-solid fa-save me-1"></i> Cập nhật thay đổi
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function toggleDiscountType() {
    const type = document.getElementById('coupon_type').value;
    const maxCol = document.getElementById('max_discount_col');
    const label = document.getElementById('value_label');
    if (type === 'percent') {
        maxCol.style.display = 'block';
        label.innerText = 'Phần trăm giảm (%):';
    } else {
        maxCol.style.display = 'none';
        label.innerText = 'Mức giảm (VNĐ):';
    }
}
document.addEventListener('DOMContentLoaded', toggleDiscountType);
</script>
@endsection
