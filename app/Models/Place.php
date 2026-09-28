<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    protected $table = 'mst_place';
    protected $primaryKey = 'place_id';
    public $timestamps = false;

    protected $fillable = [
        'place_name',
        'input_date',
        'last_update',
    ];

    public function biblios()
    {
        return $this->hasMany(Biblio::class, 'publish_place_id');
    }
}
