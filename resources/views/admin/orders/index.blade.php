@extends('admin.layouts.master')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="fw-bold mb-0">Quản lý Đơn hàng</h3>
            <span class="text-muted small">Quản lý và cập nhật tiến trình xử lý đơn hàng</span>
        </div>
        <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-primary btn-sm">
            Báo cáo doanh thu
        </a>
    </div>

    <!-- Bộ lọc & Tìm kiếm -->
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.orders.index') }}" class="row g-2 align-items-center">
                <input type="hidden" name="tab" value="{{ $activeTab }}">
                
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Mã đơn, khách hàng, SĐT, sản phẩm..." value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">-- Trạng thái đơn --</option>
                        <option value="pending" @selected(request('status') === 'pending')>Chờ xử lý</option>
                        <option value="cod_ordered" @selected(request('status') === 'cod_ordered')>COD đã đặt</option>
                        <option value="cod_paid" @selected(request('status') === 'cod_paid')>COD đã thu</option>
                        <option value="paid" @selected(request('status') === 'paid')>Đã thanh toán</option>
                        <option value="paid_momo" @selected(request('status') === 'paid_momo')>Đã trả qua MoMo</option>
                        <option value="cancelled" @selected(request('status') === 'cancelled')>Đã hủy</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}" title="Từ ngày">
                </div>

                <div class="col-md-2">
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}" title="Đến ngày">
                </div>

                <div class="col-md-2 d-flex gap-1">
                    <button type="submit" class="btn btn-primary w-100"><i class="fa-solid fa-filter me-1"></i> Lọc</button>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-light" title="Đặt lại"><i class="fa-solid fa-rotate-left"></i></a>
                </div>
            </form>
        </div>
    </div>

    <!-- Thanh Tabs trạng thái -->
    <div class="mb-3 overflow-auto">
        <ul class="nav nav-pills flex-nowrap gap-1 pb-1">
            @foreach($tabs as $key => $t)
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab === $key ? 'active bg-primary' : 'bg-white text-dark border' }} py-1 px-3 rounded-pill small fw-semibold" 
                       href="{{ route('admin.orders.index', array_merge(request()->except('page'), ['tab' => $key])) }}">
                        {{ $t['label'] }}
                        <span class="badge rounded-pill {{ $activeTab === $key ? 'bg-white text-primary' : 'bg-light text-dark border' }} ms-1">
                            {{ $t['count'] }}
                        </span>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>

    <!-- Form Cập nhật trạng thái hàng loạt qua Checkbox -->
    <form action="{{ route('admin.orders.bulkUpdateStatus') }}" method="POST" id="bulk-update-form">
        @csrf

        <!-- Thanh công cụ khi tích chọn đơn hàng -->
        <div class="card bg-light border-primary border-opacity-50 mb-3 shadow-sm" id="bulk-action-bar" style="display: none;">
            <div class="card-body p-2 d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div class="d-flex align-items-center">
                    <i class="fa-solid fa-circle-check text-primary fs-5 me-2"></i>
                    <span class="fw-bold">Đã chọn: <span id="selected-count" class="text-primary fs-6">0</span> đơn hàng</span>
                </div>

                <div class="d-flex flex-wrap align-items-center gap-2">
                    <select name="shipping_status" class="form-select form-select-sm" style="width: auto;">
                        <option value="">-- Đổi trạng thái giao hàng --</option>
                        <option value="pending">Chờ xử lý</option>
                        <option value="ready_to_pick">Chờ lấy hàng</option>
                        <option value="picking">Đang lấy hàng</option>
                        <option value="delivering">Đang giao hàng</option>
                        <option value="delivered">Giao thành công</option>
                        <option value="return">Hoàn hàng</option>
                        <option value="cancelled">Hủy đơn hàng</option>
                    </select>

                    <button type="submit" class="btn btn-primary btn-sm px-3" onclick="return confirm('Bạn có chắc chắn muốn cập nhật trạng thái cho các đơn đã chọn?');">
                        <i class="fa-solid fa-check me-1"></i> Cập nhật đã chọn
                    </button>
                </div>
            </div>
        </div>

        <!-- Bảng danh sách đơn hàng -->
        <div class="card shadow-sm border-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted small text-uppercase">
                        <tr>
                            <th width="40px" class="text-center">
                                <input type="checkbox" id="check-all-orders" class="form-check-input">
                            </th>
                            <th>Mã đơn</th>
                            <th>Ngày tạo</th>
                            <th>Khách hàng</th>
                            <th>Sản phẩm</th>
                            <th>Tổng tiền</th>
                            <th>Mã vận đơn (GHN)</th>
                            <th>Trạng thái giao</th>
                            <th class="text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" name="order_ids[]" value="{{ $order->id }}" class="form-check-input order-check-item">
                                </td>
                                <td>
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="fw-bold text-decoration-none">
                                        #DH{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}
                                    </a>
                                    <br>
                                    <span class="badge {{ $order->status === 'cancelled' ? 'bg-danger' : ($order->status === 'paid' || $order->status === 'paid_momo' ? 'bg-success' : 'bg-secondary') }} small">
                                        {{ $order->status }}
                                    </span>
                                </td>
                                <td class="small">
                                    {{ $order->created_at->format('d/m/Y') }}<br>
                                    <span class="text-muted">{{ $order->created_at->format('H:i') }}</span>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $order->name }}</div>
                                    <div class="small text-muted"><i class="fa-solid fa-phone me-1"></i>{{ $order->phone }}</div>
                                </td>
                                <td class="small" style="max-width: 250px;">
                                    @foreach($order->items as $item)
                                        <div class="text-truncate">
                                            • {{ $item->product->name ?? 'SP #'.$item->product_id }} 
                                            <span class="text-muted">x{{ $item->quantity }}</span>
                                        </div>
                                    @endforeach
                                </td>
                                <td class="fw-bold text-primary">
                                    {{ number_format($order->total_price, 0, ',', '.') }} đ
                                </td>
                                <td>
                                    @if($order->ghn_order_code)
                                        <span class="badge bg-light text-dark border font-monospace">{{ $order->ghn_order_code }}</span>
                                    @else
                                        <span class="text-muted small">Chưa có</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $order->shipping_status_badge ?? 'bg-secondary' }}">
                                        {{ $shippingLabels[$order->shipping_status] ?? $order->shipping_status }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-outline-info">
                                        <i class="fa-solid fa-eye me-1"></i> Chi tiết
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">
                                    <i class="fa-regular fa-folder-open fa-2x mb-2 d-block"></i>
                                    Không tìm thấy đơn hàng nào phù hợp với bộ lọc.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($orders->hasPages())
                <div class="card-footer bg-white d-flex justify-content-end p-2">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const checkAll = document.getElementById('check-all-orders');
    const itemChecks = document.querySelectorAll('.order-check-item');
    const bulkBar = document.getElementById('bulk-action-bar');
    const selectedCount = document.getElementById('selected-count');

    function updateBulkState() {
        const checkedBoxes = document.querySelectorAll('.order-check-item:checked');
        const count = checkedBoxes.length;

        if (selectedCount) selectedCount.innerText = count;

        if (bulkBar) {
            bulkBar.style.display = count > 0 ? 'block' : 'none';
        }

        if (checkAll && itemChecks.length > 0) {
            checkAll.checked = count === itemChecks.length;
        }
    }

    if (checkAll) {
        checkAll.addEventListener('change', function () {
            itemChecks.forEach(cb => cb.checked = checkAll.checked);
            updateBulkState();
        });
    }

    itemChecks.forEach(cb => {
        cb.addEventListener('change', updateBulkState);
    });
});
</script>
@endsection
