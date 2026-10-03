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
                    <circle cx="50" cy="50" r="46" fill="#ffffff" stroke="#4f46e5" stroke-width="4"/>
                    <!-- 8 Slices -->
                    <path d="M50 50 L50 4 A46 46 0 0 1 82.5 17.5 Z" fill="#4f46e5"/>
                    <path d="M50 50 L82.5 17.5 A46 46 0 0 1 96 50 Z" fill="#1e293b"/>
                    <path d="M50 50 L96 50 A46 46 0 0 1 82.5 82.5 Z" fill="#0284c7"/>
                    <path d="M50 50 L82.5 82.5 A46 46 0 0 1 50 96 Z" fill="#6366f1"/>
                    <path d="M50 50 L50 96 A46 46 0 0 1 17.5 82.5 Z" fill="#334155"/>
                    <path d="M50 50 L17.5 82.5 A46 46 0 0 1 4 50 Z" fill="#10b981"/>
                    <path d="M50 50 L4 50 A46 46 0 0 1 17.5 17.5 Z" fill="#7c3aed"/>
                    <path d="M50 50 L17.5 17.5 A46 46 0 0 1 50 4 Z" fill="#f59e0b"/>
                    <!-- Center -->
                    <circle cx="50" cy="50" r="14" fill="#ffffff" stroke="#4f46e5" stroke-width="2"/>
                    <polygon points="50,42 46,54 54,54" fill="#4f46e5"/>
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
                <div>
                    <h4 class="wheel-modal-title mb-0">VÒNG QUAY MAY MẮN</h4>
                    <small class="wheel-modal-subtitle">Quay mỗi ngày • 100% rinh mã giảm giá cực đỉnh</small>
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
                                    <span class="text-muted small">Lượt quay hôm nay:</span>
                                    <span id="wheel-spins-left" class="fw-bold fs-5 text-emerald-laser">1 lượt</span>
                                </div>
                                <div class="progress wheel-progress mb-2">
                                    <div id="wheel-spins-bar" class="progress-bar bg-emerald-laser" style="width: 100%;"></div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center text-muted" style="font-size: 0.76rem;">
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
                <button type="button" class="btn btn-modern-primary rounded-pill px-4" onclick="closeWinModal()">
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
    background: #ffffff;
    border: 3px solid #4f46e5;
    box-shadow: 0 4px 20px rgba(79, 70, 229, 0.4), 0 2px 8px rgba(0, 0, 0, 0.08);
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
    color: #f59e0b;
    text-shadow: 0 1px 3px rgba(0,0,0,0.4);
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
    box-shadow: 0 2px 8px rgba(239, 68, 68, 0.5);
    border: 2px solid #ffffff;
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
    border: 2px solid rgba(79, 70, 229, 0.5);
    animation: ringGlow 2.5s ease-out infinite;
    pointer-events: none;
}
@keyframes ringGlow {
    0% { transform: scale(0.9); opacity: 1; }
    100% { transform: scale(1.3); opacity: 0; }
}

/* 2. Modal Phóng to (Clean Modern Light Theme) */
.wheel-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(15, 23, 42, 0.65);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
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
    background: #ffffff;
    border: 1px solid #e2e8f0;
    box-shadow: 0 25px 60px -15px rgba(15, 23, 42, 0.25), 0 0 35px rgba(79, 70, 229, 0.15);
    border-radius: 26px;
    width: 100%;
    max-width: 860px;
    overflow: hidden;
    color: #0f172a;
}
.wheel-modal-header {
    padding: 18px 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #f1f5f9;
    background: #f8fafc;
}
.wheel-header-icon {
    width: 42px;
    height: 42px;
    border-radius: 12px;
    background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    color: #ffffff;
    box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
}
.wheel-modal-title {
    font-weight: 800;
    letter-spacing: -0.02em;
    color: #0f172a;
    font-size: 1.25rem;
}
.wheel-modal-subtitle {
    color: #64748b;
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
    color: #4f46e5;
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
    background: radial-gradient(circle, rgba(79, 70, 229, 0.08) 0%, rgba(241, 245, 249, 0.9) 70%);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06), inset 0 0 15px rgba(79, 70, 229, 0.1);
}
#wheelCanvas {
    display: block;
    max-width: 100%;
    height: auto;
    border-radius: 50%;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
}
.wheel-pointer-arrow {
    position: absolute;
    top: 2px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 20;
    font-size: 34px;
    color: #f59e0b;
    filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.3));
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
    background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
    border: 4px solid #ffffff;
    box-shadow: 0 6px 20px rgba(79, 70, 229, 0.5), inset 0 2px 4px rgba(255, 255, 255, 0.5);
    color: #ffffff;
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
    box-shadow: 0 8px 28px rgba(79, 70, 229, 0.7);
}
.wheel-center-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    filter: grayscale(0.5);
}

