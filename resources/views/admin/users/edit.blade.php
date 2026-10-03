@extends('admin.layouts.master')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold mb-0">Chỉnh sửa người dùng</h3>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Quay lại
        </a>
    </div>

    <div class="card shadow-sm border-0 col-md-8">
        <div class="card-body p-4">
            <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label fw-semibold">Tên:</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Email:</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Mật khẩu mới (bỏ trống nếu không đổi):</label>
                    <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu mới...">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Vai trò:</label>
                    <select name="role" class="form-select" required>
                        <option value="user" @selected($user->role === 'user')>Người dùng (user)</option>
                        <option value="customer" @selected($user->role === 'customer')>Khách hàng (customer)</option>
                        <option value="admin" @selected($user->role === 'admin')>Quản trị (admin)</option>
                    </select>
                </div>

                <div class="p-3 mb-3 rounded-3 bg-light border">
                    <h6 class="fw-bold text-success mb-2">Cấu hình Vòng Quay May Mắn (Lucky Wheel)</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Lượt quay tối đa mỗi ngày (24h):</label>
                            <input type="number" name="daily_spins_limit" class="form-control" min="0" max="100" value="{{ old('daily_spins_limit', $user->daily_spins_limit ?? 1) }}">
                            <small class="text-muted">Số lượt hệ thống tự cấp lại cho tài khoản này mỗi 24 giờ</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Số lượt quay hiện tại có thể dùng:</label>
                            <input type="number" name="spins_left" class="form-control" min="0" max="100" value="{{ old('spins_left', $user->spins_left ?? 1) }}">
                            <small class="text-muted">Chỉnh sửa trực tiếp số lượt quay còn lại của người dùng</small>
                        </div>
                    </div>
                </div>

                <div class="p-3 mb-3 rounded-3 bg-warning bg-opacity-10 border border-warning border-opacity-50">
                    <h6 class="fw-bold text-dark mb-2">Cấu hình Xu Mua Hàng & Giới hạn Xu Xem Video Mỗi Ngày</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Số Xu hiện tại trong ví:</label>
                            <input type="number" name="coins" class="form-control" min="0" value="{{ old('coins', $user->coins ?? 0) }}">
                            <small class="text-muted">1 Xu = 500₫ giảm trực tiếp vào tổng tiền khi đặt hàng ({{ number_format(($user->coins ?? 0) * 500) }}₫)</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Giới hạn nhận tối đa mỗi ngày (Xu/24h):</label>
                            <input type="number" name="daily_coins_limit" class="form-control" min="0" max="1000" value="{{ old('daily_coins_limit', $user->daily_coins_limit ?? 10) }}">
                            <small class="text-muted">Số Xu tối đa được nhận khi lướt video (Mặc định 10 Xu = 5.000₫/ngày, tự động reset sau 24h)</small>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary px-4">Cập nhật</button>
            </form>
        </div>
    </div>
</div>
@endsection
