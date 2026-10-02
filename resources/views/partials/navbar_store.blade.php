<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top navbar-custom py-3">
    <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center" href="{{ route('home') }}">
            <span class="brand-name">VUA TABLET</span>
        </a>
        
        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarStoreNav" aria-controls="navbarStoreNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarStoreNav">
            <!-- Left nav links -->
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-3 gap-1">
                <li class="nav-item">
                    <a class="nav-link px-3 py-2 rounded-pill text-nowrap {{ request()->routeIs('home') || request()->routeIs('products.*') ? 'active text-white fw-semibold bg-white bg-opacity-10' : 'text-white-50' }}" href="{{ route('home') }}">
                        Cửa hàng
                    </a>
                </li>
            </ul>

            <!-- Right nav links / Auth -->
            <div class="d-flex flex-wrap align-items-center gap-2 mt-3 mt-lg-0">
                @php
                    $cart = auth()->check() ? (auth()->user()->cart ?? []) : [];
                    $cartCount = array_sum(array_column($cart, 'quantity'));
                    $orderUpdatesCount = auth()->check() 
                        ? \App\Models\Order::where('user_id', auth()->id())->where('has_unread_update', true)->count() 
                        : 0;
                @endphp

                <a href="{{ route('cart.index') }}" class="btn btn-outline-light btn-sm rounded-pill px-3 position-relative d-inline-flex align-items-center text-nowrap {{ request()->routeIs('cart.*') ? 'active border-primary text-white bg-primary bg-opacity-25' : '' }}" id="nav-cart-btn">
                    <span>Giỏ hàng</span>
                    <span id="nav-cart-count" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger {{ $cartCount > 0 ? '' : 'd-none' }}" style="font-size: 0.72rem; padding: 0.25rem 0.5rem;">
                        {{ $cartCount }}
                    </span>
                </a>

                @auth
                    <a href="{{ route('orders.index') }}" class="btn btn-outline-info btn-sm rounded-pill px-3 position-relative d-inline-flex align-items-center text-nowrap {{ request()->routeIs('orders.*') ? 'active bg-info bg-opacity-25 text-white' : '' }}" id="nav-orders-btn" title="Lịch sử đơn hàng & tiến độ GHN">
                        <span>Đơn hàng của tôi</span>
                        <span id="nav-order-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger {{ $orderUpdatesCount > 0 ? '' : 'd-none' }}" style="font-size: 0.72rem; padding: 0.25rem 0.5rem;" title="Có cập nhật trạng thái đơn hàng mới!">
                            {{ $orderUpdatesCount }}
                        </span>
                    </a>

                    <a href="{{ route('profile') }}" class="d-flex align-items-center text-decoration-none bg-white bg-opacity-10 px-3 py-1 rounded-pill border border-white border-opacity-10 text-white small gap-2 user-pill-btn text-nowrap {{ request()->routeIs('profile*') ? 'border-emerald active' : '' }}" title="Xem trang thông tin cá nhân & lộ trình đơn mua">
                        <span>Xin chào, <strong class="text-white">{{ Auth::user()->name }}</strong></span>
                        @if(Auth::user()->hasVerifiedEmail())
                            <span class="badge badge-verified-pill" title="Tài khoản đã xác thực email thành công">
                                Đã kích hoạt
                            </span>
                        @else
                            <span class="badge badge-unverified-pill" title="Tài khoản chưa kích hoạt email">
                                Chưa kích hoạt
                            </span>
                        @endif
                    </a>

                    @if(Auth::user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-warning btn-sm fw-bold rounded-pill px-3 d-inline-flex align-items-center text-nowrap">
                            Admin
                        </a>
                    @endif

                    <form action="{{ route('logout') }}" method="POST" class="d-inline mb-0">
                        @csrf
                        <button type="submit" class="btn btn-outline-light btn-sm rounded-pill px-3 d-inline-flex align-items-center text-nowrap">
                            Đăng xuất
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm rounded-pill px-3">
                        Đăng nhập
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-primary btn-sm rounded-pill px-3 fw-bold">
                        Đăng ký
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>
