<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    protected $table = 'mst_topic';
    protected $primaryKey = 'topic_id';
    public $timestamps = false;

    protected $fillable = [
        'topic',
        'topic_type',
        'auth_list',
        'classification',
        'input_date',
        'last_update',
    ];

    public function biblios()
    {
        return $this->belongsToMany(Biblio::class, 'biblio_topic', 'topic_id', 'biblio_id')
                    ->withPivot('level');
    }
}
