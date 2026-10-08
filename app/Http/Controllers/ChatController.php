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
        $isBlocked = $room ? ($room->status === 'blocked') : false;

        return response()->json([
            'librarian_online' => $isOnline,
            'status_label'     => $isOnline ? 'Pustakawan Online' : 'Pustakawan Sedang Offline',
            'unread_count'     => $room ? (int)$room->unread_member_count : 0,
            'is_blocked'       => $isBlocked,
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

        $messages = ChatMessage::with('replyTo')
            ->where('room_id', $room->id)
            ->orderBy('id', 'asc')
            ->get()
            ->map(function ($msg) {
                $isDeleted = (bool)$msg->is_deleted;
                $replyData = null;
                if ($msg->replyTo) {
                    $replyData = [
                        'id'              => $msg->replyTo->id,
                        'sender_type'     => $msg->replyTo->sender_type,
                        'sender_name'     => $msg->replyTo->sender_name,
                        'message'         => $msg->replyTo->is_deleted ? 'Pesan telah dihapus' : mb_substr($msg->replyTo->message, 0, 100),
                        'attachment_type' => $msg->replyTo->is_deleted ? null : $msg->replyTo->attachment_type,
                        'attachment_name' => $msg->replyTo->is_deleted ? null : $msg->replyTo->attachment_name,
                    ];
                }

                return [
                    'id'              => $msg->id,
                    'sender_type'     => $msg->sender_type,
                    'sender_name'     => $msg->sender_name,
                    'message'         => $isDeleted ? '🚫 Pesan ini telah dihapus' : $msg->message,
                    'is_read'         => (bool)$msg->is_read,
                    'is_deleted'      => $isDeleted,
                    'deleted_by'      => $msg->deleted_by,
                    'reply_to'        => $replyData,
                    'attachment_path' => $isDeleted ? null : $msg->attachment_path,
                    'attachment_name' => $isDeleted ? null : $msg->attachment_name,
                    'attachment_type' => $isDeleted ? null : $msg->attachment_type,
                    'attachment_size' => $isDeleted ? null : $msg->attachment_size,
                    'attachment_url'  => $isDeleted ? null : $msg->attachment_url,
                    'time'            => $msg->created_at ? $msg->created_at->format('H:i') : '',
                    'date'            => $msg->created_at ? $msg->created_at->translatedFormat('d M Y') : '',
                ];
            });

        return response()->json([
            'room_id'          => $room->id,
            'librarian_online' => $this->isLibrarianOnline(),
            'is_blocked'       => $room->status === 'blocked',
            'messages'         => $messages,
        ]);
    }

    /**
     * Member sends message or file/image to library staff
     */
    public function memberSendMessage(Request $request)
    {
        $member = Auth::guard('member')->user();
        if (!$member) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $request->validate([
            'message'     => 'nullable|string|max:3000',
            'reply_to_id' => 'nullable|integer',
            'attachment'  => 'nullable|file|max:10240|mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip',
        ]);

        $messageText = trim($request->input('message', ''));
        $hasFile = $request->hasFile('attachment');

        if ($messageText === '' && !$hasFile) {
            return response()->json(['error' => 'Pesan atau file lampiran tidak boleh kosong.'], 422);
        }

        $attachmentPath = null;
        $attachmentName = null;
        $attachmentType = null;
        $attachmentSize = null;

        if ($hasFile) {
            $file = $request->file('attachment');
            $attachmentName = $file->getClientOriginalName();
            $attachmentSize = $file->getSize();
            $ext = strtolower($file->getClientOriginalExtension());
            $attachmentType = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']) ? 'image' : 'file';

            $uploadDir = public_path('uploads/chat');
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $fileName = 'chat_m_' . time() . '_' . uniqid() . '.' . $ext;
            $file->move($uploadDir, $fileName);
            $attachmentPath = 'uploads/chat/' . $fileName;

            if ($messageText === '') {
                $messageText = $attachmentType === 'image' ? '[Foto: ' . $attachmentName . ']' : '[File: ' . $attachmentName . ']';
            }
        }

        $room = ChatRoom::firstOrCreate(
            ['member_id' => $member->member_id],
            ['status' => 'active', 'unread_admin_count' => 0, 'unread_member_count' => 0]
        );

        if ($room->status === 'blocked') {
            return response()->json([
                'error' => 'Akses chat Anda telah diblokir oleh petugas perpustakaan karena pengiriman pesan atau berkas yang tidak pantas.'
            ], 403);
        }

        $chatMsg = ChatMessage::create([
            'room_id'         => $room->id,
            'reply_to_id'     => $request->input('reply_to_id'),
            'sender_type'     => 'member',
            'sender_id'       => $member->member_id,
            'sender_name'     => $member->member_name ?? $member->member_id,
            'message'         => $messageText,
            'attachment_path' => $attachmentPath,
            'attachment_name' => $attachmentName,
            'attachment_type' => $attachmentType,
            'attachment_size' => $attachmentSize,
            'is_read'         => false,
        ]);

        $room->increment('unread_admin_count', 1, [
            'last_message'    => mb_substr($messageText, 0, 150),
            'last_message_at' => now(),
            'status'          => 'active',
        ]);

        return response()->json([
            'success' => true,
            'message' => [
                'id'              => $chatMsg->id,
                'sender_type'     => $chatMsg->sender_type,
                'sender_name'     => $chatMsg->sender_name,
                'message'         => $chatMsg->message,
                'attachment_path' => $chatMsg->attachment_path,
                'attachment_name' => $chatMsg->attachment_name,
                'attachment_type' => $chatMsg->attachment_type,
                'attachment_size' => $chatMsg->attachment_size,
                'attachment_url'  => $chatMsg->attachment_url,
                'time'            => $chatMsg->created_at->format('H:i'),
                'date'            => $chatMsg->created_at->translatedFormat('d M Y'),
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
                'status'              => $room->status ?: 'active',
                'is_blocked'          => $room->status === 'blocked',
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

        $messages = ChatMessage::with('replyTo')
            ->where('room_id', $room->id)
            ->orderBy('id', 'asc')
            ->get()
            ->map(function ($msg) {
                $isDeleted = (bool)$msg->is_deleted;
                $replyData = null;
                if ($msg->replyTo) {
                    $replyData = [
                        'id'              => $msg->replyTo->id,
                        'sender_type'     => $msg->replyTo->sender_type,
                        'sender_name'     => $msg->replyTo->sender_name,
                        'message'         => $msg->replyTo->is_deleted ? 'Pesan telah dihapus' : mb_substr($msg->replyTo->message, 0, 100),
                        'attachment_type' => $msg->replyTo->is_deleted ? null : $msg->replyTo->attachment_type,
                        'attachment_name' => $msg->replyTo->is_deleted ? null : $msg->replyTo->attachment_name,
                    ];
                }

                return [
                    'id'              => $msg->id,
                    'sender_type'     => $msg->sender_type,
                    'sender_name'     => $msg->sender_name,
                    'message'         => $isDeleted ? '🚫 Pesan ini telah dihapus' : $msg->message,
                    'is_read'         => (bool)$msg->is_read,
                    'is_deleted'      => $isDeleted,
                    'deleted_by'      => $msg->deleted_by,
                    'reply_to'        => $replyData,
                    'attachment_path' => $isDeleted ? null : $msg->attachment_path,
                    'attachment_name' => $isDeleted ? null : $msg->attachment_name,
                    'attachment_type' => $isDeleted ? null : $msg->attachment_type,
                    'attachment_size' => $isDeleted ? null : $msg->attachment_size,
                    'attachment_url'  => $isDeleted ? null : $msg->attachment_url,
                    'time'            => $msg->created_at ? $msg->created_at->format('H:i') : '',
                    'date'            => $msg->created_at ? $msg->created_at->translatedFormat('d M Y') : '',
                ];
            });

        return response()->json([
            'room' => [
                'id'          => $room->id,
                'member_id'   => $room->member_id,
                'member_name' => $room->member ? $room->member->member_name : $room->member_id,
                'member_inst' => $room->member ? ($room->member->inst_name ?? '-') : '-',
                'status'      => $room->status ?: 'active',
                'is_blocked'  => $room->status === 'blocked',
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
            'message'     => 'nullable|string|max:3000',
            'reply_to_id' => 'nullable|integer',
            'attachment'  => 'nullable|file|max:10240|mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip',
        ]);

        $messageText = trim($request->input('message', ''));
        $hasFile = $request->hasFile('attachment');

        if ($messageText === '' && !$hasFile) {
            return response()->json(['error' => 'Pesan atau file lampiran tidak boleh kosong.'], 422);
        }

        $attachmentPath = null;
        $attachmentName = null;
        $attachmentType = null;
        $attachmentSize = null;

        if ($hasFile) {
            $file = $request->file('attachment');
            $attachmentName = $file->getClientOriginalName();
            $attachmentSize = $file->getSize();
            $ext = strtolower($file->getClientOriginalExtension());
            $attachmentType = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']) ? 'image' : 'file';

            $uploadDir = public_path('uploads/chat');
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $fileName = 'chat_a_' . time() . '_' . uniqid() . '.' . $ext;
            $file->move($uploadDir, $fileName);
            $attachmentPath = 'uploads/chat/' . $fileName;

            if ($messageText === '') {
                $messageText = $attachmentType === 'image' ? '[Foto: ' . $attachmentName . ']' : '[File: ' . $attachmentName . ']';
            }
        }

        $room = ChatRoom::findOrFail($id);

        $senderName = $admin->realname ?: ($admin->username ?: 'Pustakawan');

        $chatMsg = ChatMessage::create([
            'room_id'         => $room->id,
            'reply_to_id'     => $request->input('reply_to_id'),
            'sender_type'     => 'admin',
            'sender_id'       => (string)$admin->user_id,
            'sender_name'     => 'Pustakawan (' . $senderName . ')',
            'message'         => $messageText,
            'attachment_path' => $attachmentPath,
            'attachment_name' => $attachmentName,
            'attachment_type' => $attachmentType,
            'attachment_size' => $attachmentSize,
            'is_read'         => false,
        ]);

        $room->increment('unread_member_count', 1, [
            'last_message'    => mb_substr($messageText, 0, 150),
            'last_message_at' => now(),
            'status'          => 'active',
        ]);

        return response()->json([
            'success' => true,
            'message' => [
                'id'              => $chatMsg->id,
                'sender_type'     => $chatMsg->sender_type,
                'sender_name'     => $chatMsg->sender_name,
                'message'         => $chatMsg->message,
                'attachment_path' => $chatMsg->attachment_path,
                'attachment_name' => $chatMsg->attachment_name,
                'attachment_type' => $chatMsg->attachment_type,
                'attachment_size' => $chatMsg->attachment_size,
                'attachment_url'  => $chatMsg->attachment_url,
                'time'            => $chatMsg->created_at->format('H:i'),
                'date'            => $chatMsg->created_at->translatedFormat('d M Y'),
            ],
        ]);
    }

    /**
     * Admin toggle block / unblock member chat room
     */
    public function adminToggleBlockRoom(Request $request, $id)
    {
        $this->touchLibrarianHeartbeat();

        $admin = Auth::guard('web')->user();
        if (!$admin) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $room = ChatRoom::with('member')->findOrFail($id);
        $isBlocked = $room->status === 'blocked';
        $newStatus = $isBlocked ? 'active' : 'blocked';
        $room->update(['status' => $newStatus]);

        $adminName = $admin->realname ?: ($admin->username ?: 'Pustakawan');
        $systemText = $newStatus === 'blocked'
            ? '⚠️ PEMBERITAHUAN: Akses chat anggota ini telah DIBLOKIR oleh petugas perpustakaan (' . $adminName . '). Anggota tidak dapat mengirimkan pesan atau berkas lagi.'
            : '✅ PEMBERITAHUAN: Akses chat anggota ini telah DIBUKA KEMBALI (UNBLOCK) oleh petugas perpustakaan (' . $adminName . ').';

        ChatMessage::create([
            'room_id'     => $room->id,
            'sender_type' => 'admin',
            'sender_id'   => (string)$admin->user_id,
            'sender_name' => 'Sistem Perpustakaan',
            'message'     => $systemText,
            'is_read'     => true,
        ]);

        $room->update([
            'last_message'    => mb_substr($systemText, 0, 150),
            'last_message_at' => now(),
        ]);

        return response()->json([
            'success'    => true,
            'new_status' => $newStatus,
            'is_blocked' => $newStatus === 'blocked',
            'message'    => $newStatus === 'blocked'
                ? 'Member berhasil diblokir dari layanan chat.'
                : 'Blokir member berhasil dibuka.',
        ]);
    }

    /**
     * Member deletes their own message (WhatsApp style: 'Pesan ini telah dihapus')
     */
    public function memberDeleteMessage(Request $request, $id)
    {
        $member = Auth::guard('member')->user();
        if (!$member) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $msg = ChatMessage::findOrFail($id);

        if ($msg->sender_type !== 'member' || $msg->sender_id !== $member->member_id) {
            return response()->json(['error' => 'Anda hanya dapat menghapus pesan Anda sendiri.'], 403);
        }

        $msg->update([
            'is_deleted' => true,
            'deleted_by' => 'member',
            'deleted_at' => now(),
        ]);

        if ($msg->attachment_path && file_exists(public_path($msg->attachment_path))) {
            @unlink(public_path($msg->attachment_path));
        }

        return response()->json([
            'success' => true,
            'message' => 'Pesan berhasil dihapus.',
        ]);
    }

    /**
     * Admin deletes any chat message (WhatsApp style: 'Pesan ini telah dihapus')
     */
    public function adminDeleteMessage(Request $request, $id)
    {
        $this->touchLibrarianHeartbeat();
        $admin = Auth::guard('web')->user();
        if (!$admin) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $msg = ChatMessage::findOrFail($id);

        $msg->update([
            'is_deleted' => true,
            'deleted_by' => 'admin',
            'deleted_at' => now(),
        ]);

        if ($msg->attachment_path && file_exists(public_path($msg->attachment_path))) {
            @unlink(public_path($msg->attachment_path));
        }

        return response()->json([
            'success' => true,
            'message' => 'Pesan berhasil dihapus.',
        ]);
    }
}
