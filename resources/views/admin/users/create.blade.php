@extends('admin.layouts.master')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold mb-0">Thêm người dùng</h3>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Quay lại
        </a>
    </div>

    <div class="card shadow-sm border-0 col-md-8">
        <div class="card-body p-4">
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">Tên:</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Email:</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Mật khẩu:</label>
                    <input type="password" name="password" class="form-control" required minlength="6">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Vai trò:</label>
                    <select name="role" class="form-select" required>
                        <option value="user">Người dùng (user)</option>
                        <option value="customer">Khách hàng (customer)</option>
                        <option value="admin">Quản trị (admin)</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-success px-4">Lưu người dùng</button>
            </form>
        </div>
    </div>
</div>
@endsection
