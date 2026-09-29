<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'user';
    protected $primaryKey = 'user_id';
    public $timestamps = false;

    protected $fillable = [
        'username',
        'realname',
        'passwd',
        'email',
        'role',
        'user_type',
        'user_image',
        'last_login',
        'last_login_ip',
        'groups',
        'admin_template',
        'input_date',
        'last_update',
    ];

    protected $hidden = [
        'passwd',
        'remember_token',
    ];

    public function getAuthPassword(): string
    {
        return $this->passwd ?? '';
    }

    public function getAuthPasswordName(): string
    {
        return 'passwd';
    }

    public function getAuthIdentifierName(): string
    {
        return 'user_id';
    }

    public function isAdmin(): bool
    {
        return (int) $this->user_type === 1;
    }

    public function isDeveloper(): bool
    {
        $role = strtolower(trim($this->role ?? ''));
        if ($role === 'pengembang sistem' || $role === 'developer' || $role === 'superadmin') {
            return true;
        }

        $groups = @unserialize($this->groups ?? '');
        return is_array($groups) && in_array('2', $groups, true);
    }

    public function getRoleNameAttribute(): string
    {
        if ($this->isDeveloper()) {
            return 'Pengembang Sistem';
        }

        $role = strtolower(trim($this->role ?? ''));
        if ($role === 'staf' || $role === 'staff') {
            return 'Staf Perpustakaan';
        }

        return 'Administrator';
    }

    public function getAvatarUrlAttribute(): string
    {
        if (!empty($this->user_image) && file_exists(public_path('images/persons/' . $this->user_image))) {
            return asset('images/persons/' . $this->user_image);
        }
        $bg = $this->isDeveloper() ? '4f46e5' : '0284c7';
        return "https://ui-avatars.com/api/?name=" . urlencode($this->realname ?: $this->username) . "&background=" . $bg . "&color=fff&size=100";
    }
}
