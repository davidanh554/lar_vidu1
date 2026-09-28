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

    <!-- Livechat Floating Widget (for logged in customers) -->
    @include('partials.chat_user')

    <!-- Lucky Wheel Floating Widget & Popup Modal (Ẩn ở trang Đăng nhập / Đăng ký) -->
    @if(!request()->routeIs('login', 'register', 'verification.*'))
        @include('partials.lucky_wheel')
    @endif

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @stack('scripts')
</body>
</html>
