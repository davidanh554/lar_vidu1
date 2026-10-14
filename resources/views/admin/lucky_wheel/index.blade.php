@extends('admin.layouts.master')

@section('content')
<div class="container-fluid px-0">
    <!-- Header & Thao tác nhanh -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h3 class="fw-bold mb-1 d-flex align-items-center gap-2">
                <i class="fa-solid fa-arrows-spin text-primary"></i> Quản lý & Cấu hình Vòng Quay May Mắn
            </h3>
            <p class="text-muted small mb-0">Tùy chỉnh 8 lát cắt giải thưởng, mã coupon liên kết, tỉ lệ trúng thưởng và hạn mức quay mỗi ngày</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-secondary rounded-pill px-3 shadow-sm">
                <i class="fa-solid fa-ticket me-1"></i> Kho Mã Giảm Giá
            </a>
            <form action="{{ route('admin.lucky-wheel.reset') }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn khôi phục 8 lát cắt về cấu hình mặc định ban đầu?');" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-warning rounded-pill px-3 shadow-sm">
                    <i class="fa-solid fa-rotate-left me-1"></i> Khôi phục mặc định
                </button>
            </form>
        </div>
    </div>

    <!-- Alert thông báo -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm mb-4" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm mb-4" role="alert">
            <i class="fa-solid fa-triangle-exclamation me-2"></i> Có lỗi xảy ra, vui lòng kiểm tra lại thông tin bên dưới!
            <ul class="mb-0 mt-1 small">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- 4 Thẻ thống kê tổng quan -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-4 p-3 {{ $enabled ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">
                        <i class="fa-solid fa-power-off fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Trạng thái Vòng quay</div>
                        <div class="fw-bold fs-6">
                            @if($enabled)
                                <span class="badge bg-success-subtle text-success px-2 py-1"><i class="fa-solid fa-circle-check me-1"></i> Đang hoạt động</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary px-2 py-1"><i class="fa-solid fa-circle-xmark me-1"></i> Đang tạm đóng</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-4 p-3 bg-primary-subtle text-primary">
                        <i class="fa-solid fa-chart-pie fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Lát cắt giải thưởng</div>
                        <div class="fw-bold fs-5">8 lát cắt <small class="text-muted fs-6">({{ count(array_filter($slices, fn($s) => ($s['type'] ?? '') === 'coupon')) }} voucher)</small></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-4 p-3 bg-warning-subtle text-warning">
                        <i class="fa-solid fa-user-clock fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Hạn mức mỗi ngày</div>
                        <div class="fw-bold fs-5">{{ $dailyLimit }} lượt / ngày</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-4 p-3 bg-info-subtle text-info">
                        <i class="fa-solid fa-gift fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Mã giảm giá khả dụng</div>
                        <div class="fw-bold fs-5">{{ $activeCouponsCount }} mã đang bật</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Cấu hình chính (Chia làm 2 cột: Cấu hình 8 lát cắt & Live Preview) -->
    <form action="{{ route('admin.lucky-wheel.settings') }}" method="POST" id="wheelForm">
        @csrf
        <div class="row g-4">
            <!-- Cột Trái: Cấu hình 8 Lát Cắt & Cấu hình chung -->
            <div class="col-lg-7 col-xl-8">
                <!-- Cấu hình chung -->
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
                    <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                        <i class="fa-solid fa-sliders text-primary"></i> 1. Cấu hình Chung Vòng Quay
                    </h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-muted">Kích hoạt tính năng</label>
                            <div class="form-check form-switch pt-1">
                                <input class="form-check-input" type="checkbox" role="switch" id="wheelEnabled" name="lucky_wheel_enabled" value="1" {{ $enabled ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold ms-2" for="wheelEnabled">
                                    Bật hiển thị Vòng quay may mắn ngoài website
                                </label>
                            </div>
                            <small class="text-muted d-block mt-1">Nếu tắt, bong bóng nổi và popup vòng quay sẽ được ẩn khỏi người dùng.</small>
                        </div>
                        <div class="col-md-6">
                            <label for="dailyLimit" class="form-label fw-semibold small text-muted">Lượt quay miễn phí mỗi ngày / User</label>
                            <div class="input-group">
                                <input type="number" class="form-control rounded-start-3" id="dailyLimit" name="daily_spins_limit" value="{{ old('daily_spins_limit', $dailyLimit) }}" min="1" max="50" required>
                                <span class="input-group-text rounded-end-3">lượt / 24h</span>
                            </div>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" id="syncAllUsers" name="sync_all_users" value="1">
                                <label class="form-check-label small text-secondary" for="syncAllUsers">
                                    Đồng bộ hạn mức này cho toàn bộ khách hàng hiện tại
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cấu hình 8 lát cắt -->
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <div>
                            <h5 class="fw-bold mb-0 d-flex align-items-center gap-2">
                                <i class="fa-solid fa-circle-notch text-primary"></i> 2. Chi tiết 8 Lát Cắt Giải Thưởng
                            </h5>
                            <small class="text-muted">Các thay đổi sẽ được mô phỏng trực tiếp trên vòng quay xem trước bên phải</small>
                        </div>
                        <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">
                            Tổng trọng số: <strong id="totalWeightBadge" class="text-primary">{{ $totalWeight }}</strong>
                        </span>
                    </div>

                    <div class="d-flex flex-column gap-3" id="slicesContainer">
                        @foreach($slices as $index => $slice)
                            @php
                                $sliceType = old("slices.{$index}.type", $slice['type'] ?? 'empty');
                                $sliceName = old("slices.{$index}.name", $slice['name'] ?? '');
                                $sliceCode = old("slices.{$index}.code", $slice['code'] ?? '');
                                $sliceWeight = old("slices.{$index}.weight", $slice['weight'] ?? 10);
                                $sliceColor = old("slices.{$index}.color", $slice['color'] ?? '#4f46e5');
                                $sliceTextColor = old("slices.{$index}.textColor", $slice['textColor'] ?? '#ffffff');
                                $sliceIcon = old("slices.{$index}.icon", $slice['icon'] ?? 'fa-ticket');
                            @endphp
                            <div class="border rounded-4 p-3 slice-card bg-light-subtle transition-all" data-index="{{ $index }}">
                                <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-dark text-white rounded-pill px-2 py-1 font-monospace">Ô #{{ $index + 1 }}</span>
                                        <span class="slice-color-preview-pill" style="display:inline-block; width:16px; height:16px; border-radius:50%; background-color: {{ $sliceColor }}; border: 1px solid #cbd5e1;"></span>
                                        <span class="fw-bold text-dark slice-title-display">{{ $sliceName }}</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-primary-subtle text-primary rounded-pill px-2 py-1 slice-percent-badge">--%</span>
                                    </div>
                                </div>

                                <div class="row g-2 align-items-center">
                                    <!-- 1. Loại giải -->
                                    <div class="col-md-3">
                                        <label class="form-label small text-muted mb-1">Loại giải thưởng</label>
                                        <select class="form-select form-select-sm rounded-3 slice-type-select" name="slices[{{ $index }}][type]" data-index="{{ $index }}">
                                            <option value="coupon" {{ $sliceType === 'coupon' ? 'selected' : '' }}>🎁 Mã Voucher</option>
                                            <option value="empty" {{ $sliceType === 'empty' ? 'selected' : '' }}>🍀 Chúc may mắn</option>
                                        </select>
                                    </div>

                                    <!-- 2. Mã Voucher liên kết (Chỉ hiển thị khi type == coupon) -->
                                    <div class="col-md-3 coupon-code-col" style="{{ $sliceType === 'empty' ? 'display: none;' : '' }}">
                                        <label class="form-label small text-muted mb-1">Mã Voucher liên kết</label>
                                        <select class="form-select form-select-sm rounded-3 slice-code-select" name="slices[{{ $index }}][code]" data-index="{{ $index }}">
                                            <option value="">-- Chọn mã coupon --</option>
                                            @foreach($coupons as $c)
                                                <option value="{{ $c->code }}" 
                                                    {{ $sliceCode === $c->code ? 'selected' : '' }}
                                                    data-title="{{ $c->title }}"
                                                    data-value="{{ $c->type === 'percent' ? $c->value . '%' : number_format($c->value) . 'đ' }}"
                                                    {{ !$c->is_active ? 'class=text-muted' : '' }}>
                                                    {{ $c->code }} ({{ $c->type === 'percent' ? '-' . $c->value . '%' : '-' . number_format($c->value) . 'đ' }})
                                                    {{ !$c->is_active ? '[Đã tắt]' : '' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- 3. Tên hiển thị trên vòng xoay -->
                                    <div class="{{ $sliceType === 'empty' ? 'col-md-6' : 'col-md-3' }} slice-name-col">
                                        <label class="form-label small text-muted mb-1">Tên trên vòng xoay</label>
                                        <input type="text" class="form-control form-control-sm rounded-3 slice-name-input" name="slices[{{ $index }}][name]" value="{{ $sliceName }}" placeholder="VD: Voucher 50K..." required data-index="{{ $index }}">
                                    </div>

                                    <!-- 4. Trọng số xác suất (Weight) -->
                                    <div class="col-md-3">
                                        <label class="form-label small text-muted mb-1">Trọng số (Weight)</label>
                                        <input type="number" step="any" min="0" max="1000" class="form-control form-control-sm rounded-3 slice-weight-input" name="slices[{{ $index }}][weight]" value="{{ $sliceWeight }}" required data-index="{{ $index }}">
                                    </div>

                                    <!-- 5. Màu nền & Màu chữ -->
                                    <div class="col-md-6 mt-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="flex-grow-1">
                                                <label class="form-label small text-muted mb-1">Màu nền lát cắt</label>
                                                <div class="d-flex align-items-center gap-1">
                                                    <input type="color" class="form-control form-control-color form-control-sm border-0 p-0 rounded-circle slice-color-picker" value="{{ $sliceColor }}" style="width: 30px; height: 30px; cursor: pointer;" data-index="{{ $index }}">
                                                    <input type="text" class="form-control form-control-sm font-monospace slice-color-input" name="slices[{{ $index }}][color]" value="{{ $sliceColor }}" style="font-size: 0.8rem;" data-index="{{ $index }}">
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <label class="form-label small text-muted mb-1">Màu chữ lát cắt</label>
                                                <div class="d-flex align-items-center gap-1">
                                                    <input type="color" class="form-control form-control-color form-control-sm border-0 p-0 rounded-circle slice-textcolor-picker" value="{{ $sliceTextColor }}" style="width: 30px; height: 30px; cursor: pointer;" data-index="{{ $index }}">
                                                    <input type="text" class="form-control form-control-sm font-monospace slice-textcolor-input" name="slices[{{ $index }}][textColor]" value="{{ $sliceTextColor }}" style="font-size: 0.8rem;" data-index="{{ $index }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- 6. Icon ngầm định -->
                                    <input type="hidden" name="slices[{{ $index }}][icon]" value="{{ $sliceIcon }}" class="slice-icon-input">
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <button type="submit" class="btn btn-success rounded-pill px-4 shadow-sm fw-bold">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Lưu Cấu Hình Vòng Quay
                        </button>
                    </div>
                </div>
            </div>

            <!-- Cột Phải: Live Preview Canvas (Vòng Quay Mô Phỏng Thời Gian Thực) -->
            <div class="col-lg-5 col-xl-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white sticky-top" style="top: 20px; z-index: 10;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0 d-flex align-items-center gap-2">
                            <i class="fa-solid fa-eye text-primary"></i> Mô Phỏng Trực Quan
                        </h6>
                        <span class="badge bg-success-subtle text-success small rounded-pill">Live Preview</span>
                    </div>

                    <!-- Canvas Vòng quay -->
                    <div class="text-center my-2 position-relative d-flex justify-content-center">
                        <div class="admin-wheel-preview-wrapper position-relative">
                            <!-- Kim chỉ (Pointer) -->
                            <div class="admin-pointer-arrow">
                                <i class="fa-solid fa-location-dot"></i>
                            </div>

                            <!-- Canvas -->
                            <canvas id="previewWheelCanvas" width="300" height="300" class="rounded-circle shadow-sm"></canvas>

                            <!-- Nút tâm demo -->
                            <button type="button" id="adminTestSpinBtn" class="admin-wheel-center-btn" title="Bấm để quay thử mô phỏng!">
                                <span>QUAY</span>
                                <small>THỬ</small>
                            </button>
                        </div>
                    </div>

                    <p class="text-center text-muted small mt-2 mb-3">
                        <i class="fa-solid fa-hand-pointer text-warning me-1"></i> Click vào <strong>QUAY THỬ</strong> ở tâm để thử nghiệm vòng xoay!
                    </p>

                    <!-- Bảng phân bổ xác suất thực tế -->
                    <div class="border rounded-3 p-2 bg-light-subtle">
                        <div class="fw-bold small mb-2 d-flex justify-content-between">
                            <span>Phân bổ xác suất:</span>
                            <span class="text-muted" id="totalProbabilityText">100%</span>
                        </div>
                        <div class="progress mb-2" style="height: 8px;" id="probabilityProgressBar">
                            <!-- Các thanh màu thanh phân bổ -->
                        </div>
                        <div id="probabilitySummaryList" class="small d-flex flex-column gap-1" style="max-height: 170px; overflow-y: auto;">
                            <!-- Danh sách render bằng JS -->
                        </div>
                    </div>

                    <!-- Hướng dẫn nhanh -->
                    <div class="mt-3 p-3 bg-light rounded-3 small text-muted">
                        <div class="fw-semibold text-dark mb-1"><i class="fa-solid fa-lightbulb text-warning me-1"></i> Mẹo quản lý:</div>
                        <ul class="ps-3 mb-0" style="font-size: 0.78rem;">
                            <li>Nên để 1-2 ô "Chúc may mắn" với trọng số 20-30% để tạo thử thách.</li>
                            <li>Voucher giá trị cao (như 100k) nên để trọng số thấp (1-2) để tránh lạm phát chi phí.</li>
                            <li>Khi đổi mã giảm giá trong Kho Voucher, hãy vào đây chọn lại mã liên kết tương ứng.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Styles đặc thù cho trang quản trị Vòng Quay -->
<style>
.slice-card {
    transition: all 0.2s ease-in-out;
}
.slice-card:hover {
    background-color: #f8fafc !important;
    border-color: #cbd5e1 !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
}
.admin-wheel-preview-wrapper {
    position: relative;
    display: inline-block;
    padding: 8px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(79, 70, 229, 0.08) 0%, rgba(241, 245, 249, 0.9) 70%);
}
#previewWheelCanvas {
    display: block;
    max-width: 100%;
    height: auto;
}
.admin-pointer-arrow {
    position: absolute;
    top: 0px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 15;
    font-size: 26px;
    color: #f59e0b;
    filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));
    pointer-events: none;
}
.admin-wheel-center-btn {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
    border: 3px solid #ffffff;
    box-shadow: 0 4px 12px rgba(79, 70, 229, 0.4);
    color: #ffffff;
    font-weight: 800;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 10;
    outline: none;
    transition: transform 0.2s, box-shadow 0.2s;
}
.admin-wheel-center-btn span {
    font-size: 0.85rem;
    line-height: 1;
}
.admin-wheel-center-btn small {
    font-size: 0.6rem;
    letter-spacing: 0.5px;
}
.admin-wheel-center-btn:hover {
    transform: translate(-50%, -50%) scale(1.08);
    box-shadow: 0 6px 16px rgba(79, 70, 229, 0.6);
}
</style>

<!-- Script Live Preview & Tương tác tức thì -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const canvas = document.getElementById('previewWheelCanvas');
    const ctx = canvas.getContext('2d');
    const testSpinBtn = document.getElementById('adminTestSpinBtn');

    let currentRotation = 0;
    let isSpinning = false;

    // 1. Hàm đọc dữ liệu lát cắt hiện thời từ form HTML
    function getSlicesData() {
        const slices = [];
        const cards = document.querySelectorAll('.slice-card');
        
        cards.forEach((card, idx) => {
            const type = card.querySelector('.slice-type-select').value;
            const name = card.querySelector('.slice-name-input').value || `Ô #${idx + 1}`;
            const code = card.querySelector('.slice-code-select') ? card.querySelector('.slice-code-select').value : '';
            const weight = parseFloat(card.querySelector('.slice-weight-input').value) || 0;
            const color = card.querySelector('.slice-color-input').value || '#4f46e5';
            const textColor = card.querySelector('.slice-textcolor-input').value || '#ffffff';

            slices.push({
                type,
                name,
                code,
                weight,
                color,
                textColor
            });
        });

        return slices;
    }

    // 2. Tính toán & cập nhật hiển thị xác suất (%) cho từng lát cắt
    function updateProbabilities(slices) {
        const totalWeight = slices.reduce((sum, s) => sum + s.weight, 0);
        document.getElementById('totalWeightBadge').innerText = totalWeight.toFixed(1);

        const summaryContainer = document.getElementById('probabilitySummaryList');
        const progressBar = document.getElementById('probabilityProgressBar');
        summaryContainer.innerHTML = '';
        progressBar.innerHTML = '';

        slices.forEach((s, idx) => {
            const percent = totalWeight > 0 ? ((s.weight / totalWeight) * 100).toFixed(1) : '0.0';
            
            // Cập nhật badge trên card lát cắt
            const card = document.querySelector(`.slice-card[data-index="${idx}"]`);
            if (card) {
                const badge = card.querySelector('.slice-percent-badge');
                if (badge) badge.innerText = `${percent}% xác suất`;
            }

            // Thanh progress bar nhiều màu
            const barSegment = document.createElement('div');
            barSegment.className = 'progress-bar';
            barSegment.style.width = `${percent}%`;
            barSegment.style.backgroundColor = s.color;
            progressBar.appendChild(barSegment);

            // Mục tóm tắt
            const row = document.createElement('div');
            row.className = 'd-flex justify-content-between align-items-center py-1 border-bottom border-light';
            row.innerHTML = `
                <div class="d-flex align-items-center gap-1 text-truncate" style="max-width: 70%;">
                    <span style="display:inline-block; width:8px; height:8px; border-radius:50%; background:${s.color};"></span>
                    <span class="text-truncate">#${idx + 1}. ${s.name}</span>
                </div>
                <div class="fw-bold font-monospace text-dark">${percent}%</div>
            `;
            summaryContainer.appendChild(row);
        });
    }

    // 3. Hàm vẽ Vòng Quay lên Canvas
    function drawPreviewWheel(angle = 0) {
        const slices = getSlicesData();
        if (slices.length === 0) return;

        const numSlices = slices.length;
        const sliceAngle = (2 * Math.PI) / numSlices;
        const centerX = canvas.width / 2;
        const centerY = canvas.height / 2;
        const radius = centerX - 10;

        ctx.clearRect(0, 0, canvas.width, canvas.height);

        // Vòng viền ngoài
        ctx.save();
        ctx.beginPath();
        ctx.arc(centerX, centerY, radius + 5, 0, 2 * Math.PI);
        ctx.strokeStyle = '#4f46e5';
        ctx.lineWidth = 5;
        ctx.stroke();
        ctx.restore();

        // Từng lát cắt
        for (let i = 0; i < numSlices; i++) {
            const startAngle = angle + i * sliceAngle;
            const endAngle = startAngle + sliceAngle;
            const slice = slices[i];

            ctx.save();
            ctx.beginPath();
            ctx.moveTo(centerX, centerY);
            ctx.arc(centerX, centerY, radius, startAngle, endAngle);
            ctx.closePath();

            ctx.fillStyle = slice.color;
            ctx.fill();

            // Đường ranh giới
            ctx.strokeStyle = 'rgba(255, 255, 255, 0.3)';
            ctx.lineWidth = 1.5;
            ctx.stroke();

            // Chữ trên lát
            ctx.save();
            ctx.translate(centerX, centerY);
            ctx.rotate(startAngle + sliceAngle / 2);
            ctx.textAlign = 'right';
            ctx.fillStyle = slice.textColor || '#ffffff';
            ctx.font = 'bold 11px sans-serif';

            let text = slice.name;
            if (text.length > 14) {
                text = text.substring(0, 12) + '..';
            }
            ctx.fillText(text, radius - 18, 4);

            ctx.restore();
            ctx.restore();
        }

        // Đèn viền nhỏ
        const numLeds = 16;
        for (let i = 0; i < numLeds; i++) {
            const ledAngle = (i * 2 * Math.PI) / numLeds;
            const lx = centerX + (radius + 2) * Math.cos(ledAngle);
            const ly = centerY + (radius + 2) * Math.sin(ledAngle);

            ctx.beginPath();
            ctx.arc(lx, ly, 2.5, 0, 2 * Math.PI);
            ctx.fillStyle = i % 2 === 0 ? '#f59e0b' : '#ffffff';
            ctx.fill();
        }

        updateProbabilities(slices);
    }

    // 4. Lắng nghe thay đổi trên Form để cập nhật Canvas tức thì (Real-time)
    document.querySelectorAll('.slice-name-input').forEach(input => {
        input.addEventListener('input', function() {
            const idx = this.dataset.index;
            const card = document.querySelector(`.slice-card[data-index="${idx}"]`);
            if (card) {
                card.querySelector('.slice-title-display').innerText = this.value || `Ô #${parseInt(idx) + 1}`;
            }
            drawPreviewWheel(currentRotation);
        });
    });

    document.querySelectorAll('.slice-weight-input').forEach(input => {
        input.addEventListener('input', () => drawPreviewWheel(currentRotation));
    });

    // Đồng bộ Color Picker và Text Hex
    document.querySelectorAll('.slice-color-picker').forEach(picker => {
        picker.addEventListener('input', function() {
            const idx = this.dataset.index;
            const textInput = document.querySelector(`.slice-color-input[data-index="${idx}"]`);
            if (textInput) textInput.value = this.value;

            const card = document.querySelector(`.slice-card[data-index="${idx}"]`);
            if (card) {
                card.querySelector('.slice-color-preview-pill').style.backgroundColor = this.value;
            }
            drawPreviewWheel(currentRotation);
        });
    });

    document.querySelectorAll('.slice-color-input').forEach(input => {
        input.addEventListener('input', function() {
            const idx = this.dataset.index;
            const picker = document.querySelector(`.slice-color-picker[data-index="${idx}"]`);
            if (picker && /^#[0-9A-Fa-f]{6}$/.test(this.value)) {
                picker.value = this.value;
                const card = document.querySelector(`.slice-card[data-index="${idx}"]`);
                if (card) {
                    card.querySelector('.slice-color-preview-pill').style.backgroundColor = this.value;
                }
            }
            drawPreviewWheel(currentRotation);
        });
    });

    document.querySelectorAll('.slice-textcolor-picker').forEach(picker => {
        picker.addEventListener('input', function() {
            const idx = this.dataset.index;
            const textInput = document.querySelector(`.slice-textcolor-input[data-index="${idx}"]`);
            if (textInput) textInput.value = this.value;
            drawPreviewWheel(currentRotation);
        });
    });

    document.querySelectorAll('.slice-textcolor-input').forEach(input => {
        input.addEventListener('input', function() {
            const idx = this.dataset.index;
            const picker = document.querySelector(`.slice-textcolor-picker[data-index="${idx}"]`);
            if (picker && /^#[0-9A-Fa-f]{6}$/.test(this.value)) {
                picker.value = this.value;
            }
            drawPreviewWheel(currentRotation);
        });
    });

    // Thay đổi loại lát cắt (Coupon vs May mắn)
    document.querySelectorAll('.slice-type-select').forEach(select => {
        select.addEventListener('change', function() {
            const idx = this.dataset.index;
            const card = document.querySelector(`.slice-card[data-index="${idx}"]`);
            const couponCol = card.querySelector('.coupon-code-col');
            const nameCol = card.querySelector('.slice-name-col');
            const nameInput = card.querySelector('.slice-name-input');

            if (this.value === 'empty') {
                couponCol.style.display = 'none';
                nameCol.className = 'col-md-6 slice-name-col';
                if (!nameInput.value || nameInput.value.includes('Voucher')) {
                    nameInput.value = 'Chúc bạn may mắn lần sau';
                    card.querySelector('.slice-title-display').innerText = 'Chúc bạn may mắn lần sau';
                }
            } else {
                couponCol.style.display = 'block';
                nameCol.className = 'col-md-3 slice-name-col';
                if (nameInput.value === 'Chúc bạn may mắn lần sau') {
                    nameInput.value = 'Voucher Mới';
                    card.querySelector('.slice-title-display').innerText = 'Voucher Mới';
                }
            }
            drawPreviewWheel(currentRotation);
        });
    });

    // Khi chọn mã Coupon trong dropdown, tự gợi ý tên nếu phù hợp
    document.querySelectorAll('.slice-code-select').forEach(select => {
        select.addEventListener('change', function() {
            const idx = this.dataset.index;
            const card = document.querySelector(`.slice-card[data-index="${idx}"]`);
            const nameInput = card.querySelector('.slice-name-input');
            const selectedOpt = this.options[this.selectedIndex];

            if (this.value && selectedOpt) {
                const valStr = selectedOpt.dataset.value;
                nameInput.value = `Voucher ${valStr}`;
                card.querySelector('.slice-title-display').innerText = nameInput.value;
                drawPreviewWheel(currentRotation);
            }
        });
    });

    // 5. Quay Thử Demo trên Canvas
    testSpinBtn.addEventListener('click', function() {
        if (isSpinning) return;
        isSpinning = true;
        testSpinBtn.disabled = true;

        const slices = getSlicesData();
        const totalWeight = slices.reduce((sum, s) => sum + s.weight, 0);

        // Thuật toán chọn lát cắt dựa vào trọng số
        let rand = Math.random() * totalWeight;
        let targetSlice = 0;
        let cur = 0;
        for (let i = 0; i < slices.length; i++) {
            cur += slices[i].weight;
            if (rand <= cur) {
                targetSlice = i;
                break;
            }
        }

        const numSlices = slices.length;
        const sliceAngle = (2 * Math.PI) / numSlices;
        const targetAngleAtPointer = (3 * Math.PI / 2) - (targetSlice * sliceAngle + sliceAngle / 2);
        const extraRounds = 4 * (2 * Math.PI);
        const finalRotation = currentRotation + extraRounds + ((targetAngleAtPointer - (currentRotation % (2 * Math.PI)) + 2 * Math.PI) % (2 * Math.PI));

        const duration = 3500;
        const startRot = currentRotation;
        const startTime = performance.now();

        function easeOutCubic(t) {
            return 1 - Math.pow(1 - t, 3);
        }

        function animate(time) {
            const elapsed = time - startTime;
            const progress = Math.min(elapsed / duration, 1);
            const eased = easeOutCubic(progress);

            currentRotation = startRot + (finalRotation - startRot) * eased;
            drawPreviewWheel(currentRotation);

            if (progress < 1) {
                requestAnimationFrame(animate);
            } else {
                isSpinning = false;
                testSpinBtn.disabled = false;
                const won = slices[targetSlice];
                alert(`[QUAY THỬ MÔ PHỎNG]\nKết quả trúng: Ô #${targetSlice + 1} - "${won.name}"\n(Trọng số: ${won.weight})`);
            }
        }

        requestAnimationFrame(animate);
    });

    // Vẽ lần đầu khi trang load
    drawPreviewWheel(0);
});
</script>
@endsection
