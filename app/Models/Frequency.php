<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Frequency extends Model
{
    protected $table = 'mst_frequency';
    protected $primaryKey = 'frequency_id';
    public $timestamps = false;

    protected $fillable = [
        'frequency',
        'language_prefix',
        'time_increment',
        'time_unit',
        'input_date',
        'last_update',
    ];

    public function biblios()
    {
        return $this->hasMany(Biblio::class, 'frequency_id');
    }
}
