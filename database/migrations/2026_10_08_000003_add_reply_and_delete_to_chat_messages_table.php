<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            if (!Schema::hasColumn('chat_messages', 'reply_to_id')) {
                $table->unsignedBigInteger('reply_to_id')->nullable()->after('room_id')->index();
            }
            if (!Schema::hasColumn('chat_messages', 'is_deleted')) {
                $table->boolean('is_deleted')->default(false)->after('is_read');
            }
            if (!Schema::hasColumn('chat_messages', 'deleted_by')) {
                $table->string('deleted_by', 50)->nullable()->after('is_deleted');
            }
            if (!Schema::hasColumn('chat_messages', 'deleted_at')) {
                $table->timestamp('deleted_at')->nullable()->after('deleted_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $cols = [];
            if (Schema::hasColumn('chat_messages', 'reply_to_id')) $cols[] = 'reply_to_id';
            if (Schema::hasColumn('chat_messages', 'is_deleted')) $cols[] = 'is_deleted';
            if (Schema::hasColumn('chat_messages', 'deleted_by')) $cols[] = 'deleted_by';
            if (Schema::hasColumn('chat_messages', 'deleted_at')) $cols[] = 'deleted_at';
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};
