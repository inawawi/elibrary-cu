<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BiblioFile extends Model
{
    protected $table = 'files';
    protected $primaryKey = 'file_id';
    public $timestamps = false;

    protected $fillable = [
        'file_title',
        'file_name',
        'file_url',
        'file_dir',
        'mime_type',
        'file_desc',
        'uploader_id',
        'input_date',
        'last_update',
    ];

    public function getUrlAttribute(): ?string
    {
        if (!empty($this->file_url)) {
            $u = trim($this->file_url);
            if (Str::startsWith($u, ['http://', 'https://'])) {
                return $u;
            }
        }

        if (!empty($this->file_name)) {
            $fn = trim($this->file_name);
            if (Str::startsWith($fn, ['http://', 'https://'])) {
                return $fn;
            }
            if (!empty($this->file_dir)) {
                return asset($this->file_dir . '/' . $fn);
            }
            return asset('files/' . $fn);
        }

        return null;
    }
}
