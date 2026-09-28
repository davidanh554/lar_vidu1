@extends('admin.layouts.master')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="fw-bold mb-1"><i class="fa-solid fa-ticket-simple text-success me-2"></i>Quản lý Mã Giảm Giá (Coupons)</h3>
            <p class="text-muted small mb-0">Quản lý mã khuyến mãi, voucher trúng thưởng từ Vòng Quay May Mắn</p>
        </div>
        <a href="{{ route('admin.coupons.create') }}" class="btn btn-success rounded-pill px-3 shadow-sm">
            <i class="fa-solid fa-plus me-1"></i> Tạo mã giảm giá mới
        </a>
    </div>

    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="70px">ID</th>
                        <th>MÃ CODE</th>
                        <th>TÊN CHƯƠNG TRÌNH</th>
                        <th>GIẢM GIÁ</th>
                        <th>ĐƠN TỐI THIỂU</th>
                        <th>ĐÃ DÙNG / TỔNG</th>
                        <th>HẠN DÙNG</th>
                        <th>TRẠNG THÁI</th>
                        <th class="text-center" width="180px">HÀNH ĐỘNG</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($coupons as $coupon)
                        <tr>
                            <td class="text-muted font-monospace">#{{ $coupon->id }}</td>
                            <td>
                                <span class="badge bg-dark font-monospace fs-6 px-2 py-1 text-warning border border-warning-subtle">
                                    {{ $coupon->code }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $coupon->title }}</div>
                                <small class="text-muted">Được cấp cho {{ $coupon->user_coupons_count }} lượt user</small>
                            </td>
                            <td>
                                @if($coupon->type === 'percent')
                                    <span class="badge bg-info-subtle text-info fw-bold fs-6">
                                        Giảm {{ $coupon->value }}%
                                    </span>
                                    @if($coupon->max_discount)
                                        <div class="text-muted small">Tối đa {{ number_format($coupon->max_discount) }}₫</div>
                                    @endif
                                @else
                                    <span class="badge bg-success-subtle text-success fw-bold fs-6">
                                        -{{ number_format($coupon->value) }}₫
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span class="text-secondary small">
                                    {{ $coupon->min_order_value > 0 ? number_format($coupon->min_order_value) . '₫' : 'Không giới hạn' }}
                                </span>
                            </td>
                            <td>
                                <span class="fw-bold">{{ $coupon->used_count }}</span>
                                <span class="text-muted">/ {{ $coupon->quantity ?? '∞' }}</span>
                            </td>
                            <td>
                                <small class="text-secondary d-block">
                                    {{ $coupon->start_date ? $coupon->start_date->format('d/m/Y') : 'Không giới hạn' }}
                                    - {{ $coupon->end_date ? $coupon->end_date->format('d/m/Y') : 'Vô thời hạn' }}
                                </small>
                            </td>
                            <td>
                                <form action="{{ route('admin.coupons.toggle', $coupon->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @if($coupon->is_active)
                                        <button type="submit" class="btn btn-sm btn-outline-success rounded-pill py-0 px-2" title="Click để tắt">
                                            <i class="fa-solid fa-circle-check me-1"></i> Đang bật
                                        </button>
                                    @else
                                        <button type="submit" class="btn btn-sm btn-outline-secondary rounded-pill py-0 px-2" title="Click để bật">
                                            <i class="fa-solid fa-circle-xmark me-1"></i> Tạm khóa
                                        </button>
                                    @endif
                                </form>
                            </td>
                            <td class="text-center">
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('admin.coupons.edit', $coupon->id) }}" class="btn btn-primary btn-sm rounded-pill px-3">
                                        <i class="fa-solid fa-pen-to-square"></i> Sửa
                                    </a>
                                    <form action="{{ route('admin.coupons.destroy', $coupon->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa mã giảm giá này?');" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm rounded-pill px-2">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                Chưa có mã giảm giá nào. Hãy tạo mã đầu tiên!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($coupons->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $coupons->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
