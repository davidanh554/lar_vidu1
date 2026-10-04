@extends('admin.layouts.master')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">Danh sách người dùng</h3>
            <p class="text-muted small mb-0">Quản lý tài khoản khách hàng và quản trị viên của hệ thống</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="btn btn-success rounded-pill px-3 shadow-sm">
            <i class="fa-solid fa-user-plus me-1"></i> Thêm người dùng
        </a>
    </div>

    <!-- Bộ lọc & Xếp hạng khách hàng theo chi tiêu / đơn mua -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
        <div class="btn-group btn-group-sm shadow-sm" role="group">
            <a href="{{ route('admin.users.index') }}" class="btn {{ (!request('sort') || request('sort') === 'newest') ? 'btn-primary' : 'btn-outline-secondary' }}">
                Tất cả người dùng
            </a>
            <a href="{{ route('admin.users.index', ['sort' => 'spent_desc']) }}" class="btn {{ request('sort') === 'spent_desc' ? 'btn-primary' : 'btn-outline-secondary' }}">
                Mua nhiều nhất (Top VIP)
            </a>
            <a href="{{ route('admin.users.index', ['sort' => 'orders_desc']) }}" class="btn {{ request('sort') === 'orders_desc' ? 'btn-primary' : 'btn-outline-secondary' }}">
                Nhiều đơn mua nhất
            </a>
        </div>
        <div class="text-muted small">
            Đang hiển thị <strong>{{ $users->count() }}</strong> tài khoản
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="48px" class="text-center">
                            <input type="checkbox" id="check-all-users" class="form-check-input" title="Chọn tất cả người dùng hợp lệ">
                        </th>
                        <th width="70px">ID</th>
                        <th>TÊN NGƯỜI DÙNG</th>
                        <th>EMAIL</th>
                        <th>VAI TRÒ</th>
                        <th>CHI TIÊU & CẤP BẬC</th>
                        <th class="text-center">LƯỢT QUAY / 24H</th>
                        <th class="text-center">XU & HẠN MỨC NGÀY</th>
                        <th class="text-center" width="220px">HÀNH ĐỘNG</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        @php
                            $isAdmin = ($user->role === 'admin');
                            $isCurrentAuth = ($user->id === auth()->id());
                        @endphp
                        <tr>
                            <td class="text-center">
                                @if($isAdmin || $isCurrentAuth)
                                    <input type="checkbox" class="form-check-input opacity-25" disabled title="Tài khoản Quản trị viên được bảo vệ, không thể chọn">
                                @else
                                    <input type="checkbox" class="form-check-input user-check-item" value="{{ $user->id }}">
                                @endif
                            </td>
                            <td class="text-muted font-monospace">#{{ $user->id }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center text-primary fw-bold" style="width: 34px; height: 34px; font-size: 0.85rem;">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold text-dark">{{ $user->name }}</div>
                                        @if($isCurrentAuth)
                                            <span class="badge bg-info-subtle text-info small" style="font-size: 0.7rem;">(Bạn)</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-1 flex-wrap">
                                    <span class="text-secondary small">{{ $user->email }}</span>
                                    @if($user->hasVerifiedEmail())
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0" style="font-size: 0.68rem;" title="Đã xác thực email ({{ $user->email_verified_at ? $user->email_verified_at->format('d/m/Y H:i') : '' }})">
                                            <i class="fa-solid fa-circle-check text-success"></i> Đã kích hoạt
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-muted border rounded-pill px-2 py-0" style="font-size: 0.68rem;" title="Chưa kích hoạt email">
                                            <i class="fa-regular fa-circle-question"></i> Chưa kích hoạt
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($isAdmin)
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 rounded-pill fw-bold">
                                        <i class="fa-solid fa-shield-halved me-1"></i> admin
                                    </span>
                                @else
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 rounded-pill">
                                        <i class="fa-solid fa-user me-1"></i> {{ $user->role ?? 'customer' }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $spent = $user->total_spent;
                                    $tier = $user->membership_tier;
                                    $orderCount = $user->orders_count ?? 0;
                                @endphp
                                <div>
                                    <strong class="text-dark">{{ number_format($spent, 0, ',', '.') }}₫</strong>
                                    <small class="text-muted ms-1">({{ $orderCount }} đơn)</small>
                                </div>
                                <span class="badge rounded-pill fw-bold" style="background-color: {{ $tier['bg'] }}; color: {{ $tier['color'] }}; border: 1px solid {{ $tier['border'] }}; font-size: 0.68rem; padding: 2px 7px;">
                                    {{ $tier['badge'] }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-inline-flex align-items-center gap-1">
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill fw-semibold" title="Còn {{ $user->spins_left ?? 1 }} lượt / Tối đa {{ $user->daily_spins_limit ?? 1 }} lượt mỗi 24h">
                                        <i class="fa-solid fa-dharmachakra me-1"></i> {{ $user->spins_left ?? 1 }}/{{ $user->daily_spins_limit ?? 1 }} lượt
                                    </span>
                                    <form action="{{ route('admin.users.adjustSpins', $user->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="add_spins" value="1">
                                        <button type="submit" class="btn btn-outline-success btn-sm rounded-circle p-0" style="width: 24px; height: 24px; font-size: 0.75rem;" title="Cộng thêm 1 lượt quay ngay lập tức">
                                            +1
                                        </button>
                                    </form>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="d-inline-flex align-items-center gap-1">
                                    <span class="badge bg-warning-subtle text-dark border border-warning px-2 py-1 rounded-pill fw-bold" title="1 Xu = 500₫ trừ vào đơn hàng ({{ number_format(($user->coins ?? 0) * 500) }}₫)">
                                        <i class="fa-solid fa-coins text-warning me-1"></i> {{ number_format($user->coins ?? 0) }} Xu
                                    </span>
                                    <span class="text-muted small" style="font-size: 0.75rem;" title="Giới hạn số xu nhận tối đa trong 1 ngày ({{ number_format(($user->daily_coins_limit ?? 10) * 500) }}₫/ngày)">
                                        ({{ $user->daily_coins_limit ?? 10 }} xu/ngày)
                                    </span>
                                    <form action="{{ route('admin.users.adjustCoins', $user->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="add_coins" value="5">
                                        <button type="submit" class="btn btn-outline-warning btn-sm rounded-circle p-0" style="width: 24px; height: 24px; font-size: 0.75rem;" title="Cộng thưởng nhanh +5 Xu (+2.500₫)">
                                            +5
                                        </button>
                                    </form>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="d-inline-flex align-items-center gap-1">
                                    <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-info btn-sm text-white rounded-pill px-3" title="Xem thông tin">
                                        Xem
                                    </a>
                                    <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary btn-sm rounded-pill px-3" title="Chỉnh sửa">
                                        Sửa
                                    </a>
                                    
                                    @if($isAdmin || $isCurrentAuth)
                                        <button class="btn btn-light btn-sm text-muted border rounded-pill px-3 opacity-75" disabled title="Tài khoản Quản trị viên được bảo vệ, không thể xóa">
                                            <i class="fa-solid fa-lock me-1"></i> Khóa
                                        </button>
                                    @else
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline mb-0" onsubmit="return confirm('Bạn có chắc chắn muốn xóa người dùng {{ $user->name }} không?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm rounded-pill px-3" title="Xóa tài khoản">
                                                Xóa
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-user-slash fs-2 mb-2 d-block"></i>
                                Không có người dùng nào.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const checkAll = document.getElementById('check-all-users');
    const itemChecks = document.querySelectorAll('.user-check-item');

    if (checkAll) {
        checkAll.addEventListener('change', function () {
            itemChecks.forEach(cb => cb.checked = checkAll.checked);
        });

        itemChecks.forEach(cb => {
            cb.addEventListener('change', function () {
                checkAll.checked = Array.from(itemChecks).length > 0 && Array.from(itemChecks).every(i => i.checked);
            });
        });
    }
});
</script>
@endsection
