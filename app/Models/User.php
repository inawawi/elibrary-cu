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
}
