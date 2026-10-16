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

    protected $attributes = [
        'authority_type' => 'p',
        'auth_list' => '',
    ];

    protected static function booted()
    {
        static::creating(function ($author) {
            if ($author->auth_list === null) {
                $author->auth_list = '';
            }
            if (empty($author->authority_type)) {
                $author->authority_type = 'p';
            }
            if (empty($author->input_date)) {
                $author->input_date = now()->toDateString();
            }
            if (empty($author->last_update)) {
                $author->last_update = now()->toDateString();
            }
        });
    }

    public function biblios()
    {
        return $this->belongsToMany(Biblio::class, 'biblio_author', 'author_id', 'biblio_id')
                    ->withPivot('level');
    }
}
