<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Member;
use App\Models\MemberType;
use Carbon\Carbon;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Update Mahasiswa member_type periode to 2555 days (7 years)
        MemberType::where('member_type_id', 1)->update([
            'member_periode' => 2555,
        ]);

        // 2. Update Dosen member_type periode to 0 (no expiry / unlimited)
        MemberType::where('member_type_id', 2)->update([
            'member_periode' => 0,
        ]);

        // 3. Update existing Mahasiswa expiry dates to 7 years calculated from the 2-digit angkatan year in NIM
        $students = Member::where('member_type_id', 1)->get();
        foreach ($students as $student) {
            $nim = $student->member_id;
            if (strlen($nim) >= 4 && ctype_digit(substr($nim, 2, 2))) {
                $entryYear = 2000 + (int)substr($nim, 2, 2);
                $expireYear = $entryYear + 7;
                $reg = $student->register_date ? Carbon::parse($student->register_date) : null;
                $month = $reg ? $reg->month : 8;
                $day = $reg ? max(1, $reg->day - 1) : 31;
                $newExpireDate = sprintf('%04d-%02d-%02d', $expireYear, $month, $day);

                $student->update(['expire_date' => $newExpireDate]);
            }
        }

        // 4. Update existing Dosen expiry dates to null (valid as long as active lecturer)
        Member::where('member_type_id', 2)->update([
            'expire_date' => null,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op
    }
};