/* Panel Info bên phải */
.wheel-panel-info {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    padding: 18px;
}
.wheel-info-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 12px 14px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
}
.text-emerald-laser {
    color: #4f46e5 !important;
}
.bg-emerald-laser {
    background: linear-gradient(90deg, #4f46e5 0%, #6366f1 100%) !important;
}
.wheel-progress {
    height: 6px;
    background: #e2e8f0;
    border-radius: 999px;
}
.wheel-nav-tabs .nav-link {
    color: #64748b;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    font-size: 0.85rem;
    font-weight: 600;
    transition: all 0.2s;
}
.wheel-nav-tabs .nav-link.active {
    color: #ffffff;
    background: #4f46e5;
    border-color: #4f46e5;
    box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
}

/* Danh sách giải thưởng */
.wheel-prizes-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.prize-pill-item {
    font-size: 0.82rem;
    color: #334155;
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 7px 12px;
    background: #ffffff;
    border-radius: 8px;
    border: 1px solid #f1f5f9;
    border-left: 3px solid #4f46e5;
}
.badge-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    display: inline-block;
}
.dot-gold { background: #f59e0b; box-shadow: 0 0 6px #f59e0b; }
.dot-emerald { background: #4f46e5; }
.dot-green { background: #0284c7; }
.dot-neon { background: #10b981; }
.dot-teal { background: #7c3aed; }

.wheel-rule-note {
    font-size: 0.75rem;
    color: #64748b;
    line-height: 1.4;
    padding: 10px;
    background: #eef2ff;
    border-radius: 8px;
    border: 1px solid #e0e7ff;
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
    background: #ffffff;
    border: 1px dashed #cbd5e1;
    border-radius: 10px;
    padding: 10px 14px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.voucher-mini-code {
    font-family: monospace;
    font-size: 0.92rem;
    font-weight: 800;
    color: #4f46e5;
    letter-spacing: 0.5px;
}
.btn-copy-code {
    background: #eef2ff;
    border: 1px solid #c7d2fe;
    color: #4f46e5;
    font-size: 0.75rem;
    font-weight: 600;
    padding: 4px 10px;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s;
}
.btn-copy-code:hover {
    background: #4f46e5;
    color: #ffffff;
}

/* 3. Modal Chiến Thắng (Win Modal) */
.wheel-win-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(15, 23, 42, 0.75);
    backdrop-filter: blur(16px);
    z-index: 10005;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 16px;
    animation: fadeInModal 0.3s ease;
}
.wheel-win-card {
    background: #ffffff;
    border: 2px solid #4f46e5;
    box-shadow: 0 25px 60px rgba(0, 0, 0, 0.3), 0 0 35px rgba(79, 70, 229, 0.25);
    border-radius: 24px;
    width: 100%;
    max-width: 440px;
    padding: 32px 24px;
    color: #0f172a;
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
    background: #f8fafc;
    border: 2px dashed #f59e0b;
    border-radius: 14px;
    padding: 14px;
}
.win-coupon-label {
    font-size: 0.72rem;
    color: #d97706;
    font-weight: 700;
    letter-spacing: 1px;
}
.win-coupon-code {
    font-family: monospace;
    font-size: 1.45rem;
    font-weight: 900;
    color: #4f46e5;
    margin: 4px 0;
}
.win-coupon-desc {
    font-size: 0.85rem;
    color: #475569;
}
.btn-emerald-glow {
    background: #4f46e5 !important;
    color: #ffffff !important;
    font-weight: 700;
    border: none !important;
    box-shadow: 0 4px 15px rgba(79, 70, 229, 0.4) !important;
    transition: all 0.2s ease;
}
.btn-emerald-glow:hover {
    background: #4338ca !important;
    color: #ffffff !important;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(79, 70, 229, 0.5) !important;
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
                        <div class="small text-muted">${valText} • <span class="text-secondary">${minText}</span></div>
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
        ctx.strokeStyle = '#4f46e5';
        ctx.lineWidth = 6;
        ctx.shadowColor = 'rgba(79, 70, 229, 0.45)';
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

            ctx.fillStyle = slice.color || '#4f46e5';
            ctx.fill();

            // Viền ngăn cách giữa các lát
            ctx.strokeStyle = 'rgba(255, 255, 255, 0.25)';
            ctx.lineWidth = 1.5;
            ctx.stroke();

            // Vẽ chữ trên lát cắt
            ctx.save();
            ctx.translate(centerX, centerY);
            ctx.rotate(startAngle + sliceAngle / 2);
            ctx.textAlign = 'right';
            ctx.fillStyle = slice.textColor || '#ffffff';
            ctx.font = 'bold 13px Plus Jakarta Sans, sans-serif';
            ctx.shadowColor = 'rgba(0, 0, 0, 0.6)';
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
            ctx.fillStyle = i % 2 === 0 ? '#f59e0b' : '#4f46e5';
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
