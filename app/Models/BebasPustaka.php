<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BebasPustaka extends Model
{
    use HasFactory;

    protected $table = 'tb_bebaspustaka';
    protected $primaryKey = 'id_bebaspustaka';
    public $timestamps = false;

    protected $fillable = [
        'nim',
        'tgl_in',
        'id_admin',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class, 'nim', 'member_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'id_admin', 'user_id');
    }
}
