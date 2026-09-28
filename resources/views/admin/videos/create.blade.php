@extends('admin.layouts.master')

@section('content')
<div class="container-fluid px-0" style="max-width: 900px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1"><i class="fa-solid fa-plus-circle text-danger me-2"></i>Thêm Video Mới</h3>
            <p class="text-muted small mb-0">Hỗ trợ dán link YouTube Shorts hoặc tải trực tiếp file MP4 từ máy tính</p>
        </div>
        <a href="{{ route('admin.videos.index') }}" class="btn btn-outline-secondary rounded-pill px-3 shadow-sm">
            <i class="fa-solid fa-arrow-left me-1"></i> Quay lại
        </a>
    </div>

    @if (isset($errors) && $errors->any())
        <div class="alert alert-danger rounded-3 shadow-sm mb-4">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-body p-4">
            <form action="{{ route('admin.videos.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Tiêu đề video -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Tiêu đề Video <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control rounded-3" placeholder="Ví dụ: Đánh giá nhanh iPad Air 6 M2 cực đỉnh" value="{{ old('title') }}" required>
                </div>

                <!-- Chọn Nguồn Video (Link YouTube hoặc Tải File) -->
                <div class="mb-4 p-3 bg-light rounded-4 border">
                    <label class="form-label fw-bold d-block mb-2">Nguồn Video <span class="text-danger">*</span></label>
                    <div class="d-flex gap-4 mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="video_type" id="type_url" value="url" checked onchange="toggleVideoSource()">
                            <label class="form-check-label fw-semibold" for="type_url">
                                <i class="fa-brands fa-youtube text-danger me-1"></i> Dán Link YouTube / Shorts
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="video_type" id="type_file" value="file" onchange="toggleVideoSource()">
                            <label class="form-check-label fw-semibold" for="type_file">
                                <i class="fa-solid fa-upload text-primary me-1"></i> Tải File MP4 từ máy tính
                            </label>
                        </div>
                    </div>

                    <!-- Ô nhập link YouTube -->
                    <div id="wrapper_video_url" class="mb-2">
                        <label class="form-label small text-muted">Đường dẫn YouTube (hỗ trợ cả Shorts và Video thường):</label>
                        <input type="text" name="video_url" class="form-control rounded-3" placeholder="https://www.youtube.com/shorts/... hoặc https://youtube.com/watch?v=..." value="{{ old('video_url') }}">
                        <small class="text-muted fst-italic">Ví dụ: https://www.youtube.com/watch?v=dQw4w9WgXcQ hoặc https://youtube.com/shorts/XXXXX</small>
                    </div>

                    <!-- Ô chọn File Upload -->
                    <div id="wrapper_video_file" class="mb-2" style="display: none;">
                        <label class="form-label small text-muted">Chọn file Video từ máy tính (.mp4, .webm, .mov, tối đa 100MB):</label>
                        <input type="file" name="video_file" class="form-control rounded-3" accept="video/mp4,video/webm,video/quicktime">
                        <small class="text-muted fst-italic">File video sẽ được lưu trữ an toàn trong thư mục public/uploads/videos/ của hệ thống.</small>
                    </div>
                </div>

                <!-- Sản phẩm liên kết (iPad / Phụ kiện) -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Sản phẩm iPad / Phụ kiện gắn kèm Video</label>
                    <select name="product_id" class="form-select rounded-3">
                        <option value="">-- Không gắn sản phẩm --</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}" {{ old('product_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->name }} ({{ number_format($p->sale_price ?? $p->price) }}₫)
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted">Khi xem video, thẻ sản phẩm này sẽ hiển thị ở góc dưới kèm nút "Xem ngay" để kích thích mua hàng.</small>
                </div>

                <!-- Mô tả video -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Mô tả ngắn</label>
                    <textarea name="description" class="form-control rounded-3" rows="3" placeholder="Mô tả ngắn gọn về nội dung video hoặc tính năng sản phẩm...">{{ old('description') }}</textarea>
                </div>

                <!-- Trạng thái kích hoạt -->
                <div class="form-check form-switch mb-4">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" checked>
                    <label class="form-check-label fw-semibold" for="is_active">Kích hoạt phát video ngay trên website</label>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.videos.index') }}" class="btn btn-light rounded-pill px-4">Hủy</a>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">
                        <i class="fa-solid fa-cloud-arrow-up me-1"></i> Lưu & Đăng Video
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleVideoSource() {
    const isUrl = document.getElementById('type_url').checked;
    document.getElementById('wrapper_video_url').style.display = isUrl ? 'block' : 'none';
    document.getElementById('wrapper_video_file').style.display = isUrl ? 'none' : 'block';
}
</script>
@endsection
