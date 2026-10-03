@extends('layouts.store')

@section('title', 'Lướt Video Tích Xu Trừ Tiền Mua Hàng - VUA TABLET')

@section('content')
<div class="video-reels-page-wrapper">
    <!-- Header Bar Tích Xu Trừ Tiền Mặt - Phong cách tối giản hiện đại VUA TABLET -->
    <div class="container pt-3 pb-3">
        <div class="video-top-bar-card p-3 px-md-4 rounded-4 shadow-sm bg-white border">
            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                <!-- Cột trái: Thông điệp tích xu thưởng -->
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge rounded-pill fw-bold px-3 py-1 text-nowrap" style="background: #eef2ff; color: #4f46e5; font-size: 0.75rem; border: 1px solid #c7d2fe;">
                        TÍCH XU
                    </span>
                    <span class="text-dark fw-medium small">
                        Xem video 30s nhận ngay <strong style="color: #4f46e5;">+1 Xu</strong> (trừ 500₫ khi mua) • Tối đa 5.000₫/ngày
                    </span>
                </div>

                <!-- Cột phải: Ví Xu hiện tại của User & Hạn mức ngày & Nút mua sắm -->
                <div class="d-flex align-items-center gap-2 gap-md-3 flex-wrap">
                    <div class="d-inline-flex align-items-center gap-2 bg-light px-3 py-1 rounded-pill border small">
                        <span class="text-muted">Ví Xu:</span>
                        <strong class="font-monospace text-primary fw-bold" id="userCoinsBalanceDisplay">{{ number_format($userCoins) }} Xu</strong>
                        <span class="badge bg-primary-subtle text-primary rounded-pill font-monospace" style="font-size: 0.72rem;" id="coinsCashValueBadge">
                            -{{ number_format($userCoins * 500) }}₫
                        </span>
                    </div>

                    <div class="small text-muted text-nowrap">
                        Còn nhận: <strong class="text-dark font-monospace" id="coinsRemainingTodayDisplay">{{ $coinsRemainingToday }}</strong>/{{ $dailyLimit }} Xu
                    </div>

                    <a href="{{ route('home') }}" class="btn btn-primary btn-sm rounded-pill px-3 py-1 fw-bold text-white text-nowrap shadow-sm ms-auto ms-lg-0" style="font-size: 0.8rem; background: #4f46e5; border-color: #4f46e5;">
                        Dùng Xu mua sắm
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stage Wrapper giữ cho Vòng Quay Luôn Cố Định Trên Mọi Video -->
    <div class="reels-stage-wrapper position-relative">
        <!-- Vòng tròn tiến trình tích lũy Xu (Luôn ghim cố định góc trên bên phải, xem video nào cũng thấy) -->
        <div class="watch-earn-floating-badge {{ $coinsRemainingToday <= 0 ? 'limit-reached' : '' }}" id="watchEarnBadge" title="{{ $coinsRemainingToday <= 0 ? 'Bạn đã đạt giới hạn nhận Xu hôm nay. Bấm để xem chi tiết.' : 'Xem mỗi ' . ($watchSeconds ?? 30) . 's để tích lũy +' . ($coinPerReward ?? 1) . ' Xu (trừ ' . number_format(($coinPerReward ?? 1) * ($coinRate ?? 500)) . '₫)!' }}">
            <div class="ring-progress-svg-wrapper">
                <svg viewBox="0 0 60 60" class="ring-svg">
                    <circle cx="30" cy="30" r="26" class="ring-bg"/>
                    <circle cx="30" cy="30" r="26" class="ring-fill" id="ringProgressCircle" style="{{ $coinsRemainingToday <= 0 ? 'stroke-dashoffset: 0; stroke: #64748b;' : '' }}"/>
                </svg>
                <div class="gift-inner-icon">
                    <i class="fa-solid {{ $coinsRemainingToday <= 0 ? 'fa-circle-check text-success' : 'fa-coins animate-pulse' }}" style="color: #f59e0b;" id="coinStatusIcon"></i>
                </div>
            </div>
            <div class="ring-status-text {{ $coinsRemainingToday <= 0 ? 'text-muted' : '' }}" id="ringTimerText">{{ $coinsRemainingToday <= 0 ? 'Hết lượt' : ($watchSeconds ?? 30) . 's' }}</div>
        </div>

        <!-- Toast thông báo nhận Xu trôi nhẹ mà không che màn hình hay làm dừng video -->
        <div id="coinEarnedToast" class="coin-earned-toast">
            <div class="d-flex align-items-center gap-2">
                <i class="fa-solid fa-circle-check fs-5" style="color: #10b981;"></i>
                <div>
                    <strong class="d-block" style="font-size: 0.85rem; color: #fbbf24;">+1 XU ĐÃ VÀO VÍ (+500₫)!</strong>
                    <span class="small text-white-50" style="font-size: 0.72rem;">Đang tiếp tục đếm xu tiếp theo...</span>
                </div>
            </div>
        </div>

        <!-- Container cuộn Video Feed TikTok / Reels Style -->
        <div class="reels-feed-container" id="reelsContainer">
            @forelse($videos as $index => $v)
                <div class="reel-slide-item {{ $index === 0 ? 'active' : '' }}" data-index="{{ $index }}" data-video-id="{{ $v->id }}">
                    <div class="reel-card-frame shadow-lg">
                        @if($v->is_youtube)
                            <!-- Trình phát YouTube Shorts / Video nhúng ẩn sạch UI -->
                            <div class="reel-youtube-wrapper">
                                <iframe class="reel-youtube-iframe"
                                        src="{{ $v->embed_url }}"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                        allowfullscreen>
                                </iframe>
                            </div>
                        @else
                            <!-- Video Player chuẩn HTML5 Video Local 100% hoạt động mượt mà -->
                            <video class="reel-video-element" 
                                   loop 
                                   playsinline 
                                   preload="auto" 
                                   src="{{ asset($v->video_url) }}" 
                                   poster="{{ asset($v->thumbnail ?? '') }}">
                            </video>
                        @endif

                        <!-- Lớp phủ Click Play/Pause toàn màn hình không dính nút YouTube -->
                        <div class="reel-click-overlay"></div>

                        <!-- Lớp phủ bóng gradient để chữ và icon luôn sắc nét -->
                        <div class="reel-gradient-overlay"></div>

                        <!-- Nút Bật/Tắt âm thanh -->
                        <button type="button" class="reel-sound-toggle-btn" title="Bật/Tắt âm thanh">
                            <i class="fa-solid fa-volume-xmark"></i>
                        </button>
                        <div class="reel-play-indicator" style="display: none;">
                            <i class="fa-solid fa-play"></i>
                        </div>

                        <!-- Sidebar Hành động bên phải (Like, Mua hàng, Share) -->
                        <div class="reel-action-sidebar">
                            <!-- Nút Tim -->
                            <div class="action-item-wrap">
                                <button type="button" class="action-btn-circle btn-like-video" data-id="{{ $v->id }}">
                                    <i class="fa-solid fa-heart"></i>
                                </button>
                                <span class="action-count-text likes-count">{{ number_format($v->likes_count) }}</span>
                            </div>

                            <!-- Nút Mua Sản phẩm đính kèm -->
                            @if($v->product)
                                <div class="action-item-wrap">
                                    <a href="{{ route('products.show', $v->product->slug ?? $v->product->id) }}" class="action-btn-circle text-decoration-none" title="Xem sản phẩm đính kèm">
                                        <i class="fa-solid fa-bag-shopping" style="color: #818cf8;"></i>
                                    </a>
                                    <span class="action-count-text" style="color: #c7d2fe;">Mua</span>
                                </div>
                            @endif

                            <!-- Nút Chia sẻ -->
                            <div class="action-item-wrap">
                                <button type="button" class="action-btn-circle" onclick="navigator.clipboard.writeText(window.location.href); alert('Đã sao chép liên kết video!');">
                                    <i class="fa-solid fa-share-nodes"></i>
                                </button>
                                <span class="action-count-text">Chia sẻ</span>
                            </div>
                        </div>
                        <div class="reel-bottom-info">

                            <h6 class="reel-title-text mb-2">{{ $v->title }}</h6>
                            @if($v->description)
                                <p class="reel-desc-text mb-2 text-white-50">{{ Str::limit($v->description, 85) }}</p>
                            @endif

                            <!-- Product Overlay Card (Thẻ mua nhanh sản phẩm kèm theo) -->
                            @if($v->product)
                                <div class="reel-product-card-overlay">
                                    <div class="d-flex align-items-center gap-2 flex-grow-1 overflow-hidden">
                                        <img src="{{ asset($v->product->image ?? 'images/placeholder-ipad.png') }}" alt="{{ $v->product->name }}" class="product-thumb-img">
                                        <div class="overflow-hidden">
                                            <div class="product-name-clamp text-white fw-bold small">{{ $v->product->name }}</div>
                                            <div class="d-flex align-items-baseline gap-1">
                                                <span class="fw-bold small" style="color: #60a5fa;">{{ number_format($v->product->sale_price ?? $v->product->price) }}₫</span>
                                                @if($v->product->sale_price)
                                                    <span class="text-white-50 text-decoration-line-through" style="font-size: 0.7rem;">{{ number_format($v->product->price) }}₫</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <a href="{{ route('products.show', $v->product->slug ?? $v->product->id) }}" class="btn btn-sm rounded-pill px-3 py-1 flex-shrink-0 text-decoration-none fw-bold" style="background: #4f46e5; color: #ffffff; border: none; font-size: 0.8rem; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.35);">
                                        Xem ngay
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5 text-white">
                    <i class="fa-solid fa-video-slash fs-1 text-white-50 mb-3"></i>
                    <p>Hiện chưa có video nào được đăng tải.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Nút Mũi Tên Chuyển Video Trên Desktop -->
    <div class="reels-nav-controls d-none d-md-flex">
        <button type="button" class="btn-reel-nav" id="btnPrevReel" title="Video trước"><i class="fa-solid fa-chevron-up"></i></button>
        <button type="button" class="btn-reel-nav" id="btnNextReel" title="Video kế tiếp"><i class="fa-solid fa-chevron-down"></i></button>
    </div>
