<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gmd extends Model
{
    protected $table = 'mst_gmd';
    protected $primaryKey = 'gmd_id';
    public $timestamps = false;

    protected $fillable = [
        'gmd_code',
        'gmd_name',
        'icon_image',
        'input_date',
        'last_update',
    ];

    public const CURATED_IDS = [1, 262, 263, 30, 5, 6, 7];

    public function biblios()
    {
        return $this->hasMany(Biblio::class, 'gmd_id');
    }

    public function scopeCurated($query)
    {
        return $query->whereIn('gmd_id', self::CURATED_IDS)
                     ->orderByRaw('FIELD(gmd_id, 1, 262, 263, 30, 5, 6, 7)');
    }
}
