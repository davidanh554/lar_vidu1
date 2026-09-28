@extends('admin.layouts.master')

@section('content')
<div class="container-fluid px-0">
    <!-- Breadcrumb & Header -->
    <nav aria-label="breadcrumb" class="mb-2">
        <ol class="breadcrumb mb-1">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Quản trị</a></li>
            <li class="breadcrumb-item active" aria-current="page">Giao dịch thanh toán</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="fw-bold mb-0">Giao dịch thanh toán</h3>
            <span class="text-muted small">Tra cứu thanh toán theo đơn hàng và cập nhật trạng thái COD.</span>
        </div>
    </div>

    <!-- Sub-navigation tabs -->
    <ul class="nav nav-tabs mb-4 border-bottom">
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('admin.finance.index') }}">
                <i class="fa-solid fa-chart-pie me-1"></i> Thống kê chỉ số
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link active fw-semibold" href="{{ route('admin.finance.transactions') }}">
                <i class="fa-solid fa-list-check me-1"></i> Giao dịch thanh toán
            </a>
        </li>
    </ul>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Card Bộ lọc -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.finance.transactions') }}">
                <div class="row g-3 mb-2">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold text-muted mb-1">Tìm đơn hàng</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Mã đơn, tên hoặc số điện thoại" value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold text-muted mb-1">Từ ngày tạo đơn</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold text-muted mb-1">Đến ngày tạo đơn</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold text-muted mb-1">Số tiền từ (đ)</label>
                        <input type="number" step="any" name="min_amount" class="form-control" placeholder="Không giới hạn" value="{{ request('min_amount') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold text-muted mb-1">Số tiền đến (đ)</label>
                        <input type="number" step="any" name="max_amount" class="form-control" placeholder="Không giới hạn" value="{{ request('max_amount') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold text-muted mb-1">Phương thức</label>
                        <select name="gateway" class="form-select">
                            <option value="">Tất cả</option>
                            @foreach ($methods as $key => $name)
                                <option value="{{ $key }}" @selected(request('gateway') === $key)>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold text-muted mb-1">Trạng thái thanh toán</label>
                        <select name="payment_status" class="form-select">
                            <option value="">Tất cả</option>
                            @foreach ($statuses as $key => $name)
                                <option value="{{ $key }}" @selected(request('payment_status') === $key)>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold text-muted mb-1">Sắp xếp</label>
                        <select name="sort" class="form-select">
                            <option value="newest" @selected(request('sort', 'newest') === 'newest')>Mới nhất</option>
                            <option value="oldest" @selected(request('sort') === 'oldest')>Cũ nhất</option>
                            <option value="amount_asc" @selected(request('sort') === 'amount_asc')>Số tiền tăng dần</option>
                            <option value="amount_desc" @selected(request('sort') === 'amount_desc')>Số tiền giảm dần</option>
                        </select>
                    </div>
                    <div class="col-md-9 d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-3">
                            <i class="fa-solid fa-filter me-1"></i> Áp dụng bộ lọc
                        </button>
                        <a href="{{ route('admin.finance.transactions') }}" class="btn btn-light border px-3">
                            <i class="fa-solid fa-rotate-left me-1"></i> Xóa bộ lọc
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Thông báo số lượng -->
    <div class="text-muted small mb-3">
        Có <strong class="text-dark">{{ number_format($orders->total()) }}</strong> đơn phù hợp bộ lọc. Số tiền bao gồm phí vận chuyển; ngày lọc là ngày tạo đơn.
    </div>

    <!-- Bảng danh sách giao dịch -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3 border-0">
            <h5 class="fw-bold mb-1">Danh sách giao dịch ({{ number_format($orders->total()) }} đơn)</h5>
            <div class="text-muted small">COD: xác nhận thu tiền hoặc thất bại; đơn đã thu tiền có thể chuyển sang chờ hoàn tiền rồi xác nhận đã hoàn tiền.</div>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0 table-hover">
                <thead class="table-light">
                    <tr>
                        <th class="py-2 px-3">Đơn hàng</th>
                        <th class="py-2 px-3">Khách hàng</th>
                        <th class="py-2 px-3">Phương thức</th>
                        <th class="py-2 px-3 text-end">Số tiền</th>
                        <th class="py-2 px-3">Thanh toán</th>
                        <th class="py-2 px-3" style="min-width: 230px;">Cập nhật COD</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr>
                            <td class="py-3 px-3">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="fw-bold text-decoration-none text-primary">
                                    #{{ $order->id }}
                                </a>
                                <div class="text-muted small">
                                    {{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i') }}
                                </div>
                            </td>
                            <td class="py-3 px-3">
                                <div class="fw-semibold text-dark">{{ $order->name }}</div>
                                <div class="text-muted small">{{ $order->phone }}</div>
                            </td>
                            <td class="py-3 px-3">
                                @if ($order->gateway === 'cod')
                                    <span class="badge bg-primary-subtle text-primary border border-primary px-2 py-1">
                                        <i class="fa-solid fa-truck-ramp-box me-1"></i> COD
                                    </span>
                                @elseif ($order->gateway === 'momo')
                                    <span class="badge bg-danger-subtle text-danger border border-danger px-2 py-1">
                                        <i class="fa-solid fa-wallet me-1"></i> MoMo
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary px-2 py-1">
                                        <i class="fa-solid fa-question me-1"></i> Chưa xác định
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-end fw-bold text-dark">
                                {{ number_format($order->total_price) }} đ
                            </td>
                            <td class="py-3 px-3">
                                @php
                                    $badgeClass = match ($order->payment_status) {
                                        'paid' => 'bg-success-subtle text-success border border-success',
                                        'pending' => 'bg-warning-subtle text-warning border border-warning',
                                        'initiated' => 'bg-info-subtle text-info border border-info',
                                        'failed', 'cancelled' => 'bg-danger-subtle text-danger border border-danger',
                                        'refund_pending' => 'bg-warning-subtle text-warning border border-warning',
                                        'refunded' => 'bg-secondary-subtle text-secondary border border-secondary',
                                        default => 'bg-light text-dark border',
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }} px-2 py-1">
                                    {{ $statuses[$order->payment_status] ?? $order->payment_status }}
                                </span>
                                @if ($order->paid_at)
                                    <div class="text-muted small mt-1">
                                        <i class="fa-regular fa-clock me-1"></i>{{ \Carbon\Carbon::parse($order->paid_at)->format('d/m/Y H:i') }}
                                    </div>
                                @endif
                            </td>
                            <td class="py-3 px-3">
                                @if ($order->gateway === 'cod')
                                    @php
                                        $allowedTransitions = $codTransitions[$order->payment_status] ?? [];
                                    @endphp
                                    <form method="POST" action="{{ route('admin.finance.update-status', $order->id) }}" class="d-flex align-items-center gap-1">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="current_payment_status" value="{{ $order->payment_status }}">
                                        <input type="hidden" name="current_order_status" value="{{ $order->status }}">
                                        <input type="hidden" name="current_payment_id" value="{{ $order->payment_id ?? 0 }}">
                                        
                                        <select name="payment_status" class="form-select form-select-sm" style="min-width: 140px;">
                                            @foreach ($allowedTransitions as $statusKey)
                                                <option value="{{ $statusKey }}" @selected($order->payment_status === $statusKey)>
                                                    {{ $statuses[$statusKey] ?? $statusKey }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn btn-sm btn-outline-primary text-nowrap" onclick="return confirm('Bạn có chắc muốn lưu cập nhật trạng thái đơn COD #{{ $order->id }}?')">
                                            Lưu
                                        </button>
                                    </form>
                                @else
                                    <span class="text-muted small fst-italic">Cổng tự động</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="fa-regular fa-folder-open fa-2x mb-2 d-block"></i>
                                Không có đơn hàng phù hợp với bộ lọc.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($orders->hasPages())
            <div class="card-footer bg-white d-flex justify-content-end p-2 border-top">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