</div>

<!-- Modal Thông Báo Khi Đạt Giới Hạn Xu Trong Ngày (Coin Limit Popup) -->
<div id="coin-reward-modal" class="reward-modal-overlay" style="display: none;">
    <div class="reward-modal-card text-center">
        <div class="reward-icon-box animate-bounce" style="color: #f59e0b;">
            <i class="fa-solid fa-coins"></i>
        </div>
        <h3 class="fw-bold text-white mb-2" id="rewardModalTitle" style="font-size: 1.35rem;">ĐẠT GIỚI HẠN XU HÔM NAY!</h3>
        <p class="text-white-50 small mb-3" id="rewardModalSubtitle">Bạn đã tích lũy tối đa hạn mức hôm nay. Hệ thống sẽ tự động reset sau 24h để bạn tiếp tục nhận thêm!</p>

        <div class="reward-coupon-ticket mb-3">
            <div class="text-white-50 small mb-1">TỔNG XU HIỆN CÓ TRONG VÍ</div>
            <div class="fs-2 fw-bold" style="color: #f59e0b;" id="modal-current-coins-val">{{ number_format($userCoins) }} Xu</div>
            <div class="badge fs-6 py-1 px-3 rounded-pill mt-2 font-monospace" style="background: rgba(79, 70, 229, 0.25); color: #c7d2fe; border: 1px solid rgba(79, 70, 229, 0.4);" id="modal-coins-cash-val">
                Tương đương giảm: {{ number_format($userCoins * 500) }}₫ tiền mặt
            </div>
            <div class="small text-white-50 mt-2" id="modal-remaining-today-text">
                Hôm nay đã nhận đủ hạn mức! Quay lại sau 24h nhé.
            </div>
        </div>

        <div class="d-flex justify-content-center gap-2 mt-4">
            <a href="{{ route('home') }}" class="btn btn-primary rounded-pill px-4 fw-bold text-white" style="background: #4f46e5; border-color: #4f46e5;">
                Dùng Xu mua sắm ngay
            </a>
            <button type="button" class="btn btn-outline-light rounded-pill px-3" onclick="closeCoinModal()">
                Tiếp tục xem giải trí
            </button>
        </div>
    </div>
