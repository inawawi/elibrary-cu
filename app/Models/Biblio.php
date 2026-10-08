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

    public function frequency()
    {
        return $this->belongsTo(Frequency::class, 'frequency_id');
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

    public function setFrequencyIdAttribute($value)
    {
        $this->attributes['frequency_id'] = !empty($value) ? (int)$value : 0;
    }

    public function getSpineLabelComponentsAttribute(): array
    {
        $header = 'ELIBRARY CYBER UNIVERSITY';
        $callNumber = trim($this->call_number ?? '');

        // Jika nomor panggil diisi, gunakan pemisahan kata/token nomor panggil tersebut
        if (!empty($callNumber)) {
            $parts = preg_split('/\s+/', $callNumber);
            return [
                'header' => $header,
                'lines' => $parts,
                'full_call_number' => $callNumber,
                'classification' => $parts[0] ?? '000',
                'author_code' => $parts[1] ?? '',
                'title_code' => $parts[2] ?? '',
            ];
        }

        // Fallback jika call_number kosong: buat dari klasifikasi, 3 huruf pengarang, 1 huruf judul
        $classification = !empty($this->classification) ? trim($this->classification) : '000';
        
        // 3 huruf pertama nama pengarang utama (uppercase)
        $author = $this->authors->first();
        $authorName = $author ? trim($author->author_name) : trim($this->sor ?? '');
        $cleanAuthor = preg_replace('/^(dr\.|prof\.|drs\.|ir\.|h\.|hj\.)\s+/i', '', $authorName);
        $authorCode = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $cleanAuthor), 0, 3));
        if (empty($authorCode)) {
            $authorCode = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $this->title), 0, 3));
        }

        // 1 huruf pertama judul buku (lowercase, ignore kata sandang)
        $cleanTitle = preg_replace('/^(the|a|an)\s+/i', '', trim($this->title));
        $titleCode = strtolower(substr(preg_replace('/[^a-zA-Z0-9]/', '', $cleanTitle), 0, 1));

        $lines = array_values(array_filter([$classification, $authorCode, $titleCode]));

        return [
            'header' => $header,
            'lines' => $lines,
            'classification' => $classification,
            'author_code' => $authorCode,
            'title_code' => $titleCode,
            'full_call_number' => "{$classification} {$authorCode} {$titleCode}",
        ];
    }
}
