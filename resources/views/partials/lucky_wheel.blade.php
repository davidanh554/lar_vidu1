<!-- ============================================================
     LUCKY WHEEL FLOATING WIDGET & MODAL
     Theme: Emerald Aurora Laser & Cyberpunk Liquid Glass
     ============================================================ -->
<div id="lucky-wheel-container">
    <!-- 1. Mini Floating Widget (Góc dưới bên trái) -->
    <div id="wheel-mini-trigger" class="wheel-mini-btn" title="Vòng quay may mắn - Nhận Voucher ngay!">
        <div class="wheel-mini-inner">
            <div class="wheel-mini-spinner">
                <svg viewBox="0 0 100 100" class="wheel-svg-mini">
                    <circle cx="50" cy="50" r="46" fill="#11141c" stroke="#00ff87" stroke-width="4"/>
                    <!-- 8 Slices -->
                    <path d="M50 50 L50 4 A46 46 0 0 1 82.5 17.5 Z" fill="#059669"/>
                    <path d="M50 50 L82.5 17.5 A46 46 0 0 1 96 50 Z" fill="#1e293b"/>
                    <path d="M50 50 L96 50 A46 46 0 0 1 82.5 82.5 Z" fill="#10b981"/>
                    <path d="M50 50 L82.5 82.5 A46 46 0 0 1 50 96 Z" fill="#0f766e"/>
                    <path d="M50 50 L50 96 A46 46 0 0 1 17.5 82.5 Z" fill="#1e293b"/>
                    <path d="M50 50 L17.5 82.5 A46 46 0 0 1 4 50 Z" fill="#00ff87"/>
                    <path d="M50 50 L4 50 A46 46 0 0 1 17.5 17.5 Z" fill="#047857"/>
                    <path d="M50 50 L17.5 17.5 A46 46 0 0 1 50 4 Z" fill="#fbbf24"/>
                    <!-- Center -->
                    <circle cx="50" cy="50" r="14" fill="#11141c" stroke="#00ff87" stroke-width="2"/>
                    <polygon points="50,42 46,54 54,54" fill="#fbbf24"/>
                </svg>
            </div>
            <div class="wheel-mini-pointer">▼</div>
        </div>
        <!-- Badge số lượt quay còn lại -->
        <span id="wheel-badge-spins" class="wheel-badge">...</span>
        <div class="wheel-pulse-ring"></div>
    </div>

    <!-- 2. Modal Phóng to Vòng quay may mắn -->
    <div id="wheel-modal-overlay" class="wheel-overlay" style="display: none;">
        <div class="wheel-modal-card">
            <!-- Modal Header -->
            <div class="wheel-modal-header">
                <div class="d-flex align-items-center gap-2">
                    <span class="wheel-header-icon"><i class="fa-solid fa-dharmachakra"></i></span>
                    <div>
                        <h4 class="wheel-modal-title mb-0">VÒNG QUAY MAY MẮN</h4>
                        <small class="wheel-modal-subtitle">Quay mỗi ngày • 100% rinh mã giảm giá cực đỉnh</small>
                    </div>
                </div>
                <button type="button" id="wheel-modal-close" class="wheel-btn-close">&times;</button>
            </div>

            <!-- Modal Body -->
            <div class="wheel-modal-body">
                <div class="row g-4 align-items-center">
                    <!-- Cột 1: Vòng quay Canvas -->
                    <div class="col-lg-7 text-center">
                        <div class="wheel-board-wrapper">
                            <!-- Kim chỉ (Pointer) -->
                            <div class="wheel-pointer-arrow">
                                <i class="fa-solid fa-location-dot"></i>
                            </div>

                            <!-- Canvas vòng xoay -->
                            <canvas id="wheelCanvas" width="380" height="380"></canvas>

                            <!-- Nút QUAY ở giữa tâm -->
                            <button id="wheel-spin-btn" class="wheel-center-btn" type="button">
                                <span>QUAY</span>
                                <small>NGAY</small>
                            </button>
                        </div>
                    </div>

                    <!-- Cột 2: Thông tin lượt quay & Kho Voucher -->
                    <div class="col-lg-5">
                        <div class="wheel-panel-info">
                            <!-- Thẻ Lượt quay hôm nay -->
                            <div class="wheel-info-card mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="text-white-50 small">Lượt quay hôm nay:</span>
                                    <span id="wheel-spins-left" class="fw-bold fs-5 text-emerald-laser">1 lượt</span>
                                </div>
                                <div class="progress wheel-progress mb-2">
                                    <div id="wheel-spins-bar" class="progress-bar bg-emerald-laser" style="width: 100%;"></div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center text-white-50" style="font-size: 0.76rem;">
                                    <span><i class="fa-regular fa-clock me-1"></i> Reset sau 24h:</span>
                                    <span id="wheel-countdown" class="font-monospace text-warning fw-semibold">--:--:--</span>
                                </div>
                            </div>

                            <!-- Tabs Chuyển đổi: Thể lệ / Kho Voucher -->
                            <ul class="nav nav-pills wheel-nav-tabs mb-3" id="wheelTabs" role="tablist">
                                <li class="nav-item flex-grow-1" role="presentation">
                                    <button class="nav-link active w-100 py-1" id="pills-prizes-tab" data-bs-toggle="pill" data-bs-target="#pills-prizes" type="button">
                                        <i class="fa-solid fa-gift me-1"></i> Giải thưởng
                                    </button>
                                </li>
                                <li class="nav-item flex-grow-1" role="presentation">
                                    <button class="nav-link w-100 py-1" id="pills-coupons-tab" data-bs-toggle="pill" data-bs-target="#pills-coupons" type="button">
                                        <i class="fa-solid fa-ticket me-1"></i> Kho voucher (<span id="coupon-count-badge">0</span>)
                                    </button>
                                </li>
                            </ul>

                            <div class="tab-content" id="wheelTabsContent">
                                <!-- Tab 1: Danh sách giải thưởng -->
                                <div class="tab-pane fade show active" id="pills-prizes" role="tabpanel">
                                    <div class="wheel-prizes-list">
                                        <div class="prize-pill-item"><span class="badge-dot dot-gold"></span> Voucher Siêu Cấp 100.000₫</div>
                                        <div class="prize-pill-item"><span class="badge-dot dot-emerald"></span> Voucher Giảm 10% (Tối đa 100k)</div>
                                        <div class="prize-pill-item"><span class="badge-dot dot-green"></span> Voucher Giảm 50.000₫</div>
                                        <div class="prize-pill-item"><span class="badge-dot dot-neon"></span> Miễn phí vận chuyển 30.000₫</div>
                                        <div class="prize-pill-item"><span class="badge-dot dot-teal"></span> Voucher Giảm 20.000₫ & 30.000₫</div>
                                    </div>
                                    <div class="wheel-rule-note mt-3">
                                        <i class="fa-solid fa-circle-info text-emerald-laser me-1"></i>
                                        <span>Lượt quay được cấp tự động mỗi 24h. Mã trúng thưởng lưu trực tiếp vào tài khoản!</span>
                                    </div>
                                </div>

                                <!-- Tab 2: Kho Voucher của tôi -->
                                <div class="tab-pane fade" id="pills-coupons" role="tabpanel">
                                    <div id="wheel-my-coupons" class="wheel-my-coupons-list">
                                        <div class="text-center text-muted py-3 small">
                                            <i class="fa-solid fa-spinner fa-spin me-1"></i> Đang tải kho voucher...
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Popup Thông báo trúng thưởng -->
    <div id="wheel-win-overlay" class="wheel-win-modal" style="display: none;">
        <div class="wheel-win-card text-center">
            <h3 id="win-title" class="fw-bold text-white mb-2">CHÚC MỪNG BẠN!</h3>
            <p id="win-message" class="text-light mb-3">Bạn đã quay trúng phần thưởng tuyệt vời!</p>

            <div id="win-coupon-box" class="win-coupon-ticket mb-3" style="display: none;">
                <div class="win-coupon-label">MÃ GIẢM GIÁ CỦA BẠN</div>
                <div id="win-coupon-code" class="win-coupon-code">VUA50K</div>
                <div id="win-coupon-desc" class="win-coupon-desc">Giảm ngay 50.000đ cho đơn hàng</div>
                <button type="button" class="btn btn-sm btn-outline-warning rounded-pill mt-2" onclick="copyWinCode()">
                    <i class="fa-regular fa-copy me-1"></i> Sao chép mã
                </button>
            </div>

            <div class="d-flex justify-content-center gap-2 mt-4">
                <button type="button" class="btn btn-emerald-glow rounded-pill px-4" onclick="closeWinModal()">
                    Tuyệt vời!
                </button>
                <a href="{{ route('home') }}" class="btn btn-outline-light rounded-pill px-3">
                    Mua sắm ngay
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Canvas Confetti Library for victory fireworks -->
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>

