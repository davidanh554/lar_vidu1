@extends('layouts.user')

@section('content')
<div class="container my-4">
    <h2>🛒 Giỏ hàng của bạn</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('cart') && count(session('cart')) > 0)
        <table class="table table-bordered align-middle mt-3">
            <thead class="table-dark">
                <tr>
                    <th>Hình ảnh</th>
                    <th>Tên sản phẩm</th>
                    <th>Đơn giá</th>
                    <th>Số lượng</th>
                    <th>Thành tiền</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @php $total = 0; @endphp
                @foreach(session('cart') as $id => $details)
                    @php 
                        $subtotal = $details['price'] * $details['quantity'];
                        $total += $subtotal;
                    @endphp
                    <tr>
                        <td>
                            <img src="{{ asset('uploads/products/' . $details['image']) }}" width="60" class="img-thumbnail">
                        </td>
                        <td>{{ $details['name'] }}</td>
                        <td>{{ number_format($details['price'], 0, ',', '.') }}đ</td>
                        <td>
                            <input type="number" value="{{ $details['quantity'] }}" class="form-control update-cart" data-id="{{ $id }}" min="1" style="width: 80px;">
                        </td>
                        <td>{{ number_format($subtotal, 0, ',', '.') }}đ</td>
                        <td>
                            <form action="{{ route('cart.remove', $id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="d-flex justify-content-between align-items-center mt-3">
            <h4>Tổng tiền: <span class="text-primary">{{ number_format($total, 0, ',', '.') }}đ</span></h4>
            <a href="{{ route('checkout.index') }}" class="btn btn-success btn-lg">Tiến hành Thanh toán</a>
        </div>
    @else
        <div class="alert alert-info mt-3">Giỏ hàng đang trống. <a href="{{ route('welcome') }}">Tiếp tục mua sắm</a></div>
    @endif
</div>
@endsection