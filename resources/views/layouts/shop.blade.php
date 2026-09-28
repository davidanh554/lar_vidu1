<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KING OF IPADS</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        :root {
            --app-bg: #f5f5f7;
            --app-blue: #0066cc;
            --app-blue-hover: #0052a3;
            --app-dark: #1d1d1f;
            --app-gray: #86868b;
            --app-card-bg: #ffffff;
        }

        body {
            background-color: var(--app-bg);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            color: var(--app-dark);
            -webkit-font-smoothing: antialiased;
        }

        .navbar-main {
            background: rgba(29, 29, 31, 0.92);
            backdrop-filter: blur(20px);
            position: sticky;
            top: 0;
            z-index: 1000;
            padding: 12px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .search-input-group input {
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fff;
            border-radius: 30px;
            padding: 8px 20px 8px 40px;
            font-size: 14px;
            transition: all 0.3s;
        }

        .search-input-group input:focus {
            background: rgba(255, 255, 255, 0.25);
            color: #fff;
            box-shadow: 0 0 0 3px rgba(0, 102, 204, 0.4);
            border-color: transparent;
        }

        .search-input-group input::placeholder {
            color: #a1a1a6;
        }

        .search-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #a1a1a6;
            z-index: 5;
        }

        .sub-nav {
            background: #2d2d2f;
            overflow-x: auto;
            white-space: nowrap;
        }

        .sub-nav .nav-link {
            color: #d2d2d7;
            font-size: 14px;
            font-weight: 500;
            padding: 12px 25px;
            transition: all 0.2s;
        }

        .sub-nav .nav-link:hover, .sub-nav .nav-link.active {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.12);
        }

        .product-section {
            background: linear-gradient(180deg, #5d93c9ff 0%, #003d7a 100%);
            border-radius: 28px;
            padding: 35px 25px 30px 25px;
            margin-top: 20px;
            box-shadow: 0 20px 40px rgba(0, 61, 122, 0.2);
            position: relative;
        }

        .section-badge-hot {
            position: absolute;
            top: -20px;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(135deg, #ff4e50, #f9d423);
            color: #fff;
            font-weight: 800;
            font-size: 15px;
            padding: 8px 32px;
            border-radius: 30px;
            box-shadow: 0 8px 20px rgba(255, 78, 80, 0.4);
            letter-spacing: 0.5px;
        }

        .product-card {
            background: var(--app-card-bg);
            border-radius: 20px;
            border: none;
            transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 16px 35px rgba(0, 0, 0, 0.18);
        }

        .card-img-container {
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
        }

        .card-img-container img {
            max-height: 100%;
            max-width: 100%;
            object-fit: contain;
            transition: transform 0.4s;
        }

        .badge-sale {
            position: absolute;
            top: 15px;
            left: 15px;
            background: #e00000;
            color: white;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 8px;
            z-index: 2;
        }

        .badge-installment {
            position: absolute;
            top: 15px;
            right: 15px;
            border: 1px solid var(--app-blue);
            color: var(--app-blue);
            background: rgba(0, 102, 204, 0.05);
            font-size: 11px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 8px;
            z-index: 2;
        }

        .product-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--app-dark);
            line-height: 1.35;
            min-height: 40px;
        }

        .spec-pills {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
            margin-bottom: 12px;
        }

        .spec-pill {
            background: #f2f2f7;
            color: #515154;
            font-size: 11px;
            font-weight: 600;
            padding: 3px 8px;
            border-radius: 6px;
        }

        .price-current {
            color: var(--app-blue);
            font-size: 18px;
            font-weight: 800;
        }

        .price-old {
            color: var(--app-gray);
            font-size: 13px;
            text-decoration: line-through;
            margin-left: 6px;
        }

        .btn-app-primary {
            background-color: var(--app-blue);
            color: white;
            border-radius: 30px;
            font-weight: 600;
            font-size: 13px;
            padding: 8px 18px;
            border: none;
        }

        .btn-app-outline {
            border: 1.5px solid #d2d2d7;
            color: var(--app-dark);
            border-radius: 30px;
            font-weight: 600;
            font-size: 13px;
            padding: 7px 16px;
            background: transparent;
        }

        footer {
            background: #1f1f7aff;
            color: #86868b;
            font-size: 13px;
            padding: 40px 0 20px 0;
            margin-top: 60px;
        }
    </style>
</head>
<body>

    <!-- Top Header -->
    <nav class="navbar-main">
        <div class="container d-flex align-items-center justify-content-between">
            <a href="{{ route('products.index') }}" class="text-white text-decoration-none fw-bold fs-4 d-flex align-items-center">
                <i class="fa-brands fa-apple fs-2 me-2"></i> King of ipads
            </a>

            <!-- Form Search -->
            <form action="{{ route('products.index') }}" method="GET" class="position-relative search-input-group d-none d-md-block" style="width: 400px;">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                <input type="text" name="search" class="form-control" placeholder="Tìm kiếm tên sản phẩm..." value="{{ request('search') }}">
            </form>

            <div class="d-flex align-items-center gap-3">
                <a href="#" class="text-white text-decoration-none fs-6 px-2"><i class="fa-solid fa-bag-shopping me-1"></i> Giỏ hàng</a>
                <a href="{{ route('products.create') }}" class="btn btn-app-primary">
                    <i class="fa-solid fa-plus me-1"></i> Thêm sản phẩm
                </a>
            </div>
        </div>
    </nav>

    <!-- Sub Navigation Menu: Chỉ còn Tất cả, iPad, Phụ kiện -->
    <div class="sub-nav">
        <div class="container d-flex justify-content-center gap-2">
            <a href="{{ route('products.index') }}" class="nav-link {{ !request('category_id') ? 'active' : '' }}">
                <i class="fa-solid fa-border-all me-1"></i> Tất cả
            </a>
            
            <!-- category_id = 1: iPad / Máy tính bảng -->
            <a href="{{ route('products.index', ['category_id' => 1]) }}" class="nav-link {{ request('category_id') == 1 ? 'active' : '' }}">
                <i class="fa-solid fa-tablet-screen-button me-1"></i> iPad
            </a>

            <!-- category_id = 2: Phụ kiện -->
            <a href="{{ route('products.index', ['category_id' => 2]) }}" class="nav-link {{ request('category_id') == 2 ? 'active' : '' }}">
                <i class="fa-solid fa-keyboard me-1"></i> Phụ kiện
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <main class="container my-4">
        @if ($message = Session::get('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i> {{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <footer>
        <div class="container text-center">
            <p class="mb-1">© 2026 King of ipads - fouder Anh Đậu sáng lập </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>