<!-- CSS của Vòng quay may mắn (Emerald Aurora Laser Theme) -->
<style>
/* 1. Floating Mini Trigger ở góc dưới bên trái */
.wheel-mini-btn {
    position: fixed;
    bottom: 24px;
    left: 24px;
    z-index: 9998;
    width: 68px;
    height: 68px;
    cursor: pointer;
    user-select: none;
    transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.wheel-mini-btn:hover {
    transform: scale(1.1) translateY(-4px);
}
.wheel-mini-inner {
    position: relative;
    width: 100%;
    height: 100%;
    border-radius: 50%;
    background: #11141c;
    border: 3px solid #00ff87;
    box-shadow: 0 0 25px rgba(0, 255, 135, 0.55), 0 10px 25px rgba(0, 0, 0, 0.6);
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}
.wheel-mini-spinner {
    width: 100%;
    height: 100%;
    animation: spinMiniSlow 14s linear infinite;
}
.wheel-mini-btn:hover .wheel-mini-spinner {
    animation-duration: 4s;
}
@keyframes spinMiniSlow {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
.wheel-svg-mini {
    width: 100%;
    height: 100%;
    display: block;
}
.wheel-mini-pointer {
    position: absolute;
    top: 2px;
    left: 50%;
    transform: translateX(-50%);
    font-size: 13px;
    color: #fbbf24;
    text-shadow: 0 2px 4px rgba(0,0,0,0.8);
    pointer-events: none;
}
.wheel-badge {
    position: absolute;
    top: -4px;
    right: -4px;
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: #ffffff;
    font-size: 0.72rem;
    font-weight: 800;
    padding: 2px 7px;
    border-radius: 9999px;
    box-shadow: 0 0 12px rgba(239, 68, 68, 0.7);
    border: 2px solid #11141c;
    animation: badgePulse 2s infinite;
}
@keyframes badgePulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.15); }
}
.wheel-pulse-ring {
    position: absolute;
    top: -5px;
    left: -5px;
    right: -5px;
    bottom: -5px;
    border-radius: 50%;
    border: 2px solid rgba(0, 255, 135, 0.6);
    animation: ringGlow 2.5s ease-out infinite;
    pointer-events: none;
}
@keyframes ringGlow {
    0% { transform: scale(0.9); opacity: 1; }
    100% { transform: scale(1.3); opacity: 0; }
}

