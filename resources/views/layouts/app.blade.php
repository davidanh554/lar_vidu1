<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShopDunk - Máy tính bảng iPad</title>
    <!-- Bootstrap 5 CSS & FontAwesome Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            background-color: #f5f5f7;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
        /* Top Navigation Bar */
        .top-navbar {
            background-color: #692b7fff;
            padding: 10px 0;
        }
        .search-box {
            border-radius: 20px;
            padding: 6px 15px;
            font-size: 14px;
        }
        .nav-menu {
            background-color: #3e3e3f;
            border-bottom: 1px solid #515154;
        }
        .nav-menu .nav-link {
            color: #d2d2d7;
            font-size: 14px;
            padding: 10px 15px;
        }
        .nav-menu .nav-link:hover {
            color: #fff;
        }
        /* Hot Products Banner Section */
        .hot-banner {
            background-color: #0066cc;
            border-radius: 20px;
            padding: 25px 20px 20px 20px;
            margin-top: 25px;
            position: relative;
        }
        .hot-badge {
            position: absolute;
            top: -18px;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(180deg, #42a5f5, #0066cc);
            color: white;
            padding: 6px 30px;
            border-radius: 20px;
            font-weight: bold;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }
        /* Product Card Styling */
        .product-card {
            border: none;
            border-radius: 16px;
            transition: transform 0.2s, box-shadow 0.2s;
            height: 100%;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.12);
        }
        .badge-discount {
            position: absolute;
            top: 12px;
            left: 12px;
            background-color: #e00000;
            color: white;
            font-size: 11px;
            font-weight: bold;
            padding: 3px 8px;
            border-radius: 4px;
        }
        .badge-installment {
            position: absolute;
            top: 12px;
            right: 12px;
            border: 1px solid #0066cc;
            color: #0066cc;
            font-size: 11px;
            font-weight: bold;
            padding: 2px 6px;
            border-radius: 4px;
            background: white;
        }
        .product-img {
            height: 180px;
            object-fit: contain;
            margin: 25px 0 15px 0;
        }
        .product-title {
            font-size: 15px;
            font-weight: 600;
            color: #1d1d1f;
            min-height: 42px;
        }
        .price-current {
            color: #0066cc;
            font-weight: bold;
            font-size: 16px;
        }
        .price-old {
            color: #86868b;
            text-decoration: line-through;
            font-size: 13px;
            margin-left: 6px;
        }
        /* Bottom Category Filter Tag Pills */
        .tag-pill {
            background: white;
            border: 1px solid #e5e5e5;
            border-radius: 8px;
            padding: 6px 16px;
            font-size: 13px;
            color: #1d1d1f;
            text-decoration: none;
            display: inline-block;
            margin: 4px;
        }
        .tag-pill:hover {
            border-color: #0066cc;
            color: #0066cc;
        }
    </style>
</head>
<body>

    <!-- Header Top -->
    <header class="top-navbar">
        <div class="container d-flex align-items-center justify-content-between">
            <a href="/" class="navbar-brand text-white fw-bold fs-4">
                <i class="fa-brands fa-apple me-2"></i>SHOPDUNK
            </a>
            
            <div class="w-50">
                <input type="text" class="form-control search-box" placeholder="Bạn tìm gì...">
            </div>

            <div class="text-white d-flex align-items-center gap-4">
                <a href="#" class="text-white text-decoration-none"><i class="fa-solid fa-cart-shopping me-1"></i> Giỏ hàng</a>
                <a href="#" class="text-white text-decoration-none"><i class="fa-regular fa-user me-1"></i> Tài khoản</a>
            </div>
        </div>
    </header>

    <!-- Navigation Bar Menu -->
    <nav class="nav-menu">
        <div class="container d-flex justify-content-center">
            <a class="nav-link" href="#"><i class="fa-solid fa-bars me-1"></i> Dịch vụ</a>
            <a class="nav-link" href="#">iPhone</a>
            <a class="nav-link fw-bold text-white" href="#">iPad</a>
            <a class="nav-link" href="#">Mac</a>
            <a class="nav-link" href="#">Watch</a>
            <a class="nav-link" href="#">Phụ kiện</a>
            <a class="nav-link" href="#">Âm thanh</a>
            <a class="nav-link" href="#">Camera</a>
            <a class="nav-link" href="#">Gia dụng</a>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container my-4">
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>