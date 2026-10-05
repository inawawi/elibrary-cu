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

    /**
     * Opsi subjek standar sesuai ketentuan kurikulum & reviewer
     */
    public const REVIEWER_SUBJECTS = [
        'Sistem Informasi',
        'Sistem dan Teknologi Informasi',
        'Teknologi Informasi',
        'Bisnis Digital',
        'Kewirausahaan',
        'Metodologi Penelitian',
        'Agama',
        'Ekonomi dan Keuangan',
        'Pancasila dan Kewarganegaraan',
    ];

    /**
     * Rekomendasi subjek otomatis berdasarkan judul pustaka/skripsi dan prodi
     */
    public static function suggestSubjectsFromTitle(string $title, ?string $prodi = null): array
    {
        $suggested = [];
        $lower = strtolower($title);

        $map = [
            'Sistem Informasi' => ['sistem informasi', 'database', 'basis data', 'aplikasi web', 'informasi', 'sim', 'erp', 'crm'],
            'Teknologi Informasi' => ['teknologi informasi', 'jaringan', 'network', 'keamanan siber', 'cyber security', 'cloud', 'iot', 'server', 'komputer', 'machine learning', 'deep learning'],
            'Sistem dan Teknologi Informasi' => ['sistem dan teknologi informasi'],
            'Bisnis Digital' => ['bisnis digital', 'e-commerce', 'ecommerce', 'marketplace', 'digital marketing', 'fintech', 'startup', 'sosial media', 'e-wallet', 'qris'],
            'Kewirausahaan' => ['kewirausahaan', 'wirausaha', 'umkm', 'usaha kecil', 'entrepreneur', 'strategi bisnis'],
            'Ekonomi dan Keuangan' => ['ekonomi', 'keuangan', 'financial', 'bank', 'bri', 'brimo', 'kredit', 'kur', 'akuntansi', 'laba', 'profit', 'investasi', 'saham', 'pajak'],
            'Metodologi Penelitian' => ['metode', 'analisis', 'pengaruh', 'evaluasi', 'perancangan', 'implementasi', 'algoritma', 'model', 'klasifikasi', 'usability', 'kualitatif', 'kuantitatif'],
            'Agama' => ['agama', 'islam', 'syariah', 'fiqih', 'fikih', 'zakat', 'wakaf', 'dakwah', 'santri'],
            'Pancasila dan Kewarganegaraan' => ['pancasila', 'kewarganegaraan', 'kebangsaan', 'nasionalisme', 'hukum', 'kebijakan publik', 'demokrasi'],
        ];

        // 1. Cek keyword pada judul
        foreach ($map as $subj => $keywords) {
            foreach ($keywords as $kw) {
                if (str_contains($lower, $kw)) {
                    $suggested[] = $subj;
                    break;
                }
            }
        }

        // 2. Jika ada prodi, cocokkan dengan daftar subjek
        if (!empty($prodi)) {
            foreach (self::REVIEWER_SUBJECTS as $subj) {
                if (str_contains(strtolower($prodi), strtolower($subj)) || str_contains(strtolower($subj), strtolower($prodi))) {
                    $suggested[] = $subj;
                }
            }
        }

        return array_values(array_unique($suggested));
    }
}


