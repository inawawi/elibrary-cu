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

    public function isExpired(): bool
    {
        if (empty($this->expire_date)) {
            return false;
        }
        return Carbon::parse($this->expire_date)->isPast();
    }

    public function getAvatarUrlAttribute(): string
    {
        if (!empty($this->member_image) && file_exists(public_path('images/persons/' . $this->member_image))) {
            return asset('images/persons/' . $this->member_image);
        }
        return "https://ui-avatars.com/api/?name=" . urlencode($this->member_name) . "&background=0D8ABC&color=fff&size=200";
    }
}
