<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_bukutamu', function (Blueprint $table) {
            if (!Schema::hasColumn('tb_bukutamu', 'prodi')) {
                $table->string('prodi', 60)->nullable()->after('status');
            }
            if (!Schema::hasColumn('tb_bukutamu', 'tujuan')) {
                $table->string('tujuan', 50)->nullable()->after('prodi');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tb_bukutamu', function (Blueprint $table) {
            if (Schema::hasColumn('tb_bukutamu', 'prodi')) {
                $table->dropColumn('prodi');
            }
            if (Schema::hasColumn('tb_bukutamu', 'tujuan')) {
                $table->dropColumn('tujuan');
            }
        });
    }
};
