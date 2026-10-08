<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;

    protected $table = 'chat_messages';

    protected $fillable = [
        'room_id',
        'sender_type',
        'sender_id',
        'sender_name',
        'message',
        'is_read',
        'reply_to_id',
        'is_deleted',
        'deleted_by',
        'deleted_at',
        'attachment_path',
        'attachment_name',
        'attachment_type',
        'attachment_size',
    ];

    protected $casts = [
        'is_read'    => 'boolean',
        'is_deleted' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    protected $appends = ['attachment_url'];

    public function getAttachmentUrlAttribute()
    {
        return $this->attachment_path ? asset($this->attachment_path) : null;
    }

    public function room()
    {
        return $this->belongsTo(ChatRoom::class, 'room_id');
    }

    public function replyTo()
    {
        return $this->belongsTo(ChatMessage::class, 'reply_to_id');
    }
}
