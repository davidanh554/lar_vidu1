@extends('admin.layouts.master')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">Bảng Điều Khiển Quản Trị</h3>
            <p class="text-muted small mb-0">Chào mừng trở lại! Dưới đây là tình hình tổng quan hệ thống của bạn.</p>
        </div>
        <div>
            <a href="{{ route('admin.reports.index') }}" class="btn btn-primary btn-sm px-3 shadow-sm">
                Xem báo cáo chi tiết
            </a>
        </div>
    </div>

    <!-- Stat Cards Grid -->
    <div class="row g-4 mb-4">
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 p-3" style="border-left: 4px solid #4f46e5 !important;">
                <span class="text-muted small fw-semibold text-uppercase">Tổng Sản Phẩm</span>
                <h2 class="fw-bold mb-0 mt-2 text-dark">{{ $totalProducts }}</h2>
                <div class="mt-3 pt-2 border-top">
                    <a href="{{ route('admin.products.index') }}" class="small text-decoration-none text-primary fw-semibold">
                        Quản lý kho máy
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 p-3" style="border-left: 4px solid #10b981 !important;">
                <span class="text-muted small fw-semibold text-uppercase">Khách Hàng</span>
                <h2 class="fw-bold mb-0 mt-2 text-dark">{{ $totalCustomers }}</h2>
                <div class="mt-3 pt-2 border-top">
                    <a href="{{ route('admin.users.index') }}" class="small text-decoration-none text-success fw-semibold">
                        Danh sách tài khoản
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 p-3" style="border-left: 4px solid #f59e0b !important;">
                <span class="text-muted small fw-semibold text-uppercase">Đơn Hàng</span>
                <h2 class="fw-bold mb-0 mt-2 text-dark">
                    {{ \App\Models\Order::count() }}
                </h2>
                <div class="mt-3 pt-2 border-top">
                    <a href="{{ route('admin.orders.index') }}" class="small text-decoration-none text-warning fw-semibold">
                        Xử lý đơn hàng
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 p-3" style="border-left: 4px solid #06b6d4 !important;">
                <span class="text-muted small fw-semibold text-uppercase">Live Chat</span>
                <h2 class="fw-bold mb-0 mt-2 text-dark">
                    {{ \App\Models\Message::count() }}
                </h2>
                <div class="mt-3 pt-2 border-top">
                    <span class="small text-muted fw-semibold">Tin nhắn hệ thống</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Shortcuts Card -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="card-title fw-bold mb-0">Truy cập nhanh chức năng</h5>
        </div>
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <a href="{{ route('admin.orders.index') }}" class="text-decoration-none">
                        <div class="p-3 border rounded-3 bg-light bg-opacity-50 text-dark h-100 hover-shadow transition">
                            <h6 class="fw-bold mb-1">Quản lý Đơn hàng & Vận chuyển</h6>
                            <p class="small text-muted mb-0">Xem trạng thái đơn, lọc theo ngày và cập nhật hàng loạt qua GHN.</p>
                        </div>
                    </a>
                </div>

                <div class="col-md-4">
                    <a href="{{ route('admin.reports.charts') }}" class="text-decoration-none">
                        <div class="p-3 border rounded-3 bg-light bg-opacity-50 text-dark h-100 hover-shadow transition">
                            <h6 class="fw-bold mb-1">Biểu đồ Báo cáo Doanh thu</h6>
                            <p class="small text-muted mb-0">Thống kê doanh số theo ngày, tháng, năm và phương thức thanh toán.</p>
                        </div>
                    </a>
                </div>

                <div class="col-md-4">
                    <a href="{{ route('admin.products.create') }}" class="text-decoration-none">
                        <div class="p-3 border rounded-3 bg-light bg-opacity-50 text-dark h-100 hover-shadow transition">
                            <h6 class="fw-bold mb-1">Thêm Sản phẩm Mới</h6>
                            <p class="small text-muted mb-0">Đăng bán máy tính bảng mới kèm hình ảnh, biến thể màu sắc và giá.</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection