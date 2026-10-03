@extends('admin.layouts.master')

@section('content')
<div class="container-fluid px-0">
    <!-- Breadcrumb & Header -->
    <nav aria-label="breadcrumb" class="mb-2">
        <ol class="breadcrumb mb-1">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Quản trị</a></li>
            <li class="breadcrumb-item active" aria-current="page">Thống kê tài chính</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="fw-bold mb-0">Thống kê tài chính</h3>
            <span class="text-muted small">Tổng hợp giá trị thanh toán theo trạng thái và phương thức</span>
        </div>
    </div>

    <!-- Sub-navigation tabs -->
    <ul class="nav nav-tabs mb-4 border-bottom">
        <li class="nav-item">
            <a class="nav-link active fw-semibold" href="{{ route('admin.finance.index') }}">
                Thống kê chỉ số
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-secondary" href="{{ route('admin.finance.transactions') }}">
                Giao dịch thanh toán
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
            <form method="GET" action="{{ route('admin.finance.index') }}">
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

                <div class="row g-3 align-items-end">
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

                <div class="d-flex gap-2 mt-3">
                    <button type="submit" class="btn btn-primary px-3">
                        <i class="fa-solid fa-filter me-1"></i> Áp dụng bộ lọc
                    </button>
                    <a href="{{ route('admin.finance.index') }}" class="btn btn-light border px-3">
                        <i class="fa-solid fa-rotate-left me-1"></i> Xóa bộ lọc
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Text thông báo kết quả lọc -->
    <div class="text-muted small mb-3">
        Có <strong class="text-dark">{{ number_format($summary->order_count ?? 0) }}</strong> đơn phù hợp. Số tiền bao gồm phí vận chuyển; thống kê theo ngày tạo đơn trên toàn bộ kết quả lọc.
    </div>

    <!-- 8 thẻ chỉ số thống kê (Grid 4x2) -->
    <div class="row g-3 mb-4">
        <!-- Tổng giá trị đơn hàng -->
        <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm border-0 h-100 bg-light">
                <div class="card-body p-3">
                    <div class="text-muted small fw-medium mb-1">Tổng giá trị đơn hàng</div>
                    <div class="h4 fw-bold text-primary mb-1">{{ number_format($summary->total_amount ?? 0) }} đ</div>
                    <div class="text-muted small">{{ number_format($summary->order_count ?? 0) }} đơn, bao gồm đơn đã hủy</div>
                </div>
            </div>
        </div>

        <!-- Chờ thanh toán -->
        <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm border-0 h-100 bg-light">
                <div class="card-body p-3">
                    <div class="text-muted small fw-medium mb-1">Chờ thanh toán</div>
                    <div class="h4 fw-bold text-warning mb-1">{{ number_format($statusTotals['pending']->total_amount ?? 0) }} đ</div>
                    <div class="text-muted small">{{ number_format($statusTotals['pending']->order_count ?? 0) }} đơn</div>
                </div>
            </div>
        </div>

        <!-- Đang chờ MoMo -->
        <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm border-0 h-100 bg-light">
                <div class="card-body p-3">
                    <div class="text-muted small fw-medium mb-1">Đang chờ MoMo</div>
                    <div class="h4 fw-bold text-info mb-1">{{ number_format($statusTotals['initiated']->total_amount ?? 0) }} đ</div>
                    <div class="text-muted small">{{ number_format($statusTotals['initiated']->order_count ?? 0) }} đơn</div>
                </div>
            </div>
        </div>

        <!-- Đã thanh toán -->
        <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm border-0 h-100 bg-light">
                <div class="card-body p-3">
                    <div class="text-muted small fw-medium mb-1">Đã thanh toán</div>
                    <div class="h4 fw-bold text-success mb-1">{{ number_format($statusTotals['paid']->total_amount ?? 0) }} đ</div>
                    <div class="text-muted small">{{ number_format($statusTotals['paid']->order_count ?? 0) }} đơn</div>
                </div>
            </div>
        </div>

        <!-- Thanh toán thất bại -->
        <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm border-0 h-100 bg-light">
                <div class="card-body p-3">
                    <div class="text-muted small fw-medium mb-1">Thanh toán thất bại</div>
                    <div class="h4 fw-bold text-danger mb-1">{{ number_format($statusTotals['failed']->total_amount ?? 0) }} đ</div>
                    <div class="text-muted small">{{ number_format($statusTotals['failed']->order_count ?? 0) }} đơn</div>
                </div>
            </div>
        </div>

        <!-- Đã hủy -->
        <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm border-0 h-100 bg-light">
                <div class="card-body p-3">
                    <div class="text-muted small fw-medium mb-1">Đã hủy</div>
                    <div class="h4 fw-bold text-secondary mb-1">{{ number_format($statusTotals['cancelled']->total_amount ?? 0) }} đ</div>
                    <div class="text-muted small">{{ number_format($statusTotals['cancelled']->order_count ?? 0) }} đơn</div>
                </div>
            </div>
        </div>

        <!-- Chờ hoàn tiền -->
        <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm border-0 h-100 bg-light">
                <div class="card-body p-3">
                    <div class="text-muted small fw-medium mb-1">Chờ hoàn tiền</div>
                    <div class="h4 fw-bold text-warning mb-1">{{ number_format($statusTotals['refund_pending']->total_amount ?? 0) }} đ</div>
                    <div class="text-muted small">{{ number_format($statusTotals['refund_pending']->order_count ?? 0) }} đơn</div>
                </div>
            </div>
        </div>

        <!-- Đã hoàn tiền -->
        <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm border-0 h-100 bg-light">
                <div class="card-body p-3">
                    <div class="text-muted small fw-medium mb-1">Đã hoàn tiền</div>
                    <div class="h4 fw-bold text-dark mb-1">{{ number_format($statusTotals['refunded']->total_amount ?? 0) }} đ</div>
                    <div class="text-muted small">{{ number_format($statusTotals['refunded']->order_count ?? 0) }} đơn</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Thống kê theo phương thức -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3 border-0">
            <h5 class="fw-bold mb-0">Thống kê theo phương thức</h5>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0 table-hover">
                <thead class="table-light">
                    <tr>
                        <th class="py-2 px-3">Phương thức</th>
                        <th class="py-2 px-3 text-center">Số đơn</th>
                        <th class="py-2 px-3 text-end">Tổng giá trị</th>
                        <th class="py-2 px-3 text-end">Đã thanh toán</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($methods as $key => $name)
                        @php
                            $row = $methodTotals[$key] ?? null;
                        @endphp
                        <tr>
                            <td class="py-3 px-3">
                                @if ($key === 'cod')
                                    <span class="badge bg-primary-subtle text-primary border border-primary px-2 py-1"><i class="fa-solid fa-truck-ramp-box me-1"></i> COD</span>
                                @elseif ($key === 'momo')
                                    <span class="badge bg-danger-subtle text-danger border border-danger px-2 py-1"><i class="fa-solid fa-wallet me-1"></i> MoMo</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary px-2 py-1"><i class="fa-solid fa-question me-1"></i> Chưa xác định</span>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-center fw-medium">
                                {{ number_format($row->order_count ?? 0) }}
                            </td>
                            <td class="py-3 px-3 text-end fw-semibold">
                                {{ number_format($row->total_amount ?? 0) }} đ
                            </td>
                            <td class="py-3 px-3 text-end text-success fw-bold">
                                {{ number_format($row->paid_amount ?? 0) }} đ
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">Không có dữ liệu</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
