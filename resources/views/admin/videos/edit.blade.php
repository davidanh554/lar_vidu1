@extends('admin.layouts.master')

@section('content')
<div class="container-fluid px-0" style="max-width: 900px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1"><i class="fa-solid fa-pen-to-square text-primary me-2"></i>Chỉnh Sửa Video #{{ $video->id }}</h3>
            <p class="text-muted small mb-0">Cập nhật tiêu đề, nguồn video, sản phẩm đính kèm hoặc trạng thái</p>
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
            <form action="{{ route('admin.videos.update', $video) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Tiêu đề video -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Tiêu đề Video <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control rounded-3" value="{{ old('title', $video->title) }}" required>
                </div>

                <!-- Video hiện tại -->
                <div class="mb-3 p-3 bg-light rounded-3 border">
                    <label class="form-label small fw-bold text-muted d-block">Video đang sử dụng:</label>
                    <div class="d-flex align-items-center gap-3">
                        @if($video->is_youtube)
                            <span class="badge bg-danger fs-6"><i class="fa-brands fa-youtube me-1"></i>YouTube</span>
                        @else
                            <span class="badge bg-primary fs-6"><i class="fa-solid fa-file-video me-1"></i>File Local</span>
                        @endif
                        <span class="font-monospace text-truncate small" style="max-width: 500px;">{{ $video->video_url }}</span>
                    </div>
                </div>

                <!-- Thay đổi nguồn video nếu muốn -->
                <div class="mb-4 p-3 bg-light rounded-4 border">
                    <label class="form-label fw-bold d-block mb-2">Thay đổi Nguồn Video</label>
                    <div class="d-flex gap-4 mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="video_type" id="type_keep" value="keep" checked onchange="toggleVideoSourceEdit()">
                            <label class="form-check-label fw-semibold" for="type_keep">
                                Giữ nguyên video hiện tại
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="video_type" id="type_url" value="url" onchange="toggleVideoSourceEdit()">
                            <label class="form-check-label fw-semibold" for="type_url">
                                <i class="fa-brands fa-youtube text-danger me-1"></i> Đổi sang Link YouTube mới
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="video_type" id="type_file" value="file" onchange="toggleVideoSourceEdit()">
                            <label class="form-check-label fw-semibold" for="type_file">
                                <i class="fa-solid fa-upload text-primary me-1"></i> Tải File MP4 mới thay thế
                            </label>
                        </div>
                    </div>

                    <!-- Ô nhập link YouTube -->
                    <div id="wrapper_video_url" class="mb-2" style="display: none;">
                        <label class="form-label small text-muted">Đường dẫn YouTube mới:</label>
                        <input type="text" name="video_url" class="form-control rounded-3" placeholder="https://www.youtube.com/shorts/... hoặc https://youtube.com/watch?v=...">
                    </div>

                    <!-- Ô chọn File Upload -->
                    <div id="wrapper_video_file" class="mb-2" style="display: none;">
                        <label class="form-label small text-muted">Chọn file Video MP4 mới từ máy tính:</label>
                        <input type="file" name="video_file" class="form-control rounded-3" accept="video/mp4,video/webm,video/quicktime">
                    </div>
                </div>

                <!-- Sản phẩm liên kết (iPad / Phụ kiện) -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Sản phẩm iPad / Phụ kiện gắn kèm Video</label>
                    <select name="product_id" class="form-select rounded-3">
                        <option value="">-- Không gắn sản phẩm --</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}" {{ old('product_id', $video->product_id) == $p->id ? 'selected' : '' }}>
                                {{ $p->name }} ({{ number_format($p->sale_price ?? $p->price) }}₫)
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Mô tả video -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Mô tả ngắn</label>
                    <textarea name="description" class="form-control rounded-3" rows="3">{{ old('description', $video->description) }}</textarea>
                </div>

                <!-- Trạng thái kích hoạt -->
                <div class="form-check form-switch mb-4">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', $video->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold" for="is_active">Kích hoạt phát video trên website</label>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.videos.index') }}" class="btn btn-light rounded-pill px-4">Hủy</a>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Lưu Thay Đổi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleVideoSourceEdit() {
    const isUrl = document.getElementById('type_url').checked;
    const isFile = document.getElementById('type_file').checked;
    document.getElementById('wrapper_video_url').style.display = isUrl ? 'block' : 'none';
    document.getElementById('wrapper_video_file').style.display = isFile ? 'block' : 'none';
}
</script>
@endsection
