@extends('layouts.store')

@section('title', 'Lịch Sử Đơn Hàng - VUA TABLET')

@section('content')
<div class="container my-5">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="fw-bold mb-1 d-flex align-items-center">
                <i class="fa-solid fa-clock-rotate-left me-3 text-primary"></i>Lịch sử đơn hàng của bạn
            </h2>
            <p class="text-muted mb-0 small">Theo dõi tiến độ vận chuyển GHN và chi tiết đơn hàng đã đặt</p>
        </div>
        <a href="{{ route('home') }}" class="btn btn-modern-outline btn-sm rounded-pill">
            <i class="fa-solid fa-store me-1"></i> Mua thêm sản phẩm
        </a>
    </div>

    @if($orders->isEmpty())
        <div class="card card-modern text-center py-5 px-4 my-3">
            <div class="mb-3">
                <div class="d-inline-flex align-items-center justify-content-center bg-light rounded-circle" style="width: 90px; height: 90px;">
                    <i class="fa-solid fa-box-open fs-2 text-muted"></i>
                </div>
            </div>
            <h4 class="fw-bold text-dark">Bạn chưa có đơn hàng nào</h4>
            <p class="text-muted mb-4">Khám phá các sản phẩm công nghệ hot nhất với giá ưu đãi ngay hôm nay.</p>
            <div>
                <a href="{{ route('home') }}" class="btn btn-modern-primary rounded-pill px-4 py-2">
                    <i class="fa-solid fa-cart-shopping me-2"></i> Mua sắm ngay
                </a>
            </div>
        </div>
    @else
        <div class="card card-modern">
            <div class="table-responsive">
                <table class="table table-modern align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Mã đơn</th>
                            <th>Người nhận</th>
                            <th>Mã vận đơn GHN</th>
                            <th>Cước ship GHN</th>
                            <th>Tổng tiền</th>
                            <th>Thanh toán</th>
                            <th>Tiến độ giao hàng</th>
                            <th class="text-end pe-4">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            @php
                                $isCancelled = in_array($order->status, ['cancelled']) || $order->shipping_status === 'cancelled';
                                $isPaid = $order->status === 'paid';
                                $hasMomo = $order->paymentTransactions && $order->paymentTransactions->where('gateway', 'momo')->isNotEmpty();
                                $isCod = $order->status === 'cod_ordered' 
                                         || ($order->paymentTransactions && $order->paymentTransactions->where('gateway', 'cod')->isNotEmpty())
                                         || (!$hasMomo && !$isPaid);
                            @endphp
                            <tr>
                                <td class="ps-4">
                                    <span class="fw-bold text-primary font-monospace">#{{ $order->id }}</span>
                                    <div class="small text-muted">{{ $order->created_at ? $order->created_at->format('d/m/Y H:i') : '' }}</div>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $order->name }}</div>
                                    <small class="text-muted"><i class="fa-solid fa-phone me-1"></i>{{ $order->phone }}</small>
                                </td>
                                <td>
                                    @if($order->ghn_order_code)
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 font-monospace px-2 py-1 rounded-pill">
                                            <i class="fa-solid fa-barcode me-1"></i>{{ $order->ghn_order_code }}
                                        </span>
                                    @else
                                        <span class="badge-soft badge-soft-secondary">Chưa tạo</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-secondary small fw-medium">{{ number_format($order->ghn_total_fee, 0, ',', '.') }}đ</span>
                                </td>
                                <td>
                                    <span class="fw-bold text-dark fs-6">{{ number_format($order->total_price, 0, ',', '.') }}đ</span>
                                    @if(($order->discount_amount ?? 0) > 0)
                                        <div class="small text-success fw-semibold" style="font-size: 0.72rem;">
                                            <i class="fa-solid fa-tag me-1"></i>Voucher: -{{ number_format($order->discount_amount) }}đ
                                        </div>
                                    @endif
                                    @if(($order->coins_discount ?? 0) > 0)
                                        <div class="small text-warning-emphasis fw-semibold" style="font-size: 0.72rem;">
                                            <i class="fa-solid fa-coins text-warning me-1"></i>Đã trừ Xu: -{{ number_format($order->coins_discount) }}đ ({{ $order->coins_used }} Xu)
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @if($isCancelled)
                                        <span class="badge-soft badge-soft-secondary"><i class="fa-solid fa-ban"></i> Đã hủy</span>
                                    @elseif($isPaid)
                                        <span class="badge-soft badge-soft-success"><i class="fa-solid fa-circle-check"></i> Đã thanh toán</span>
                                    @elseif($isCod)
                                        <span class="badge-soft badge-soft-info"><i class="fa-solid fa-truck"></i> Tiền mặt (COD)</span>
                                    @else
                                        <span class="badge-soft badge-soft-warning"><i class="fa-solid fa-hourglass-half"></i> Chờ MoMo</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $shippingClass = match($order->shipping_status) {
                                            'delivered'     => 'badge-shipping-delivered',
                                            'delivering'    => 'badge-shipping-delivering',
                                            'picking', 'ready_to_pick' => 'badge-shipping-ready_to_pick',
                                            'storing'       => 'badge-shipping-storing',
                                            'cancelled'     => 'badge-shipping-cancelled',
                                            'return'        => 'badge-shipping-return',
                                            default         => 'badge-shipping-pending',
                                        };
                                        $shippingIcon = match($order->shipping_status) {
                                            'delivered'     => 'fa-solid fa-circle-check',
                                            'delivering'    => 'fa-solid fa-truck-fast',
                                            'picking', 'ready_to_pick' => 'fa-solid fa-box-open',
                                            'storing'       => 'fa-solid fa-warehouse',
                                            'cancelled'     => 'fa-solid fa-circle-xmark',
                                            'return'        => 'fa-solid fa-rotate-left',
                                            default         => 'fa-solid fa-clock',
                                        };
                                    @endphp
                                    <span class="badge badge-shipping {{ $shippingClass }}">
                                        <i class="{{ $shippingIcon }}"></i> {{ $order->shipping_status_text }}
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-inline-flex gap-2 align-items-center flex-wrap justify-content-end">
                                        <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-modern-outline rounded-pill px-3">
                                            <i class="fa-regular fa-eye me-1"></i> Chi tiết
                                        </a>


                                        @if($order->shipping_status === 'delivered')
                                            @php
                                                $orderItemsData = $order->items->map(function($it) use ($order) {
                                                    $rev = $order->reviews ? $order->reviews->where('product_id', $it->product_id)->first() : null;
                                                    return [
                                                        'product_id' => $it->product_id,
                                                        'product_name' => $it->product->name ?? 'Sản phẩm',
                                                        'product_image' => !empty($it->product->image) ? asset($it->product->image) : asset('images/default.png'),
                                                        'color' => $it->color ?? 'Mặc định',
                                                        'reviewed' => $rev ? true : false,
                                                        'rating' => $rev ? $rev->rating : 5,
                                                        'comment' => $rev ? $rev->comment : '',
                                                        'match_description' => $rev ? $rev->match_description : 'đúng',
                                                        'quality_rating' => $rev ? $rev->quality_rating : 'Tốt',
                                                    ];
                                                });
                                            @endphp
                                            <button type="button" class="btn btn-sm btn-warning text-dark rounded-pill px-3 fw-bold shadow-sm btn-review-order" data-order-id="{{ $order->id }}" data-items='@json($orderItemsData)'>
                                                <i class="fa-solid fa-star text-danger me-1"></i> Đánh giá
                                            </button>
                                        @endif

                                        @if(!$isCancelled && !$isPaid && $hasMomo)
                                            <a href="{{ route('orders.momo.pay', $order->id) }}" class="btn btn-sm btn-danger rounded-pill px-3 fw-bold">
                                                <i class="fa-solid fa-credit-card me-1"></i> Trả lại
                                            </a>
                                        @endif

                                        @php
                                            $canCancel = !$isCancelled && in_array($order->shipping_status, ['pending', 'ready_to_pick', 'picking', 'not_shipped']);
                                        @endphp
                                        @if($canCancel)
                                            <form action="{{ route('orders.cancel', $order->id) }}" method="POST" class="d-inline mb-0" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng #{{ $order->id }}?')">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3" title="Hủy đơn hàng">
                                                    <i class="fa-solid fa-ban me-1"></i> Hủy
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4 d-flex justify-content-center">
            {{ $orders->links() }}
        </div>
    @endif
