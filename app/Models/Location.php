<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $table = 'mst_location';
    protected $primaryKey = 'location_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'location_id',
        'location_name',
        'input_date',
        'last_update',
    ];

    public function items()
    {
        return $this->hasMany(Item::class, 'location_id', 'location_id');
    }
}
