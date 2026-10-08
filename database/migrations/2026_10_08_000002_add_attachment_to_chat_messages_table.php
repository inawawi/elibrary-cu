<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            if (!Schema::hasColumn('chat_messages', 'attachment_path')) {
                $table->string('attachment_path')->nullable()->after('message');
                $table->string('attachment_name')->nullable()->after('attachment_path');
                $table->string('attachment_type', 50)->nullable()->after('attachment_name'); // 'image' or 'file'
                $table->unsignedBigInteger('attachment_size')->nullable()->after('attachment_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            if (Schema::hasColumn('chat_messages', 'attachment_path')) {
                $table->dropColumn(['attachment_path', 'attachment_name', 'attachment_type', 'attachment_size']);
            }
        });
    }
};
