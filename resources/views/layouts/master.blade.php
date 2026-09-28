<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Trang Quản Trị - Tablet Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    @include('admin.layouts.nav')

    <div class="container-fluid my-4">
        <div class="row">
            <div class="col-md-3 col-lg-2">
                @include('admin.layouts.sidebar')
            </div>
            <div class="col-md-9 col-lg-10">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @yield('content')
            </div>
        </div>
    </div>
</body>
</html>
