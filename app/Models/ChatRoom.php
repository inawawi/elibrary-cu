<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatRoom extends Model
{
    use HasFactory;

    protected $table = 'chat_rooms';

    protected $fillable = [
        'member_id',
        'status',
        'last_message',
        'last_message_at',
        'unread_admin_count',
        'unread_member_count',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id', 'member_id');
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'room_id')->orderBy('id', 'asc');
    }
}
