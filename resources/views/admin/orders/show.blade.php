@extends('admin.layouts.master')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <a href="{{ route('admin.orders.index') }}" class="text-decoration-none small text-muted">
                <i class="fa-solid fa-arrow-left me-1"></i> Quay lại danh sách đơn hàng
            </a>
            <h3 class="fw-bold mt-1">Chi tiết đơn hàng #DH{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</h3>
        </div>
        <div>
            <span class="badge {{ $order->shipping_status_badge ?? 'bg-secondary' }} fs-6 px-3 py-2">
                {{ $order->shipping_status_text ?? $order->shipping_status }}
            </span>
        </div>
    </div>

    <div class="row g-3">
        <!-- Thông tin sản phẩm trong đơn -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0 fw-bold"><i class="fa-solid fa-box-open me-2 text-primary"></i>Danh sách sản phẩm</h5>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light small text-muted">
                            <tr>
                                <th>Sản phẩm</th>
                                <th class="text-center">Số lượng</th>
                                <th class="text-end">Đơn giá</th>
                                <th class="text-end">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $item->product->name ?? 'Sản phẩm không tồn tại' }}</div>
                                        <small class="text-muted">Mã SP: #{{ $item->product_id }}</small>
                                    </td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-end">{{ number_format($item->price, 0, ',', '.') }} đ</td>
                                    <td class="text-end fw-bold">{{ number_format($item->price * $item->quantity, 0, ',', '.') }} đ</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            @if(($order->ghn_total_fee ?? 0) > 0)
                                <tr>
                                    <td colspan="3" class="text-end text-muted">Cước vận chuyển (GHN):</td>
                                    <td class="text-end text-secondary fw-semibold">+{{ number_format($order->ghn_total_fee, 0, ',', '.') }} đ</td>
                                </tr>
                            @endif
                            @if(($order->discount_amount ?? 0) > 0)
                                <tr>
                                    <td colspan="3" class="text-end text-success">Mã giảm giá ({{ $order->coupon_code }}):</td>
                                    <td class="text-end text-success fw-bold">-{{ number_format($order->discount_amount, 0, ',', '.') }} đ</td>
                                </tr>
                            @endif
                            @if(($order->coins_discount ?? 0) > 0)
                                <tr>
                                    <td colspan="3" class="text-end text-warning-emphasis">
                                        <i class="fa-solid fa-coins text-warning me-1"></i>Trừ Xu tích lũy ({{ $order->coins_used }} Xu):
                                    </td>
                                    <td class="text-end text-warning-emphasis fw-bold">-{{ number_format($order->coins_discount, 0, ',', '.') }} đ</td>
                                </tr>
                            @endif
                            <tr>
                                <td colspan="3" class="text-end fw-bold">Tổng tiền thanh toán cuối cùng:</td>
                                <td class="text-end fw-bold text-primary fs-5">{{ number_format($order->total_price, 0, ',', '.') }} đ</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Lịch sử giao dịch thanh toán -->
            @if($order->paymentTransactions && $order->paymentTransactions->count() > 0)
                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title mb-0 fw-bold"><i class="fa-solid fa-credit-card me-2 text-info"></i>Lịch sử giao dịch thanh toán</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0 small">
                            <thead class="table-light">
                                <tr>
                                    <th>Cổng</th>
                                    <th>Mã GD</th>
                                    <th>Số tiền</th>
                                    <th>Trạng thái</th>
                                    <th>Thời gian</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->paymentTransactions as $pt)
                                    <tr>
                                        <td><span class="badge bg-dark">{{ strtoupper($pt->gateway ?? 'N/A') }}</span></td>
                                        <td class="font-monospace">{{ $pt->transaction_id ?? $pt->id }}</td>
                                        <td class="fw-bold">{{ number_format($pt->amount, 0, ',', '.') }} đ</td>
                                        <td>
                                            <span class="badge {{ $pt->status === 'paid' ? 'bg-success' : 'bg-warning' }}">
                                                {{ $pt->status }}
                                            </span>
                                        </td>
                                        <td class="text-muted">{{ $pt->created_at->format('d/m/Y H:i:s') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        <!-- Cột bên phải: Thông tin khách hàng & Xử lý đơn -->
        <div class="col-lg-4">
            <!-- Thông tin người nhận -->
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0 fw-bold"><i class="fa-solid fa-user me-2 text-secondary"></i>Thông tin người nhận</h5>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>Họ tên:</strong> {{ $order->name }}</p>
                    <p class="mb-2"><strong>Số điện thoại:</strong> {{ $order->phone }}</p>
                    <p class="mb-2"><strong>Địa chỉ nhận hàng:</strong> {{ $order->address }}</p>
                    @if($order->ghn_order_code)
                        <p class="mb-0"><strong>Mã GHN:</strong> <span class="badge bg-secondary font-monospace">{{ $order->ghn_order_code }}</span></p>
                    @endif
                </div>
            </div>

            <!-- Xử lý trạng thái đơn hàng -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0 fw-bold"><i class="fa-solid fa-gears me-2 text-success"></i>Cập nhật trạng thái</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-muted d-block">Trạng thái thanh toán (chỉ xem):</label>
                            <span class="badge {{ $order->status === 'cancelled' ? 'bg-danger' : ($order->status === 'paid' || $order->status === 'paid_momo' ? 'bg-success' : 'bg-secondary') }} fs-6">
                                {{ $order->status }}
                            </span>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Trạng thái vận chuyển:</label>
                            <select name="shipping_status" class="form-select">
                                <option value="not_shipped" @selected($order->shipping_status === 'not_shipped')>Chưa giao hàng</option>
                                <option value="pending" @selected($order->shipping_status === 'pending')>Chờ xử lý</option>
                                <option value="ready_to_pick" @selected($order->shipping_status === 'ready_to_pick')>Chờ lấy hàng</option>
                                <option value="picking" @selected($order->shipping_status === 'picking')>Đang lấy hàng</option>
                                <option value="delivering" @selected($order->shipping_status === 'delivering')>Đang giao hàng</option>
                                <option value="delivered" @selected($order->shipping_status === 'delivered')>Giao thành công</option>
                                <option value="return" @selected($order->shipping_status === 'return')>Hoàn hàng</option>
                                <option value="cancelled" @selected($order->shipping_status === 'cancelled')>Hủy đơn hàng</option>
                            </select>
                        </div>

                        @if(in_array($order->shipping_status, ['delivering', 'picked', 'storing', 'transporting']))
                            <div class="alert alert-warning py-2 small mb-3">
                                <i class="fa-solid fa-triangle-exclamation me-1"></i> Đơn hàng đang giao, hệ thống sẽ chặn hành động Hủy đơn.
                            </div>
                        @endif

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Lưu thay đổi
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
