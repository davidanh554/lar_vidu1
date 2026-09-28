@extends('layouts.store')

@section('title', 'Đăng Nhập - VUA TABLET')

@section('content')
<div class="container my-5" style="max-width: 480px;">
    <div class="card card-modern p-4 p-md-5">
        <div class="text-center mb-4">
            <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 rounded-circle mb-3" style="width: 60px; height: 60px;">
                <i class="fa-solid fa-lock text-primary fs-4"></i>
            </div>
            <h3 class="fw-bold text-dark mb-1">Đăng nhập</h3>
            <p class="text-muted small">Chào mừng bạn quay trở lại với VUA TABLET</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-modern-danger alert-dismissible fade show mb-4" role="alert">
                <ul class="mb-0 ps-3 small">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label fw-semibold small">Địa chỉ Email:</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-regular fa-envelope"></i></span>
                    <input type="email" name="email" id="email" class="form-control bg-light border-start-0 ps-0" value="{{ old('email') }}" required placeholder="email@domain.com">
                </div>
            </div>

            <div class="mb-4">
                <label for="password" class="form-label fw-semibold small">Mật khẩu:</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-key"></i></span>
                    <input type="password" name="password" id="password" class="form-control bg-light border-start-0 ps-0" required placeholder="••••••••">
                </div>
            </div>

            <button type="submit" class="btn btn-modern-primary w-100 rounded-pill py-3 fw-bold fs-6 shadow mb-3">
                <i class="fa-solid fa-arrow-right-to-bracket me-2"></i> Đăng nhập ngay
            </button>
        </form>

        <div class="text-center mt-3 pt-3 border-top small text-muted">
            <span>Chưa có tài khoản? </span>
            <a href="{{ route('register') }}" class="fw-bold text-primary text-decoration-none hover-underline">Đăng ký ngay</a>
        </div>
    </div>
</div>
@endsection
