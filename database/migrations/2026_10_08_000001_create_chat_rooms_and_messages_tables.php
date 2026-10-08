<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('chat_rooms')) {
            Schema::create('chat_rooms', function (Blueprint $table) {
                $table->id();
                $table->string('member_id', 50)->index();
                $table->string('status', 20)->default('active');
                $table->text('last_message')->nullable();
                $table->timestamp('last_message_at')->nullable();
                $table->unsignedInteger('unread_admin_count')->default(0);
                $table->unsignedInteger('unread_member_count')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('chat_messages')) {
            Schema::create('chat_messages', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('room_id')->index();
                $table->string('sender_type', 20)->default('member'); // 'member' or 'admin'
                $table->string('sender_id', 50);
                $table->string('sender_name', 255);
                $table->text('message');
                $table->boolean('is_read')->default(false);
                $table->timestamps();

                $table->foreign('room_id')->references('id')->on('chat_rooms')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('chat_rooms');
    }
};
