<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    protected $table = 'mst_language';
    protected $primaryKey = 'language_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'language_id',
        'language_name',
        'input_date',
        'last_update',
    ];
}