/* 2. Modal Phóng to (Liquid Glass Theme) */
.wheel-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(10, 14, 18, 0.85);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    z-index: 10000;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 16px;
    animation: fadeInModal 0.25s ease-out;
}
@keyframes fadeInModal {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
}
.wheel-modal-card {
    background: linear-gradient(145deg, rgba(20, 28, 28, 0.95) 0%, rgba(13, 17, 22, 0.98) 100%);
    border: 1px solid rgba(0, 255, 135, 0.4);
    box-shadow: 0 25px 60px rgba(0, 0, 0, 0.8), 0 0 35px rgba(0, 255, 135, 0.25);
    border-radius: 26px;
    width: 100%;
    max-width: 860px;
    overflow: hidden;
    color: #f8fafc;
}
.wheel-modal-header {
    padding: 18px 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    background: rgba(0, 255, 135, 0.04);
}
.wheel-header-icon {
    width: 42px;
    height: 42px;
    border-radius: 12px;
    background: linear-gradient(135deg, #059669 0%, #00ff87 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    color: #064e3b;
    box-shadow: 0 0 15px rgba(0, 255, 135, 0.5);
}
.wheel-modal-title {
    font-weight: 800;
    letter-spacing: 0.5px;
    background: linear-gradient(135deg, #ffffff 0%, #00ff87 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    font-size: 1.25rem;
}
.wheel-modal-subtitle {
    color: rgba(255, 255, 255, 0.6);
    font-size: 0.8rem;
}
.wheel-btn-close {
    background: transparent;
    border: none;
    color: #94a3b8;
    font-size: 2rem;
    line-height: 1;
    cursor: pointer;
    transition: color 0.2s, transform 0.2s;
}
.wheel-btn-close:hover {
    color: #00ff87;
    transform: rotate(90deg);
}

.wheel-modal-body {
    padding: 24px;
}
.wheel-board-wrapper {
    position: relative;
    display: inline-block;
    padding: 12px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(0, 255, 135, 0.15) 0%, rgba(17, 20, 28, 0.8) 70%);
    box-shadow: 0 0 35px rgba(0, 255, 135, 0.2), inset 0 0 20px rgba(0, 0, 0, 0.6);
}
#wheelCanvas {
    display: block;
    max-width: 100%;
    height: auto;
    border-radius: 50%;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.7);
}
.wheel-pointer-arrow {
    position: absolute;
    top: 2px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 20;
    font-size: 34px;
    color: #fbbf24;
    filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.7)) drop-shadow(0 0 8px rgba(251, 191, 36, 0.8));
    pointer-events: none;
}
.wheel-center-btn {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 76px;
    height: 76px;
    border-radius: 50%;
    background: linear-gradient(135deg, #059669 0%, #10b981 50%, #00ff87 100%);
    border: 4px solid #11141c;
    box-shadow: 0 0 25px rgba(0, 255, 135, 0.7), inset 0 2px 4px rgba(255, 255, 255, 0.5);
    color: #064e3b;
    font-weight: 900;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;
    z-index: 15;
    outline: none;
}
.wheel-center-btn span {
    font-size: 1.05rem;
    line-height: 1;
}
.wheel-center-btn small {
    font-size: 0.68rem;
    letter-spacing: 0.5px;
}
.wheel-center-btn:hover:not(:disabled) {
    transform: translate(-50%, -50%) scale(1.08);
    box-shadow: 0 0 35px rgba(0, 255, 135, 0.95);
}
.wheel-center-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    filter: grayscale(0.5);
}

