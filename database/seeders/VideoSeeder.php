<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Video;
use App\Models\Product;

class VideoSeeder extends Seeder
{
    public function run(): void
    {
        $pencil = Product::where('slug', 'but-apple-pencil-pro')->first();
        $keyboard = Product::where('slug', 'ban-phim-magic-keyboard-ipad')->first();
        $paperlike = Product::where('slug', 'dan-man-hinh-paperlike-ve-viet')->first();
        $mini7 = Product::where('slug', 'ipad-mini-7-128gb')->first();
        $folio = Product::where('slug', 'bao-da-smart-folio-ipad')->first();
        $proM4 = Product::where('slug', 'ipad-pro-m4-11')->first();

        $videos = [
            [
                'title' => 'Chuyện ngắn vui nhộn: Chú Lạc Đà và người bạn Cánh Cụt tí hon',
                'author' => 'Blender Animation Shorts',
                'description' => 'Hoạt hình 3D ngắn cực kỳ hài hước và dễ thương, thích hợp thư giãn trên màn hình Retina của iPad!',
                'video_url' => 'uploads/videos/caminandes_llamigos.webm',
                'thumbnail' => 'uploads/products/1786957740_ipad.jfif',
                'product_id' => $mini7 ? $mini7->id : null,
                'likes_count' => 12450,
                'views_count' => 48900,
                'is_active' => true,
            ],
            [
                'title' => 'Phim hành động viễn tưởng: Công nghệ Robot tương lai (Tears of Steel)',
                'author' => 'Sci-Fi Studio VFX',
                'description' => 'Kỹ xảo CGI điện ảnh đỉnh cao, phô diễn sức mạnh xử lý đồ họa của chip Apple M4 trên iPad Pro.',
                'video_url' => 'uploads/videos/tears_of_steel.webm',
                'thumbnail' => 'uploads/products/1786957583_images.jpg',
                'product_id' => $proM4 ? $proM4->id : null,
                'likes_count' => 18920,
                'views_count' => 75600,
                'is_active' => true,
            ],
            [
                'title' => 'Chuyện phiêu lưu cảm động: Hành trình tìm kiếm chú rồng nhỏ (Sintel)',
                'author' => 'Fantasy Story Shorts',
                'description' => 'Câu chuyện ngắn đầy cảm xúc lôi cuốn người xem từng giây, trải nghiệm vẽ truyện tranh bằng Apple Pencil Pro.',
                'video_url' => 'uploads/videos/story_clip.webm',
                'thumbnail' => 'uploads/products/1786416578_cvb.png',
                'product_id' => $pencil ? $pencil->id : null,
                'likes_count' => 24500,
                'views_count' => 98000,
                'is_active' => true,
            ],
            [
                'title' => 'Tập phim hài hước: Chú Lạc Đà vượt bão tuyết tìm quả ngon (Gran Dillama)',
                'author' => 'Caminandes Comedy',
                'description' => 'Pha xử lý dở khóc dở cười giữa trời tuyết rơi. Xem video giải trí và nhận Xu giảm giá trực tiếp vào đơn hàng!',
                'video_url' => 'uploads/videos/caminandes_grandillama.webm',
                'thumbnail' => 'uploads/products/1786417368_ipad2.webp',
                'product_id' => $keyboard ? $keyboard->id : null,
                'likes_count' => 15600,
                'views_count' => 62100,
                'is_active' => true,
            ],
            [
                'title' => 'Thỏ mập nổi giận: Khi kẻ yếu vùng lên chống lại nghịch cảnh',
                'author' => 'Big Buck Studio',
                'description' => 'Clip hoạt hình vui nhộn giải trí cực đã mắt, kết hợp miếng dán Paperlike chống lóa mắt.',
                'video_url' => 'uploads/videos/funny_clip.webm',
                'thumbnail' => 'uploads/products/1786416194_chuky.jpg',
                'product_id' => $paperlike ? $paperlike->id : null,
                'likes_count' => 9870,
                'views_count' => 41200,
                'is_active' => true,
            ],
        ];

        foreach ($videos as $v) {
            Video::updateOrCreate(['title' => $v['title']], $v);
        }
    }
}