</div>

<!-- CSS Reel Video Shopping Theme: Phong cách tối giản cao cấp Apple/PhongMobile -->
<style>
.video-reels-page-wrapper {
    position: relative;
    width: 100%;
    min-height: calc(100vh - 120px);
    display: flex;
    flex-direction: column;
    align-items: center;
    background: transparent;
}

.video-top-bar-card {
    border-color: #e2e8f0 !important;
}

/* Khung Stage bao ngoài: Đảm bảo Ring Badge luôn cố định tuyệt đối ở góc trên khung trên MỌI video */
.reels-stage-wrapper {
    width: 100%;
    max-width: 440px;
    height: 76vh;
    min-height: 580px;
    margin-bottom: 30px;
    position: relative;
}

/* Feed Container cuộn snap */
.reels-feed-container {
    width: 100%;
    height: 100%;
    overflow-y: scroll;
    scroll-snap-type: y mandatory;
    scrollbar-width: none;
    -ms-overflow-style: none;
    position: relative;
    border-radius: 32px;
}
.reels-feed-container::-webkit-scrollbar {
    display: none;
}

/* Từng slide video */
.reel-slide-item {
    width: 100%;
    height: 100%;
    scroll-snap-align: start;
    scroll-snap-stop: always;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}
.reel-card-frame {
    width: 100%;
    height: 100%;
    position: relative;
    border-radius: 32px;
    overflow: hidden;
    background: #0f172a;
    border: 1px solid rgba(255, 255, 255, 0.14);
    box-shadow: 0 25px 60px -15px rgba(15, 23, 42, 0.4), 0 0 0 1px rgba(15, 23, 42, 0.08);
}

