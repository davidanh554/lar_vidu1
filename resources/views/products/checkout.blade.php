@extends('layouts.store')

@section('title', 'Thanh Toán Đơn Hàng - VUA TABLET')

@section('content')
<div class="container my-5">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="fw-bold mb-1 d-flex align-items-center">
                <i class="fa-solid fa-credit-card me-3 text-primary"></i>Thanh toán đơn hàng
            </h2>
            <p class="text-muted mb-0 small">Hoàn tất địa chỉ nhận hàng và phương thức thanh toán an toàn</p>
        </div>
        <a href="{{ route('cart.index') }}" class="btn btn-modern-outline btn-sm rounded-pill">
            <i class="fa-solid fa-arrow-left me-1"></i> Trở về giỏ hàng
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-modern-danger alert-dismissible fade show mb-4" role="alert">
            <div class="fw-bold mb-1"><i class="fa-solid fa-circle-exclamation me-2"></i>Vui lòng kiểm tra lại thông tin:</div>
            <ul class="mb-0 ps-3 small">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('payment.process') }}" method="POST" id="checkout-order-form">
        @csrf
        <div class="row g-4">
            <!-- Thông tin người nhận -->
            <div class="col-lg-7">
                <div class="card card-modern p-4 mb-4">
                    <h5 class="fw-bold mb-3 d-flex align-items-center">
                        <i class="fa-solid fa-location-dot me-2 text-primary"></i>1. Thông tin giao hàng GHN
                    </h5>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Họ và tên người nhận <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', old('fullname', auth()->user()->name ?? '')) }}" required placeholder="Nhập đầy đủ họ và tên">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Số điện thoại liên hệ <span class="text-danger">*</span></label>
                        <input type="tel" name="phone" class="form-control" value="{{ old('phone') }}" required maxlength="11" placeholder="Ví dụ: 0987654321">
                    </div>

                    <!-- Chọn Tỉnh/Thành, Quận/Huyện, Phường/Xã từ GHN -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Tỉnh / Thành phố <span class="text-danger">*</span></label>
                            <select id="province_select" name="province_id" class="form-select" required>
                                <option value="">-- Đang tải... --</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Quận / Huyện <span class="text-danger">*</span></label>
                            <select id="district_select" name="to_district_id" class="form-select" disabled required>
                                <option value="">-- Chọn Quận/Huyện --</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Phường / Xã <span class="text-danger">*</span></label>
                            <select id="ward_select" name="to_ward_code" class="form-select" disabled required>
                                <option value="">-- Chọn Phường/Xã --</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Địa chỉ chi tiết (Số nhà, tên đường) <span class="text-danger">*</span></label>
                        <textarea name="address" class="form-control" rows="2" required maxlength="100" placeholder="Số nhà, ngõ ngách, tên đường...">{{ old('address') }}</textarea>
                    </div>

                    <!-- Inputs ẩn lưu tên địa danh phục vụ hiển thị & API -->
                    <input type="hidden" name="province_name" id="province_name">
                    <input type="hidden" name="district_name" id="district_name">
                    <input type="hidden" name="ward_name" id="ward_name">

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Ghi chú đơn hàng (Tùy chọn)</label>
                        <textarea name="note" class="form-control" rows="2" placeholder="Ví dụ: Giao giờ hành chính, gọi trước khi giao..."></textarea>
                    </div>

                    <!-- Phương thức thanh toán (COD hoặc MoMo) -->
                    <div class="mt-4 pt-3 border-top">
                        <h5 class="fw-bold mb-3 d-flex align-items-center">
                            <i class="fa-solid fa-wallet me-2 text-primary"></i>2. Phương thức thanh toán <span class="text-danger">*</span>
                        </h5>
                        <div class="d-flex flex-column gap-3">
                            <label for="payment_cod" class="p-3 border rounded-3 d-flex align-items-center bg-white shadow-sm" style="cursor: pointer;">
                                <input class="form-check-input mt-0 me-3 fs-5" type="radio" name="payment_method" value="cod" id="payment_cod" checked style="cursor: pointer;">
                                <div>
                                    <div class="fw-bold text-dark"><i class="fa-solid fa-truck text-success me-2"></i>Thanh toán khi nhận hàng (COD)</div>
                                    <small class="text-muted">Nhận máy, kiểm tra hàng chính hãng và thanh toán tiền mặt cho nhân viên bưu tá GHN.</small>
                                </div>
                            </label>

                            <label for="payment_momo" class="p-3 border rounded-3 d-flex align-items-center bg-white shadow-sm" style="cursor: pointer;">
                                <input class="form-check-input mt-0 me-3 fs-5" type="radio" name="payment_method" value="momo" id="payment_momo" style="cursor: pointer;">
                                <div>
                                    <div class="fw-bold text-dark d-flex align-items-center">
                                        <span class="badge me-2" style="background-color: #a50064; font-size: 0.78rem; font-weight: 700; padding: 4px 8px; border-radius: 6px;">MoMo</span>
                                        Thanh toán qua Cổng MoMo (Ví MoMo / Thẻ ATM Napas / QR)
                                    </div>
                                    <small class="text-muted">Chuyển hướng an toàn tới Cổng MoMo để thanh toán trực tuyến tức thì.</small>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tóm tắt đơn hàng thanh toán -->
            <div class="col-lg-5">
                <div class="summary-card">
                    <h5 class="fw-bold mb-3 d-flex align-items-center">
                        <i class="fa-solid fa-bag-shopping me-2 text-primary"></i>Sản phẩm thanh toán
                    </h5>
                    
                    <div class="list-group list-group-flush mb-3">
                        @foreach(($cart ?? $checkoutItems ?? []) as $key => $item)
                            <input type="hidden" name="selected_items[]" value="{{ $key }}">
                            <div class="list-group-item d-flex align-items-center justify-content-between px-0 py-3 bg-transparent">
                                <div class="d-flex align-items-center">
                                    @if(!empty($item['image']))
                                        <div class="rounded-3 border p-1 bg-white me-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; flex-shrink: 0;">
                                            <img src="{{ asset($item['image']) }}" alt="{{ $item['name'] }}" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                        </div>
                                    @endif
                                    <div>
                                        <span class="fw-bold d-block small text-dark">{{ $item['name'] }}</span>
                                        <small class="text-muted">Màu: <strong>{{ $item['color'] ?? 'Tiêu chuẩn' }}</strong> | SL: x{{ $item['quantity'] }}</small>
                                    </div>
                                </div>
                                <span class="fw-bold text-dark fs-6">{{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}đ</span>
                            </div>
                        @endforeach
                    </div>

                    <hr class="my-3 border-secondary border-opacity-25">

                    <!-- Khung Mã Giảm Giá / Voucher từ Vòng Quay May Mắn -->
                    <div class="mb-3 p-3 rounded-3" style="background: rgba(0, 255, 135, 0.05); border: 1px solid rgba(0, 255, 135, 0.25);">
                        <label class="form-label fw-bold small text-dark d-flex align-items-center justify-content-between mb-2">
                            <span><i class="fa-solid fa-ticket text-success me-1"></i> Mã giảm giá / Voucher</span>
                            <span class="badge bg-success-subtle text-success small">Tối ưu chi phí</span>
                        </label>

                        <div class="input-group mb-2">
                            <input type="text" id="coupon_code_input" class="form-control text-uppercase font-monospace fw-bold" placeholder="NHẬP MÃ GIẢM GIÁ..." autocomplete="off">
                            <button type="button" id="btn-apply-coupon" class="btn btn-modern-primary px-3">
                                Áp dụng
                            </button>
                        </div>
                        <div id="coupon-message" class="small mt-1" style="display: none;"></div>

                        @if(!empty($userCoupons) && $userCoupons->count() > 0)
                            <div class="mt-2 pt-2 border-top border-secondary border-opacity-10">
                                <small class="text-muted d-block mb-1"><i class="fa-solid fa-gift text-warning me-1"></i> Voucher từ Vòng Quay của bạn:</small>
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach($userCoupons as $uc)
                                        <button type="button" class="btn btn-sm btn-outline-success py-0 px-2 rounded-pill font-monospace btn-quick-coupon" style="font-size: 0.75rem;" data-code="{{ $uc->coupon->code }}" title="{{ $uc->coupon->title }}">
                                            {{ $uc->coupon->code }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Hộp Dùng Xu Trừ Tiền Mặt Trực Tiếp -->
                    @php
                        $userCoins = auth()->user()->coins ?? 0;
                        $coinsCashValue = $userCoins * 500;
                    @endphp
                    <div class="card p-3 rounded-4 mb-3 border-warning border-opacity-50" style="background: rgba(251, 191, 36, 0.08);">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle bg-warning text-dark d-flex align-items-center justify-content-center fw-bold" style="width: 38px; height: 38px;">
                                    <i class="fa-solid fa-coins fs-5"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark d-flex align-items-center gap-1">
                                        <span>Dùng Xu Vua Tablet</span>
                                        <span class="badge bg-warning text-dark py-0 px-2" style="font-size: 0.68rem;">1 Xu = 500₫</span>
                                    </div>
                                    <div class="small text-muted">
                                        Bạn có <strong class="text-warning text-dark-emphasis">{{ number_format($userCoins) }} Xu</strong> (Quy đổi: -{{ number_format($coinsCashValue) }}₫)
                                    </div>
                                </div>
                            </div>
                            <div class="form-check form-switch fs-4 mb-0">
                                <input class="form-check-input" type="checkbox" id="use_coins_switch" name="use_coins" value="1" {{ $userCoins > 0 ? '' : 'disabled' }}>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Tiền hàng:</span>
                        <span class="fw-bold text-dark">{{ number_format($totalPrice ?? $total ?? 0, 0, ',', '.') }}đ</span>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Cước vận chuyển (GHN):</span>
                        <span id="shipping_fee_text" class="text-primary fw-bold">0 VNĐ</span>
                    </div>

                    <!-- Dòng hiển thị số tiền được giảm giá từ Coupon -->
                    <div id="discount_row" class="justify-content-between mb-2 text-success align-items-center" style="display: none !important;">
                        <span class="d-flex align-items-center gap-1">
                            <i class="fa-solid fa-circle-check text-success"></i> Giảm giá (<strong id="applied_code_badge"></strong>):
                            <button type="button" id="btn-remove-coupon" class="btn btn-link btn-sm text-danger p-0 ms-1" style="text-decoration: none; font-size: 0.75rem;" title="Hủy mã này">(Gỡ)</button>
                        </span>
                        <span id="discount_amount_text" class="fw-bold fs-6">-0đ</span>
                    </div>

                    <!-- Dòng hiển thị số tiền được giảm từ Xu -->
                    <div id="coins_discount_row" class="justify-content-between mb-3 text-warning align-items-center" style="display: none !important;">
                        <span class="d-flex align-items-center gap-1 text-dark fw-semibold">
                            <i class="fa-solid fa-coins text-warning"></i> Giảm giá từ Xu:
                        </span>
                        <span id="coins_discount_amount_text" class="fw-bold fs-6 text-warning-emphasis">-0đ</span>
                    </div>

                    <div class="p-3 bg-light rounded-3 mb-3 small text-muted">
                        <i class="fa-solid fa-truck-fast text-primary me-1"></i> Tự động tính cước trực tuyến từ giao vận Giao Hàng Nhanh (GHN).
                    </div>

                    <hr class="my-3 border-secondary border-opacity-25">

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="fw-bold fs-6">Tổng thanh toán:</span>
                        <span id="final_total_text" class="fw-bold fs-4 text-primary">{{ number_format($totalPrice ?? $total ?? 0, 0, ',', '.') }}đ</span>
                    </div>

                    <!-- Input ẩn lưu mã giảm giá áp dụng -->
                    <input type="hidden" name="coupon_code" id="coupon_code_hidden" value="">

                    <!-- Input ẩn tính cước & submit -->
                    <input type="hidden" id="total_price_input" value="{{ $totalPrice ?? $total ?? 0 }}">
                    <input type="hidden" name="shipping_fee" id="shipping_fee_input" value="0">

                    <button type="submit" id="btn-submit-order" class="btn btn-modern-primary w-100 rounded-pill py-3 fw-bold fs-6 shadow">
                        <i class="fa-solid fa-circle-check me-2"></i> Xác nhận đặt hàng
                    </button>
                    
                    <div class="text-center mt-3 small text-muted">
                        <i class="fa-solid fa-shield-halved me-1"></i> Cam kết bảo mật thông tin khách hàng tuyệt đối
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<!-- Script GHN tự động tải địa chỉ và tính phí giao hàng -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    const provinceSelect = document.getElementById('province_select');
    const districtSelect = document.getElementById('district_select');
    const wardSelect = document.getElementById('ward_select');
    const shippingFeeText = document.getElementById('shipping_fee_text');
    const finalTotalText = document.getElementById('final_total_text');
    const totalPriceInput = document.getElementById('total_price_input');
    const shippingFeeInput = document.getElementById('shipping_fee_input');

    const districtsUrl = "{{ route('locations.districts', ['provinceId' => '__PROVINCE__']) }}";
    const wardsUrl = "{{ route('locations.wards', ['districtId' => '__DISTRICT__']) }}";

    const subtotal = parseInt(totalPriceInput ? totalPriceInput.value : 0) || 0;

    // 1. Tải danh sách Tỉnh/Thành từ GHN
    fetch("{{ route('locations.provinces') }}")
        .then(res => res.json())
        .then(res => {
            if (res.data) {
                let options = '<option value="">-- Chọn Tỉnh/Thành --</option>';
                res.data.forEach(p => {
                    options += `<option value="${p.ProvinceID}">${p.ProvinceName}</option>`;
                });
                provinceSelect.innerHTML = options;
            } else {
                provinceSelect.innerHTML = '<option value="">-- Không tải được tỉnh/thành --</option>';
            }
        })
        .catch(err => {
            console.error("Lỗi load tỉnh thành:", err);
            provinceSelect.innerHTML = '<option value="">-- Lỗi kết nối GHN --</option>';
        });

    // 2. Chọn Tỉnh -> Tải Quận/Huyện
    provinceSelect.addEventListener('change', function () {
        const selectedText = provinceSelect.options[provinceSelect.selectedIndex]?.text || '';
        const provinceNameInput = document.getElementById('province_name');
        if (provinceNameInput) provinceNameInput.value = provinceSelect.value ? selectedText : '';

        districtSelect.innerHTML = '<option value="">-- Đang tải... --</option>';
        districtSelect.disabled = true;
        wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
        wardSelect.disabled = true;
        updateTotals(0);

        if (!this.value) return;

        fetch(districtsUrl.replace('__PROVINCE__', this.value))
            .then(res => res.json())
            .then(res => {
                if (res.data) {
                    let options = '<option value="">-- Chọn Quận/Huyện --</option>';
                    res.data.forEach(d => {
                        options += `<option value="${d.DistrictID}">${d.DistrictName}</option>`;
                    });
                    districtSelect.innerHTML = options;
                    districtSelect.disabled = false;
                } else {
                    districtSelect.innerHTML = '<option value="">-- Không tải được quận/huyện --</option>';
                }
            })
            .catch(err => {
                console.error("Lỗi load quận huyện:", err);
                districtSelect.innerHTML = '<option value="">-- Lỗi kết nối GHN --</option>';
            });
    });

    // 3. Chọn Quận/Huyện -> Tải Phường/Xã
    districtSelect.addEventListener('change', function () {
        const selectedText = districtSelect.options[districtSelect.selectedIndex]?.text || '';
        const districtNameInput = document.getElementById('district_name');
        if (districtNameInput) districtNameInput.value = districtSelect.value ? selectedText : '';

        wardSelect.innerHTML = '<option value="">-- Đang tải... --</option>';
        wardSelect.disabled = true;
        updateTotals(0);

        if (!this.value) return;

        fetch(wardsUrl.replace('__DISTRICT__', this.value))
            .then(res => res.json())
            .then(res => {
                if (res.data) {
                    let options = '<option value="">-- Chọn Phường/Xã --</option>';
                    res.data.forEach(w => {
                        options += `<option value="${w.WardCode}">${w.WardName}</option>`;
                    });
                    wardSelect.innerHTML = options;
                    wardSelect.disabled = false;
                } else {
                    wardSelect.innerHTML = '<option value="">-- Không tải được phường/xã --</option>';
                }
            })
            .catch(err => {
                console.error("Lỗi load phường xã:", err);
                wardSelect.innerHTML = '<option value="">-- Lỗi kết nối GHN --</option>';
            });
    });

    // 4. Chọn Phường/Xã -> Tính cước vận chuyển GHN
    wardSelect.addEventListener('change', function () {
        const selectedText = wardSelect.options[wardSelect.selectedIndex]?.text || '';
        const wardNameInput = document.getElementById('ward_name');
        if (wardNameInput) wardNameInput.value = wardSelect.value ? selectedText : '';

        if (!this.value || !districtSelect.value) return;

        shippingFeeText.innerText = 'Đang tính cước...';

        fetch("{{ route('locations.fee') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                to_district_id: districtSelect.value,
                to_ward_code: this.value
            })
        })
            .then(res => res.json())
            .then(res => {
                if (res.code === 200 && res.data) {
                    const fee = parseInt(res.data.total) || 0;
                    updateTotals(fee);
                } else {
                    shippingFeeText.innerText = 'Chưa hỗ trợ';
                    updateTotals(0);
                }
            })
            .catch(err => {
                console.error("Lỗi tính phí:", err);
                shippingFeeText.innerText = 'Lỗi tính phí';
                updateTotals(0);
            });
    });

    let currentShippingFee = 0;
    let currentDiscount = 0;
    let currentCoinsDiscount = 0;
    const userCoins = {{ auth()->user()->coins ?? 0 }};
    const maxCoinsCash = userCoins * 500;
    const useCoinsSwitch = document.getElementById('use_coins_switch');
    const coinsDiscountRow = document.getElementById('coins_discount_row');
    const coinsDiscountText = document.getElementById('coins_discount_amount_text');

    function updateTotals(fee) {
        if (typeof fee === 'number') {
            currentShippingFee = fee;
        }
        shippingFeeText.innerText = new Intl.NumberFormat('vi-VN').format(currentShippingFee) + ' VNĐ';
        
        const payableBeforeCoins = Math.max(0, subtotal + currentShippingFee - currentDiscount);

        if (useCoinsSwitch && useCoinsSwitch.checked && userCoins > 0) {
            currentCoinsDiscount = Math.min(maxCoinsCash, payableBeforeCoins);
            if (coinsDiscountRow) coinsDiscountRow.style.setProperty('display', 'flex', 'important');
            if (coinsDiscountText) coinsDiscountText.innerText = '-' + new Intl.NumberFormat('vi-VN').format(currentCoinsDiscount) + 'đ';
        } else {
            currentCoinsDiscount = 0;
            if (coinsDiscountRow) coinsDiscountRow.style.setProperty('display', 'none', 'important');
        }

        const finalAmount = Math.max(0, payableBeforeCoins - currentCoinsDiscount);
        finalTotalText.innerText = new Intl.NumberFormat('vi-VN').format(finalAmount) + ' VNĐ';
        if (totalPriceInput) {
            totalPriceInput.value = finalAmount;
        }
        if (shippingFeeInput) {
            shippingFeeInput.value = currentShippingFee;
        }
    }

    if (useCoinsSwitch) {
        useCoinsSwitch.addEventListener('change', function() {
            updateTotals();
        });
    }

    // Xử lý Áp Dụng Mã Giảm Giá
    const couponInput = document.getElementById('coupon_code_input');
    const btnApplyCoupon = document.getElementById('btn-apply-coupon');
    const couponMsg = document.getElementById('coupon-message');
    const discountRow = document.getElementById('discount_row');
    const discountText = document.getElementById('discount_amount_text');
    const appliedCodeBadge = document.getElementById('applied_code_badge');
    const btnRemoveCoupon = document.getElementById('btn-remove-coupon');
    const couponHiddenInput = document.getElementById('coupon_code_hidden');

    function applyCouponCode(codeToApply) {
        const code = (codeToApply || (couponInput ? couponInput.value : '')).trim();
        if (!code) {
            showCouponMsg('Vui lòng nhập mã giảm giá!', 'text-danger');
            return;
        }

        btnApplyCoupon.disabled = true;
        btnApplyCoupon.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

        fetch("{{ route('checkout.applyCoupon') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                code: code,
                subtotal: subtotal
            })
        })
        .then(res => res.json())
        .then(res => {
            btnApplyCoupon.disabled = false;
            btnApplyCoupon.innerText = 'Áp dụng';

            if (res.valid) {
                currentDiscount = res.discount || 0;
                discountText.innerText = res.discount_formatted;
                appliedCodeBadge.innerText = res.code;
                discountRow.style.setProperty('display', 'flex', 'important');
                if (couponHiddenInput) couponHiddenInput.value = res.code;
                if (couponInput) couponInput.value = res.code;

                showCouponMsg(res.message + ' (' + res.title + ')', 'text-success');
                updateTotals();
            } else {
                showCouponMsg(res.message, 'text-danger');
            }
        })
        .catch(err => {
            console.error('Error applying coupon:', err);
            btnApplyCoupon.disabled = false;
            btnApplyCoupon.innerText = 'Áp dụng';
            showCouponMsg('Lỗi kết nối khi áp dụng mã giảm giá!', 'text-danger');
        });
    }

    if (btnApplyCoupon) {
        btnApplyCoupon.addEventListener('click', function() {
            applyCouponCode();
        });
    }

    if (couponInput) {
        couponInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                applyCouponCode();
            }
        });
    }

    // Click nhanh vào các voucher có sẵn trong kho
    document.querySelectorAll('.btn-quick-coupon').forEach(btn => {
        btn.addEventListener('click', function() {
            const code = this.getAttribute('data-code');
            if (code) {
                applyCouponCode(code);
            }
        });
    });

    // Gỡ mã giảm giá
    if (btnRemoveCoupon) {
        btnRemoveCoupon.addEventListener('click', function() {
            currentDiscount = 0;
            discountRow.style.setProperty('display', 'none', 'important');
            if (couponHiddenInput) couponHiddenInput.value = '';
            if (couponInput) couponInput.value = '';
            showCouponMsg('Đã hủy áp dụng mã giảm giá', 'text-muted');
            updateTotals();
        });
    }

    function showCouponMsg(msg, colorClass) {
        if (!couponMsg) return;
        couponMsg.style.display = 'block';
        couponMsg.className = 'small mt-1 ' + colorClass;
        couponMsg.innerHTML = msg;
    }

    // Chống double click / submit nhiều lần gây redirect về giỏ hàng trống
    const orderForm = document.getElementById('checkout-order-form');
    const submitBtn = document.getElementById('btn-submit-order');
    if (orderForm && submitBtn) {
        orderForm.addEventListener('submit', function (e) {
            if (!orderForm.checkValidity()) {
                return;
            }
            if (orderForm.dataset.submitting === 'true') {
                e.preventDefault();
                return false;
            }
            orderForm.dataset.submitting = 'true';
            setTimeout(() => {
                submitBtn.disabled = true;
            }, 50);
            submitBtn.style.pointerEvents = 'none';
            submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i> Đang xử lý tạo đơn & kết nối GHN...';
        });
    }
});
</script>
@endpush
