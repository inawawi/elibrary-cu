<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Biblio extends Model
{
    protected $table = 'biblio';
    protected $primaryKey = 'biblio_id';
    public $timestamps = false;

    protected $fillable = [
        'gmd_id',
        'title',
        'sor',
        'edition',
        'isbn_issn',
        'publisher_id',
        'publish_year',
        'collation',
        'series_title',
        'call_number',
        'language_id',
        'source',
        'publish_place_id',
        'classification',
        'notes',
        'image',
        'file_att',
        'opac_hide',
        'promoted',
        'labels',
        'frequency_id',
        'spec_detail_info',
        'content_type_id',
        'media_type_id',
        'carrier_type_id',
        'input_date',
        'last_update',
        'uid',
    ];

    public function items()
    {
        return $this->hasMany(Item::class, 'biblio_id');
    }

    public function publisher()
    {
        return $this->belongsTo(Publisher::class, 'publisher_id');
    }

    public function place()
    {
        return $this->belongsTo(Place::class, 'publish_place_id');
    }

    public function gmd()
    {
        return $this->belongsTo(Gmd::class, 'gmd_id');
    }

    public function authors()
    {
        return $this->belongsToMany(Author::class, 'biblio_author', 'biblio_id', 'author_id')
                    ->withPivot('level')
                    ->orderBy('biblio_author.level', 'asc');
    }

    public function topics()
    {
        return $this->belongsToMany(Topic::class, 'biblio_topic', 'biblio_id', 'topic_id')
                    ->withPivot('level');
    }

    public function getAuthorNamesAttribute(): string
    {
        if ($this->authors->isNotEmpty()) {
            return $this->authors->pluck('author_name')->implode(', ');
        }
        return $this->sor ?: 'Pengarang Tidak Diketahui';
    }

    public function getCoverUrlAttribute(): string
    {
        if (!empty($this->image) && file_exists(public_path('images/docs/' . $this->image))) {
            return asset('images/docs/' . $this->image);
        }
        return asset('images/default_cover.svg');
    }

    public function hasRealCover(): bool
    {
        return !empty($this->image) && file_exists(public_path('images/docs/' . $this->image));
    }

    public function getTotalCopiesAttribute(): int
    {
        return $this->items()->count();
    }

    public function getAvailableCopiesAttribute(): int
    {
        // Items that are not in active loans
        return $this->items()->whereDoesntHave('activeLoan')->count();
    }
}
