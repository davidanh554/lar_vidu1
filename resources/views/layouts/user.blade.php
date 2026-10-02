<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyShop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}?v={{ filemtime(public_path('css/custom.css')) }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="{{ route('welcome') }}">MyShop</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('welcome') }}">Trang chủ</a>
                </li>
            </ul>

            <ul class="navbar-nav">
                @auth
                    <li class="nav-item d-flex align-items-center">
                        <span class="nav-link text-light me-1">👋 Xin chào, {{ Auth::user()->name }}</span>
                        @if(Auth::user()->hasVerifiedEmail())
                            <span class="badge bg-success rounded-pill px-2 py-1 small" title="Email đã kích hoạt"><i class="fa-solid fa-circle-check me-1"></i>Đã kích hoạt</span>
                        @else
                            <a href="{{ route('verification.notice') }}" class="badge bg-warning text-dark rounded-pill px-2 py-1 text-decoration-none small" title="Bấm để kích hoạt email"><i class="fa-solid fa-triangle-exclamation me-1"></i>Chưa kích hoạt</a>
                        @endif
                    </li>
                    <li class="nav-item">
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-link nav-link text-decoration-none">Đăng xuất</button>
                        </form>
                    </li>
                @endauth

                @guest
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register') }}">Đăng ký</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">Đăng nhập</a>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4">
    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

@include('partials.chat_gemini')
@include('partials.chat_user')
</body>
</html>