</div>

<!-- Modal Đánh Giá Sản Phẩm (Shopee Style) -->
<div class="modal fade" id="orderReviewModal" tabindex="-1" aria-labelledby="orderReviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
            <div class="modal-header border-bottom px-4 py-3 bg-light">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center" id="orderReviewModalLabel">
                    <i class="fa-solid fa-star text-warning me-2 fs-5"></i>Đánh giá sản phẩm đã mua
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="reviewModalBody">
                <!-- Nội dung danh sách sản phẩm cần đánh giá được render qua Javascript -->
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const reviewModalEl = document.getElementById('orderReviewModal');
    if (!reviewModalEl) return;
    const reviewModal = new bootstrap.Modal(reviewModalEl);
    const reviewModalBody = document.getElementById('reviewModalBody');

    const starLabels = {
        5: 'Tuyệt vời',
        4: 'Hài lòng',
        3: 'Bình thường',
        2: 'Không hài lòng',
        1: 'Rất tệ'
    };

    document.querySelectorAll('.btn-review-order').forEach(btn => {
        btn.addEventListener('click', function() {
            const orderId = this.dataset.orderId;
            const items = JSON.parse(this.dataset.items || '[]');

            if (!items.length) {
                alert('Không tìm thấy sản phẩm nào trong đơn hàng để đánh giá.');
                return;
            }

            let html = `<div class="d-flex flex-column gap-4">`;
            items.forEach((item, index) => {
                html += `
                    <div class="card border rounded-3 p-3 bg-white review-item-block" data-product-id="${item.product_id}" data-order-id="${orderId}">
                        <div class="d-flex align-items-center gap-3 mb-3 border-bottom pb-3">
                            <img src="${item.product_image}" alt="${item.product_name}" class="rounded border p-1" style="width: 56px; height: 56px; object-fit: contain; background: #fff;">
                            <div>
                                <h6 class="fw-bold mb-1 text-dark">${item.product_name}</h6>
                                <span class="text-muted small">Phân loại hàng: <strong>${item.color}</strong></span>
                                ${item.reviewed ? '<span class="badge bg-success-subtle text-success border border-success-subtle ms-2 rounded-pill px-2">Đã đánh giá</span>' : ''}
                            </div>
                        </div>

                        <!-- Chọn sao -->
                        <div class="mb-3 d-flex align-items-center gap-3">
                            <span class="text-dark small fw-bold">Chất lượng sản phẩm:</span>
                            <div class="star-rating-picker" data-rating="${item.rating}">
                                ${[1, 2, 3, 4, 5].map(s => `
                                    <i class="fa-solid fa-star star-item ${s <= item.rating ? 'active' : ''}" data-value="${s}"></i>
                                `).join('')}
                            </div>
                            <span class="star-feedback-label fw-bold text-danger small">${starLabels[item.rating] || 'Tuyệt vời'}</span>
                        </div>

                        <!-- Đúng với mô tả -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary mb-1">Đúng với mô tả:</label>
                            <div class="d-flex gap-2 flex-wrap desc-match-pills">
                                ${['đúng', 'rất đúng', 'đúng một phần'].map(opt => `
                                    <button type="button" class="btn btn-sm ${item.match_description === opt ? 'btn-danger' : 'btn-outline-secondary'} rounded-pill px-3 btn-pill-desc" data-val="${opt}">
                                        ${opt}
                                    </button>
                                `).join('')}
                            </div>
                        </div>

                        <!-- Chất lượng -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary mb-1">Chất lượng sản phẩm:</label>
                            <div class="d-flex gap-2 flex-wrap quality-pills">
                                ${['Tốt', 'Tuyệt vời', 'Bình thường'].map(opt => `
                                    <button type="button" class="btn btn-sm ${item.quality_rating === opt ? 'btn-danger' : 'btn-outline-secondary'} rounded-pill px-3 btn-pill-quality" data-val="${opt}">
                                        ${opt}
                                    </button>
                                `).join('')}
                            </div>
                        </div>

                        <!-- Nội dung bình luận -->
                        <div class="mb-3">
                            <textarea class="form-control rounded-3 review-comment-input" rows="3" placeholder="Hãy chia sẻ cảm nhận về sản phẩm...">${item.comment || ''}</textarea>
                        </div>

                        <div class="text-end">
                            <button type="button" class="btn btn-danger btn-submit-single-review rounded-pill px-4 fw-bold">
                                <i class="fa-solid fa-paper-plane me-1"></i> ${item.reviewed ? 'Cập nhật đánh giá' : 'Gửi đánh giá'}
                            </button>
                        </div>
                    </div>
                `;
            });
            html += `</div>`;

            reviewModalBody.innerHTML = html;
            attachModalEvents();
            reviewModal.show();
        });
    });

    function attachModalEvents() {
        // Sao tương tác
        document.querySelectorAll('.star-rating-picker').forEach(picker => {
            const stars = picker.querySelectorAll('.star-item');
            const label = picker.parentElement.querySelector('.star-feedback-label');

            stars.forEach(star => {
                star.addEventListener('click', function() {
                    const val = parseInt(this.dataset.value);
                    picker.dataset.rating = val;
                    stars.forEach(s => {
                        const sVal = parseInt(s.dataset.value);
                        if (sVal <= val) {
                            s.classList.add('active');
                        } else {
                            s.classList.remove('active');
                        }
                    });
                    if (label) label.textContent = starLabels[val] || '';
                });
            });
        });

        // Pill chọn đúng với mô tả
        document.querySelectorAll('.desc-match-pills').forEach(group => {
            group.querySelectorAll('.btn-pill-desc').forEach(btn => {
                btn.addEventListener('click', function() {
                    group.querySelectorAll('.btn-pill-desc').forEach(b => {
                        b.classList.remove('btn-danger');
                        b.classList.add('btn-outline-secondary');
                    });
                    this.classList.remove('btn-outline-secondary');
                    this.classList.add('btn-danger');
                });
            });
        });

        // Pill chọn chất lượng
        document.querySelectorAll('.quality-pills').forEach(group => {
            group.querySelectorAll('.btn-pill-quality').forEach(btn => {
                btn.addEventListener('click', function() {
                    group.querySelectorAll('.btn-pill-quality').forEach(b => {
                        b.classList.remove('btn-danger');
                        b.classList.add('btn-outline-secondary');
                    });
                    this.classList.remove('btn-outline-secondary');
                    this.classList.add('btn-danger');
                });
            });
        });

        // Gửi đánh giá
        document.querySelectorAll('.btn-submit-single-review').forEach(btn => {
            btn.addEventListener('click', async function() {
                const card = this.closest('.review-item-block');
                const productId = card.dataset.productId;
                const orderId = card.dataset.orderId;
                const rating = card.querySelector('.star-rating-picker').dataset.rating || 5;
                const activeDescBtn = card.querySelector('.desc-match-pills .btn-danger');
                const matchDesc = activeDescBtn ? activeDescBtn.dataset.val : 'đúng';
                const activeQualityBtn = card.querySelector('.quality-pills .btn-danger');
                const qualityRating = activeQualityBtn ? activeQualityBtn.dataset.val : 'Tốt';
                const comment = card.querySelector('.review-comment-input').value.trim();

                this.disabled = true;
                this.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Đang lưu...';

                try {
                    const res = await fetch('{{ route("reviews.store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            product_id: productId,
                            order_id: orderId,
                            rating: rating,
                            match_description: matchDesc,
                            quality_rating: qualityRating,
                            comment: comment
                        })
                    });

                    const data = await res.json();
                    if (data.success) {
                        alert('Đánh giá thành công! Cảm ơn bạn đã phản hồi.');
                        window.location.reload();
                    } else {
                        alert(data.message || 'Có lỗi xảy ra, vui lòng thử lại.');
                        this.disabled = false;
                        this.innerHTML = '<i class="fa-solid fa-paper-plane me-1"></i> Thử lại';
                    }
                } catch (err) {
                    console.error(err);
                    alert('Lỗi kết nối máy chủ.');
                    this.disabled = false;
                    this.innerHTML = '<i class="fa-solid fa-paper-plane me-1"></i> Thử lại';
                }
            });
        });
    }
});
</script>
@endpush
@endsection
