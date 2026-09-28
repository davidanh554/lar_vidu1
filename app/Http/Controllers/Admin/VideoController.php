<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Video;
use App\Models\Product;
use Illuminate\Support\Facades\File;

class VideoController extends Controller
{
    /**
     * Danh sách video
     */
    public function index(Request $request)
    {
        $query = Video::with('product')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%");
            });
        }

        $videos = $query->paginate(10);
        return view('admin.videos.index', compact('videos'));
    }

    /**
     * Form thêm video mới
     */
    public function create()
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();
        return view('admin.videos.create', compact('products'));
    }

    /**
     * Lưu video mới
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'        => 'required|string|max:255',
            'product_id'   => 'nullable|exists:products,id',
            'video_type'   => 'required|in:url,file',
            'video_url'    => 'nullable|required_if:video_type,url|string|max:500',
            'video_file'   => 'nullable|required_if:video_type,file|file|mimes:mp4,webm,mov,ogg|max:102400', // 100MB
            'thumbnail'    => 'nullable|image|mimes:jpeg,png,jpg,webp,jfif|max:5120',
            'description'  => 'nullable|string',
            'is_active'    => 'nullable|boolean',
        ], [
            'title.required'      => 'Vui lòng nhập tiêu đề video.',
            'video_url.required_if' => 'Vui lòng nhập link YouTube hoặc đường dẫn video.',
            'video_file.required_if' => 'Vui lòng chọn file video tải lên.',
            'video_file.max'      => 'File video dung lượng tối đa 100MB.',
        ]);

        $videoPath = '';

        if ($request->video_type === 'file' && $request->hasFile('video_file')) {
            $file = $request->file('video_file');
            $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $file->getClientOriginalName());
            $destinationPath = public_path('uploads/videos');
            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }
            $file->move($destinationPath, $fileName);
            $videoPath = 'uploads/videos/' . $fileName;
        } else {
            $videoPath = trim($request->video_url);
        }

        $thumbPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumb = $request->file('thumbnail');
            $thumbName = time() . '_' . $thumb->getClientOriginalName();
            $thumbDest = public_path('uploads/videos/thumbs');
            if (!File::exists($thumbDest)) {
                File::makeDirectory($thumbDest, 0755, true);
            }
            $thumb->move($thumbDest, $thumbName);
            $thumbPath = 'uploads/videos/thumbs/' . $thumbName;
        }

        Video::create([
            'title'        => $request->title,
            'author'       => 'Vua Tablet Shop',
            'product_id'   => $request->product_id,
            'video_url'    => $videoPath,
            'thumbnail'    => $thumbPath,
            'description'  => $request->description,
            'is_active'    => $request->has('is_active') ? (bool)$request->is_active : true,
            'likes_count'  => rand(500, 3000),
            'views_count'  => rand(1000, 8000),
        ]);

        return redirect()->route('admin.videos.index')->with('success', 'Đã thêm video thành công!');
    }

    /**
     * Form chỉnh sửa video
     */
    public function edit(Video $video)
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();
        return view('admin.videos.edit', compact('video', 'products'));
    }

    /**
     * Cập nhật video
     */
    public function update(Request $request, Video $video)
    {
        $request->validate([
            'title'        => 'required|string|max:255',
            'product_id'   => 'nullable|exists:products,id',
            'video_type'   => 'required|in:keep,url,file',
            'video_url'    => 'nullable|required_if:video_type,url|string|max:500',
            'video_file'   => 'nullable|required_if:video_type,file|file|mimes:mp4,webm,mov,ogg|max:102400',
            'thumbnail'    => 'nullable|image|mimes:jpeg,png,jpg,webp,jfif|max:5120',
            'description'  => 'nullable|string',
            'is_active'    => 'nullable|boolean',
        ]);

        $data = [
            'title'       => $request->title,
            'product_id'  => $request->product_id,
            'description' => $request->description,
            'is_active'   => $request->has('is_active') ? (bool)$request->is_active : false,
        ];

        if ($request->video_type === 'file' && $request->hasFile('video_file')) {
            $file = $request->file('video_file');
            $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $file->getClientOriginalName());
            $destinationPath = public_path('uploads/videos');
            $file->move($destinationPath, $fileName);
            $data['video_url'] = 'uploads/videos/' . $fileName;
        } elseif ($request->video_type === 'url' && $request->filled('video_url')) {
            $data['video_url'] = trim($request->video_url);
        }

        if ($request->hasFile('thumbnail')) {
            $thumb = $request->file('thumbnail');
            $thumbName = time() . '_' . $thumb->getClientOriginalName();
            $thumbDest = public_path('uploads/videos/thumbs');
            if (!File::exists($thumbDest)) {
                File::makeDirectory($thumbDest, 0755, true);
            }
            $thumb->move($thumbDest, $thumbName);
            $data['thumbnail'] = 'uploads/videos/thumbs/' . $thumbName;
        }

        $video->update($data);

        return redirect()->route('admin.videos.index')->with('success', 'Đã cập nhật video thành công!');
    }

    /**
     * Bật / Tắt trạng thái hiển thị
     */
    public function toggle(Video $video)
    {
        $video->is_active = !$video->is_active;
        $video->save();

        return back()->with('success', 'Đã cập nhật trạng thái hiển thị video!');
    }

    /**
     * Xóa video
     */
    public function destroy(Video $video)
    {
        // Nếu là file nội bộ thì xóa file
        if ($video->video_url && str_starts_with($video->video_url, 'uploads/videos/') && File::exists(public_path($video->video_url))) {
            File::delete(public_path($video->video_url));
        }

        $video->delete();

        return redirect()->route('admin.videos.index')->with('success', 'Đã xóa video thành công!');
    }
}
