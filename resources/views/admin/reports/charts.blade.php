@extends('admin.layouts.master')
@section('title', 'Biểu đồ báo cáo doanh thu')
@section('content')
<style>
.chart-wrap { min-height: 360px; position: relative; }
.chart-wrap canvas { width: 100% !important; height: 360px !important; }
</style>

<div class="container-fluid px-0">
    <h2 class="fw-bold mb-0">Biểu đồ báo cáo doanh thu</h2>
    
    <nav class="nav nav-pills my-3" aria-label="Báo cáo">
        <a class="nav-link" href="{{ route('admin.reports.index', request()->query()) }}">Bảng số liệu</a>
        <a class="nav-link active" aria-current="page" href="{{ route('admin.reports.charts', request()->query()) }}">Biểu đồ</a>
        <a class="nav-link" href="{{ route('admin.reviews.index') }}">Báo cáo Đánh giá</a>
    </nav>

    <!-- Bộ lọc theo ngày -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.reports.charts') }}" class="row g-2 align-items-center">
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
                    <button type="submit" class="btn btn-primary btn-sm px-3"><i class="fa-solid fa-filter me-1"></i> Lọc biểu đồ</button>
                    <a href="{{ route('admin.reports.charts') }}" class="btn btn-light btn-sm" title="Đặt lại"><i class="fa-solid fa-rotate-left"></i></a>
                </div>
            </form>
        </div>
    </div>

    <p class="text-muted small">Chỉ gồm đơn đã thanh toán, chưa hoàn tiền và không bị hủy hoặc hoàn hàng. Doanh thu tính theo ngày tạo đơn; số liệu theo danh mục không gồm phí vận chuyển.</p>
    
    <div id="report-chart-error" class="alert alert-warning d-none" role="alert">
        Không tải được thư viện biểu đồ. Bạn có thể xem số liệu tại trang <a href="{{ route('admin.reports.index') }}">Bảng số liệu</a>.
    </div>

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white fw-bold">Doanh thu theo danh mục</div>
                <div class="card-body chart-wrap">
                    <canvas id="categoryRevenueChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white fw-bold">Doanh thu theo ngày</div>
                <div class="card-body chart-wrap">
                    <canvas id="revenueByDateChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white fw-bold">Doanh thu theo tháng (12 tháng gần nhất)</div>
                <div class="card-body chart-wrap">
                    <canvas id="revenueByMonthChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white fw-bold">Doanh thu theo năm</div>
                <div class="card-body chart-wrap">
                    <canvas id="revenueByYearChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-12 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white fw-bold">Doanh thu theo phương thức thanh toán</div>
                <div class="card-body chart-wrap" style="max-height: 400px;">
                    <canvas id="revenueByPaymentMethodChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="report-chart-data" hidden data-chart-data="{{ json_encode([
    'catLabels' => $catLabels ?? [],
    'catRevenue' => $catRevenue ?? [],
    'revDateLabels' => $revDateLabels ?? [],
    'revDateData' => $revDateData ?? [],
    'revMonthLabels' => $revMonthLabels ?? [],
    'revMonthData' => $revMonthData ?? [],
    'revYearLabels' => $revYearLabels ?? [],
    'revYearData' => $revYearData ?? [],
    'paymentMethodLabels' => $paymentMethodLabels ?? [],
    'paymentMethodRevenue' => $paymentMethodRevenue ?? [],
]) }}"></div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
window.addEventListener('DOMContentLoaded', () => {
    if (typeof Chart === 'undefined') {
        document.getElementById('report-chart-error').classList.remove('d-none');
        return;
    }

    const reportData = JSON.parse(document.getElementById('report-chart-data').dataset.chartData);

    const catLabels = reportData.catLabels;
    const catRevenue = reportData.catRevenue.map(Number);
    const revDateLabels = reportData.revDateLabels;
    const revDateData = reportData.revDateData.map(Number);
    const revMonthLabels = reportData.revMonthLabels;
    const revMonthData = reportData.revMonthData.map(Number);
    const revYearLabels = reportData.revYearLabels;
    const revYearData = reportData.revYearData.map(Number);
    const payLabels = reportData.paymentMethodLabels;
    const payRevenue = reportData.paymentMethodRevenue.map(Number);

    const mk = (el, type, labels, data, label) => {
        if (!el) return;
        new Chart(el, {
            type: type,
            data: {
                labels: labels,
                datasets: [{
                    label: label,
                    data: data,
                    backgroundColor: type === 'line' ? 'rgba(13, 110, 253, 0.1)' : 'rgba(13, 110, 253, 0.7)',
                    borderColor: '#0d6efd',
                    borderWidth: 2,
                    fill: type === 'line',
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    };

    mk(document.getElementById('categoryRevenueChart'), 'bar', catLabels, catRevenue, 'Doanh thu (VNĐ)');
    mk(document.getElementById('revenueByDateChart'), 'line', revDateLabels, revDateData, 'Doanh thu (VNĐ)');
    mk(document.getElementById('revenueByMonthChart'), 'bar', revMonthLabels, revMonthData, 'Doanh thu (VNĐ)');
    mk(document.getElementById('revenueByYearChart'), 'bar', revYearLabels, revYearData, 'Doanh thu (VNĐ)');

    const payChartEl = document.getElementById('revenueByPaymentMethodChart');
    if (payChartEl) {
        new Chart(payChartEl, {
            type: 'pie',
            data: {
                labels: payLabels,
                datasets: [{
                    label: 'Doanh thu (VNĐ)',
                    data: payRevenue,
                    backgroundColor: ['#d63384', '#198754', '#6c757d']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }
});
</script>
@endsection
