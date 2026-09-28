<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Publisher extends Model
{
    protected $table = 'mst_publisher';
    protected $primaryKey = 'publisher_id';
    public $timestamps = false;

    protected $fillable = [
        'publisher_name',
        'input_date',
        'last_update',
    ];

    public function biblios()
    {
        return $this->hasMany(Biblio::class, 'publisher_id');
    }
}
