<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fine extends Model
{
    use HasFactory;

    protected $table = 'fines';
    protected $primaryKey = 'fines_id';
    public $timestamps = false;

    protected $fillable = [
        'fines_date',
        'member_id',
        'debet',
        'credit',
        'description',
    ];

    protected $casts = [
        'fines_date' => 'date',
        'debet' => 'integer',
        'credit' => 'integer',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id', 'member_id');
    }

    /**
     * Sisa tagihan denda (debet - credit)
     */
    public function getBalanceAttribute(): int
    {
        return max(0, (int)$this->debet - (int)$this->credit);
    }

    /**
     * Apakah denda sudah lunas
     */
    public function getIsPaidAttribute(): bool
    {
        return (int)$this->credit >= (int)$this->debet;
    }

    /**
     * Scope denda belum lunas
     */
    public function scopeUnpaid($query)
    {
        return $query->whereRaw('debet > credit');
    }

    /**
     * Scope denda lunas
     */
    public function scopePaid($query)
    {
        return $query->whereRaw('credit >= debet');
    }
}