.reel-video-element {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    cursor: pointer;
    background: #000;
}

/* Wrapper nhúng YouTube chuyên nghiệp: Phóng to và căn giữa để giấu sạch viền đen & thanh tiêu đề YouTube */
.reel-youtube-wrapper {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    overflow: hidden;
    background: #000;
    z-index: 1;
}

.reel-youtube-iframe {
    position: absolute;
    top: 50%;
    left: 50%;
    width: 140%;
    height: 140%;
    transform: translate(-50%, -50%);
    border: none;
    pointer-events: none;
}

/* Lớp phủ Click Play/Pause mượt mà cho toàn bộ video */
.reel-click-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 3;
    cursor: pointer;
}

/* Overlay gradient tối ở đáy để dễ đọc chữ */
.reel-gradient-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 50%;
    background: linear-gradient(180deg, transparent 0%, rgba(15, 23, 42, 0.85) 60%, rgba(15, 23, 42, 0.98) 100%);
    pointer-events: none;
    z-index: 2;
}

/* Nút Mute / Unmute */
.reel-sound-toggle-btn {
    position: absolute;
    top: 18px;
    left: 18px;
    z-index: 10;
    background: rgba(15, 23, 42, 0.7);
    backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #ffffff;
    width: 38px;
    height: 38px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.9rem;
    cursor: pointer;
    transition: all 0.2s;
}
.reel-sound-toggle-btn:hover {
    background: #4f46e5;
    border-color: #4f46e5;
    color: #ffffff;
}

