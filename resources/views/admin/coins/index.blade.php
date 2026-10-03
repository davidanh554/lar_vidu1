@extends('admin.layouts.master')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="fw-bold mb-1">Quản Lý Xu & Hạn Mức Tích Xu</h3>
            <p class="text-muted small mb-0">Quản lý cơ chế tích xu xem video, tỷ lệ đổi tiền mặt (1 Xu = 500₫) và kiểm soát hạn mức nhận mỗi ngày</p>
        </div>
        <a href="{{ route('videos.index') }}" target="_blank" class="btn btn-outline-dark rounded-pill px-3 shadow-sm">
            Kiểm tra Giao diện Video
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- 1. Thống kê tổng quan -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-4 p-3 bg-white">
                <div class="text-muted small fw-semibold">TỔNG XU ĐANG CÓ</div>
                <div class="fs-4 fw-bold text-dark mt-1">{{ number_format($totalCoinsInCirculation) }} Xu</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-4 p-3 bg-white">
                <div class="text-muted small fw-semibold">QUY ĐỔI TIỀN MẶT</div>
                <div class="fs-4 fw-bold text-success mt-1">{{ number_format($totalMoneyValue) }}₫</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-4 p-3 bg-white">
                <div class="text-muted small fw-semibold">USER ĐANG TÍCH XU</div>
                <div class="fs-4 fw-bold text-info mt-1">{{ number_format($usersWithCoinsCount) }} người</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-4 p-3 bg-white">
                <div class="text-muted small fw-semibold">XU ĐÃ PHÁT HÔM NAY</div>
                <div class="fs-4 fw-bold text-primary mt-1">{{ number_format($coinsEarnedToday) }} Xu</div>
            </div>
        </div>
    </div>

    <!-- 2. Form Cấu Hình Hạn Mức Xu Toàn Hệ Thống -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
            <h5 class="fw-bold mb-1">Cấu Hình Hạn Mức Thưởng & Tỷ Lệ Đổi Tiền Toàn Hệ Thống</h5>
            <p class="text-muted small mb-0">Admin có toàn quyền thiết lập giới hạn nhận xu mỗi ngày và cơ chế vòng đếm thời gian</p>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('admin.coins.settings') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <!-- Tỷ lệ quy đổi 1 Xu -->
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Giá trị 1 Xu (VNĐ) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="coin_rate" class="form-control rounded-start-3" value="{{ old('coin_rate', $coinRate) }}" min="1" required>
                            <span class="input-group-text bg-light">₫ / Xu</span>
                        </div>
                        <small class="text-muted">Mặc định: 500₫ (1 Xu trừ trực tiếp 500₫ khi thanh toán)</small>
                    </div>

                    <!-- Giới hạn nhận Xu mỗi ngày -->
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Giới hạn nhận tối đa / ngày <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="daily_coins_limit" class="form-control rounded-start-3" value="{{ old('daily_coins_limit', $dailyLimit) }}" min="1" max="1000" required>
                            <span class="input-group-text bg-light">Xu / ngày</span>
                        </div>
                        <small class="text-success fw-semibold">Tương đương tối đa: {{ number_format($dailyLimit * $coinRate) }}₫ / ngày</small>
                    </div>

                    <!-- Thời gian xem video mỗi chu kỳ -->
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Thời gian đếm chu kỳ xem <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="coin_watch_seconds" class="form-control rounded-start-3" value="{{ old('coin_watch_seconds', $watchSeconds) }}" min="5" max="300" required>
                            <span class="input-group-text bg-light">giây</span>
                        </div>
                        <small class="text-muted">Mặc định: 30 giây (xem đủ 30s được cộng xu)</small>
                    </div>

                    <!-- Số xu mỗi chu kỳ -->
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Số Xu nhận mỗi chu kỳ <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="coin_per_reward" class="form-control rounded-start-3" value="{{ old('coin_per_reward', $coinPerReward) }}" min="1" max="100" required>
                            <span class="input-group-text bg-light">Xu</span>
                        </div>
                        <small class="text-muted">Mặc định: 1 Xu / 30 giây</small>
                    </div>
                </div>

                <div class="form-check mt-3">
                    <input class="form-check-input" type="checkbox" name="sync_all_users" id="sync_all_users" value="1">
                    <label class="form-check-label fw-semibold text-danger" for="sync_all_users">
                        Đồng bộ hạn mức ngày mới này cho toàn bộ khách hàng hiện tại ngay lập tức
                    </label>
                </div>

                <div class="mt-4 pt-2 border-top d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">
                        <i class="fa-solid fa-check me-1"></i> Lưu Cấu Hình Hệ Thống
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 3. Quản lý Xu & Hạn mức của Từng Khách Hàng -->
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-header bg-white border-0 pt-4 px-4 pb-2">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h5 class="fw-bold mb-1"><i class="fa-solid fa-user-gear text-secondary me-2"></i>Quản Lý Xu & Hạn Mức Cá Nhân Khách Hàng</h5>
                    <p class="text-muted small mb-0">Cộng/trừ xu thưởng hoặc điều chỉnh hạn mức ngày riêng biệt cho từng khách hàng</p>
                </div>
                <form action="{{ route('admin.coins.index') }}" method="GET" class="d-flex gap-2" style="max-width: 320px;">
                    <input type="text" name="search" class="form-control form-control-sm rounded-3" placeholder="Tìm tên, email khách..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-sm btn-dark rounded-3 px-3">Tìm</button>
                    @if(request('search'))
                        <a href="{{ route('admin.coins.index') }}" class="btn btn-sm btn-outline-secondary rounded-3">Hủy</a>
                    @endif
                </form>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="60px">ID</th>
                        <th>KHÁCH HÀNG</th>
                        <th>VÍ XU HIỆN CÓ</th>
                        <th>GIÁ TRỊ TIỀN MẶT</th>
                        <th>ĐÃ NHẬN HÔM NAY</th>
                        <th>HẠN MỨC NGÀY (CAP)</th>
                        <th class="text-center" width="220px">HÀNH ĐỘNG</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $u)
                        <tr>
                            <td class="text-muted font-monospace">#{{ $u->id }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $u->name }}</div>
                                <small class="text-muted">{{ $u->email }}</small>
                                @if($u->role === 'admin')
                                    <span class="badge bg-danger ms-1" style="font-size: 0.65rem;">Admin</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-warning bg-opacity-25 text-warning-emphasis fs-6 px-3 py-1 rounded-pill fw-bold border border-warning-subtle">
                                    <i class="fa-solid fa-coins me-1 text-warning"></i>{{ number_format($u->coins) }} Xu
                                </span>
                            </td>
                            <td>
                                <span class="text-success fw-bold font-monospace">
                                    {{ number_format($u->coins * $coinRate) }}₫
                                </span>
                            </td>
                            <td>
                                <span class="text-muted small">
                                    {{ number_format($u->coins_earned_today ?? 0) }} Xu
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-info-subtle text-info fw-bold fs-6">
                                    {{ $u->daily_coins_limit ?? $dailyLimit }} Xu / ngày
                                </span>
                                <small class="text-muted d-block" style="font-size: 0.75rem;">(Tối đa {{ number_format(($u->daily_coins_limit ?? $dailyLimit) * $coinRate) }}₫)</small>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center align-items-center gap-1">
                                    <!-- Nút cộng nhanh 5 xu -->
                                    <form action="{{ route('admin.coins.adjust', $u) }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="action" value="add">
                                        <input type="hidden" name="amount" value="5">
                                        <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-2 py-0" title="Cộng nhanh +5 Xu" style="font-size: 0.75rem;">
                                            +5 Xu
                                        </button>
                                    </form>

                                    <!-- Nút trừ nhanh 5 xu -->
                                    <form action="{{ route('admin.coins.adjust', $u) }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="action" value="subtract">
                                        <input type="hidden" name="amount" value="5">
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-0" title="Trừ nhanh -5 Xu" style="font-size: 0.75rem;">
                                            -5 Xu
                                        </button>
                                    </form>

                                    <!-- Nút mở modal chỉnh sửa chi tiết -->
                                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-2 py-0 ms-1" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editCoinModal{{ $u->id }}" 
                                            style="font-size: 0.75rem;">
                                        <i class="fa-solid fa-gear me-1"></i>Sửa
                                    </button>
                                </div>

                                <!-- Modal Điều Chỉnh Chi Tiết Cho Khách Hàng -->
                                <div class="modal fade text-start" id="editCoinModal{{ $u->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content rounded-4 border-0 shadow">
                                            <div class="modal-header border-0 pb-0">
                                                <h5 class="modal-title fw-bold">
                                                    <i class="fa-solid fa-coins text-warning me-2"></i>Điều Chỉnh Xu: {{ $u->name }}
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body p-4">
                                                <div class="bg-light p-3 rounded-3 mb-3">
                                                    <div class="d-flex justify-content-between mb-1">
                                                        <span class="text-muted">Ví hiện có:</span>
                                                        <span class="fw-bold text-warning-emphasis">{{ number_format($u->coins) }} Xu (={{ number_format($u->coins * $coinRate) }}₫)</span>
                                                    </div>
                                                    <div class="d-flex justify-content-between">
                                                        <span class="text-muted">Hạn mức nhận/ngày hiện tại:</span>
                                                        <span class="fw-bold text-info">{{ $u->daily_coins_limit ?? $dailyLimit }} Xu/ngày</span>
                                                    </div>
                                                </div>

                                                <!-- Form 1: Đổi trực tiếp số Xu -->
                                                <form action="{{ route('admin.coins.adjust', $u) }}" method="POST" class="mb-3 pb-3 border-bottom">
                                                    @csrf
                                                    <input type="hidden" name="action" value="set">
                                                    <label class="form-label fw-bold small">1. Đặt lại số Xu cố định trong ví:</label>
                                                    <div class="input-group">
                                                        <input type="number" name="amount" class="form-control form-control-sm" value="{{ $u->coins }}" min="0" required>
                                                        <button type="submit" class="btn btn-sm btn-dark">Lưu Số Xu</button>
                                                    </div>
                                                </form>

                                                <!-- Form 2: Đổi hạn mức nhận ngày riêng -->
                                                <form action="{{ route('admin.coins.adjust', $u) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="action" value="update_limit">
                                                    <label class="form-label fw-bold small">2. Điều chỉnh Hạn Mức Nhận Xu Mỗi Ngày cho khách này:</label>
                                                    <div class="input-group">
                                                        <input type="number" name="amount" class="form-control form-control-sm" value="{{ $u->daily_coins_limit ?? $dailyLimit }}" min="0" max="500" required>
                                                        <span class="input-group-text small">Xu/ngày</span>
                                                        <button type="submit" class="btn btn-sm btn-primary">Lưu Hạn Mức</button>
                                                    </div>
                                                    <small class="text-muted" style="font-size: 0.75rem;">(Khách này sẽ chỉ nhận tối đa số xu này trong 24h, sau 24h sẽ reset)</small>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                Không tìm thấy khách hàng nào.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $users->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>
@endsection
