<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Member extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'member';
    protected $primaryKey = 'member_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'member_id',
        'member_name',
        'gender',
        'birth_date',
        'member_type_id',
        'member_address',
        'member_mail_address',
        'member_email',
        'postal_code',
        'inst_name',
        'is_new',
        'member_image',
        'pin',
        'member_phone',
        'member_fax',
        'member_since_date',
        'register_date',
        'expire_date',
        'member_notes',
        'is_pending',
        'mpasswd',
        'last_login',
        'last_login_ip',
        'input_date',
        'last_update',
    ];

    protected $hidden = [
        'mpasswd',
        'pin',
    ];

    public function getAuthPassword(): string
    {
        return $this->mpasswd ?? '';
    }

    public function getAuthPasswordName(): string
    {
        return 'mpasswd';
    }

    public function getAuthIdentifierName(): string
    {
        return 'member_id';
    }

    public function memberType()
    {
        return $this->belongsTo(MemberType::class, 'member_type_id');
    }

    public function loans()
    {
        return $this->hasMany(Loan::class, 'member_id', 'member_id');
    }

    public function activeLoans()
    {
        return $this->hasMany(Loan::class, 'member_id', 'member_id')->where('is_return', 0);
    }

    public function loanHistories()
    {
        return $this->hasMany(LoanHistory::class, 'member_id', 'member_id');
    }

    public function reserves()
    {
        return $this->hasMany(Reserve::class, 'member_id', 'member_id');
    }

    public function isLecturer(): bool
    {
        return (int)$this->member_type_id === 2 
            || str_contains(strtolower($this->memberType?->member_type_name ?? ''), 'dosen');
    }

    public function isStaff(): bool
    {
        return (int)$this->member_type_id === 3 
            || str_contains(strtolower($this->memberType?->member_type_name ?? ''), 'staf');
    }

    public function isNonStudent(): bool
    {
        return $this->isLecturer() || $this->isStaff() || (int)$this->member_type_id !== 1;
    }

    public function isStudent(): bool
    {
        return (int)$this->member_type_id === 1 
            || str_contains(strtolower($this->memberType?->member_type_name ?? ''), 'mahasiswa');
    }

    public function getProdiCodeAttribute(): ?string
    {
        if (strlen($this->member_id) >= 2 && ctype_digit(substr($this->member_id, 0, 2))) {
            return substr($this->member_id, 0, 2);
        }
        return null;
    }

    public function getAngkatanYearAttribute(): ?int
    {
        if (strlen($this->member_id) >= 4 && ctype_digit(substr($this->member_id, 2, 2))) {
            return 2000 + (int)substr($this->member_id, 2, 2);
        }
        return null;
    }

    /**
     * Hitung tanggal kadaluarsa mahasiswa (masa aktif 7 tahun dari 2 digit tahun NIM)
     */
    public static function calculateStudentExpireDate(?string $nim, ?string $registerDate = null): ?string
    {
        if (empty($nim)) {
            return null;
        }

        // NIM 8 digit tersusun dari 2 digit kode prodi, 2 digit tahun angkatan, 4 digit nomor urut
        if (strlen($nim) >= 4 && ctype_digit(substr($nim, 2, 2))) {
            $entryYear = 2000 + (int)substr($nim, 2, 2);
            $expireYear = $entryYear + 7;

            if (!empty($registerDate)) {
                $reg = Carbon::parse($registerDate);
                $day = max(1, $reg->day - 1);
                return sprintf('%04d-%02d-%02d', $expireYear, $reg->month, $day);
            }

            return sprintf('%04d-08-31', $expireYear);
        }

        $base = $registerDate ? Carbon::parse($registerDate) : Carbon::today();
        return $base->copy()->addYears(7)->toDateString();
    }

    public function isExpired(): bool
    {
        // Dosen dan Staf tidak ada masa aktif kedaluwarsa (berlaku selama masih bertugas)
        if ($this->isNonStudent()) {
            return false;
        }

        if (empty($this->expire_date)) {
            return false;
        }

        return Carbon::parse($this->expire_date)->isPast();
    }

    public function getExpiryDisplayAttribute(): string
    {
        if ($this->isNonStudent()) {
            return 'Aktif Selama Bertugas';
        }

        if (!empty($this->expire_date)) {
            return Carbon::parse($this->expire_date)->translatedFormat('d F Y');
        }

        return 'Seumur Hidup';
    }

    public function getStatusLabelAttribute(): string
    {
        if ($this->is_pending == 1) {
            return 'Non-Aktif';
        }

        if ($this->isExpired()) {
            return 'Kedaluwarsa';
        }

        return 'Aktif';
    }

    public function getAvatarUrlAttribute(): string
    {
        if (!empty($this->member_image) && file_exists(public_path('images/persons/' . $this->member_image))) {
            return asset('images/persons/' . $this->member_image);
        }
        return "https://ui-avatars.com/api/?name=" . urlencode($this->member_name) . "&background=0D8ABC&color=fff&size=200";
    }

    /**
     * Hitung semester mahasiswa berdasarkan tahun angkatan (digit ke 3 & 4 pada NIM)
     */
    public function getSemesterAttribute(): int
    {
        if (!$this->isStudent() || empty($this->member_id) || strlen($this->member_id) < 4) {
            return 0;
        }

        $angkatan = $this->angkatan_year;
        if (!$angkatan) {
            return 0;
        }

        $now = Carbon::now();
        $diffYears = $now->year - $angkatan;
        $month = $now->month;

        // Semester Ganjil: Bulan September s.d. Februari (9 s.d. 12 & 1 s.d. 2)
        // Semester Genap: Bulan Maret s.d. Agustus (3 s.d. 8)
        if ($month >= 9) {
            $semester = ($diffYears * 2) + 1;
        } elseif ($month <= 2) {
            $semester = (($diffYears - 1) * 2) + 1;
        } else {
            $semester = ($diffYears * 2);
        }

        return max(1, $semester);
    }

    /**
     * Cek apakah mahasiswa merupakan mahasiswa tingkat akhir (semester >= 7)
     */
    public function isSeniorStudent(): bool
    {
        return $this->isStudent() && $this->semester >= 7;
    }

    /**
     * Nama Program Studi berdasarkan prefix NIM atau catatan profil
     */
    public function getProdiNameAttribute(): string
    {
        if (!empty($this->member_notes) && preg_match('/Prodi:\s*([^|]+)/i', $this->member_notes, $matches)) {
            return trim($matches[1]);
        }

        $prefix = $this->prodi_code;
        return match ($prefix) {
            '11' => 'Sistem Informasi',
            '12' => 'Teknologi Informasi',
            '13' => 'Sistem dan Teknologi Informasi',
            '21' => 'Bisnis Digital',
            '22' => 'Akuntansi',
            '24' => 'Manajemen',
            '25' => 'Kewirausahaan',
            default => 'Teknologi Informasi',
        };
    }

    /**
     * Riwayat / data pengajuan skripsi mahasiswa di bibliografi
     */
    public function thesisSubmission()
    {
        return Biblio::where('gmd_id', 262)
            ->where(function ($query) {
                $query->where('isbn_issn', $this->member_id)
                      ->orWhere('spec_detail_info', 'like', '%"nim":"' . $this->member_id . '"%');
            })
            ->latest('input_date')
            ->first();
    }

    /**
     * Data surat bebas pustaka
     */
    public function bebasPustakaRecord()
    {
        return BebasPustaka::where('nim', $this->member_id)->latest('tgl_in')->first();
    }

    /**
     * Cek apakah memenuhi syarat untuk mencetak surat bebas pustaka:
     * 1. Skripsi telah disetujui pustakawan (status = approved / ada di tb_bebaspustaka)
     * 2. Tidak ada tanggungan peminjaman buku yang aktif
     */
    public function isBebasPustakaEligible(): bool
    {
        $hasActiveLoans = $this->activeLoans()->exists();
        if ($hasActiveLoans) {
            return false;
        }

        if ($this->bebasPustakaRecord()) {
            return true;
        }

        $thesis = $this->thesisSubmission();
        if ($thesis) {
            $spec = json_decode($thesis->spec_detail_info ?? '{}', true);
            if (($spec['status'] ?? '') === 'approved') {
                return true;
            }
        }

        return false;
    }
}

