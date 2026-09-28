@extends('admin.layouts.master')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold mb-0">Tạo mã giảm giá mới</h3>
        <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Quay lại
        </a>
    </div>

    <div class="card shadow-sm border-0 col-md-9">
        <div class="card-body p-4">
            <form action="{{ route('admin.coupons.store') }}" method="POST">
                @csrf

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Mã code (Ví dụ: SALE50K, FREESHIP):</label>
                        <div class="input-group">
                            <input type="text" name="code" id="coupon_code" class="form-control text-uppercase font-monospace fw-bold" value="{{ old('code') }}" required placeholder="NHẬP MÃ...">
                            <button type="button" class="btn btn-outline-secondary" onclick="generateCode()">Tự sinh mã</button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Tên / Tiêu đề khuyến mãi:</label>
                        <input type="text" name="title" class="form-control" value="{{ old('title') }}" required placeholder="VD: Khuyến mãi mừng hè, Voucher may mắn...">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Loại giảm giá:</label>
                        <select name="type" id="coupon_type" class="form-select" onchange="toggleDiscountType()">
                            <option value="fixed" @selected(old('type') === 'fixed')>Số tiền cố định (VNĐ)</option>
                            <option value="percent" @selected(old('type') === 'percent')>Theo phần trăm (%)</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold" id="value_label">Mức giảm (VNĐ):</label>
                        <input type="number" step="0.01" name="value" class="form-control" value="{{ old('value') }}" required placeholder="VD: 50000 hoặc 10">
                    </div>
                    <div class="col-md-4" id="max_discount_col" style="display: none;">
                        <label class="form-label fw-semibold">Giảm tối đa (VNĐ):</label>
                        <input type="number" step="0.01" name="max_discount" class="form-control" value="{{ old('max_discount') }}" placeholder="VD: 100000 (bỏ trống nếu ko giới hạn)">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Đơn hàng tối thiểu (VNĐ):</label>
                        <input type="number" step="0.01" name="min_order_value" class="form-control" value="{{ old('min_order_value', 0) }}" placeholder="0 = Không yêu cầu">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Tổng số lượt dùng tối đa:</label>
                        <input type="number" name="quantity" class="form-control" value="{{ old('quantity') }}" placeholder="Bỏ trống nếu không giới hạn lượt dùng">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Thời gian bắt đầu:</label>
                        <input type="datetime-local" name="start_date" class="form-control" value="{{ old('start_date') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Thời gian kết thúc (Hết hạn):</label>
                        <input type="datetime-local" name="end_date" class="form-control" value="{{ old('end_date') }}">
                    </div>
                </div>

                <div class="mb-4 form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" checked>
                    <label class="form-check-label fw-semibold" for="is_active">Kích hoạt ngay sau khi tạo</label>
                </div>

                <button type="submit" class="btn btn-success px-4 rounded-pill">
                    <i class="fa-solid fa-check me-1"></i> Lưu mã giảm giá
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
function generateCode() {
    const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    let code = 'VUA-';
    for (let i = 0; i < 6; i++) {
        code += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    document.getElementById('coupon_code').value = code;
}
document.addEventListener('DOMContentLoaded', toggleDiscountType);
</script>
@endsection
