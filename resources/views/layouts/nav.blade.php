<nav class="navbar navbar-dark bg-dark px-3">
    <a class="navbar-brand" href="{{ route('admin.dashboard') }}">ADMIN</a>
    <div class="text-white">
        Xin chào, <strong>{{ Auth::user()->name }}</strong>
        <form action="{{ route('logout') }}" method="POST" class="d-inline ms-3">
            @csrf
            <button type="submit" class="btn btn-danger btn-sm">Đăng xuất</button>
        </form>
    </div>
</nav>