/* Panel Info bên phải */
.wheel-panel-info {
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 20px;
    padding: 18px;
}
.wheel-info-card {
    background: rgba(0, 255, 135, 0.05);
    border: 1px solid rgba(0, 255, 135, 0.2);
    border-radius: 14px;
    padding: 12px 14px;
}
.text-emerald-laser {
    color: #00ff87 !important;
}
.bg-emerald-laser {
    background: linear-gradient(90deg, #059669 0%, #00ff87 100%) !important;
}
.wheel-progress {
    height: 6px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 999px;
}
.wheel-nav-tabs .nav-link {
    color: #94a3b8;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 10px;
    font-size: 0.85rem;
    font-weight: 600;
    transition: all 0.2s;
}
.wheel-nav-tabs .nav-link.active {
    color: #064e3b;
    background: linear-gradient(135deg, #10b981 0%, #00ff87 100%);
    box-shadow: 0 0 15px rgba(0, 255, 135, 0.35);
}

/* Danh sách giải thưởng */
.wheel-prizes-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.prize-pill-item {
    font-size: 0.82rem;
    color: #cbd5e1;
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 6px 10px;
    background: rgba(255, 255, 255, 0.02);
    border-radius: 8px;
    border-left: 3px solid #00ff87;
}
.badge-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    display: inline-block;
}
.dot-gold { background: #fbbf24; box-shadow: 0 0 6px #fbbf24; }
.dot-emerald { background: #10b981; }
.dot-green { background: #059669; }
.dot-neon { background: #00ff87; box-shadow: 0 0 6px #00ff87; }
.dot-teal { background: #0f766e; }

.wheel-rule-note {
    font-size: 0.75rem;
    color: #94a3b8;
    line-height: 1.4;
    padding: 8px;
    background: rgba(0, 255, 135, 0.03);
    border-radius: 8px;
}

/* Kho Voucher */
.wheel-my-coupons-list {
    max-height: 240px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 8px;
    padding-right: 4px;
}
.voucher-mini-card {
    background: rgba(255, 255, 255, 0.04);
    border: 1px dashed rgba(0, 255, 135, 0.4);
    border-radius: 10px;
    padding: 10px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.voucher-mini-code {
    font-family: monospace;
    font-size: 0.92rem;
    font-weight: 800;
    color: #00ff87;
    letter-spacing: 0.5px;
}
.btn-copy-code {
    background: rgba(0, 255, 135, 0.15);
    border: 1px solid rgba(0, 255, 135, 0.4);
    color: #00ff87;
    font-size: 0.72rem;
    padding: 3px 8px;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s;
}
.btn-copy-code:hover {
    background: #00ff87;
    color: #064e3b;
}

/* 3. Modal Chiến Thắng (Win Modal) */
.wheel-win-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(0, 0, 0, 0.85);
    backdrop-filter: blur(20px);
    z-index: 10005;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 16px;
    animation: fadeInModal 0.3s ease;
}
.wheel-win-card {
    background: linear-gradient(145deg, #182420 0%, #0d1315 100%);
    border: 2px solid #00ff87;
    box-shadow: 0 0 50px rgba(0, 255, 135, 0.45);
    border-radius: 24px;
    width: 100%;
    max-width: 440px;
    padding: 32px 24px;
}
.wheel-confetti-icon {
    font-size: 3.5rem;
    line-height: 1;
    margin-bottom: 12px;
    animation: bounceIcon 1s infinite alternate;
}
@keyframes bounceIcon {
    from { transform: translateY(0); }
    to { transform: translateY(-8px); }
}
.win-coupon-ticket {
    background: rgba(0, 0, 0, 0.5);
    border: 2px dashed #fbbf24;
    border-radius: 14px;
    padding: 14px;
}
.win-coupon-label {
    font-size: 0.72rem;
    color: #fbbf24;
    font-weight: 700;
    letter-spacing: 1px;
}
.win-coupon-code {
    font-family: monospace;
    font-size: 1.45rem;
    font-weight: 900;
    color: #00ff87;
    margin: 4px 0;
}
.win-coupon-desc {
    font-size: 0.85rem;
    color: #cbd5e1;
}
.btn-emerald-glow {
    background: linear-gradient(135deg, rgba(0, 245, 155, 0.18) 0%, rgba(16, 185, 129, 0.12) 100%) !important;
    color: #00f59b !important;
    font-weight: 700;
    border: 1px solid rgba(0, 245, 155, 0.45) !important;
    border-top: 1.5px solid rgba(255, 255, 255, 0.55) !important;
    box-shadow: inset 0 1px 1px rgba(255, 255, 255, 0.4) !important;
    backdrop-filter: blur(16px) !important;
    -webkit-backdrop-filter: blur(16px) !important;
    transition: all 0.28s cubic-bezier(0.16, 1, 0.3, 1);
}
.btn-emerald-glow:hover {
    background: linear-gradient(135deg, #00f59b 0%, #00d674 100%) !important;
    border-color: rgba(255, 255, 255, 0.9) !important;
    color: #022c16 !important;
    box-shadow: 0 0 28px rgba(0, 245, 155, 0.95), 0 0 50px rgba(0, 214, 116, 0.55), inset 0 1px 2px rgba(255, 255, 255, 0.85) !important;
    transform: translateY(-2px) scale(1.02);
}

@media (max-width: 768px) {
    .wheel-modal-card {
        max-height: 90vh;
        overflow-y: auto;
    }
    .wheel-board-wrapper {
        transform: scale(0.85);
    }
}
</style>

<!-- JavaScript Xử lý Vòng quay may mắn -->
<script>
(function() {
    let wheelStatus = null;
    let wheelSlices = [];
    let currentRotation = 0;
    let isSpinning = false;
    let countdownInterval = null;

    const miniBtn = document.getElementById('wheel-mini-trigger');
    const modalOverlay = document.getElementById('wheel-modal-overlay');
    const modalCloseBtn = document.getElementById('wheel-modal-close');
    const spinBtn = document.getElementById('wheel-spin-btn');
    const canvas = document.getElementById('wheelCanvas');
    const ctx = canvas.getContext('2d');

    // Mở / Đóng Modal
    miniBtn.addEventListener('click', function() {
        modalOverlay.style.display = 'flex';
        fetchWheelStatus();
    });

    modalCloseBtn.addEventListener('click', function() {
        modalOverlay.style.display = 'none';
    });

    modalOverlay.addEventListener('click', function(e) {
        if (e.target === modalOverlay && !isSpinning) {
            modalOverlay.style.display = 'none';
        }
    });

    // 1. Tải trạng thái lượt quay & voucher từ server
    function fetchWheelStatus() {
        fetch('{{ route('luckywheel.status') }}')
            .then(res => res.json())
            .then(data => {
                wheelStatus = data;
                wheelSlices = data.slices || [];

                // Cập nhật giao diện
                updateUIWithStatus(data);
                drawWheel(currentRotation);
            })
            .catch(err => {
                console.error('Error fetching wheel status:', err);
            });
    }

    // 2. Cập nhật UI
    function updateUIWithStatus(data) {
        const badge = document.getElementById('wheel-badge-spins');
        const spinsLeftEl = document.getElementById('wheel-spins-left');
        const spinsBarEl = document.getElementById('wheel-spins-bar');
        const couponBadge = document.getElementById('coupon-count-badge');

        if (!data.logged_in) {
            badge.innerText = 'Đăng nhập';
            spinsLeftEl.innerHTML = '<a href="{{ route('login') }}" class="text-warning text-decoration-none small">Đăng nhập để quay</a>';
            spinBtn.disabled = false;
            return;
        }

        const spinsLeft = data.spins_left ?? 0;
        const dailyLimit = data.daily_limit ?? 1;

        badge.innerText = spinsLeft + ' lượt';
        spinsLeftEl.innerText = spinsLeft + ' lượt';

        const percent = dailyLimit > 0 ? (spinsLeft / dailyLimit) * 100 : 0;
        spinsBarEl.style.width = percent + '%';

        // Voucher count
        const coupons = data.my_coupons || [];
        couponBadge.innerText = coupons.length;
        renderMyCoupons(coupons);

        // Start countdown timer
        startCountdown(data.seconds_until_reset);
    }

    // 3. Render danh sách voucher của tôi
    function renderMyCoupons(coupons) {
        const container = document.getElementById('wheel-my-coupons');
        if (!coupons || coupons.length === 0) {
            container.innerHTML = '<div class="text-center text-muted py-4 small">Bạn chưa có voucher nào. Hãy quay ngay để nhận thưởng!</div>';
            return;
        }

        let html = '';
        coupons.forEach(c => {
            const valText = c.type === 'percent' ? `Giảm ${c.value}%` : `Giảm ${Number(c.value).toLocaleString()}₫`;
            const minText = c.min_order_value > 0 ? `Đơn từ ${Number(c.min_order_value).toLocaleString()}₫` : 'Không giới hạn đơn';
            html += `
                <div class="voucher-mini-card">
                    <div>
                        <div class="voucher-mini-code">${c.code}</div>
                        <div class="small text-light">${valText} • <span class="text-white-50">${minText}</span></div>
                    </div>
                    <button type="button" class="btn-copy-code" onclick="navigator.clipboard.writeText('${c.code}'); alert('Đã sao chép mã: ${c.code}');">
                        <i class="fa-regular fa-copy me-1"></i> Copy
                    </button>
                </div>
            `;
        });
        container.innerHTML = html;
    }

    // 4. Đồng hồ đếm ngược 24h
    function startCountdown(totalSeconds) {
        if (countdownInterval) clearInterval(countdownInterval);
        let remain = totalSeconds || 0;

        function update() {
            if (remain <= 0) {
                document.getElementById('wheel-countdown').innerText = '00:00:00 (Sẵn sàng)';
                return;
            }
            const h = Math.floor(remain / 3600).toString().padStart(2, '0');
            const m = Math.floor((remain % 3600) / 60).toString().padStart(2, '0');
            const s = Math.floor(remain % 60).toString().padStart(2, '0');
            document.getElementById('wheel-countdown').innerText = `${h}:${m}:${s}`;
            remain--;
        }
        update();
        countdownInterval = setInterval(update, 1000);
    }

    // 5. Vẽ Canvas Vòng quay sắc nét
    function drawWheel(angle) {
        if (!wheelSlices || wheelSlices.length === 0) return;

        const numSlices = wheelSlices.length;
        const sliceAngle = (2 * Math.PI) / numSlices;
        const centerX = canvas.width / 2;
        const centerY = canvas.height / 2;
        const radius = centerX - 12;

        ctx.clearRect(0, 0, canvas.width, canvas.height);

        // Vòng viền ngoài phát sáng
        ctx.save();
        ctx.beginPath();
        ctx.arc(centerX, centerY, radius + 8, 0, 2 * Math.PI);
        ctx.strokeStyle = '#00ff87';
        ctx.lineWidth = 6;
        ctx.shadowColor = '#00ff87';
        ctx.shadowBlur = 18;
        ctx.stroke();
        ctx.restore();

        // Vẽ từng lát cắt
        for (let i = 0; i < numSlices; i++) {
            const startAngle = angle + i * sliceAngle;
            const endAngle = startAngle + sliceAngle;
            const slice = wheelSlices[i];

            ctx.save();
            ctx.beginPath();
            ctx.moveTo(centerX, centerY);
            ctx.arc(centerX, centerY, radius, startAngle, endAngle);
            ctx.closePath();

            ctx.fillStyle = slice.color || '#10b981';
            ctx.fill();

            // Viền ngăn cách giữa các lát
            ctx.strokeStyle = 'rgba(255, 255, 255, 0.15)';
            ctx.lineWidth = 1.5;
            ctx.stroke();

            // Vẽ chữ trên lát cắt
            ctx.save();
            ctx.translate(centerX, centerY);
            ctx.rotate(startAngle + sliceAngle / 2);
            ctx.textAlign = 'right';
            ctx.fillStyle = slice.textColor || '#ffffff';
            ctx.font = 'bold 13px Plus Jakarta Sans, sans-serif';
            ctx.shadowColor = 'rgba(0, 0, 0, 0.8)';
            ctx.shadowBlur = 4;

            // Cắt ngắn nếu chữ dài
            let text = slice.name;
            if (text.length > 18) {
                text = text.substring(0, 16) + '...';
            }
            ctx.fillText(text, radius - 24, 5);

            ctx.restore();
            ctx.restore();
        }

        // Đèn LED xung quanh viền
        const numLeds = 24;
        for (let i = 0; i < numLeds; i++) {
            const ledAngle = (i * 2 * Math.PI) / numLeds;
            const lx = centerX + (radius + 4) * Math.cos(ledAngle);
            const ly = centerY + (radius + 4) * Math.sin(ledAngle);

            ctx.beginPath();
            ctx.arc(lx, ly, 3, 0, 2 * Math.PI);
            ctx.fillStyle = i % 2 === 0 ? '#fbbf24' : '#00ff87';
            ctx.fill();
        }
    }

    // 6. Xử lý Quay thưởng khi click nút QUAY
    spinBtn.addEventListener('click', function() {
        if (isSpinning) return;

        if (!wheelStatus || !wheelStatus.logged_in) {
            alert('Vui lòng đăng nhập tài khoản để tham gia Vòng quay may mắn!');
            window.location.href = '{{ route('login') }}';
            return;
        }

        if (wheelStatus.spins_left <= 0) {
            alert(wheelStatus.message || 'Bạn đã hết lượt quay hôm nay. Vui lòng đợi hết thời gian đếm ngược 24h!');
            return;
        }

        // Gọi Server để quyết định kết quả bảo mật
        isSpinning = true;
        spinBtn.disabled = true;

        fetch('{{ route('luckywheel.spin') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(res => res.json())
        .then(res => {
            if (!res.success) {
                alert(res.message || 'Có lỗi xảy ra, vui lòng thử lại!');
                isSpinning = false;
                spinBtn.disabled = false;
                return;
            }

            // Server trả về index ô trúng thưởng: res.slice_index (0 -> 7)
            const targetSlice = res.slice_index;
            const numSlices = wheelSlices.length;
            const sliceAngle = (2 * Math.PI) / numSlices;

            // Kim chỉ nằm ở đỉnh (góc 3*PI/2 = 270 độ = -90 độ).
            // Để ô trúng dừng đúng kim chỉ:
            const targetAngleAtPointer = (3 * Math.PI / 2) - (targetSlice * sliceAngle + sliceAngle / 2);

            // Quay ít nhất 5 vòng đầy đủ (5 * 2 * PI) để tạo kịch tính
            const extraRounds = 5 * (2 * Math.PI);
            
            // Tính góc quay cuối cùng
            const finalRotation = currentRotation + extraRounds + ((targetAngleAtPointer - (currentRotation % (2 * Math.PI)) + 2 * Math.PI) % (2 * Math.PI));

            // Animation quay mượt mà bằng RequestAnimationFrame
            const duration = 5000; // 5 giây
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
                drawWheel(currentRotation);

                if (progress < 1) {
                    requestAnimationFrame(animate);
                } else {
                    // Dừng quay hoàn tất
                    isSpinning = false;
                    spinBtn.disabled = false;

                    // Cập nhật lại số lượt quay
                    wheelStatus.spins_left = res.spins_left;
                    updateUIWithStatus(wheelStatus);

                    // Hiển thị phần thưởng
                    showWinModal(res);
                }
            }

            requestAnimationFrame(animate);
        })
        .catch(err => {
            console.error('Spin error:', err);
            isSpinning = false;
            spinBtn.disabled = false;
        });
    });

    // 7. Hiển thị Popup chúc mừng trúng thưởng
    function showWinModal(result) {
        const winOverlay = document.getElementById('wheel-win-overlay');
        const titleEl = document.getElementById('win-title');
        const msgEl = document.getElementById('win-message');
        const couponBox = document.getElementById('win-coupon-box');

        const prize = result.prize;
        const coupon = result.awarded_coupon;

        if (prize.type === 'coupon' && coupon) {
            titleEl.innerText = 'CHÚC MỪNG BẠN TRÚNG THƯỞNG!';
            msgEl.innerText = `Bạn vừa quay trúng: ${prize.name}! Mã giảm giá đã được lưu vào kho voucher của bạn.`;
            document.getElementById('win-coupon-code').innerText = coupon.code;
            const minText = coupon.min_order_value > 0 ? `Áp dụng cho đơn từ ${Number(coupon.min_order_value).toLocaleString()}₫` : 'Áp dụng cho mọi đơn hàng';
            document.getElementById('win-coupon-desc').innerText = `${prize.name} • ${minText}`;
            couponBox.style.display = 'block';

            // Pháo hoa rực rỡ sắc màu Emerald & Gold
            if (typeof confetti === 'function') {
                confetti({
                    particleCount: 100,
                    spread: 70,
                    origin: { y: 0.6 },
                    colors: ['#00ff87', '#10b981', '#fbbf24', '#ffffff']
                });
            }
        } else {
            titleEl.innerText = 'CHÚC BẠN MAY MẮN LẦN SAU!';
            msgEl.innerText = 'Đừng buồn nhé! Bạn sẽ được nhận thêm lượt quay miễn phí vào ngày mai.';
            couponBox.style.display = 'none';
        }

        winOverlay.style.display = 'flex';
    }

    // Đóng Popup chiến thắng
    window.closeWinModal = function() {
        document.getElementById('wheel-win-overlay').style.display = 'none';
        fetchWheelStatus(); // Tải lại danh sách voucher
    };

    window.copyWinCode = function() {
        const code = document.getElementById('win-coupon-code').innerText;
        navigator.clipboard.writeText(code).then(() => {
            alert('Đã sao chép mã giảm giá: ' + code);
        });
    };

    // Khởi tạo ngay khi tải trang
    document.addEventListener('DOMContentLoaded', function() {
        fetchWheelStatus();
    });
})();
</script>