/* Play indicator */
.reel-play-indicator {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background: rgba(15, 23, 42, 0.75);
    backdrop-filter: blur(12px);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    z-index: 10;
    pointer-events: none;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

/* Sidebar hành động bên phải */
.reel-action-sidebar {
    position: absolute;
    right: 12px;
    bottom: 95px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 14px;
    z-index: 15;
}
.action-item-wrap {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 3px;
}
.action-btn-circle {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: rgba(15, 23, 42, 0.7);
    backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    cursor: pointer;
    transition: all 0.25s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
.action-btn-circle:hover {
    transform: scale(1.12);
    background: rgba(79, 70, 229, 0.4);
    border-color: #818cf8;
}
.action-btn-circle.active-liked {
    color: #ef4444 !important;
    background: rgba(239, 68, 68, 0.25);
    border-color: #ef4444;
}
.action-count-text {
    font-size: 0.72rem;
    font-weight: 600;
    color: #e2e8f0;
    text-shadow: 0 1px 3px rgba(0, 0, 0, 0.8);
}

/* Thông tin mô tả ở dưới đáy */
.reel-bottom-info {
    position: absolute;
    bottom: 12px;
    left: 12px;
    right: 68px;
    z-index: 15;
    pointer-events: auto;
}
.reel-title-text {
    font-size: 0.88rem;
    font-weight: 700;
    color: #ffffff;
    line-height: 1.35;
    margin: 0;
    text-shadow: 0 1px 4px rgba(0, 0, 0, 0.8);
}
.reel-desc-text {
    font-size: 0.75rem;
    line-height: 1.3;
}

/* Product Card Overlay */
.reel-product-card-overlay {
    background: rgba(15, 23, 42, 0.88);
    backdrop-filter: blur(16px);
    border: 1px solid rgba(255, 255, 255, 0.16);
    border-radius: 16px;
    padding: 8px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.45);
}
.product-thumb-img {
    width: 38px;
    height: 38px;
    border-radius: 8px;
    object-fit: cover;
    background: #000;
    border: 1px solid rgba(255, 255, 255, 0.1);
}
.product-name-clamp {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 145px;
}

/* Nút Next/Prev trên Desktop */
.reels-nav-controls {
    position: absolute;
    right: max(20px, calc(50% - 300px));
    top: 50%;
    transform: translateY(-50%);
    flex-direction: column;
    gap: 12px;
    z-index: 20;
}
.btn-reel-nav {
    width: 46px;
    height: 46px;
    border-radius: 50%;
    background: #ffffff;
    border: 1.5px solid #e2e8f0;
    color: #0f172a;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 1rem;
    box-shadow: 0 6px 18px rgba(15, 23, 42, 0.08);
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}
.btn-reel-nav:hover {
    background: #4f46e5;
    border-color: #4f46e5;
    color: #ffffff;
    transform: scale(1.08) translateY(-2px);
    box-shadow: 0 10px 22px rgba(79, 70, 229, 0.3);
}

/* Floating Watch-to-Earn Ring Badge: Cố định tuyệt đối góc trên bên phải khung */
.watch-earn-floating-badge {
    position: absolute;
    top: 18px;
    right: 18px;
    z-index: 40;
    display: flex;
    flex-direction: column;
    align-items: center;
    cursor: pointer;
    background: rgba(15, 23, 42, 0.78);
    backdrop-filter: blur(14px);
    border: 1.5px solid rgba(245, 158, 11, 0.45);
    border-radius: 20px;
    padding: 6px 8px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
    pointer-events: auto;
    transition: all 0.3s ease;
}
.watch-earn-floating-badge.limit-reached {
    border-color: rgba(100, 116, 139, 0.4);
    background: rgba(15, 23, 42, 0.92);
}
.watch-earn-floating-badge.limit-reached .ring-status-text {
    color: #94a3b8 !important;
}
.ring-progress-svg-wrapper {
    position: relative;
    width: 46px;
    height: 46px;
}
.ring-svg {
    width: 100%;
    height: 100%;
    transform: rotate(-90deg);
}
.ring-bg {
    fill: none;
    stroke: rgba(255, 255, 255, 0.15);
    stroke-width: 4;
}
.ring-fill {
    fill: none;
    stroke: #f59e0b;
    stroke-width: 4;
    stroke-linecap: round;
    stroke-dasharray: 163.36;
    stroke-dashoffset: 163.36;
    transition: stroke-dashoffset 0.8s linear;
}
.gift-inner-icon {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 1.1rem;
}
.ring-status-text {
    font-size: 0.72rem;
    font-weight: 700;
    color: #f59e0b;
    margin-top: 2px;
}

/* Toast thông báo cộng Xu trôi mượt mà */
.coin-earned-toast {
    position: absolute;
    top: 80px;
    right: 18px;
    z-index: 45;
    background: rgba(15, 23, 42, 0.94);
    backdrop-filter: blur(16px);
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-left: 4px solid #4f46e5;
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.6);
    border-radius: 14px;
    padding: 10px 14px;
    pointer-events: none;
    opacity: 0;
    transform: translateY(-10px);
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}
.coin-earned-toast.show {
    opacity: 1;
    transform: translateY(0);
}

/* Modals */
.reward-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.75);
    backdrop-filter: blur(12px);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}
