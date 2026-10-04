<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'VUA TABLET - Cửa Hàng Máy Tính Bảng & Phụ Kiện Hàng Đầu')</title>
    
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5.3 & Font Awesome 6 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <!-- Custom Storefront CSS -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}?v={{ filemtime(public_path('css/custom.css')) }}">

    @stack('styles')
</head>
<body class="d-flex flex-column min-vh-100">

    <!-- Unified Storefront Header -->
    @include('partials.navbar_store')

    <!-- Flash Notifications -->
    <div class="container mt-3">
        @if(session('success'))
            <div class="alert alert-modern-success alert-dismissible fade show d-flex align-items-center shadow-sm" role="alert">
                <i class="fa-solid fa-circle-check fs-5 me-2"></i>
                <div class="flex-grow-1">{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-modern-danger alert-dismissible fade show d-flex align-items-center shadow-sm" role="alert">
                <i class="fa-solid fa-circle-exclamation fs-5 me-2"></i>
                <div class="flex-grow-1">{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-modern-warning alert-dismissible fade show d-flex align-items-center shadow-sm" role="alert">
                <i class="fa-solid fa-triangle-exclamation fs-5 me-2"></i>
                <div class="flex-grow-1">{{ session('warning') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    </div>

    <!-- Main Content Area -->
    <main class="flex-grow-1">
        @yield('content')
    </main>

    <!-- Unified Storefront Footer -->
    @include('partials.footer_store')

    <!-- Chat Widgets: AI Gemini (Tự động 24/7) & Hỗ trợ khách hàng (Gặp Admin) -->
    @include('partials.chat_gemini')
    @include('partials.chat_user')

    <!-- Lucky Wheel Floating Widget & Popup Modal (Ẩn ở trang Đăng nhập / Đăng ký) -->
    @if(!request()->routeIs('login', 'register', 'verification.*'))
        @include('partials.lucky_wheel')
    @endif

    <!-- Global Toast Notification Container for Wishlist & Actions -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 9999;">
        <div id="globalToast" class="toast align-items-center text-white border-0 shadow-lg rounded-3" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center gap-2 py-3 px-3 fs-6" id="globalToastBody">
                    <i class="fa-solid fa-circle-check fs-5"></i>
                    <span id="globalToastMessage">Thông báo</span>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Wishlist & Global Toast Handler -->
    <script>
    @guest
    // Bắt sự kiện click ngay từ capture phase khi khách chưa đăng nhập: chuyển hướng ngay sang trang đăng nhập
    document.addEventListener('click', function (e) {
        // 1. Nút Yêu thích
        const wishlistBtn = e.target.closest('.btn-wishlist-toggle, #nav-wishlist-btn');
        if (wishlistBtn) {
            e.preventDefault();
            e.stopImmediatePropagation();
            window.location.href = "{{ route('login') }}";
            return false;
        }

        // 2. Nút Thêm vào giỏ hàng
        const addCartBtn = e.target.closest('.btn-ajax-add, .btn-card-detail, #btn-add-cart, button[value="add"], button[value="add_to_cart"]');
        if (addCartBtn) {
            e.preventDefault();
            e.stopImmediatePropagation();
            window.location.href = "{{ route('login') }}";
            return false;
        }

        // 3. Nút Mua ngay
        const buyNowBtn = e.target.closest('.btn-card-buy, #btn-buy-now, button[value="buy_now"]');
        if (buyNowBtn) {
            e.preventDefault();
            e.stopImmediatePropagation();
            window.location.href = "{{ route('login') }}";
            return false;
        }

        // 4. Form thêm giỏ hàng / mua ngay nếu submit trực tiếp
        const cartForm = e.target.closest('form.ajax-add-cart-form, form#purchase-form');
        if (cartForm && (e.target.type === 'submit' || e.target.tagName === 'BUTTON')) {
            e.preventDefault();
            e.stopImmediatePropagation();
            window.location.href = "{{ route('login') }}";
            return false;
        }

        // 5. Nút / Link xem video nhận xu
        const videoBtn = e.target.closest('a[href*="/videos"], .btn-hero-video');
        if (videoBtn) {
            e.preventDefault();
            e.stopImmediatePropagation();
            window.location.href = "{{ route('login') }}";
            return false;
        }
    }, true);
    @endguest

    document.addEventListener('DOMContentLoaded', function () {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        window.showGlobalToast = function (message, isSuccess = true) {
            const toastEl = document.getElementById('globalToast');
            const toastBody = document.getElementById('globalToastBody');
            const toastMsg = document.getElementById('globalToastMessage');
            if (!toastEl || !toastMsg) return;

            toastEl.className = 'toast align-items-center text-white border-0 shadow-lg rounded-3 ' + (isSuccess ? 'bg-success' : 'bg-danger');
            toastMsg.textContent = message;
            const icon = toastBody.querySelector('i');
            if (icon) {
                icon.className = 'fs-5 fa-solid ' + (isSuccess ? 'fa-circle-check' : 'fa-circle-exclamation');
            }

            const toast = new bootstrap.Toast(toastEl, { delay: 2800 });
            toast.show();
        };

        // Event delegation for wishlist toggle buttons (supports dynamic & AJAX cards)
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.btn-wishlist-toggle');
            if (!btn) return;

            e.preventDefault();
            e.stopPropagation();

            const url = btn.dataset.url;
            if (!url) return;

            btn.disabled = true;

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({})
            })
            .then(response => {
                if (response.status === 401) {
                    window.location.href = "{{ route('login') }}";
                    return null;
                }
                return response.json();
            })
            .then(data => {
                if (!data) return;
                btn.disabled = false;
                if (data.redirect) {
                    window.location.href = data.redirect;
                    return;
                }
                if (data.success) {
                    // Update current button
                    const icon = btn.querySelector('i');
                    if (data.in_wishlist) {
                        btn.classList.add('active');
                        btn.setAttribute('title', 'Bỏ thích');
                        if (icon) {
                            icon.className = 'fa-solid fa-heart text-danger';
                        }
                    } else {
                        btn.classList.remove('active');
                        btn.setAttribute('title', 'Thêm vào yêu thích');
                        if (icon) {
                            icon.className = 'fa-regular fa-heart';
                        }
                    }

                    // Synchronize any matching buttons on same page
                    const prodId = btn.dataset.productId;
                    if (prodId) {
                        document.querySelectorAll(`.btn-wishlist-toggle[data-product-id="${prodId}"]`).forEach(otherBtn => {
                            if (otherBtn !== btn) {
                                const oIcon = otherBtn.querySelector('i');
                                if (data.in_wishlist) {
                                    otherBtn.classList.add('active');
                                    otherBtn.setAttribute('title', 'Bỏ thích');
                                    if (oIcon) oIcon.className = 'fa-solid fa-heart text-danger';
                                } else {
                                    otherBtn.classList.remove('active');
                                    otherBtn.setAttribute('title', 'Thêm vào yêu thích');
                                    if (oIcon) oIcon.className = 'fa-regular fa-heart';
                                }
                            }
                        });
                    }

                    // Update Navbar badge
                    const navBadge = document.getElementById('nav-wishlist-count');
                    if (navBadge) {
                        navBadge.textContent = data.count;
                        if (data.count > 0) {
                            navBadge.classList.remove('d-none');
                        } else {
                            navBadge.classList.add('d-none');
                        }
                    }

                    // Toast message
                    window.showGlobalToast(data.message, true);
                }
            })
            .catch(err => {
                btn.disabled = false;
                console.error('Wishlist error:', err);
                window.showGlobalToast('Không thể cập nhật danh sách yêu thích. Vui lòng thử lại!', false);
            });
        });
    });
    </script>
    @stack('scripts')
</body>
</html>
