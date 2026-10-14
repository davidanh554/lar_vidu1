@extends('layouts.store')

@section('title', 'Thanh Toán Chuyển Khoản VietQR - Đơn Hàng #' . $order->id . ' - VUA TABLET')

@section('content')
<div class="container my-5">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="fw-bold mb-1 d-flex align-items-center">
                <i class="fa-solid fa-qrcode me-3 text-primary"></i>Thanh toán qua Chuyển khoản VietQR
            </h2>
            <p class="text-muted mb-0 small">Hỗ trợ quét mã bằng mọi ứng dụng Ngân hàng (MB, VCB, ACB, Techcombank...) hoặc Ví điện tử</p>
        </div>
        <a href="{{ route('orders.index') }}" class="btn btn-modern-outline btn-sm rounded-pill">
            <i class="fa-solid fa-arrow-left me-1"></i> Danh sách đơn hàng
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm mb-4" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm mb-4" role="alert">
            <i class="fa-solid fa-circle-exclamation me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4 justify-content-center">
        <!-- Cột Bên Trái: Mã QR Thanh Toán -->
        <div class="col-lg-5">
            <div class="card card-modern p-4 text-center border-0 shadow-sm rounded-4 h-100 position-relative overflow-hidden" style="background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);">
                <!-- Dải ruy-băng VietQR -->
                <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-primary px-3 py-2 rounded-pill fw-bold" style="font-size: 0.8rem; letter-spacing: 0.5px;">
                            <i class="fa-solid fa-bolt me-1"></i> VietQR PRO
                        </span>
                        <span class="badge bg-light text-dark border px-2 py-1 rounded-pill small">Napas 247</span>
                    </div>
                    <span class="text-muted small fw-semibold">Đơn hàng: <strong class="text-primary font-monospace">#{{ $order->id }}</strong></span>
                </div>

                <!-- Khung Mã QR có hiệu ứng quét -->
                <div class="position-relative d-inline-block mx-auto my-2 p-3 bg-white rounded-4 shadow-sm border border-primary border-opacity-25" style="max-width: 320px;">
                    <img id="vietqr-image" 
                         src="{{ $paymentDetails['qr_url'] }}" 
                         alt="Mã QR Chuyển khoản VietQR SePay" 
                         class="img-fluid rounded-3"
                         style="width: 280px; height: 280px; object-fit: contain;"
                         onerror="this.onerror=null; this.src='{{ $paymentDetails['vietqr_url'] }}';">
                    
                    <!-- Hiệu ứng viền góc công nghệ quét mã -->
                    <div class="position-absolute top-0 start-0 m-2 border-top border-start border-primary border-3 rounded-top-start" style="width: 20px; height: 20px;"></div>
                    <div class="position-absolute top-0 end-0 m-2 border-top border-end border-primary border-3 rounded-top-end" style="width: 20px; height: 20px;"></div>
                    <div class="position-absolute bottom-0 start-0 m-2 border-bottom border-start border-primary border-3 rounded-bottom-start" style="width: 20px; height: 20px;"></div>
                    <div class="position-absolute bottom-0 end-0 m-2 border-bottom border-end border-primary border-3 rounded-bottom-end" style="width: 20px; height: 20px;"></div>
                </div>

                <div class="mt-3">
                    <p class="fw-semibold text-dark mb-1 small">
                        <i class="fa-solid fa-camera text-primary me-1"></i> Quét mã bằng App Ngân hàng hoặc Ví điện tử
                    </p>
                    <p class="text-muted" style="font-size: 0.78rem;">
                        Số tiền và nội dung chuyển khoản đã được tích hợp tự động trong mã QR.
                    </p>
                </div>

                <!-- Đồng hồ đếm ngược -->
                <div class="p-2 mt-auto rounded-3 bg-primary bg-opacity-10 border border-primary border-opacity-25 d-flex align-items-center justify-content-center gap-2">
                    <i class="fa-regular fa-clock text-primary fs-5"></i>
                    <span class="small text-muted">Mã QR hết hạn trong:</span>
                    <strong class="text-primary font-monospace fs-6" id="countdown-timer">15:00</strong>
                </div>
            </div>
        </div>

        <!-- Cột Bên Phải: Thông Tin Chuyển Khoản Chi Tiết & Trạng Thái -->
        <div class="col-lg-7">
            <div class="card card-modern p-4 border-0 shadow-sm rounded-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom">
                    <h5 class="fw-bold mb-0 text-dark d-flex align-items-center">
                        <i class="fa-solid fa-file-invoice-dollar text-primary me-2"></i>Thông tin chuyển khoản thủ công
                    </h5>
                    <!-- Box trạng thái real-time -->
                    <span class="badge rounded-pill px-3 py-2 d-flex align-items-center gap-2" id="payment-status-badge" style="background: #fff7ed; color: #ea580c; border: 1px solid #fed7aa;">
                        <span class="spinner-grow spinner-grow-sm text-warning" role="status" style="width: 10px; height: 10px;"></span>
                        <span id="payment-status-text" class="fw-semibold" style="font-size: 0.75rem;">Đang chờ nhận tiền...</span>
                    </span>
                </div>

                <p class="text-muted small mb-3">
                    Nếu không tiện quét mã QR, bạn có thể chuyển khoản trực tiếp qua Internet Banking với thông tin bên dưới:
                </p>

                <!-- Bảng Thông Tin Chi Tiết Có Nút Copy -->
                <div class="d-flex flex-column gap-3 mb-4">
                    <!-- Ngân Hàng -->
                    <div class="p-3 bg-light rounded-3 d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small d-block">Ngân hàng thụ hưởng</span>
                            <strong class="text-dark fs-6">{{ $paymentDetails['bank'] }}</strong>
                        </div>
                        <span class="badge bg-white text-primary border px-3 py-2 rounded-pill fw-bold">24/7 Napas</span>
                    </div>

                    <!-- Số Tài Khoản -->
                    <div class="p-3 bg-light rounded-3 d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small d-block">Số tài khoản nhận tiền</span>
                            <strong class="text-primary fs-5 font-monospace" id="account-no-text">{{ $paymentDetails['account_no'] }}</strong>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 copy-btn" data-clipboard="{{ $paymentDetails['account_no'] }}">
                            <i class="fa-regular fa-copy me-1"></i> Sao chép
                        </button>
                    </div>

                    <!-- Tên Chủ Tài Khoản -->
                    <div class="p-3 bg-light rounded-3 d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small d-block">Chủ tài khoản (Người nhận)</span>
                            <strong class="text-dark fs-6 text-uppercase">{{ $paymentDetails['account_name'] }}</strong>
                        </div>
                        <i class="fa-solid fa-circle-check text-success fs-5" title="Đã xác thực"></i>
                    </div>

                    <!-- Số Tiền -->
                    <div class="p-3 bg-light rounded-3 d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small d-block">Số tiền thanh toán</span>
                            <strong class="text-danger fs-4 font-monospace">{{ $paymentDetails['amount_formatted'] }}</strong>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 copy-btn" data-clipboard="{{ $paymentDetails['amount'] }}">
                            <i class="fa-regular fa-copy me-1"></i> Sao chép
                        </button>
                    </div>

                    <!-- Nội Dung Chuyển Khoản (QUAN TRỌNG NHẤT) -->
                    <div class="p-3 rounded-3 d-flex align-items-center justify-content-between border-2 border-warning" style="background: #fffbeb; border-style: dashed !important;">
                        <div>
                            <div class="d-flex align-items-center gap-1 mb-1">
                                <span class="badge bg-warning text-dark fw-bold px-2 py-0" style="font-size: 0.7rem;">BẮT BUỘC CHÍNH XÁC</span>
                                <span class="text-muted small">Nội dung chuyển khoản:</span>
                            </div>
                            <strong class="text-dark fs-4 font-monospace text-primary" id="transfer-content-text">{{ $paymentDetails['transfer_content'] }}</strong>
                        </div>
                        <button type="button" class="btn btn-sm btn-warning text-dark fw-bold rounded-pill px-3 copy-btn shadow-sm" data-clipboard="{{ $paymentDetails['transfer_content'] }}">
                            <i class="fa-regular fa-copy me-1"></i> Sao chép
                        </button>
                    </div>
                </div>

                <!-- Lưu ý bảo mật & thông báo tự động -->
                <div class="alert alert-warning py-2 px-3 rounded-3 d-flex align-items-start gap-2 mb-4" style="font-size: 0.8rem;">
                    <i class="fa-solid fa-triangle-exclamation text-warning fs-6 mt-1"></i>
                    <div>
                        <strong>Lưu ý quan trọng:</strong> Vui lòng nhập đúng nội dung chuyển khoản <code class="fw-bold text-danger">{{ $paymentDetails['transfer_content'] }}</code> để hệ thống tự động xác nhận và khởi tạo đơn hàng sang bưu tá GHN ngay lập tức.
                    </div>
                </div>

                <!-- Các nút hành động thao tác -->
                <div class="d-flex flex-column gap-2 mt-auto">
                    <button type="button" id="btn-manual-check" class="btn btn-modern-primary w-100 rounded-pill py-3 fw-bold fs-6 shadow">
                        <i class="fa-solid fa-arrows-rotate me-2"></i> Tôi đã chuyển khoản xong (Kiểm tra ngay)
                    </button>

                    <div class="d-flex gap-2">
                        <!-- Nút Mô Phỏng Thanh Toán (Dành cho môi trường Demo / Localhost) -->
                        <form action="{{ route('orders.sepay.mock', $order->id) }}" method="POST" class="flex-fill">
                            @csrf
                            <button type="submit" class="btn btn-outline-success w-100 rounded-pill py-2 small fw-bold" onclick="return confirm('Mô phỏng thanh toán thành công ngay lập tức để test luồng vận đơn GHN?')">
                                <i class="fa-solid fa-flask me-1"></i> Mô phỏng chuyển tiền (Demo Test)
                            </button>
                        </form>

                        <!-- Nút Hủy Thanh Toán / Đổi Phương Thức -->
                        <form id="form-cancel-payment" action="{{ route('orders.sepay.cancel', $order->id) }}" method="POST" class="flex-fill">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger w-100 rounded-pill py-2 small fw-bold" onclick="return confirm('Bạn có muốn hủy đơn này để quay lại chỉnh sửa thông tin giao hàng & giỏ hàng không? Tồn kho, Xu và Giỏ hàng sẽ được khôi phục nguyên vẹn.')">
                                <i class="fa-solid fa-pen-to-square me-1"></i> Sửa lại thông tin giỏ hàng
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toast Thông Báo Sao Chép Nhanh -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1090">
    <div id="copyToast" class="toast align-items-center text-white bg-dark border-0 rounded-pill px-2 shadow" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body small">
                <i class="fa-solid fa-circle-check text-success me-2"></i><span id="toast-message">Đã sao chép vào bộ nhớ tạm!</span>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    const orderId = {{ $order->id }};
    const checkStatusUrl = "{{ route('orders.sepay.check-status', $order->id) }}";
    const statusBadge = document.getElementById('payment-status-badge');
    const statusText = document.getElementById('payment-status-text');
    const btnManualCheck = document.getElementById('btn-manual-check');

    // 1. Xử lý Countdown Timer 15 phút (900 giây)
    let timeLeft = 15 * 60;
    const timerElem = document.getElementById('countdown-timer');

    const countdownInterval = setInterval(function () {
        if (timeLeft <= 0) {
            clearInterval(countdownInterval);
            timerElem.innerText = "00:00 (Hết hạn)";
            timerElem.classList.replace('text-primary', 'text-danger');
            if (statusText) statusText.innerText = "Mã QR đã hết hạn. Vui lòng thử lại!";
            return;
        }
        timeLeft--;
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        timerElem.innerText = (minutes < 10 ? "0" : "") + minutes + ":" + (seconds < 10 ? "0" : "") + seconds;
    }, 1000);

    // 2. Chức năng Sao chép Clipboard nhanh
    const copyToastElem = document.getElementById('copyToast');
    const copyToast = copyToastElem ? new bootstrap.Toast(copyToastElem, { delay: 2000 }) : null;
    const toastMsg = document.getElementById('toast-message');

    document.querySelectorAll('.copy-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const textToCopy = this.getAttribute('data-clipboard');
            if (!textToCopy) return;

            navigator.clipboard.writeText(textToCopy).then(() => {
                if (toastMsg) toastMsg.innerText = 'Đã sao chép: ' + textToCopy;
                if (copyToast) copyToast.show();

                const originalHtml = this.innerHTML;
                this.innerHTML = '<i class="fa-solid fa-check me-1"></i> Đã chép!';
                this.classList.replace('btn-outline-primary', 'btn-success');
                setTimeout(() => {
                    this.innerHTML = originalHtml;
                    this.classList.replace('btn-success', 'btn-outline-primary');
                }, 1500);
            }).catch(err => {
                console.error('Không thể sao chép:', err);
            });
        });
    });

    // 3. Tự động kiểm tra trạng thái thanh toán theo thời gian thực (Polling mỗi 2.5s)
    let isChecking = false;
    let pollInterval = null;

    function checkPaymentStatus(isManual = false) {
        if (isChecking) return;
        isChecking = true;

        if (isManual && btnManualCheck) {
            btnManualCheck.disabled = true;
            btnManualCheck.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i> Đang đối soát với ngân hàng...';
        }

        fetch(checkStatusUrl, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            isChecking = false;
            if (isManual && btnManualCheck) {
                btnManualCheck.disabled = false;
                btnManualCheck.innerHTML = '<i class="fa-solid fa-arrows-rotate me-2"></i> Tôi đã chuyển khoản xong (Kiểm tra ngay)';
            }

            if (data.is_paid) {
                clearInterval(pollInterval);
                clearInterval(countdownInterval);

                // Cập nhật giao diện báo thành công rực rỡ
                if (statusBadge) {
                    statusBadge.style.background = '#dcfce7';
                    statusBadge.style.color = '#15803d';
                    statusBadge.style.borderColor = '#86efac';
                }
                if (statusText) {
                    statusText.innerHTML = '<i class="fa-solid fa-circle-check text-success me-1"></i> Thanh toán thành công!';
                }

                if (btnManualCheck) {
                    btnManualCheck.className = 'btn btn-success w-100 rounded-pill py-3 fw-bold fs-6 shadow';
                    btnManualCheck.innerHTML = '<i class="fa-solid fa-circle-check me-2"></i> Thanh toán thành công! Đang chuyển trang...';
                }

                // Chuyển hướng tới trang chi tiết đơn hàng
                setTimeout(() => {
                    window.location.href = data.redirect_url;
                }, 1200);
            } else if (isManual) {
                if (toastMsg) toastMsg.innerText = 'Hệ thống chưa nhận được tiền cho đơn này. Vui lòng chờ vài giây rồi thử lại!';
                if (copyToast) copyToast.show();
            }
        })
        .catch(err => {
            console.error('Lỗi kiểm tra trạng thái thanh toán:', err);
            isChecking = false;
            if (isManual && btnManualCheck) {
                btnManualCheck.disabled = false;
                btnManualCheck.innerHTML = '<i class="fa-solid fa-arrows-rotate me-2"></i> Tôi đã chuyển khoản xong (Kiểm tra ngay)';
            }
        });
    }

    // Bắt đầu Polling kiểm tra mỗi 2.5 giây
    pollInterval = setInterval(() => {
        checkPaymentStatus(false);
    }, 2500);

    // Xử lý nút bấm kiểm tra thủ công
    if (btnManualCheck) {
        btnManualCheck.addEventListener('click', function () {
            checkPaymentStatus(true);
        });
    }

    // 4. Bắt sự kiện người dùng bấm nút Quay lại (Back) của trình duyệt
    window.history.pushState(null, "", window.location.href);
    window.onpopstate = function () {
        if (confirm("Đơn hàng #{{ $order->id }} đang chờ bạn thanh toán. Bạn có muốn hủy đơn này và quay lại Giỏ hàng để thay đổi thông tin không?")) {
            const formCancel = document.getElementById('form-cancel-payment');
            if (formCancel) {
                formCancel.submit();
            } else {
                window.location.href = "{{ route('checkout.index', ['action' => 'edit']) }}";
            }
        } else {
            window.history.pushState(null, "", window.location.href);
        }
    };
});
</script>
@endpush
