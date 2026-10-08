<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reserve extends Model
{
    protected $table = 'reserve';
    protected $primaryKey = 'reserve_id';
    public $timestamps = false;

    protected $fillable = [
        'member_id',
        'biblio_id',
        'item_code',
        'reserve_date',
    ];

    protected $casts = [
        'reserve_date' => 'datetime',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id', 'member_id');
    }

    public function biblio()
    {
        return $this->belongsTo(Biblio::class, 'biblio_id', 'biblio_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_code', 'item_code');
    }
}