.reward-modal-card {
    background: #0f172a;
    border: 1px solid rgba(255, 255, 255, 0.15);
    box-shadow: 0 25px 60px rgba(0, 0, 0, 0.9);
    border-radius: 28px;
    padding: 32px 28px;
    max-width: 430px;
    width: 90%;
}
.reward-icon-box {
    font-size: 3.2rem;
    margin-bottom: 12px;
}
.reward-coupon-ticket {
    background: rgba(79, 70, 229, 0.1);
    border: 1px dashed rgba(79, 70, 229, 0.35);
    border-radius: 16px;
    padding: 16px;
}
</style>

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    const container = document.getElementById('reelsContainer');
    const slides = document.querySelectorAll('.reel-slide-item');
    const ringCircle = document.getElementById('ringProgressCircle');
    const ringTimerText = document.getElementById('ringTimerText');
    const coinModal = document.getElementById('coin-reward-modal');
    const coinToast = document.getElementById('coinEarnedToast');
    const coinsDisplay = document.getElementById('userCoinsBalanceDisplay');
    const cashBadge = document.getElementById('coinsCashValueBadge');
    const modalCoinsVal = document.getElementById('modal-current-coins-val');
    const modalCashVal = document.getElementById('modal-coins-cash-val');
    const remainingTodayDisplay = document.getElementById('coinsRemainingTodayDisplay');

    const totalSecondsNeeded = {{ $watchSeconds ?? 30 }}; // Thời gian do Admin thiết lập
    const coinRate = {{ $coinRate ?? 500 }}; // Giá trị 1 Xu do Admin thiết lập
    let secondsWatched = 0;
    let watchTimer = null;
    let isClaiming = false;
    let coinsRemainingToday = {{ $coinsRemainingToday }};
    const circumference = 2 * Math.PI * 26; // 163.36
    let isGlobalMuted = true;

    // 1. Quản lý trạng thái khi hết lượt nhận Xu
    function setLimitReachedState() {
        coinsRemainingToday = 0;
        pauseWatchTimer();
        secondsWatched = 0;
        if (ringTimerText) {
            ringTimerText.innerText = 'Hết lượt';
            ringTimerText.style.color = '#94a3b8';
        }
        if (ringCircle) {
            ringCircle.style.strokeDashoffset = '0';
            ringCircle.style.stroke = '#64748b';
        }
        const coinIcon = document.getElementById('coinStatusIcon');
        if (coinIcon) {
            coinIcon.className = 'fa-solid fa-circle-check text-success';
        }
        const badge = document.getElementById('watchEarnBadge');
        if (badge) {
            badge.classList.add('limit-reached');
            badge.title = 'Hôm nay bạn đã nhận đủ giới hạn Xu! Bấm để xem chi tiết.';
        }
    }

    // 2. Khởi động Timer tích lũy thời gian xem video
    function startWatchTimer() {
        if (coinsRemainingToday <= 0) {
            setLimitReachedState();
            return;
        }

        if (watchTimer) {
            clearInterval(watchTimer);
            watchTimer = null;
        }

        watchTimer = setInterval(() => {
            // Kiểm tra ngay trong chu kỳ lặp: nếu đã hết lượt thì dừng tuyệt đối
            if (coinsRemainingToday <= 0) {
                setLimitReachedState();
                return;
            }

            if (isClaiming) return;

            secondsWatched++;
            const remaining = Math.max(0, totalSecondsNeeded - secondsWatched);
            ringTimerText.innerText = remaining + 's';

            // Cập nhật vòng tròn tiến trình SVG
            const progress = secondsWatched / totalSecondsNeeded;
            const offset = circumference - (progress * circumference);
            ringCircle.style.strokeDashoffset = Math.max(0, offset);

            // Khi chạm 30s: Gửi yêu cầu nhận Xu và chuyển sang chu kỳ tiếp theo
            if (secondsWatched >= totalSecondsNeeded) {
                secondsWatched = 0;
                ringCircle.style.strokeDashoffset = circumference;
                claimWatchCoins();
            }
        }, 1000);
    }

    function pauseWatchTimer() {
        if (watchTimer) {
            clearInterval(watchTimer);
            watchTimer = null;
        }
    }

    // 3. Gửi API nhận Xu thưởng (Watch-to-Earn Coins)
    function claimWatchCoins() {
        if (coinsRemainingToday <= 0) {
            setLimitReachedState();
            return;
        }

        isClaiming = true;
        ringTimerText.innerText = '+1 XU!';
        
        fetch("{{ route('videos.claimReward') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(res => res.json())
        .then(res => {
            isClaiming = false;

            if (res.need_login) {
                pauseWatchTimer();
                alert(res.message);
                window.location.href = "{{ route('login') }}";
                return;
            }

            if (res.limit_reached) {
                setLimitReachedState();
                document.getElementById('rewardModalTitle').innerText = 'HẾT LƯỢT HÔM NAY!';
                document.getElementById('rewardModalSubtitle').innerText = res.message;
                coinModal.style.display = 'flex';
                return;
            }

            if (res.success) {
                coinsRemainingToday = res.coins_remaining_today;
                updateCoinsDisplay(res.total_coins, res.coins_remaining_today, res.daily_limit);
                
                // Hiển thị toast nhẹ nhàng +1 Xu không làm ngắt quãng xem video
                showCoinToast();

                if (typeof confetti === 'function') {
                    confetti({
                        particleCount: 50,
                        spread: 50,
                        origin: { y: 0.6 },
                        colors: ['#fbbf24', '#00ff87', '#ffffff']
                    });
                }

                // Nếu còn lượt nhận trong ngày, tự động đếm tiếp lần tiếp theo
                if (coinsRemainingToday > 0) {
                    ringTimerText.innerText = totalSecondsNeeded + 's';
                } else {
                    setLimitReachedState();
                    const maxCashFormatted = Number(res.daily_limit * coinRate).toLocaleString();
                    document.getElementById('rewardModalTitle').innerText = 'ĐẠT GIỚI HẠN ' + maxCashFormatted + '₫ HÔM NAY!';
                    document.getElementById('rewardModalSubtitle').innerText = 'Bạn đã nhận tối đa ' + res.daily_limit + ' Xu (' + maxCashFormatted + '₫) hôm nay! Hệ thống sẽ tự động reset sau 24h để bạn tiếp tục nhận thêm.';
                    coinModal.style.display = 'flex';
                }
            } else {
                if (coinsRemainingToday <= 0) {
                    setLimitReachedState();
                } else {
                    ringTimerText.innerText = totalSecondsNeeded + 's';
                }
            }
        })
        .catch(err => {
            isClaiming = false;
            if (coinsRemainingToday <= 0) {
                setLimitReachedState();
            } else {
                ringTimerText.innerText = totalSecondsNeeded + 's';
            }
            console.error('Error claiming reward:', err);
        });
    }

    function showCoinToast() {
        if (!coinToast) return;
        coinToast.classList.add('show');
        setTimeout(() => {
            coinToast.classList.remove('show');
        }, 3500);
    }

    function updateCoinsDisplay(coins, remainingToday, dailyLimit) {
        const formattedCoins = Number(coins).toLocaleString() + ' Xu';
        const formattedCash = '=-' + Number(coins * coinRate).toLocaleString() + '₫';

        if (coinsDisplay) coinsDisplay.innerText = formattedCoins;
        if (cashBadge) cashBadge.innerText = formattedCash;
        if (modalCoinsVal) modalCoinsVal.innerText = formattedCoins;
        if (modalCashVal) modalCashVal.innerText = 'Tương đương giảm: ' + Number(coins * coinRate).toLocaleString() + '₫ tiền mặt';
        
        if (remainingTodayDisplay && remainingToday !== undefined) {
            remainingTodayDisplay.innerText = remainingToday;
        }
    }

    window.closeCoinModal = function() {
        coinModal.style.display = 'none';
        if (coinsRemainingToday <= 0) {
            setLimitReachedState();
        }
    };

    // Bấm vào badge khi hết lượt sẽ mở popup thông báo chi tiết
    const watchEarnBadge = document.getElementById('watchEarnBadge');
    if (watchEarnBadge) {
        watchEarnBadge.addEventListener('click', function() {
            if (coinsRemainingToday <= 0) {
                document.getElementById('rewardModalTitle').innerText = 'HẾT LƯỢT NHẬN XU HÔM NAY!';
                document.getElementById('rewardModalSubtitle').innerText = 'Bạn đã nhận tối đa giới hạn Xu hôm nay. Hãy sử dụng Xu hiện có để mua hàng trừ tiền mặt ngay bây giờ hoặc quay lại sau 24h nhé!';
                coinModal.style.display = 'flex';
            }
        });
    }

    // 4. Quản lý phát video khi Scroll (Intersection Observer)
    const observerOptions = {
        root: container,
        threshold: 0.65
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            const video = entry.target.querySelector('video');
            const iframe = entry.target.querySelector('iframe');
            const playIcon = entry.target.querySelector('.reel-play-indicator');
            if (!video && !iframe) return;

            if (entry.isIntersecting) {
                entry.target.classList.add('active');
                if (video) {
                    video.muted = isGlobalMuted;
                    video.play().then(() => {
                        if (playIcon) playIcon.style.display = 'none';
                        if (coinsRemainingToday > 0) startWatchTimer();
                        else setLimitReachedState();
                    }).catch(() => {
                        video.muted = true;
                        video.play();
                        if (coinsRemainingToday > 0) startWatchTimer();
                        else setLimitReachedState();
                    });
                } else if (iframe) {
                    if (coinsRemainingToday > 0) startWatchTimer();
                    else setLimitReachedState();
                    if (!isGlobalMuted) {
                        try {
                            iframe.contentWindow.postMessage(JSON.stringify({ event: 'command', func: 'unMute' }), '*');
                            iframe.contentWindow.postMessage(JSON.stringify({ event: 'command', func: 'setVolume', args: [100] }), '*');
                        } catch (e) {}
                    }
                }
            } else {
                entry.target.classList.remove('active');
                if (video) {
                    video.pause();
                    video.currentTime = 0;
                }
                if (iframe) {
                    try {
                        iframe.contentWindow.postMessage(JSON.stringify({ event: 'command', func: 'pauseVideo' }), '*');
                    } catch (e) {}
                }
            }
        });
    }, observerOptions);

    slides.forEach(slide => observer.observe(slide));

    // 5. Click vào video để Play / Pause
    slides.forEach(slide => {
        const video = slide.querySelector('video');
        const iframe = slide.querySelector('iframe');
        const playIcon = slide.querySelector('.reel-play-indicator');
        const clickOverlay = slide.querySelector('.reel-click-overlay');
        const soundBtn = slide.querySelector('.reel-sound-toggle-btn');

        if (clickOverlay) {
            clickOverlay.addEventListener('click', function() {
                if (video) {
                    if (video.paused) {
                        video.play();
                        if (playIcon) playIcon.style.display = 'none';
                        if (coinsRemainingToday > 0) startWatchTimer();
                        else setLimitReachedState();
                    } else {
                        video.pause();
                        if (playIcon) playIcon.style.display = 'flex';
                        pauseWatchTimer();
                    }
                } else if (iframe) {
                    const isPaused = playIcon && playIcon.style.display === 'flex';
                    if (isPaused) {
                        iframe.contentWindow.postMessage('{"event":"command","func":"playVideo","args":""}', '*');
                        if (playIcon) playIcon.style.display = 'none';
                        if (coinsRemainingToday > 0) startWatchTimer();
                        else setLimitReachedState();
                    } else {
                        iframe.contentWindow.postMessage('{"event":"command","func":"pauseVideo","args":""}', '*');
                        if (playIcon) playIcon.style.display = 'flex';
                        pauseWatchTimer();
                    }
                }
            });
        }

        if (soundBtn) {
            soundBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                isGlobalMuted = !isGlobalMuted;

                // 1. Cho thẻ video HTML5 nội bộ
                document.querySelectorAll('.reel-video-element').forEach(v => {
                    v.muted = isGlobalMuted;
                });

                // 2. Cho iframe YouTube (postMessage API)
                document.querySelectorAll('.reel-youtube-iframe').forEach(iframe => {
                    try {
                        if (isGlobalMuted) {
                            iframe.contentWindow.postMessage(JSON.stringify({
                                event: 'command',
                                func: 'mute'
                            }), '*');
                        } else {
                            iframe.contentWindow.postMessage(JSON.stringify({
                                event: 'command',
                                func: 'unMute'
                            }), '*');
                            iframe.contentWindow.postMessage(JSON.stringify({
                                event: 'command',
                                func: 'setVolume',
                                args: [100]
                            }), '*');
                            iframe.contentWindow.postMessage(JSON.stringify({
                                event: 'command',
                                func: 'playVideo'
                            }), '*');
                        }
                    } catch (err) {
                        console.warn('YouTube postMessage error:', err);
                    }
                });

                // 3. Cập nhật icon trên nút âm thanh
                document.querySelectorAll('.reel-sound-toggle-btn i').forEach(icon => {
                    icon.className = isGlobalMuted ? 'fa-solid fa-volume-xmark' : 'fa-solid fa-volume-high text-white';
                });
            });
        }
    });

    // 5. Thả tim video (Like)
    document.querySelectorAll('.btn-like-video').forEach(btn => {
        btn.addEventListener('click', function() {
            const videoId = this.getAttribute('data-id');
            const countEl = this.parentElement.querySelector('.likes-count');

            this.classList.toggle('active-liked');

            fetch(`/videos/${videoId}/like`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success && countEl) {
                    countEl.innerText = Number(data.likes).toLocaleString();
                }
            })
            .catch(err => console.error(err));
        });
    });

    // 6. Nút Mũi tên Next / Prev trên Desktop
    const btnNext = document.getElementById('btnNextReel');
    const btnPrev = document.getElementById('btnPrevReel');

    if (btnNext && btnPrev) {
        btnNext.addEventListener('click', function() {
            container.scrollBy({ top: container.clientHeight, behavior: 'smooth' });
        });
        btnPrev.addEventListener('click', function() {
            container.scrollBy({ top: -container.clientHeight, behavior: 'smooth' });
        });
    }

    // Bắt đầu đếm khi vào trang (nếu còn lượt nhận)
    if (coinsRemainingToday <= 0) {
        setLimitReachedState();
    } else {
        startWatchTimer();
    }
});
</script>
@endpush
@endsection
