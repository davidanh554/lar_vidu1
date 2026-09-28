@extends('layouts.store')

@section('title', 'Xác Thực Email - VUA TABLET')

@section('content')
<div class="container my-5" style="max-width: 560px;">
    <div class="card card-modern p-4 p-md-5 text-center shadow-lg border border-emerald-subtle position-relative overflow-hidden">
        <!-- Glow accent behind -->
        <div class="position-absolute top-0 start-50 translate-middle w-50 h-50 bg-emerald opacity-20 blur-3xl pointer-events-none"></div>

        <div class="mb-4">
            <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-emerald bg-opacity-10 border border-emerald-subtle p-3 mb-2" style="width: 80px; height: 80px;">
                <i class="fa-solid fa-envelope-circle-check fs-1 text-emerald"></i>
            </div>
            <h3 class="fw-bold text-white mt-2">Xác thực địa chỉ Email</h3>
            <p class="text-muted small">Hệ thống đã gửi một liên kết kích hoạt đến email của bạn:</p>
            <div class="badge bg-dark bg-opacity-60 text-emerald border border-emerald-subtle px-3 py-2 rounded-pill font-monospace fs-6">
                <i class="fa-regular fa-envelope me-1"></i> {{ Auth::user()->email ?? 'Email của bạn' }}
            </div>
        </div>

        @if (session('message') || session('success'))
            <div class="alert alert-success bg-emerald bg-opacity-10 border border-emerald text-emerald rounded-4 text-start small mb-4">
                <i class="fa-solid fa-circle-check me-2"></i> {{ session('message') ?? session('success') }}
            </div>
        @endif

        <div class="alert bg-black bg-opacity-40 border border-warning border-opacity-30 rounded-4 text-start small mb-4 text-light p-3">
            <div class="fw-bold text-warning mb-1">
                <i class="fa-solid fa-triangle-exclamation me-1"></i> Không thấy email trong Hộp thư đến (Inbox)?
            </div>
            <ul class="ps-3 mb-0 text-muted small" style="line-height: 1.6;">
                <li>Với email trường học (<strong class="text-light">@hunre.edu.vn</strong>), bộ lọc thường tự động xếp email vào mục <strong class="text-warning">Thư rác (Spam / Junk)</strong> hoặc <strong class="text-light">Quảng cáo (Promotions)</strong>.</li>
                <li>Vui lòng mở mục <strong class="text-warning">Spam (Thư rác)</strong> và tìm email có tiêu đề: <strong class="text-emerald">[VUA TABLET] Xác thực địa chỉ email</strong> từ <code class="text-light">daungocanh90@gmail.com</code>.</li>
                <li>Mở email và bấm <strong class="text-light">"Báo cáo không phải spam"</strong> để click link xác thực.</li>
            </ul>
        </div>

        <form method="POST" action="{{ route('verification.send') }}" class="mb-3">
            @csrf
            <button type="submit" class="btn btn-emerald w-100 py-2 fw-bold rounded-pill shadow-sm">
                <i class="fa-solid fa-paper-plane me-2"></i> Gửi lại email xác thực
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-link text-decoration-none text-muted small hover-white">
                <i class="fa-solid fa-arrow-right-from-bracket me-1"></i> Đăng xuất tài khoản này
            </button>
        </form>
    </div>
</div>
@endsection