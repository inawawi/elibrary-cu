<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add role column to user table if not exists
        if (!Schema::hasColumn('user', 'role')) {
            Schema::table('user', function (Blueprint $table) {
                $table->string('role', 50)->default('administrator')->after('email');
            });
        }

        // 2. Insert user groups in user_group table if table exists
        if (Schema::hasTable('user_group')) {
            DB::table('user_group')->updateOrInsert(
                ['group_id' => 1],
                ['group_name' => 'Administrator', 'input_date' => now(), 'last_update' => now()]
            );
            DB::table('user_group')->updateOrInsert(
                ['group_id' => 2],
                ['group_name' => 'Pengembang Sistem', 'input_date' => now(), 'last_update' => now()]
            );
            DB::table('user_group')->updateOrInsert(
                ['group_id' => 3],
                ['group_name' => 'Staf Perpustakaan', 'input_date' => now(), 'last_update' => now()]
            );
        }

        // 3. Assign user 1 (AdminLib) as Pengembang Sistem
        DB::table('user')->where('user_id', 1)->update([
            'role' => 'pengembang sistem',
            'groups' => serialize(['2']),
            'last_update' => now(),
        ]);

        // 4. Assign user 2 as administrator
        DB::table('user')->where('user_id', 2)->update([
            'role' => 'administrator',
            'groups' => serialize(['1']),
            'last_update' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('user', 'role')) {
            Schema::table('user', function (Blueprint $table) {
                $table->dropColumn('role');
            });
        }
    }
};
