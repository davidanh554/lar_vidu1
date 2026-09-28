@extends('layouts.store')

@section('title', 'Chi Tiết Đơn Hàng #' . $order->id . ' - VUA TABLET')

@section('content')
<div class="container my-5">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('orders.index') }}" class="btn btn-modern-outline btn-sm rounded-pill">
                    <i class="fa-solid fa-arrow-left me-1"></i> Danh sách đơn hàng
                </a>
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-1 rounded-pill fw-bold font-monospace">
                    Mã đơn #{{ $order->id }}
                </span>
            </div>
            <h2 class="fw-bold mb-0 text-dark">Chi tiết đơn hàng</h2>
        </div>

        @php
            $canCancel = !in_array($order->status, ['cancelled']) 
                         && $order->shipping_status !== 'cancelled' 
                         && in_array($order->shipping_status, ['pending', 'ready_to_pick', 'picking', 'not_shipped']);
        @endphp
        @if($canCancel)
            <form action="{{ route('orders.cancel', $order->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn yêu cầu hủy đơn hàng này?')">
                @csrf
                <button type="submit" class="btn btn-outline-danger rounded-pill px-3 fw-bold">
                    <i class="fa-solid fa-ban me-1"></i> Hủy đơn hàng này
                </button>
            </form>
        @endif
    </div>

    <div class="row g-4">
        <!-- Danh sách sản phẩm -->
        <div class="col-lg-7">
            <div class="card card-modern p-4 mb-4">
                <h5 class="fw-bold mb-3 d-flex align-items-center">
                    <i class="fa-solid fa-box-archive me-2 text-primary"></i>Danh sách sản phẩm trong đơn
                </h5>
                <div class="list-group list-group-flush">
                    @foreach($order->items as $item)
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-3 bg-transparent">
                            <div class="d-flex align-items-center">
                                @if(!empty($item->product->image))
                                    <div class="rounded-3 border p-1 bg-white me-3 d-flex align-items-center justify-content-center" style="width: 54px; height: 54px; flex-shrink: 0;">
                                        <img src="{{ asset($item->product->image) }}" alt="{{ $item->product->name }}" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                    </div>
                                @else
                                    <div class="bg-light rounded-3 me-3 d-flex align-items-center justify-content-center text-muted" style="width: 54px; height: 54px; flex-shrink: 0;">
                                        <i class="fa-solid fa-tablet-screen-button fs-4"></i>
                                    </div>
                                @endif
                                <div>
                                    <div class="fw-bold text-dark">{{ $item->product->name ?? 'Sản phẩm' }}</div>
                                    <small class="text-muted">Đơn giá: {{ number_format($item->price, 0, ',', '.') }}đ × <strong>{{ $item->quantity }}</strong></small>
                                </div>
                            </div>
                            <div class="fw-bold text-dark fs-6">{{ number_format($item->price * $item->quantity, 0, ',', '.') }}đ</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Thông tin vận chuyển GHN & Thanh toán -->
        <div class="col-lg-5">
            <div class="summary-card">
                <h5 class="fw-bold mb-3 d-flex align-items-center">
                    <i class="fa-solid fa-truck-fast me-2 text-primary"></i>Thông tin vận chuyển GHN
                </h5>

                <div class="p-3 bg-light rounded-3 mb-3 small">
                    <div class="mb-1"><strong>Người nhận:</strong> {{ $order->name }}</div>
                    <div class="mb-1"><strong>Điện thoại:</strong> {{ $order->phone }}</div>
                    <div class="mb-1"><strong>Địa chỉ:</strong> {{ $order->address }}</div>
                    @if($order->note)
                        <div class="mt-2 text-muted"><strong>Ghi chú:</strong> <em>{{ $order->note }}</em></div>
                    @endif
                </div>

                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small">Mã vận đơn GHN:</span>
                    @if($order->ghn_order_code)
                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 font-monospace px-3 py-1 rounded-pill">
                            <i class="fa-solid fa-barcode me-1"></i>{{ $order->ghn_order_code }}
                        </span>
                    @else
                        <span class="badge-soft badge-soft-secondary">Chưa tạo đơn GHN</span>
                    @endif
                </div>

                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small">Tiến độ giao hàng:</span>
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
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted small">Thanh toán:</span>
                    @if($order->status === 'paid')
                        <span class="badge-soft badge-soft-success"><i class="fa-solid fa-circle-check"></i> Đã thanh toán</span>
                    @elseif($order->status === 'cod_ordered')
                        <span class="badge-soft badge-soft-info"><i class="fa-solid fa-truck"></i> Chờ thu COD</span>
                    @elseif($order->status === 'cancelled' || $order->shipping_status === 'cancelled')
                        <span class="badge-soft badge-soft-secondary"><i class="fa-solid fa-ban"></i> Đã hủy</span>
                    @else
                        <span class="badge-soft badge-soft-warning"><i class="fa-solid fa-hourglass-half"></i> Chờ thanh toán</span>
                    @endif
                </div>

                <hr class="my-3 border-secondary border-opacity-25">

                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Cước vận chuyển GHN:</span>
                    <span class="fw-semibold text-secondary">{{ number_format($order->ghn_total_fee, 0, ',', '.') }}đ</span>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="fw-bold fs-6">Tổng tiền:</span>
                    <span class="fw-bold fs-4 text-primary">{{ number_format($order->total_price, 0, ',', '.') }}đ</span>
                </div>

                @php
                    $isPaid = $order->status === 'paid';
                    $isCancelled = $order->status === 'cancelled' || $order->shipping_status === 'cancelled';
                    $hasMomo = $order->paymentTransactions && $order->paymentTransactions->where('gateway', 'momo')->isNotEmpty();
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


                @if($order->shipping_status === 'delivered')
                    <div class="mt-3">
                        <button type="button" class="btn btn-warning text-dark w-100 rounded-pill py-3 fw-bold shadow-sm btn-review-order" data-order-id="{{ $order->id }}" data-items='@json($orderItemsData)'>
                            <i class="fa-solid fa-star text-danger me-2"></i> Đánh giá sản phẩm
                        </button>
                    </div>
                @endif

                @if(!$isCancelled && !$isPaid && $hasMomo)
                    <div class="mt-3">
                        <a href="{{ route('orders.momo.pay', $order->id) }}" class="btn btn-modern-primary w-100 rounded-pill py-3 fw-bold">
                            <i class="fa-solid fa-credit-card me-2"></i> Thanh toán lại qua MoMo
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
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
                <!-- Nội dung render qua JS -->
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
