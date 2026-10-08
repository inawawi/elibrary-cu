<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\ChatRoom;
use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ChatController extends Controller
{
    /**
     * Cache key for librarian online heartbeat
     */
    private const LIBRARIAN_CACHE_KEY = 'librarian_last_active_at';
    private const ONLINE_THRESHOLD_SECONDS = 90;

    /**
     * Check if librarian is currently online
     */
    private function isLibrarianOnline(): bool
    {
        $lastActive = Cache::get(self::LIBRARIAN_CACHE_KEY);
        if (!$lastActive) {
            return false;
        }
        return (now()->timestamp - (int)$lastActive) <= self::ONLINE_THRESHOLD_SECONDS;
    }

    /**
     * Update librarian heartbeat (called when admin is browsing or polling)
     */
    private function touchLibrarianHeartbeat(): void
    {
        Cache::put(self::LIBRARIAN_CACHE_KEY, now()->timestamp, 180);
    }

    // ==========================================
    // MEMBER ENDPOINTS
    // ==========================================

    /**
     * Get member chat status (online status of librarians & unread count)
     */
    public function memberStatus()
    {
        $member = Auth::guard('member')->user();
        if (!$member) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $room = ChatRoom::where('member_id', $member->member_id)->first();
        $isOnline = $this->isLibrarianOnline();

        return response()->json([
            'librarian_online' => $isOnline,
            'status_label'     => $isOnline ? 'Pustakawan Online' : 'Pustakawan Sedang Offline',
            'unread_count'     => $room ? (int)$room->unread_member_count : 0,
        ]);
    }

    /**
     * Get member chat messages and mark admin messages as read
     */
    public function memberGetMessages()
    {
        $member = Auth::guard('member')->user();
        if (!$member) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $room = ChatRoom::firstOrCreate(
            ['member_id' => $member->member_id],
            ['status' => 'active', 'unread_admin_count' => 0, 'unread_member_count' => 0]
        );

        // Mark unread admin messages as read
        ChatMessage::where('room_id', $room->id)
            ->where('sender_type', 'admin')
            ->where('is_read', false)
            ->update(['is_read' => true]);

        if ($room->unread_member_count > 0) {
            $room->update(['unread_member_count' => 0]);
        }

        $messages = ChatMessage::where('room_id', $room->id)
            ->orderBy('id', 'asc')
            ->get()
            ->map(function ($msg) {
                return [
                    'id'          => $msg->id,
                    'sender_type' => $msg->sender_type,
                    'sender_name' => $msg->sender_name,
                    'message'     => $msg->message,
                    'is_read'     => (bool)$msg->is_read,
                    'time'        => $msg->created_at ? $msg->created_at->format('H:i') : '',
                    'date'        => $msg->created_at ? $msg->created_at->translatedFormat('d M Y') : '',
                ];
            });

        return response()->json([
            'room_id'          => $room->id,
            'librarian_online' => $this->isLibrarianOnline(),
            'messages'         => $messages,
        ]);
    }

    /**
     * Member sends message to library staff
     */
    public function memberSendMessage(Request $request)
    {
        $member = Auth::guard('member')->user();
        if (!$member) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $request->validate([
            'message' => 'required|string|max:3000',
        ]);

        $messageText = trim($request->input('message'));
        if ($messageText === '') {
            return response()->json(['error' => 'Pesan tidak boleh kosong.'], 422);
        }

        $room = ChatRoom::firstOrCreate(
            ['member_id' => $member->member_id],
            ['status' => 'active', 'unread_admin_count' => 0, 'unread_member_count' => 0]
        );

        $chatMsg = ChatMessage::create([
            'room_id'     => $room->id,
            'sender_type' => 'member',
            'sender_id'   => $member->member_id,
            'sender_name' => $member->member_name ?? $member->member_id,
            'message'     => $messageText,
            'is_read'     => false,
        ]);

        $room->increment('unread_admin_count', 1, [
            'last_message'    => mb_substr($messageText, 0, 150),
            'last_message_at' => now(),
            'status'          => 'active',
        ]);

        return response()->json([
            'success' => true,
            'message' => [
                'id'          => $chatMsg->id,
                'sender_type' => $chatMsg->sender_type,
                'sender_name' => $chatMsg->sender_name,
                'message'     => $chatMsg->message,
                'time'        => $chatMsg->created_at->format('H:i'),
                'date'        => $chatMsg->created_at->translatedFormat('d M Y'),
            ],
        ]);
    }

    // ==========================================
    // ADMIN / LIBRARIAN ENDPOINTS
    // ==========================================

    /**
     * Admin heartbeat ping & unread counter
     */
    public function adminUnreadCount()
    {
        $this->touchLibrarianHeartbeat();

        $unreadTotal = (int)ChatRoom::sum('unread_admin_count');
        $activeRoomsCount = ChatRoom::count();

        return response()->json([
            'unread_total'        => $unreadTotal,
            'active_rooms_count'  => $activeRoomsCount,
            'server_time'         => now()->format('H:i:s'),
        ]);
    }

    /**
     * Get list of chat rooms for admin
     */
    public function adminRooms(Request $request)
    {
        $this->touchLibrarianHeartbeat();

        $search = trim($request->input('q', ''));

        $query = ChatRoom::with('member')->orderByDesc('last_message_at')->orderByDesc('updated_at');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('member_id', 'like', "%{$search}%")
                  ->orWhereHas('member', function ($mq) use ($search) {
                      $mq->where('member_name', 'like', "%{$search}%");
                  });
            });
        }

        $rooms = $query->take(50)->get()->map(function ($room) {
            $member = $room->member;
            return [
                'id'                  => $room->id,
                'member_id'           => $room->member_id,
                'member_name'         => $member ? $member->member_name : $room->member_id,
                'member_inst'         => $member ? ($member->inst_name ?? 'Mahasiswa/Dosen') : '-',
                'last_message'        => $room->last_message ?: 'Belum ada percakapan',
                'last_message_at'     => $room->last_message_at ? $room->last_message_at->diffForHumans() : '',
                'unread_admin_count'  => (int)$room->unread_admin_count,
            ];
        });

        return response()->json([
            'rooms'        => $rooms,
            'unread_total' => (int)ChatRoom::sum('unread_admin_count'),
        ]);
    }

    /**
     * Get messages in a room for admin and mark member messages as read
     */
    public function adminGetMessages($id)
    {
        $this->touchLibrarianHeartbeat();

        $room = ChatRoom::with('member')->findOrFail($id);

        // Mark member messages as read
        ChatMessage::where('room_id', $room->id)
            ->where('sender_type', 'member')
            ->where('is_read', false)
            ->update(['is_read' => true]);

        if ($room->unread_admin_count > 0) {
            $room->update(['unread_admin_count' => 0]);
        }

        $messages = ChatMessage::where('room_id', $room->id)
            ->orderBy('id', 'asc')
            ->get()
            ->map(function ($msg) {
                return [
                    'id'          => $msg->id,
                    'sender_type' => $msg->sender_type,
                    'sender_name' => $msg->sender_name,
                    'message'     => $msg->message,
                    'is_read'     => (bool)$msg->is_read,
                    'time'        => $msg->created_at ? $msg->created_at->format('H:i') : '',
                    'date'        => $msg->created_at ? $msg->created_at->translatedFormat('d M Y') : '',
                ];
            });

        return response()->json([
            'room' => [
                'id'          => $room->id,
                'member_id'   => $room->member_id,
                'member_name' => $room->member ? $room->member->member_name : $room->member_id,
                'member_inst' => $room->member ? ($room->member->inst_name ?? '-') : '-',
            ],
            'messages'     => $messages,
            'unread_total' => (int)ChatRoom::sum('unread_admin_count'),
        ]);
    }

    /**
     * Admin sends message to member
     */
    public function adminSendMessage(Request $request, $id)
    {
        $this->touchLibrarianHeartbeat();

        $admin = Auth::guard('web')->user();
        if (!$admin) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $request->validate([
            'message' => 'required|string|max:3000',
        ]);

        $messageText = trim($request->input('message'));
        if ($messageText === '') {
            return response()->json(['error' => 'Pesan tidak boleh kosong.'], 422);
        }

        $room = ChatRoom::findOrFail($id);

        $senderName = $admin->realname ?: ($admin->username ?: 'Pustakawan');

        $chatMsg = ChatMessage::create([
            'room_id'     => $room->id,
            'sender_type' => 'admin',
            'sender_id'   => (string)$admin->user_id,
            'sender_name' => 'Pustakawan (' . $senderName . ')',
            'message'     => $messageText,
            'is_read'     => false,
        ]);

        $room->increment('unread_member_count', 1, [
            'last_message'    => mb_substr($messageText, 0, 150),
            'last_message_at' => now(),
            'status'          => 'active',
        ]);

        return response()->json([
            'success' => true,
            'message' => [
                'id'          => $chatMsg->id,
                'sender_type' => $chatMsg->sender_type,
                'sender_name' => $chatMsg->sender_name,
                'message'     => $chatMsg->message,
                'time'        => $chatMsg->created_at->format('H:i'),
                'date'        => $chatMsg->created_at->translatedFormat('d M Y'),
            ],
        ]);
    }
}
