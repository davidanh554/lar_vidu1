@extends('admin.layouts.master')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="fw-bold mb-1"><i class="fa-solid fa-clapperboard text-danger me-2"></i>Quản lý Video Reels Giải Trí</h3>
            <p class="text-muted small mb-0">Quản lý các video ngắn thu hút khách hàng, tích Xu đổi tiền mặt và liên kết sản phẩm iPad</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('videos.index') }}" target="_blank" class="btn btn-outline-dark rounded-pill px-3 shadow-sm">
                <i class="fa-solid fa-eye me-1"></i> Xem trang Lướt Video
            </a>
            <a href="{{ route('admin.videos.create') }}" class="btn btn-danger rounded-pill px-3 shadow-sm">
                <i class="fa-solid fa-plus me-1"></i> Thêm Video Mới
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Card Tìm kiếm & Lọc -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body p-3">
            <form action="{{ route('admin.videos.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                        <input type="text" name="search" class="form-control bg-light border-0" placeholder="Tìm theo tiêu đề video..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 rounded-3">Tìm kiếm</button>
                </div>
                @if(request('search'))
                    <div class="col-md-2">
                        <a href="{{ route('admin.videos.index') }}" class="btn btn-outline-secondary w-100 rounded-3">Xóa lọc</a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Bảng danh sách video -->
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="60px">ID</th>
                        <th width="140px">VIDEO / NGUỒN</th>
                        <th>TIÊU ĐỀ & MÔ TẢ</th>
                        <th>SẢN PHẨM GẮN KÈM</th>
                        <th>THỐNG KÊ</th>
                        <th>TRẠNG THÁI</th>
                        <th class="text-center" width="160px">HÀNH ĐỘNG</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($videos as $video)
                        <tr>
                            <td class="text-muted font-monospace">#{{ $video->id }}</td>
                            <td>
                                @if($video->is_youtube)
                                    <div class="position-relative rounded-3 overflow-hidden bg-dark text-center" style="width: 110px; height: 70px;">
                                        <img src="https://img.youtube.com/vi/{{ $video->youtube_id }}/mqdefault.jpg" class="w-100 h-100 object-fit-cover" alt="YouTube Thumb">
                                        <span class="badge bg-danger position-absolute bottom-0 start-0 m-1" style="font-size: 0.65rem;">
                                            <i class="fa-brands fa-youtube me-1"></i>YouTube
                                        </span>
                                    </div>
                                @else
                                    <div class="position-relative rounded-3 overflow-hidden bg-dark text-center" style="width: 110px; height: 70px;">
                                        <video src="{{ asset($video->video_url) }}" class="w-100 h-100 object-fit-cover" muted></video>
                                        <span class="badge bg-primary position-absolute bottom-0 start-0 m-1" style="font-size: 0.65rem;">
                                            <i class="fa-solid fa-file-video me-1"></i>File Local
                                        </span>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $video->title }}</div>
                                @if($video->description)
                                    <small class="text-muted d-block text-truncate" style="max-width: 320px;">
                                        {{ $video->description }}
                                    </small>
                                @endif
                                <small class="text-secondary font-monospace" style="font-size: 0.75rem;">
                                    {{ Str::limit($video->video_url, 45) }}
                                </small>
                            </td>
                            <td>
                                @if($video->product)
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="{{ asset($video->product->image ?? 'images/placeholder-ipad.png') }}" class="rounded border" style="width: 36px; height: 36px; object-fit: cover;">
                                        <div>
                                            <div class="fw-semibold small text-truncate" style="max-width: 180px;">{{ $video->product->name }}</div>
                                            <div class="text-success fw-bold small">{{ number_format($video->product->sale_price ?? $video->product->price) }}₫</div>
                                        </div>
                                    </div>
                                @else
                                    <span class="badge bg-light text-muted border">Chưa gắn sản phẩm</span>
                                @endif
                            </td>
                            <td>
                                <div class="small text-muted"><i class="fa-solid fa-eye me-1"></i>{{ number_format($video->views_count) }} xem</div>
                                <div class="small text-muted"><i class="fa-solid fa-heart me-1 text-danger"></i>{{ number_format($video->likes_count) }} tim</div>
                            </td>
                            <td>
                                <form action="{{ route('admin.videos.toggle', $video) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm rounded-pill px-3 {{ $video->is_active ? 'btn-success bg-opacity-75' : 'btn-secondary' }}" title="Bấm để bật/tắt">
                                        <i class="fa-solid {{ $video->is_active ? 'fa-check' : 'fa-xmark' }} me-1"></i>
                                        {{ $video->is_active ? 'Đang phát' : 'Đang ẩn' }}
                                    </button>
                                </form>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.videos.edit', $video) }}" class="btn btn-outline-primary" title="Chỉnh sửa">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('admin.videos.destroy', $video) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa video này không?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Xóa">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-video-slash fs-1 d-block mb-3 text-secondary opacity-50"></i>
                                Chưa có video nào được thêm. Hãy bấm "Thêm Video Mới" để bắt đầu!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($videos->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $videos->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>
@endsection
