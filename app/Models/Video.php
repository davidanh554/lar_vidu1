<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'title',
        'author',
        'description',
        'video_url',
        'thumbnail',
        'likes_count',
        'views_count',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'likes_count' => 'integer',
        'views_count' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Lấy ID YouTube nếu URL là link YouTube
     */
    public function getYoutubeIdAttribute()
    {
        if (empty($this->video_url)) return null;
        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?|shorts)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i', $this->video_url, $match)) {
            return $match[1];
        }
        return null;
    }

    /**
     * Kiểm tra xem video này có phải YouTube không
     */
    public function getIsYoutubeAttribute()
    {
        return !empty($this->youtube_id);
    }

    /**
     * Đường dẫn nhúng (Embed) YouTube tối ưu cho Shorts / Reels
     */
    public function getEmbedUrlAttribute()
    {
        $id = $this->youtube_id;
        if (!$id) return null;
        $origin = urlencode(request()->getSchemeAndHttpHost());
        return "https://www.youtube.com/embed/{$id}?autoplay=1&mute=1&loop=1&playlist={$id}&controls=0&modestbranding=1&rel=0&playsinline=1&enablejsapi=1&fs=0&iv_load_policy=3&disablekb=1&origin={$origin}";
    }

    /**
     * Đường dẫn file video đầy đủ (nếu là file local thì dùng asset())
     */
    public function getResolvedVideoUrlAttribute()
    {
        if ($this->is_youtube) return $this->embed_url;
        if (str_starts_with($this->video_url, 'http://') || str_starts_with($this->video_url, 'https://')) {
            return $this->video_url;
        }
        return asset($this->video_url);
    }
}
