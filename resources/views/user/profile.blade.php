@extends('layouts.store')

@section('title', 'Trung Tâm Khách Hàng & Đơn Mua - ' . $user->name)

@section('content')
<div class="container my-4 my-lg-5">
    <div class="row g-4">
        
        <!-- CỘT TRÁI: THÔNG TIN CÁ NHÂN -->
        <div class="col-12 col-lg-4">
            <div class="card card-modern p-4 sticky-lg-top" style="top: 90px; z-index: 10;">
                
                <!-- Avatar & Tên -->
                <div class="text-center pb-4 border-bottom position-relative">
                    <div class="user-avatar-circle mx-auto mb-3 shadow">
                        {{ mb_strtoupper(mb_substr($user->name, 0, 1, 'UTF-8'), 'UTF-8') }}
                    </div>
                    <h5 class="fw-bold text-dark mb-1">{{ $user->name }}</h5>
                    <p class="text-muted small mb-2 text-break"><i class="fa-regular fa-envelope me-1 text-primary"></i>{{ $user->email }}</p>
                    
                    <div class="d-flex justify-content-center gap-2 flex-wrap">
                        @if($user->role === 'admin')
                            <span class="badge bg-warning text-dark fw-bold rounded-pill px-3 py-1">
                                <i class="fa-solid fa-shield-halved me-1"></i> Quản trị viên
                            </span>
                        @else
                            <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1 fw-semibold">
                                <i class="fa-solid fa-crown me-1 text-warning"></i> Khách hàng thân thiết
                            </span>
                        @endif

                        @if($user->hasVerifiedEmail())
                            <span class="badge badge-soft-success">
                                <i class="fa-solid fa-circle-check"></i> Đã kích hoạt
                            </span>
                        @else
                            <a href="{{ route('verification.notice') }}" class="badge badge-soft-warning text-decoration-none" title="Bấm vào để kích hoạt">
                                <i class="fa-solid fa-triangle-exclamation"></i> Chưa kích hoạt
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Thông số tóm tắt -->
                <div class="row g-2 text-center py-3 border-bottom">
                    <div class="col-6 border-end">
                        <div class="text-muted small">Tổng đơn mua</div>
                        <div class="fs-5 fw-bold text-primary">{{ $allOrders->count() }}</div>
                    </div>
                    <div class="col-6">
                        <div class="text-muted small">Đã chi tiêu</div>
                        <div class="fs-6 fw-bold text-dark mt-1">{{ number_format($totalSpent, 0, ',', '.') }}đ</div>
                    </div>
                </div>

                <!-- Khối Tiền Xu Thưởng Nhận Được -->
                <div class="p-3 my-3 rounded-4" style="background: linear-gradient(145deg, #fffdfa 0%, #fef8ee 100%); border: 1px solid rgba(245, 158, 11, 0.22); box-shadow: 0 2px 8px rgba(245, 158, 11, 0.05);">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="fw-bold text-dark small">Xu thưởng tích lũy</span>
                        <a href="{{ route('videos.index') }}" class="btn btn-warning btn-sm rounded-pill py-0 px-2 fw-semibold shadow-none" style="font-size: 0.72rem;">
                            Nhận thêm Xu
                        </a>
                    </div>

                    <div class="d-flex align-items-baseline justify-content-between mb-2">
                        <div>
                            <span class="fs-4 fw-bold text-dark">{{ number_format($userCoins ?? $user->coins ?? 0) }}</span>
                            <span class="small fw-semibold text-muted ms-1">Xu</span>
                        </div>
                        <div class="text-end">
                            <span class="small text-muted">Quy đổi: </span>
                            <strong class="fs-6 text-success fw-bold">{{ number_format($coinsValue ?? (($userCoins ?? $user->coins ?? 0) * ($coinRate ?? 500)), 0, ',', '.') }}đ</strong>
                        </div>
                    </div>

                    <div class="pt-2 border-top border-warning border-opacity-25 small" style="font-size: 0.76rem;">
                        <div class="d-flex justify-content-between text-muted mb-1">
                            <span>Tỷ lệ quy đổi:</span>
                            <span class="fw-medium text-dark">1 Xu = {{ number_format($coinRate ?? 500) }}đ</span>
                        </div>
                        @if(($coinsEarnedToday ?? 0) > 0)
                            <div class="d-flex justify-content-between text-muted mb-1">
                                <span>Đã nhận hôm nay:</span>
                                <span class="fw-semibold text-success">+{{ number_format($coinsEarnedToday) }} Xu ({{ number_format($coinsEarnedToday * ($coinRate ?? 500), 0, ',', '.') }}đ)</span>
                            </div>
                        @endif
                        @if(($totalCoinsUsed ?? 0) > 0)
                            <div class="d-flex justify-content-between text-muted">
                                <span>Đã dùng mua hàng:</span>
                                <span class="fw-medium text-dark">{{ number_format($totalCoinsUsed) }} Xu (-{{ number_format($totalCoinsSaved ?? 0, 0, ',', '.') }}đ)</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Chi tiết liên hệ -->
                <div class="py-3 border-bottom small">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted"><i class="fa-solid fa-phone me-1"></i>Số điện thoại:</span>
                        <strong class="text-dark">{{ $latestOrder->phone ?? 'Cập nhật khi đặt' }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted"><i class="fa-regular fa-calendar me-1"></i>Ngày tham gia:</span>
                        <span class="text-muted">{{ $user->created_at ? $user->created_at->format('d/m/Y') : 'Mới tham gia' }}</span>
                    </div>
                    <div class="mb-1">
                        <span class="text-muted d-block mb-1"><i class="fa-solid fa-location-dot me-1"></i>Địa chỉ nhận hàng mặc định:</span>
                        <span class="text-secondary fst-italic">{{ $latestOrder->address ?? 'Chưa có địa chỉ mặc định' }}</span>
                    </div>
                </div>

                <!-- Thao tác tài khoản -->
                <div class="pt-3 d-grid gap-2">
                    <button class="btn btn-modern-outline btn-sm rounded-pill" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEditProfile" aria-expanded="false">
                        <i class="fa-solid fa-pen-to-square me-1"></i> Cập nhật thông tin
                    </button>
                    <button class="btn btn-outline-secondary btn-sm rounded-pill text-muted" type="button" data-bs-toggle="collapse" data-bs-target="#collapseChangePassword" aria-expanded="false">
                        <i class="fa-solid fa-key me-1"></i> Đổi mật khẩu
                    </button>
                    
                    @if($user->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-warning btn-sm rounded-pill fw-bold">
                            <i class="fa-solid fa-gauge me-1"></i> Đi tới Trang Quản Trị
                        </a>
                    @endif
                </div>

                <!-- Form cập nhật thông tin cá nhân (Ẩn/Hiện) -->
                <div class="collapse mt-3" id="collapseEditProfile">
                    <div class="p-3 profile-collapse-box">
                        <h6 class="fw-bold text-dark small mb-3"><i class="fa-solid fa-user-pen me-1 text-primary"></i>Sửa thông tin</h6>
                        <form action="{{ route('profile.update') }}" method="POST">
                            @csrf
                            <div class="mb-2">
                                <label class="form-label text-muted small mb-1">Họ và tên</label>
                                <input type="text" name="name" class="form-control form-control-sm bg-white text-dark border" value="{{ old('name', $user->name) }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted small mb-1">Email</label>
                                <input type="email" name="email" class="form-control form-control-sm bg-white text-dark border" value="{{ old('email', $user->email) }}" required>
                            </div>
                            <button type="submit" class="btn btn-modern-primary btn-sm w-100 rounded-pill">
                                Lưu thay đổi
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Form đổi mật khẩu (Ẩn/Hiện) -->
                <div class="collapse mt-3" id="collapseChangePassword">
                    <div class="p-3 profile-collapse-box">
                        <h6 class="fw-bold text-dark small mb-3"><i class="fa-solid fa-lock me-1 text-warning"></i>Đổi mật khẩu</h6>
                        <form action="{{ route('profile.changePassword') }}" method="POST">
                            @csrf
                            <div class="mb-2">
                                <label class="form-label text-muted small mb-1">Mật khẩu hiện tại</label>
                                <input type="password" name="current_password" class="form-control form-control-sm bg-white text-dark border" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label text-muted small mb-1">Mật khẩu mới</label>
                                <input type="password" name="password" class="form-control form-control-sm bg-white text-dark border" required minlength="6">
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted small mb-1">Nhập lại mật khẩu mới</label>
                                <input type="password" name="password_confirmation" class="form-control form-control-sm bg-white text-dark border" required minlength="6">
                            </div>
                            <button type="submit" class="btn btn-warning btn-sm w-100 rounded-pill fw-bold text-dark">
                                Đổi mật khẩu
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>

        <!-- CỘT PHẢI: KHỐI ĐƠN MUA & LỘ TRÌNH VẬN CHUYỂN KIỂU SHOPEE -->
        <div class="col-12 col-lg-8">
            
            <!-- 1. KHỐI TRẠNG THÁI ĐƠN MUA KIỂU SHOPEE -->
            <div class="card card-modern p-4 mb-4">
                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-bag-shopping text-primary fs-4"></i>
                        <h5 class="fw-bold text-dark mb-0">Đơn mua của tôi</h5>
                    </div>
                    <a href="{{ route('profile', ['status' => 'all']) }}#orders-history" class="shopee-history-link text-decoration-none small d-inline-flex align-items-center text-primary fw-semibold">
                        <span>Xem tất cả ({{ $allOrders->count() }})</span>
                        <i class="fa-solid fa-chevron-right ms-1 small"></i>
                    </a>
                </div>

                <!-- 4 Biểu tượng trạng thái Shopee -->
                <div class="row row-cols-4 g-2 text-center py-2 shopee-status-row">
                    
                    <!-- 1. Chờ xác nhận -->
                    <div class="col">
                        <a href="{{ route('profile', ['status' => 'pending']) }}#orders-history" 
                           class="shopee-status-btn d-flex flex-column align-items-center text-decoration-none position-relative p-2 p-md-3 rounded-3 {{ $statusTab === 'pending' ? 'active' : '' }}">
                            <div class="shopee-icon-wrapper position-relative mb-2">
                                <i class="fa-solid fa-clipboard-list fs-3"></i>
                                @if($pendingCount > 0)
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-white">
                                        {{ $pendingCount }}
                                    </span>
                                @endif
                            </div>
                            <span class="shopee-status-text">Chờ xác nhận</span>
                        </a>
                    </div>

                    <!-- 2. Chờ lấy hàng -->
                    <div class="col">
                        <a href="{{ route('profile', ['status' => 'picking']) }}#orders-history" 
                           class="shopee-status-btn d-flex flex-column align-items-center text-decoration-none position-relative p-2 p-md-3 rounded-3 {{ $statusTab === 'picking' ? 'active' : '' }}">
                            <div class="shopee-icon-wrapper position-relative mb-2">
                                <i class="fa-solid fa-box-open fs-3"></i>
                                @if($pickingCount > 0)
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-white">
                                        {{ $pickingCount }}
                                    </span>
                                @endif
                            </div>
                            <span class="shopee-status-text">Chờ lấy hàng</span>
                        </a>
                    </div>

                    <!-- 3. Chờ giao hàng -->
                    <div class="col">
                        <a href="{{ route('profile', ['status' => 'delivering']) }}#orders-history" 
                           class="shopee-status-btn d-flex flex-column align-items-center text-decoration-none position-relative p-2 p-md-3 rounded-3 {{ $statusTab === 'delivering' ? 'active' : '' }}">
                            <div class="shopee-icon-wrapper position-relative mb-2">
                                <i class="fa-solid fa-truck-fast fs-3"></i>
                                @if($deliveringCount > 0)
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-white">
                                        {{ $deliveringCount }}
                                    </span>
                                @endif
                            </div>
                            <span class="shopee-status-text">Chờ giao hàng</span>
                        </a>
                    </div>

                    <!-- 4. Đánh giá -->
                    <div class="col">
                        <a href="{{ route('profile', ['status' => 'delivered']) }}#orders-history" 
                           class="shopee-status-btn d-flex flex-column align-items-center text-decoration-none position-relative p-2 p-md-3 rounded-3 {{ $statusTab === 'delivered' ? 'active' : '' }}">
                            <div class="shopee-icon-wrapper position-relative mb-2">
                                <i class="fa-regular fa-star fs-3"></i>
                                @if($deliveredCount > 0)
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark border border-white">
                                        {{ $deliveredCount }}
                                    </span>
                                @endif
                            </div>
                            <span class="shopee-status-text">Đánh giá</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- 2. DANH SÁCH CHI TIẾT TẤT CẢ ĐƠN HÀNG (THEO TAB LỌC) -->
            <div id="orders-history" class="card card-modern p-4" style="scroll-margin-top: 90px;">
                
                <!-- Thanh Tabs lọc -->
                <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2 pb-3 border-bottom">
                    <h5 class="fw-bold text-dark mb-0">
                        <i class="fa-solid fa-clock-rotate-left text-primary me-2"></i>Lịch sử mua hàng
                    </h5>
                    
                    <div class="d-flex gap-1 flex-wrap">
                        <a href="{{ route('profile', ['status' => 'all']) }}#orders-history" class="btn btn-sm rounded-pill px-3 {{ $statusTab === 'all' ? 'btn-modern-primary' : 'btn-light text-muted border-0' }}">
                            Tất cả ({{ $allOrders->count() }})
                        </a>
                        <a href="{{ route('profile', ['status' => 'pending']) }}#orders-history" class="btn btn-sm rounded-pill px-3 {{ $statusTab === 'pending' ? 'btn-modern-primary' : 'btn-light text-muted border-0' }}">
                            Chờ xác nhận ({{ $pendingCount }})
                        </a>
                        <a href="{{ route('profile', ['status' => 'picking']) }}#orders-history" class="btn btn-sm rounded-pill px-3 {{ $statusTab === 'picking' ? 'btn-modern-primary' : 'btn-light text-muted border-0' }}">
                            Chờ lấy hàng ({{ $pickingCount }})
                        </a>
                        <a href="{{ route('profile', ['status' => 'delivering']) }}#orders-history" class="btn btn-sm rounded-pill px-3 {{ $statusTab === 'delivering' ? 'btn-modern-primary' : 'btn-light text-muted border-0' }}">
                            Đang giao ({{ $deliveringCount }})
                        </a>
                        <a href="{{ route('profile', ['status' => 'delivered']) }}#orders-history" class="btn btn-sm rounded-pill px-3 {{ $statusTab === 'delivered' ? 'btn-modern-primary' : 'btn-light text-muted border-0' }}">
                            Đã giao ({{ $deliveredCount }})
                        </a>
                        @if($cancelledCount > 0)
                            <a href="{{ route('profile', ['status' => 'cancelled']) }}#orders-history" class="btn btn-sm rounded-pill px-3 {{ $statusTab === 'cancelled' ? 'btn-modern-primary' : 'btn-light text-muted border-0' }}">
                                Đã hủy ({{ $cancelledCount }})
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Danh sách đơn hàng -->
                @forelse($orders as $order)
                    @php
                        $isOrderCancelled = in_array($order->status, ['cancelled']) || $order->shipping_status === 'cancelled';
                        $canCancel = !$isOrderCancelled && in_array($order->shipping_status, ['pending', 'ready_to_pick', 'not_shipped']);
                    @endphp
                    <div class="p-3 p-md-4 mb-3 profile-order-card">
                        
                        <!-- Header thẻ đơn -->
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 pb-3 mb-3 border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-bold font-monospace text-primary fs-6">#{{ $order->id }}</span>
                                <span class="text-muted small"><i class="fa-regular fa-calendar me-1"></i>{{ $order->created_at ? $order->created_at->format('d/m/Y H:i') : '' }}</span>
                                @if($order->ghn_order_code)
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 font-monospace px-2 py-1 rounded-pill small">
                                        GHN: {{ $order->ghn_order_code }}
                                    </span>
                                @endif
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                @if($order->status === 'paid')
                                    <span class="badge-soft badge-soft-success small"><i class="fa-solid fa-circle-check"></i> Đã thanh toán</span>
                                @elseif($isOrderCancelled)
                                    <span class="badge-soft badge-soft-secondary small"><i class="fa-solid fa-ban"></i> Đã hủy</span>
                                @else
                                    <span class="badge-soft badge-soft-info small"><i class="fa-solid fa-truck"></i> COD</span>
                                @endif

                                <span class="badge badge-shipping {{ $order->shipping_status_badge }}">
                                    {{ $order->shipping_status_text }}
                                </span>
                            </div>
                        </div>

                        <!-- Danh sách sản phẩm của đơn -->
                        <div class="d-flex flex-column gap-3 mb-3">
                            @foreach($order->items as $item)
                                <div class="d-flex align-items-center gap-3">
                                    <div class="item-thumb-box flex-shrink-0 rounded-3 overflow-hidden bg-light border d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                                        @if($item->product && $item->product->image)
                                            <img src="{{ asset($item->product->image) }}" alt="{{ $item->product_name ?? 'Sản phẩm' }}" class="w-100 h-100 object-fit-cover">
                                        @else
                                            <i class="fa-solid fa-tablet-screen-button text-muted"></i>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1 overflow-hidden">
                                        <div class="fw-semibold text-dark text-truncate small">
                                            {{ $item->product_name ?? ($item->product->name ?? 'Sản phẩm') }}
                                        </div>
                                        <div class="small text-muted">
                                            Số lượng: x{{ $item->quantity }}
                                            @if($item->product && $item->product->chip)
                                                • <span class="text-primary">{{ $item->product->chip }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="text-end flex-shrink-0">
                                        <div class="fw-semibold text-dark">{{ number_format($item->price, 0, ',', '.') }}đ</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Footer thẻ đơn: Tổng tiền & Nút thao tác -->
                        <div class="pt-3 border-top d-flex flex-wrap justify-content-between align-items-center gap-2">
                            <div>
                                <div class="d-flex align-items-baseline gap-2">
                                    <span class="text-muted small">Thành tiền: </span>
                                    <strong class="fs-5 text-primary">{{ number_format($order->total_price, 0, ',', '.') }}đ</strong>
                                </div>
                                @if(($order->coins_used ?? 0) > 0)
                                    <div class="text-muted small mt-1" style="font-size: 0.76rem;">
                                        Đã dùng <span class="text-warning-emphasis fw-semibold">{{ number_format($order->coins_used) }} Xu</span> (-{{ number_format($order->coins_discount ?? ($order->coins_used * ($coinRate ?? 500)), 0, ',', '.') }}đ)
                                    </div>
                                @endif
                            </div>

                            <div class="d-flex gap-2 flex-wrap">
                                <a href="{{ route('orders.show', $order->id) }}" class="btn btn-modern-outline btn-sm rounded-pill px-3">
                                    <i class="fa-regular fa-eye me-1"></i> Xem chi tiết
                                </a>

                                @if($order->shipping_status === 'delivered')
                                    <a href="{{ route('orders.show', $order->id) }}#review-section" class="btn btn-warning btn-sm rounded-pill fw-bold text-dark px-3">
                                        <i class="fa-solid fa-star me-1"></i> Đánh giá ngay
                                    </a>
                                @endif

                                @if($canCancel)
                                    <form action="{{ route('orders.cancel', $order->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này?');" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3">
                                            <i class="fa-solid fa-ban me-1"></i> Hủy đơn
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>

                    </div>
                @empty
                    <div class="text-center py-5">
                        <i class="fa-solid fa-box-open fs-1 text-muted mb-3"></i>
                        <h5 class="fw-bold text-dark">Không có đơn hàng nào trong mục này</h5>
                        <p class="text-muted small mb-4">Bạn chưa có đơn mua nào phù hợp với bộ lọc hiện tại.</p>
                        <a href="{{ route('home') }}#products-section" class="btn btn-modern-primary btn-sm rounded-pill px-4">
                            <i class="fa-solid fa-cart-shopping me-1"></i> Mua sắm sản phẩm ngay
                        </a>
                    </div>
                @endforelse

                <!-- Phân trang -->
                @if($orders->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $orders->fragment('orders-history')->links('pagination::bootstrap-5') }}
                    </div>
                @endif

            </div>

        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const urlParams = new URLSearchParams(window.location.search);
    const hasStatus = urlParams.has('status');
    const isOrdersHistoryHash = window.location.hash === '#orders-history';

    if (isOrdersHistoryHash || (hasStatus && urlParams.get('status') !== 'all')) {
        const target = document.getElementById('orders-history');
        if (target) {
            setTimeout(function () {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 100);
        }
    }
});
</script>
@endpush
