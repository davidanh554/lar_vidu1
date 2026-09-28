@extends('layouts.store')

@section('title', 'Giỏ Hàng - VUA TABLET')

@section('content')
<div class="container my-5">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="fw-bold mb-1 d-flex align-items-center">
                <i class="fa-solid fa-cart-shopping me-3 text-primary"></i>Giỏ hàng của bạn
            </h2>
            <p class="text-muted mb-0 small">Kiểm tra danh sách máy tính bảng & phụ kiện đã chọn trước khi đặt hàng</p>
        </div>
        <a href="{{ route('home') }}" class="btn btn-modern-outline btn-sm rounded-pill">
            <i class="fa-solid fa-arrow-left me-1"></i> Tiếp tục mua sắm
        </a>
    </div>

    @if(!empty($cart) && count($cart) > 0)
        <!-- Form gửi các món được chọn sang Thanh Toán -->
        <form action="{{ route('checkout.index') }}" method="GET" id="checkout-form">
            <div class="row g-4">
                <!-- Danh sách sản phẩm -->
                <div class="col-lg-8">
                    <div class="card card-modern">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-modern align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th class="ps-4" style="width: 48px;">
                                                <input type="checkbox" id="select-all" class="form-check-input" checked title="Chọn tất cả">
                                            </th>
                                            <th>Sản phẩm & Màu sắc</th>
                                            <th style="min-width: 120px;">Đơn giá</th>
                                            <th style="min-width: 130px;">Số lượng</th>
                                            <th style="min-width: 130px;">Thành tiền</th>
                                            <th class="text-end pe-4" style="width: 60px;">Xóa</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $total = 0; @endphp
                                        @foreach($cart as $id => $details)
                                            @php 
                                                $subtotal = $details['price'] * $details['quantity'];
                                                $total += $subtotal;
                                                $product = $products[$details['id']] ?? null;
                                                $variants = $product ? $product->color_variants : [];
                                            @endphp
                                            <tr>
                                                <!-- Checkbox Chọn Từng Sản Phẩm -->
                                                <td class="ps-4">
                                                    <input type="checkbox" 
                                                           name="selected_items[]" 
                                                           value="{{ $id }}" 
                                                           id="checkbox-{{ $id }}"
                                                           class="form-check-input item-checkbox" 
                                                           data-subtotal="{{ $subtotal }}" 
                                                           checked>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        @if(!empty($details['image']))
                                                            <div class="rounded-3 border p-1 bg-white me-3 d-flex align-items-center justify-content-center" style="width: 64px; height: 64px; flex-shrink: 0;">
                                                                <img src="{{ asset($details['image']) }}" alt="{{ $details['name'] }}" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                                            </div>
                                                        @else
                                                            <div class="bg-light d-flex align-items-center justify-content-center rounded-3 me-3 text-muted" style="width: 64px; height: 64px; flex-shrink: 0;">
                                                                <i class="fa-solid fa-tablet-screen-button fs-4"></i>
                                                            </div>
                                                        @endif
                                                        <div>
                                                            <a href="{{ route('products.show', $details['id']) }}" class="fw-bold text-dark text-decoration-none hover-primary d-block">
                                                                {{ $details['name'] }}
                                                            </a>
                                                            
                                                            <!-- Tùy chọn đổi màu sắc trực tiếp trong giỏ hàng -->
                                                            @if(!empty($variants) && count($variants) > 1)
                                                                <div class="mt-1 d-flex align-items-center gap-1">
                                                                    <small class="text-muted"><i class="fa-solid fa-palette text-primary me-1"></i>Màu:</small>
                                                                    <select class="form-select form-select-sm py-1 ps-2 pe-4 rounded-pill bg-light border color-change-select" 
                                                                            style="width: auto; min-width: 155px; font-size: 0.8rem; font-weight: 500; cursor: pointer;" 
                                                                            data-id="{{ $id }}"
                                                                            title="Bấm để đổi màu sắc trực tiếp">
                                                                        @foreach($variants as $v)
                                                                            <option value="{{ $v['name'] }}" 
                                                                                    {{ strcasecmp($details['color'] ?? '', $v['name']) === 0 ? 'selected' : '' }}
                                                                                    {{ $v['quantity'] <= 0 ? 'disabled' : '' }}>
                                                                                {{ $v['name'] }} ({{ $v['quantity'] > 0 ? 'Còn ' . $v['quantity'] : 'Hết hàng' }})
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            @elseif(!empty($details['color']))
                                                                <small class="text-muted">Màu: <span class="badge bg-light text-dark border">{{ $details['color'] }}</span></small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="fw-semibold text-secondary">{{ number_format($details['price'], 0, ',', '.') }}đ</span>
                                                </td>
                                                <td>
                                                    @php
                                                        $itemStock = $product ? $product->getStockForColor($details['color'] ?? null) : 999;
                                                    @endphp
                                                    <div class="d-flex align-items-center gap-1">
                                                        <button class="stepper-btn btn-qty-minus" type="button" data-id="{{ $id }}"><i class="fa-solid fa-minus fs-6"></i></button>
                                                        <input type="number" 
                                                               name="quantity" 
                                                               value="{{ $details['quantity'] }}" 
                                                               min="1" 
                                                               max="{{ $itemStock }}"
                                                               class="stepper-input quantity-input" 
                                                               data-id="{{ $id }}"
                                                               data-max="{{ $itemStock }}"
                                                               data-price="{{ $details['price'] }}">
                                                        <button class="stepper-btn btn-qty-plus" type="button" data-id="{{ $id }}"><i class="fa-solid fa-plus fs-6"></i></button>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="fw-bold text-primary fs-6" id="subtotal-{{ $id }}">
                                                        {{ number_format($subtotal, 0, ',', '.') }}đ
                                                    </span>
                                                </td>
                                                <td class="text-end pe-4">
                                                    <a href="{{ route('cart.remove', $id) }}" class="btn btn-sm text-danger hover-bg-danger-subtle rounded-circle p-2" title="Xóa món này" onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?')">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tóm tắt đơn hàng (Sticky Summary) -->
                <div class="col-lg-4">
                    <div class="summary-card">
                        <h5 class="fw-bold mb-3 d-flex align-items-center">
                            <i class="fa-solid fa-receipt me-2 text-primary"></i>Tóm tắt đơn hàng
                        </h5>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Tổng tiền hàng:</span>
                            <span class="fw-bold text-dark" id="total-price">{{ number_format($total, 0, ',', '.') }}đ</span>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Vận chuyển:</span>
                            <span class="badge-soft badge-soft-success">
                                <i class="fa-solid fa-truck-fast"></i> Miễn phí
                            </span>
                        </div>

                        <div class="p-3 bg-light rounded-3 mb-3 small text-muted">
                            <i class="fa-solid fa-shield-check text-success me-1"></i> Bảo hành chính hãng 12 tháng. Đổi mới trong 30 ngày nếu lỗi NSX.
                        </div>

                        <hr class="my-3 border-secondary border-opacity-25">

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <span class="fw-bold fs-6">Tổng thanh toán:</span>
                            <span class="fw-bold fs-4 text-primary" id="final-price">{{ number_format($total, 0, ',', '.') }}đ</span>
                        </div>

                        <button type="submit" id="btn-checkout" class="btn btn-modern-primary w-100 rounded-pill py-3 fw-bold fs-6 shadow">
                            <span>Tiến hành thanh toán</span>
                            <i class="fa-solid fa-arrow-right ms-2"></i>
                        </button>
                        
                        <div class="text-center mt-3 small text-muted">
                            <i class="fa-solid fa-lock me-1"></i> Giao dịch bảo mật 100% với MoMo & GHN
                        </div>
                    </div>
                </div>
            </div>
        </form>
    @else
        <!-- Giỏ hàng trống -->
        <div class="card card-modern text-center py-5 px-4 my-3">
            <div class="mb-3">
                <div class="d-inline-flex align-items-center justify-content-center bg-light rounded-circle" style="width: 100px; height: 100px;">
                    <i class="fa-solid fa-cart-shopping fs-1 text-muted"></i>
                </div>
            </div>
            <h4 class="fw-bold text-dark">Giỏ hàng của bạn đang trống</h4>
            <p class="text-muted mb-4">Khám phá ngay các dòng máy tính bảng và phụ kiện công nghệ chất lượng cao.</p>
            <div>
                <a href="{{ route('home') }}" class="btn btn-modern-primary rounded-pill px-4 py-2">
                    <i class="fa-solid fa-bag-shopping me-2"></i> Khám phá sản phẩm ngay
                </a>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('select-all');
    const totalPriceEl = document.getElementById('total-price');
    const finalPriceEl = document.getElementById('final-price');
    const btnCheckout = document.getElementById('btn-checkout');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    function calculateTotal() {
        let currentTotal = 0;
        let checkedCount = 0;

        const currentCheckboxes = document.querySelectorAll('.item-checkbox');
        currentCheckboxes.forEach(checkbox => {
            if (checkbox.checked) {
                currentTotal += parseFloat(checkbox.getAttribute('data-subtotal')) || 0;
                checkedCount++;
            }
        });

        // Format tiền VND
        const formattedPrice = new Intl.NumberFormat('vi-VN').format(currentTotal) + 'đ';
        if (totalPriceEl) totalPriceEl.textContent = formattedPrice;
        if (finalPriceEl) finalPriceEl.textContent = formattedPrice;

        if (selectAll) {
            selectAll.checked = (checkedCount === currentCheckboxes.length && currentCheckboxes.length > 0);
        }

        if (btnCheckout) {
            btnCheckout.disabled = (checkedCount === 0);
        }
    }

    // Gửi AJAX cập nhật số lượng
    function updateQuantityAjax(id, newQty) {
        if (newQty < 1) newQty = 1;

        fetch("{{ route('cart.update') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                id: id,
                quantity: newQty
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // 1. Luôn đồng bộ lại ô input với số lượng thực tế từ Server
                const input = document.querySelector(`.quantity-input[data-id="${id}"]`);
                if (input && data.quantity !== undefined) {
                    input.value = data.quantity;
                }

                // 2. Cập nhật data-subtotal cho checkbox để tính tiền chính xác
                const checkbox = document.getElementById('checkbox-' + id);
                if (checkbox) {
                    checkbox.setAttribute('data-subtotal', data.subtotal);
                }

                // 3. Cập nhật hiển thị thành tiền
                const subtotalEl = document.getElementById('subtotal-' + id);
                if (subtotalEl) {
                    subtotalEl.textContent = data.formatted_subtotal;
                }

                // 4. Tính lại tổng tiền cả giỏ
                calculateTotal();

                // 5. Cảnh báo nếu số lượng vượt quá tồn kho trong kho hàng
                if (data.is_capped) {
                    alert(data.message || `Kho chỉ còn tối đa ${data.quantity} sản phẩm!`);
                }
            } else {
                alert(data.message || 'Không thể cập nhật số lượng!');
                if (data.quantity !== undefined) {
                    const input = document.querySelector(`.quantity-input[data-id="${id}"]`);
                    if (input) input.value = data.quantity;
                }
            }
        })
        .catch(error => {
            console.error('Lỗi khi cập nhật giỏ hàng:', error);
        });
    }

    // Đổi màu sắc
    document.querySelectorAll('.color-change-select').forEach(select => {
        let previousColor = select.value;

        select.addEventListener('focus', function() {
            previousColor = this.value;
        });

        select.addEventListener('change', function () {
            const oldId = this.getAttribute('data-id');
            const newColor = this.value;
            const selectEl = this;

            selectEl.disabled = true;

            fetch("{{ route('cart.changeColor') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    id: oldId,
                    new_color: newColor
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.message || 'Không thể đổi sang màu này!');
                    selectEl.value = previousColor;
                    selectEl.disabled = false;
                }
            })
            .catch(err => {
                console.error('Lỗi khi đổi màu:', err);
                alert('Đã xảy ra lỗi khi đổi màu sắc.');
                selectEl.value = previousColor;
                selectEl.disabled = false;
            });
        });
    });

    // Input số lượng trực tiếp
    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('change', function () {
            const id = this.getAttribute('data-id');
            let max = parseInt(this.getAttribute('max')) || 9999;
            let qty = parseInt(this.value) || 1;
            if (qty < 1) {
                qty = 1;
                this.value = 1;
            } else if (qty > max) {
                alert(`Kho chỉ còn tối đa ${max} sản phẩm!`);
                qty = max;
                this.value = max;
            }
            updateQuantityAjax(id, qty);
        });
    });

    // Nút (-)
    document.querySelectorAll('.btn-qty-minus').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const input = document.querySelector(`.quantity-input[data-id="${id}"]`);
            if (input) {
                let qty = parseInt(input.value) || 1;
                if (qty > 1) {
                    qty--;
                    input.value = qty;
                    updateQuantityAjax(id, qty);
                }
            }
        });
    });

    // Nút (+)
    document.querySelectorAll('.btn-qty-plus').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const input = document.querySelector(`.quantity-input[data-id="${id}"]`);
            if (input) {
                let max = parseInt(input.getAttribute('max')) || 9999;
                let qty = parseInt(input.value) || 1;
                if (qty >= max) {
                    alert(`Số lượng đã đạt giới hạn tồn kho tối đa (${max} sản phẩm)!`);
                    return;
                }
                qty++;
                input.value = qty;
                updateQuantityAjax(id, qty);
            }
        });
    });

    if (selectAll) {
        selectAll.addEventListener('change', function () {
            document.querySelectorAll('.item-checkbox').forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            calculateTotal();
        });
    }

    document.querySelectorAll('.item-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', calculateTotal);
    });

    calculateTotal();
});
</script>
@endpush