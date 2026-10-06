<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('item', function (Blueprint $table) {
            if (!Schema::hasColumn('item', 'edition')) {
                $table->string('edition', 100)->nullable()->after('call_number');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('item', function (Blueprint $table) {
            if (Schema::hasColumn('item', 'edition')) {
                $table->dropColumn('edition');
            }
        });
    }
};
