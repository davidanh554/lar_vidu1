<div class="list-group shadow-sm border-0 rounded-3 overflow-hidden">
    <a href="{{ route('admin.dashboard') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        Dashboard
    </a>
    <a href="{{ route('admin.products.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
        Quản lý Sản phẩm
    </a>
    <a href="{{ route('admin.orders.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
        Quản lý Đơn hàng
    </a>
    <a href="{{ route('admin.finance.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.finance.index') ? 'active' : '' }}">
        Thống kê tài chính
    </a>
    <a href="{{ route('admin.finance.transactions') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.finance.transactions') ? 'active' : '' }}">
        Giao dịch thanh toán
    </a>
    <a href="{{ route('admin.reviews.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
        Quản lý Đánh giá
    </a>
    <a href="{{ route('admin.reports.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
        Báo cáo & Thống kê
    </a>
    <a href="{{ route('admin.users.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
        Quản lý Người dùng
    </a>
    <a href="{{ route('admin.coupons.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}">
        Mã Giảm Giá (Coupons)
    </a>
    <a href="{{ route('admin.videos.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.videos.*') ? 'active' : '' }}">
        Quản lý Video Reels
    </a>
    <a href="{{ route('admin.coins.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.coins.*') ? 'active' : '' }}">
        Quản lý Xu & Hạn Mức
    </a>
    <a href="{{ route('home') }}" class="list-group-item list-group-item-action text-primary" target="_blank">
        Xem Website
    </a>
</div>
