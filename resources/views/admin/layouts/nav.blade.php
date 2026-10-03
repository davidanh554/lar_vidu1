<nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4 py-2 sticky-top">
    <div class="container-fluid px-0">
        <a class="navbar-brand fw-bold" href="{{ route('admin.dashboard') }}">
            VUA TABLET - ADMIN
        </a>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('home') }}" class="btn btn-outline-light btn-sm rounded-pill px-3 me-2" target="_blank">
                Xem Cửa Hàng
            </a>

            @auth
                <div class="d-flex align-items-center bg-white bg-opacity-10 px-3 py-1 rounded-pill border border-white border-opacity-10 text-white small">
                    <span>Xin chào, <strong class="text-white">{{ Auth::user()->name }}</strong></span>
                </div>
                <form action="{{ route('logout') }}" method="POST" class="d-inline mb-0">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3">
                        Đăng xuất
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm rounded-pill">Đăng nhập</a>
            @endauth
        </div>
    </div>
</nav>
