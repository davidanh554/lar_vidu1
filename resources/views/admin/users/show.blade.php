@extends('admin.layouts.master')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold mb-0">Thông tin người dùng</h3>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Quay lại
        </a>
    </div>

    <div class="card shadow-sm border-0 col-md-6">
        <div class="card-body p-4">
            <p class="mb-2"><strong>ID:</strong> {{ $user->id }}</p>
            <p class="mb-2"><strong>Tên:</strong> {{ $user->name }}</p>
            <p class="mb-2"><strong>Email:</strong> {{ $user->email }}</p>
            <p class="mb-3"><strong>Vai trò:</strong> <span class="badge {{ $user->role === 'admin' ? 'bg-danger' : 'bg-primary' }}">{{ $user->role }}</span></p>
            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary btn-sm">Chỉnh sửa</a>
        </div>
    </div>
</div>
@endsection
