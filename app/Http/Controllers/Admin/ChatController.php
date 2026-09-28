<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * Lấy danh sách những User đã từng nhắn tin với Admin
     */
    public function getUsers()
    {
        $adminId = Auth::id();

        // Lấy tất cả user (ngoại trừ chính admin đang đăng nhập)
        // Ưu tiên người đã từng nhắn tin lên trước
        $interactedIds = Message::where('receiver_id', $adminId)
            ->orWhere('sender_id', $adminId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($msg) use ($adminId) {
                return $msg->sender_id == $adminId ? $msg->receiver_id : $msg->sender_id;
            })
            ->unique()
            ->values()
            ->toArray();

        $users = User::where('id', '!=', $adminId)
            ->select('id', 'name', 'email')
            ->get();

        return $users->sortBy(function($u) use ($interactedIds) {
            $index = array_search($u->id, $interactedIds);
            return $index !== false ? $index : 999999;
        })->values();
    }

    /**
     * Lấy lịch sử tin nhắn của một User cụ thể
     */
    public function getMessages($userId)
    {
        $adminId = Auth::id();

        // Đánh dấu các tin nhắn user gửi tới admin là đã đọc
        Message::where('sender_id', $userId)
            ->where('receiver_id', $adminId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return Message::with('sender')
            ->where(function ($q) use ($userId, $adminId) {
                $q->where('sender_id', $userId)->where('receiver_id', $adminId);
            })
            ->orWhere(function ($q) use ($userId, $adminId) {
                $q->where('sender_id', $adminId)->where('receiver_id', $userId);
            })
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Admin gửi tin nhắn phản hồi
     */
    public function send(Request $request)
    {
        // Kiểm tra dữ liệu đầu vào
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'message' => 'required'
        ]);

        $message = Message::create([
            'sender_id'   => Auth::id(),
            'receiver_id' => $request->user_id,
            'content'     => $request->message,
            'is_read'     => false // Người nhận (khách hàng) chưa đọc
        ]);

        return response()->json($message);
    }
}
