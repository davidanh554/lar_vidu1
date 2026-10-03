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
     * Lấy danh sách những User, kèm tin nhắn gần nhất và số tin chưa đọc
     * Sắp xếp người mới nhắn tin lên đầu tiên
     */
    public function getUsers()
    {
        $adminId = Auth::id();

        // 1. Lấy tất cả user khác admin
        $users = User::where('id', '!=', $adminId)
            ->select('id', 'name', 'email')
            ->get();

        // 2. Lấy toàn bộ tin nhắn liên quan tới Admin
        $messages = Message::where('receiver_id', $adminId)
            ->orWhere('sender_id', $adminId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Nhóm tin nhắn theo đối tác trò chuyện (partner_id)
        $messagesByPartner = $messages->groupBy(function($msg) use ($adminId) {
            return $msg->sender_id == $adminId ? $msg->receiver_id : $msg->sender_id;
        });

        // 3. Xây dựng danh sách kèm thông tin chi tiết
        $userList = $users->map(function($user) use ($messagesByPartner, $adminId) {
            $userMessages = $messagesByPartner->get($user->id);
            $lastMsg = $userMessages ? $userMessages->first() : null; // Tin nhắn mới nhất vì đã sắp xếp desc
            
            // Đếm số tin nhắn user gửi tới admin chưa đọc
            $unreadCount = $userMessages 
                ? $userMessages->where('sender_id', $user->id)->where('is_read', false)->count() 
                : 0;

            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'unread_count' => $unreadCount,
                'last_message' => $lastMsg ? $lastMsg->content : null,
                'last_message_is_mine' => $lastMsg ? ($lastMsg->sender_id == $adminId) : false,
                'last_message_time' => $lastMsg && $lastMsg->created_at ? $lastMsg->created_at->format('H:i') : null,
                'last_message_date' => $lastMsg && $lastMsg->created_at ? $lastMsg->created_at->format('d/m') : null,
                'last_message_timestamp' => $lastMsg && $lastMsg->created_at ? $lastMsg->created_at->timestamp : 0,
            ];
        });

        // 4. Sắp xếp ưu tiên:
        // - Người có tin nhắn mới nhất (last_message_timestamp cao nhất) lên ĐẦU TIÊN
        // - Người chưa từng nhắn tin sắp xếp theo ID giảm dần
        $sorted = $userList->sort(function($a, $b) {
            if ($a['last_message_timestamp'] !== $b['last_message_timestamp']) {
                return $b['last_message_timestamp'] <=> $a['last_message_timestamp'];
            }
            return $b['id'] <=> $a['id'];
        })->values();

        $totalUnread = $userList->sum('unread_count');

        return response()->json([
            'users' => $sorted,
            'total_unread' => $totalUnread
        ]);
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
