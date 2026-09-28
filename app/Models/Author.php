<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    protected $table = 'mst_author';
    protected $primaryKey = 'author_id';
    public $timestamps = false;

    protected $fillable = [
        'author_name',
        'author_year',
        'authority_type',
        'auth_list',
        'input_date',
        'last_update',
    ];

    public function biblios()
    {
        return $this->belongsToMany(Biblio::class, 'biblio_author', 'author_id', 'biblio_id')
                    ->withPivot('level');
    }
}
