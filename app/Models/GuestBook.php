<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuestBook extends Model
{
    protected $table = 'tb_bukutamu';
    protected $primaryKey = 'id_bukutamu';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_bukutamu',
        'id_kampus',
        'id_anggota',
        'nama',
        'tgl',
        'jam',
        'status',
        'keperluan',
        'nomor_urut',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class, 'id_anggota', 'member_id');
    }
}
