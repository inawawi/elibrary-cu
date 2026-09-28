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

    public function biblios()
    {
        return $this->hasMany(Biblio::class, 'gmd_id');
    }
}
