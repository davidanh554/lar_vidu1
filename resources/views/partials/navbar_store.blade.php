<nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top navbar-custom py-2 py-lg-3">
    <div class="container">
        <!-- Logo VUA TABLET bên trái -->
        <a class="navbar-brand brand-logo-wrap p-0 me-2 me-lg-4" href="{{ route('home') }}" title="VuaTablet - Siêu thị Máy tính bảng & Phụ kiện chính hãng">
            <span class="brand-logo-text">
                Vua<span class="brand-gradient">Tablet</span>
            </span>
        </a>
        
        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarStoreNav" aria-controls="navbarStoreNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarStoreNav">
            <!-- Cụm nút bên phải (Giữ nguyên vị trí các nút như ảnh chụp) -->
            <!-- Cụm nút bên phải (Giữ nguyên vị trí các nút, đổi phong cách tối giản thanh lịch như ảnh mẫu) -->
            <div class="d-flex flex-wrap align-items-center gap-2 gap-lg-3 mt-3 mt-lg-0 ms-auto">
                @php
                    $cart = auth()->check() ? (auth()->user()->cart ?? []) : [];
                    $cartCount = array_sum(array_column($cart, 'quantity'));
                    $orderUpdatesCount = auth()->check() 
                        ? \App\Models\Order::where('user_id', auth()->id())->where('has_unread_update', true)->count() 
                        : 0;
                    $wishlistCount = auth()->check() 
                        ? auth()->user()->wishlists()->count() 
                        : 0;
                @endphp

                <!-- 1. Nút Yêu thích (Wishlist) -->
                <a href="{{ route('wishlist.index') }}" class="nav-item-link position-relative text-nowrap" id="nav-wishlist-btn" title="Danh sách sản phẩm yêu thích">
                    <span class="position-relative d-inline-flex align-items-center me-1">
                        <i class="fa-solid fa-heart fs-6 text-danger"></i>
                        <span id="nav-wishlist-count" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger {{ $wishlistCount > 0 ? '' : 'd-none' }}" 
                              style="font-size: 0.65rem; min-width: 17px; height: 17px; display: inline-flex; align-items: center; justify-content: center; padding: 0 4px; font-weight: 700;">
                            {{ $wishlistCount }}
                        </span>
                    </span>
                    <span class="d-none d-sm-inline">Yêu thích</span>
                </a>

                <!-- 2. Nút Giỏ hàng (Phong cách tối giản như ảnh mẫu kèm icon túi xách) -->
                <a href="{{ route('cart.index') }}" class="nav-item-link position-relative text-nowrap" id="nav-cart-btn" title="Giỏ hàng của bạn">
                    <span class="position-relative d-inline-flex align-items-center me-1">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="nav-cart-icon">
                            <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"></path>
                            <line x1="3" y1="6" x2="21" y2="6"></line>
                            <path d="M16 10a4 4 0 0 1-8 0"></path>
                        </svg>
                        <span id="nav-cart-count" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger {{ $cartCount > 0 ? '' : 'd-none' }}" 
                              style="font-size: 0.65rem; min-width: 17px; height: 17px; display: inline-flex; align-items: center; justify-content: center; padding: 0 4px; font-weight: 700;">
                            {{ $cartCount }}
                        </span>
                    </span>
                    <span>Giỏ hàng</span>
                </a>

                <!-- 3. Nút Hỏi đáp (FAQ) -->
                <a href="{{ route('faq') }}" class="nav-item-link text-nowrap d-none d-md-inline-flex align-items-center gap-1" title="Câu hỏi thường gặp">
                    <i class="fa-regular fa-circle-question small"></i>
                    <span>Hỏi đáp</span>
                </a>

                @auth
                    <!-- 4. Nút Đơn hàng của tôi (Text link thanh lịch không viền thô) -->
                    <a href="{{ route('orders.index') }}" class="nav-item-link position-relative text-nowrap" id="nav-orders-btn" title="Lịch sử đơn hàng">
                        <span>Đơn mua</span>
                        <span id="nav-order-badge" class="badge rounded-pill bg-danger ms-1 {{ $orderUpdatesCount > 0 ? '' : 'd-none' }}" 
                              style="font-size: 0.65rem; padding: 0.18rem 0.45rem; font-weight: 700;" title="Có cập nhật đơn hàng mới!">
                            {{ $orderUpdatesCount }}
                        </span>
                    </a>

                    <!-- Đường gạch đứng phân cách tinh tế như ảnh mẫu PhongMobile -->
                    <div class="nav-divider d-none d-lg-block"></div>

                    <!-- 5. Thông tin User: Xin chào, [Tên] + Badge Loyalty Tier -->
                    @php
                        $userTier = Auth::user()->membership_tier;
                    @endphp
                    <a href="{{ route('profile') }}" class="nav-user-link text-nowrap gap-2" title="Hồ sơ & Hạng thành viên">
                        <span class="nav-user-greeting">Xin chào, <strong class="nav-user-name">{{ Auth::user()->name }}</strong></span>
                        <span class="badge rounded-pill fw-bold" style="background-color: {{ $userTier['bg'] }}; color: {{ $userTier['color'] }}; border: 1px solid {{ $userTier['border'] }}; font-size: 0.72rem; padding: 3px 8px;" title="Cấp bậc: {{ $userTier['badge'] }}">
                            {{ $userTier['badge'] }}
                        </span>
                    </a>

                    <!-- 4. Nút Admin (nếu là admin) -->
                    @if(Auth::user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="btn-nav-admin text-nowrap" title="Trang quản trị">
                            Admin
                        </a>
                    @endif

                    <!-- 5. Nút Đăng xuất dạng Solid Dark Pill đúng chuẩn nút action chính ở ảnh mẫu -->
                    <form action="{{ route('logout') }}" method="POST" class="d-inline mb-0">
                        @csrf
                        <button type="submit" class="btn-nav-dark text-nowrap">
                            Đăng xuất
                        </button>
                    </form>
                @else
                    <!-- Đường gạch đứng phân cách -->
                    <div class="nav-divider d-none d-lg-block"></div>

                    <!-- Nếu là khách: Đăng nhập (Text link) & Đăng ký (Solid Dark Pill) chuẩn ảnh mẫu -->
                    <a href="{{ route('login') }}" class="nav-item-link text-nowrap">
                        Đăng nhập
                    </a>
                    <a href="{{ route('register') }}" class="btn-nav-dark text-nowrap text-decoration-none">
                        Đăng ký
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>
