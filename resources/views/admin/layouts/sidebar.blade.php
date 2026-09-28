<div class="list-group shadow-sm border-0 rounded-3 overflow-hidden">
    <a href="{{ route('admin.dashboard') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <i class="fa-solid fa-gauge me-2"></i> Dashboard
    </a>
    <a href="{{ route('admin.products.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
        <i class="fa-solid fa-tablet-screen-button me-2"></i> Quản lý Sản phẩm
    </a>
    <a href="{{ route('admin.orders.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
        <i class="fa-solid fa-clipboard-list me-2"></i> Quản lý Đơn hàng
    </a>
    <a href="{{ route('admin.finance.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.finance.index') ? 'active' : '' }}">
        <i class="fa-solid fa-file-invoice-dollar me-2 text-success"></i> Thống kê tài chính
    </a>
    <a href="{{ route('admin.finance.transactions') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.finance.transactions') ? 'active' : '' }}">
        <i class="fa-solid fa-money-bill-transfer me-2 text-info"></i> Giao dịch thanh toán
    </a>
    <a href="{{ route('admin.reviews.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
        <i class="fa-solid fa-star-half-stroke me-2 text-warning"></i> Quản lý Đánh giá
    </a>
    <a href="{{ route('admin.reports.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
        <i class="fa-solid fa-chart-line me-2"></i> Báo cáo & Thống kê
    </a>
    <a href="{{ route('admin.users.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
        <i class="fa-solid fa-users me-2"></i> Quản lý Người dùng
    </a>
    <a href="{{ route('admin.coupons.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}">
        <i class="fa-solid fa-ticket-simple me-2 text-success"></i> Mã Giảm Giá (Coupons)
    </a>
    <a href="{{ route('admin.videos.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.videos.*') ? 'active' : '' }}">
        <i class="fa-solid fa-clapperboard me-2 text-danger"></i> Quản lý Video Reels
    </a>
    <a href="{{ route('admin.coins.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.coins.*') ? 'active' : '' }}">
        <i class="fa-solid fa-coins me-2 text-warning"></i> Quản lý Xu & Hạn Mức
    </a>
    <a href="{{ route('home') }}" class="list-group-item list-group-item-action text-primary" target="_blank">
        <i class="fa-solid fa-arrow-up-right-from-square me-2"></i> Xem Website
    </a>
</div>
