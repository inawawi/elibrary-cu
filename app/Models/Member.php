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

    public function isLecturer(): bool
    {
        return (int)$this->member_type_id === 2 
            || str_contains(strtolower($this->memberType?->member_type_name ?? ''), 'dosen');
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
        // Dosen tidak ada masa aktif (berlaku selama masih menjadi dosen)
        if ($this->isLecturer()) {
            return false;
        }

        if (empty($this->expire_date)) {
            return false;
        }

        return Carbon::parse($this->expire_date)->isPast();
    }

    public function getExpiryDisplayAttribute(): string
    {
        if ($this->isLecturer()) {
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
}
