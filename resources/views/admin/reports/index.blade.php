@extends('admin.layouts.master')
@section('title', 'Báo cáo doanh thu')
@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h2 class="fw-bold mb-0">Báo cáo doanh thu</h2>
    </div>
    
    <nav class="nav nav-pills my-3" aria-label="Báo cáo">
        <a class="nav-link active" aria-current="page" href="{{ route('admin.reports.index', request()->query()) }}">Bảng số liệu</a>
        <a class="nav-link" href="{{ route('admin.reports.charts', request()->query()) }}">Biểu đồ</a>
        <a class="nav-link" href="{{ route('admin.reviews.index') }}">Báo cáo Đánh giá</a>
    </nav>

    <!-- Bộ lọc theo ngày -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.reports.index') }}" class="row g-2 align-items-center">
                <div class="col-md-4 col-lg-3">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white">Từ ngày</span>
                        <input type="date" name="date_from" class="form-control" value="{{ $dateFrom ?? '' }}">
                    </div>
                </div>
                <div class="col-md-4 col-lg-3">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white">Đến ngày</span>
                        <input type="date" name="date_to" class="form-control" value="{{ $dateTo ?? '' }}">
                    </div>
                </div>
                <div class="col-md-4 col-lg-3 d-flex gap-1">
                    <button type="submit" class="btn btn-primary btn-sm px-3"><i class="fa-solid fa-filter me-1"></i> Lọc dữ liệu</button>
                    <a href="{{ route('admin.reports.index') }}" class="btn btn-light btn-sm" title="Đặt lại"><i class="fa-solid fa-rotate-left"></i></a>
                </div>
            </form>
        </div>
    </div>

    <p class="text-muted small">Doanh thu tính theo ngày tạo đơn, chỉ gồm đơn đã thanh toán, chưa hoàn tiền và không bị hủy hoặc hoàn hàng.</p>

    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card card-body h-100 shadow-sm border-0">
                <span class="text-muted small">Tổng số đơn hàng</span>
                <h3 class="mb-0 fw-bold">{{ number_format($totalOrders) }}</h3>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card card-body h-100 shadow-sm border-0">
                <span class="text-muted small">Tổng số khách hàng</span>
                <h3 class="mb-0 fw-bold">{{ number_format($totalCustomers) }}</h3>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card card-body h-100 shadow-sm border-0">
                <span class="text-muted small">Tổng doanh thu (gồm phí vận chuyển)</span>
                <h3 class="mb-0 text-success fw-bold">{{ number_format($totalRevenue, 0, ',', '.') }} đ</h3>
            </div>
        </div>
    </div>

    <div class="card mb-4 shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <strong>Doanh thu theo danh mục</strong>
            <div class="small text-muted">Tính theo giá sản phẩm khi đặt hàng, không gồm phí vận chuyển.</div>
        </div>
        <div class="table-responsive">
            <table class="table table-striped align-middle mb-0">
                <thead>
                    <tr>
                        <th>Danh mục</th>
                        <th class="text-end">Số lượng bán</th>
                        <th class="text-end">Doanh thu</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categoryRevenue as $revenue)
                        <tr>
                            <td>{{ $revenue->category_name ?? ('Danh mục #'.$revenue->category_id) }}</td>
                            <td class="text-end">{{ number_format($revenue->total_qty) }}</td>
                            <td class="text-end text-primary fw-bold">{{ number_format($revenue->total_revenue, 0, ',', '.') }} đ</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center text-muted py-4">Chưa có doanh thu trong khoảng thời gian này.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- HÀNG THỐNG KÊ: TOP SẢN PHẨM BÁN CHẠY & TOP KHÁCH HÀNG MUA NHIỀU NHẤT -->
    <div class="row mb-4 g-3">
        <!-- Cột trái: Top 10 Sản phẩm bán chạy nhất -->
        <div class="col-12 col-xl-6">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <strong class="text-dark"><i class="fa-solid fa-trophy text-warning me-2"></i>Top 10 Sản phẩm bán chạy nhất</strong>
                        <div class="small text-muted">Xếp hạng theo số lượng máy đã bán ra thành công</div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small">
                            <tr>
                                <th width="50px" class="text-center">#</th>
                                <th>SẢN PHẨM</th>
                                <th class="text-center">ĐÃ BÁN</th>
                                <th class="text-end">DOANH THU</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topProducts as $idx => $tp)
                                <tr>
                                    <td class="text-center">
                                        <span class="badge {{ $idx === 0 ? 'bg-danger' : ($idx === 1 ? 'bg-warning text-dark' : ($idx === 2 ? 'bg-primary' : 'bg-secondary-subtle text-dark')) }} rounded-pill px-2">
                                            #{{ $idx + 1 }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            @if($tp->image)
                                                <img src="{{ asset($tp->image) }}" alt="" style="width: 36px; height: 36px; object-fit: contain;" class="rounded border p-1 bg-light">
                                            @endif
                                            <div>
                                                <div class="fw-semibold text-dark text-truncate" style="max-width: 220px;" title="{{ $tp->name }}">
                                                    {{ $tp->name }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-danger-subtle text-danger fw-bold px-2 py-1 rounded-pill">
                                            {{ number_format($tp->total_sold) }} chiếc
                                        </span>
                                    </td>
                                    <td class="text-end fw-bold text-primary">
                                        {{ number_format($tp->total_revenue, 0, ',', '.') }}₫
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted py-4">Chưa có sản phẩm nào bán ra trong khoảng thời gian này.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Cột phải: Top 10 Khách hàng mua nhiều nhất -->
        <div class="col-12 col-xl-6">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <strong class="text-dark"><i class="fa-solid fa-crown text-warning me-2"></i>Top 10 Khách hàng chi tiêu nhiều nhất</strong>
                        <div class="small text-muted">Xếp hạng theo tổng giá trị các đơn hàng thành công</div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small">
                            <tr>
                                <th width="50px" class="text-center">#</th>
                                <th>KHÁCH HÀNG</th>
                                <th class="text-center">ĐƠN HÀNG</th>
                                <th class="text-end">TỔNG CHI TIÊU</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topCustomers as $idx => $tc)
                                <tr>
                                    <td class="text-center">
                                        <span class="badge {{ $idx === 0 ? 'bg-danger' : ($idx === 1 ? 'bg-warning text-dark' : ($idx === 2 ? 'bg-primary' : 'bg-secondary-subtle text-dark')) }} rounded-pill px-2">
                                            #{{ $idx + 1 }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $tc->name }}</div>
                                        <small class="text-muted">{{ $tc->email }}</small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary-subtle text-primary rounded-pill px-2 py-1 fw-semibold">
                                            {{ number_format($tc->total_orders) }} đơn
                                        </span>
                                    </td>
                                    <td class="text-end fw-bold text-success">
                                        {{ number_format($tc->total_spent, 0, ',', '.') }}₫
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted py-4">Chưa có khách hàng phát sinh đơn hàng thành công trong khoảng thời gian này.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @foreach([
        ['Doanh thu theo ngày', 'Ngày', 'date', $revenueByDate, 'd/m/Y'],
        ['Doanh thu theo tháng', 'Tháng', 'month', $revenueByMonth, 'm/Y'],
        ['Doanh thu theo năm', 'Năm', 'year', $revenueByYear, null],
    ] as [$title, $label, $field, $rows, $format])
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-header bg-white py-3 fw-bold">{{ $title }}</div>
            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0">
                    <thead>
                        <tr>
                            <th>{{ $label }}</th>
                            <th class="text-end">Số đơn đã thanh toán</th>
                            <th class="text-end">Doanh thu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $revenue)
                            <tr>
                                <td>{{ $format ? \Carbon\Carbon::parse($revenue->{$field}.($field === 'month' ? '-01' : ''))->format($format) : $revenue->{$field} }}</td>
                                <td class="text-end">{{ number_format($revenue->order_count) }}</td>
                                <td class="text-end text-primary fw-bold">{{ number_format($revenue->total_revenue, 0, ',', '.') }} đ</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted py-4">Chưa có doanh thu trong khoảng thời gian này.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
</div>
@endsection
