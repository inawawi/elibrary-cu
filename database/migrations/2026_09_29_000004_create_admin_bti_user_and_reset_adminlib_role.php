<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Reset AdminLib to administrator (admin perpustakaan biasa seperti Gustianty)
        DB::table('user')->where('username', 'AdminLib')->update([
            'role' => 'administrator',
            'groups' => serialize(['1']),
            'last_update' => now(),
        ]);

        // 2. Create or update AdminBTI as pengembang sistem
        $existingBti = DB::table('user')->where('username', 'AdminBTI')->first();

        if ($existingBti) {
            DB::table('user')->where('username', 'AdminBTI')->update([
                'realname' => 'Administrator BTI',
                'email' => 'bti@cyber-univ.ac.id',
                'role' => 'pengembang sistem',
                'groups' => serialize(['2']),
                'passwd' => Hash::make('AdminBTI2026!'),
                'user_type' => 1,
                'last_update' => now(),
            ]);
        } else {
            DB::table('user')->insert([
                'username' => 'AdminBTI',
                'realname' => 'Administrator BTI',
                'email' => 'bti@cyber-univ.ac.id',
                'role' => 'pengembang sistem',
                'groups' => serialize(['2']),
                'passwd' => Hash::make('AdminBTI2026!'),
                'user_type' => 1,
                'input_date' => now()->toDateString(),
                'last_update' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('user')->where('username', 'AdminBTI')->delete();
    }
};
