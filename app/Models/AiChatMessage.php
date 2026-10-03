<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiChatMessage extends Model
{
    use HasFactory;

    protected $table = 'ai_chat_messages';

    protected $fillable = [
        'user_id',
        'role',
        'content',
    ];

    /**
     * Mối quan hệ với người dùng
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
