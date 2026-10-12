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

    public function reserves()
    {
        return $this->hasMany(Reserve::class, 'biblio_id');
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

    public function files()
    {
        return $this->belongsToMany(
            BiblioFile::class,
            'biblio_attachment',
            'biblio_id',
            'file_id'
        )->withPivot(['access_type', 'access_limit', 'placement']);
    }

    public function getDigitalUrlAttribute(): ?string
    {
        // 1. Direct URL in file_att column
        if (!empty($this->file_att)) {
            $val = trim($this->file_att);
            if (\Illuminate\Support\Str::startsWith($val, ['http://', 'https://'])) {
                return $val;
            }
        }

        // 2. Attached file / OJS link in files table via biblio_attachment
        if ($this->relationLoaded('files') || $this->files()->exists()) {
            $attachedFile = $this->files->first();
            if ($attachedFile && !empty($attachedFile->url)) {
                return $attachedFile->url;
            }
        }

        // 3. Fallback: if file_att is a relative file path
        if (!empty($this->file_att)) {
            return asset(trim($this->file_att));
        }

        return null;
    }

    public function getJournalLinksAttribute(): array
    {
        $links = [];

        // 1. Dari kolom file_att
        if (!empty($this->file_att)) {
            $val = trim($this->file_att);
            $url = \Illuminate\Support\Str::startsWith($val, ['http://', 'https://']) ? $val : asset($val);
            $links[] = [
                'title' => 'Buka / Akses Jurnal Daring (Online)',
                'url' => $url,
            ];
        }

        // 2. Dari relasi files (biblio_attachment)
        $attachedFiles = $this->relationLoaded('files') ? $this->files : $this->files()->get();
        foreach ($attachedFiles as $file) {
            $url = $file->url;
            if ($url && !collect($links)->pluck('url')->contains($url)) {
                $links[] = [
                    'title' => !empty($file->file_title) && !in_array(strtolower($file->file_title), ['file', 'attachment'])
                        ? $file->file_title
                        : 'Buka / Akses Jurnal Daring (Online)',
                    'url' => $url,
                ];
            }
        }

        return $links;
    }

    public function isJournal(): bool
    {
        $gmdId = (int)($this->gmd_id ?? 0);
        $gmdName = strtolower($this->gmd?->gmd_name ?? '');
        return $gmdId === 263 || str_contains($gmdName, 'jurnal') || str_contains($gmdName, 'journal');
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
        // Items that are not in active loans and not reserved
        return $this->items()->whereDoesntHave('activeLoan')->whereDoesntHave('reserve')->count();
    }

    public function setFrequencyIdAttribute($value)
    {
        $this->attributes['frequency_id'] = !empty($value) ? (int)$value : 0;
    }

    public function getSpineLabelComponentsAttribute(): array
    {
        $header = 'ELIBRARY CYBER UNIVERSITY';
        $callNumber = trim($this->call_number ?? '');

        $gmdId = (int)($this->gmd_id ?? 0);
        $gmdName = strtolower($this->gmd?->gmd_name ?? '');
        $isJurnal = $gmdId === 263 || str_contains($gmdName, 'jurnal') || str_contains($gmdName, 'serial') || str_contains($gmdName, 'periodical');
        $isSkripsi = $gmdId === 262 || str_contains($gmdName, 'skripsi');

        $classification = !empty($this->classification) ? trim($this->classification) : '000';

        // 3 huruf pertama nama pengarang utama (uppercase) atau singkatan judul untuk jurnal
        $author = $this->authors->first();
        $authorName = $author ? trim($author->author_name) : trim($this->sor ?? '');
        $cleanAuthor = preg_replace('/^(dr\.|prof\.|drs\.|ir\.|h\.|hj\.)\s+/i', '', $authorName);
        $authorCode = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $cleanAuthor), 0, 3));
        if (empty($authorCode) && !empty($this->title)) {
            $authorCode = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $this->title), 0, 3));
        }
        if (empty($authorCode)) {
            $authorCode = $isJurnal ? 'JUR' : ($isSkripsi ? 'SKR' : 'XXX');
        }

        // 1 huruf pertama judul buku (lowercase, ignore kata sandang)
        $cleanTitle = preg_replace('/^(the|a|an)\s+/i', '', trim($this->title ?? ''));
        $titleCode = strtolower(substr(preg_replace('/[^a-zA-Z0-9]/', '', $cleanTitle), 0, 1)) ?: 'x';

        // Jika nomor panggil diisi
        if (!empty($callNumber)) {
            $parts = preg_split('/\s+/', $callNumber);

            // Jika nomor panggil hanya 1 kata berupa angka klasifikasi DDC (misal hanya '658' atau '005.1')
            if (count($parts) === 1 && preg_match('/^[0-9.]+$/', $parts[0])) {
                $classNum = $parts[0];
                if ($isJurnal) {
                    $lines = ['R', $classNum, $authorCode];
                } elseif ($isSkripsi) {
                    $lines = ['SKR', $classNum, $authorCode];
                } else {
                    $lines = [$classNum, $authorCode, $titleCode];
                }
                return [
                    'header' => $header,
                    'lines' => $lines,
                    'full_call_number' => implode(' ', $lines),
                    'classification' => $classNum,
                    'author_code' => $authorCode,
                    'title_code' => $titleCode,
                ];
            }

            // Jika hanya 'R 658' (kurang kode judul/pengarang)
            if (count($parts) === 2 && strtoupper($parts[0]) === 'R' && preg_match('/^[0-9.]+$/', $parts[1])) {
                $lines = ['R', $parts[1], $authorCode];
                return [
                    'header' => $header,
                    'lines' => $lines,
                    'full_call_number' => implode(' ', $lines),
                    'classification' => $parts[1],
                    'author_code' => $authorCode,
                    'title_code' => $titleCode,
                ];
            }

            return [
                'header' => $header,
                'lines' => $parts,
                'full_call_number' => $callNumber,
                'classification' => $parts[0] ?? $classification,
                'author_code' => $parts[1] ?? $authorCode,
                'title_code' => $parts[2] ?? $titleCode,
            ];
        }

        // Fallback jika call_number kosong: buat otomatis sesuai standar perpustakaan
        if ($isJurnal) {
            $lines = ['R', $classification, $authorCode];
        } elseif ($isSkripsi) {
            $lines = ['SKR', $classification, $authorCode];
        } else {
            $lines = array_values(array_filter([$classification, $authorCode, $titleCode]));
        }

        return [
            'header' => $header,
            'lines' => $lines,
            'classification' => $classification,
            'author_code' => $authorCode,
            'title_code' => $titleCode,
            'full_call_number' => implode(' ', $lines),
        ];
    }
}
