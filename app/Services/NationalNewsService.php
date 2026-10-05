<?php

namespace App\Services;

use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class NationalNewsService
{
    /**
     * Pilihan portal berita nasional terpercaya di Indonesia
     */
    public const PORTAL_SOURCES = [
        'all' => [
            'name' => 'Kombinasi Portal Nasional (ANTARA & CNN Indonesia)',
            'description' => 'Mengambil dan memadukan berita terkini dari LKBN ANTARA dan CNN Indonesia',
        ],
        'antara' => [
            'name' => 'LKBN ANTARA News (Kantor Berita Nasional)',
            'url' => 'https://www.antaranews.com/rss/terkini.xml',
            'description' => 'Kantor Berita Resmi Republik Indonesia (LKBN Antara)',
        ],
        'cnn_tekno' => [
            'name' => 'CNN Indonesia - Teknologi & Sains',
            'url' => 'https://www.cnnindonesia.com/teknologi/rss',
            'description' => 'Portal Berita Nasional Kanal Teknologi, Internet, & Gadget',
        ],
        'cnn_nasional' => [
            'name' => 'CNN Indonesia - Berita Nasional',
            'url' => 'https://www.cnnindonesia.com/nasional/rss',
            'description' => 'Portal Berita Terkini Peristiwa dan Kebijakan Nasional',
        ],
        'republika_pendidikan' => [
            'name' => 'Republika Online - Edukasi & Khazanah',
            'url' => 'https://www.republika.co.id/rss/pendidikan',
            'description' => 'Portal Berita Nasional Kanal Pendidikan, Literasi, & Riset',
        ],
        'sindonews_edukasi' => [
            'name' => 'Sindonews - Edukasi & Sains',
            'url' => 'https://edukasi.sindonews.com/rss',
            'description' => 'Kanal Berita Pendidikan, Beasiswa, dan Riset Sains',
        ],
    ];

    /**
     * Ambil berita nasional terkini secara otomatis (dengan caching)
     */
    public static function getLatestNationalNews(bool $forceRefresh = false, int $limit = 12): array
    {
        $enabled = Setting::get('national_news_enabled', true);
        if (!$enabled) {
            return [];
        }

        $sourceKey = Setting::get('national_news_source', 'all');
        $cacheKey = 'national_news_feed_' . $sourceKey . '_' . $limit;

        if ($forceRefresh) {
            Cache::forget($cacheKey);
        }

        return Cache::remember($cacheKey, 1800, function () use ($sourceKey, $limit) {
            return self::fetchNewsFromSource($sourceKey, $limit);
        });
    }

    /**
     * Fetch feed dari portal yang dipilih
     */
    public static function fetchNewsFromSource(string $sourceKey, int $limit = 12): array
    {
        $articles = [];

        if ($sourceKey === 'all') {
            // Kombinasikan dari LKBN Antara dan CNN Indonesia
            $antara = self::fetchFromUrl('https://www.antaranews.com/rss/terkini.xml', 'LKBN ANTARA', 'Warta Nasional', 8);
            $cnnTekno = self::fetchFromUrl('https://www.cnnindonesia.com/teknologi/rss', 'CNN Indonesia', 'Teknologi', 8);
            $republika = self::fetchFromUrl('https://www.republika.co.id/rss/pendidikan', 'Republika', 'Pendidikan & Literasi', 6);

            $merged = array_merge($antara, $cnnTekno, $republika);

            // Urutkan berdasarkan waktu publikasi terbaru
            usort($merged, function ($a, $b) {
                return ($b['timestamp'] ?? 0) <=> ($a['timestamp'] ?? 0);
            });

            return array_slice($merged, 0, $limit);
        }

        $sourceConfig = self::PORTAL_SOURCES[$sourceKey] ?? self::PORTAL_SOURCES['antara'];
        $url = $sourceConfig['url'] ?? 'https://www.antaranews.com/rss/terkini.xml';
        $sourceName = $sourceConfig['name'];

        return self::fetchFromUrl($url, $sourceName, 'Nasional', $limit);
    }

    /**
     * Parser feed RSS XML dari portal berita
     */
    public static function fetchFromUrl(string $url, string $sourceName, string $defaultCategory, int $limit = 10): array
    {
        $articles = [];

        try {
            $response = Http::withoutVerifying()
                ->timeout(6)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36 (Compatible; CyberUnivLibraryBot/1.0)',
                ])
                ->get($url);

            if (!$response->successful()) {
                return [];
            }

            // Proteksi XXE: Cegah entitas jaringan eksternal via LIBXML_NONET
            $xml = @simplexml_load_string($response->body(), 'SimpleXMLElement', LIBXML_NONET | LIBXML_NOWARNING | LIBXML_NOERROR);
            if (!$xml || !isset($xml->channel->item)) {
                return [];
            }

            $items = $xml->channel->item;
            $count = 0;

            foreach ($items as $item) {
                if ($count >= $limit) {
                    break;
                }

                $title = trim(strip_tags((string)$item->title));
                $link = trim((string)$item->link);
                $rawPubDate = trim((string)$item->pubDate);
                $rawDesc = (string)$item->description;

                if (empty($title) || empty($link)) {
                    continue;
                }

                // Proteksi URL Injection: Validasi format link hanya https:// atau http:// yang valid
                if (!filter_var($link, FILTER_VALIDATE_URL) || !preg_match('/^https?:\/\//i', $link)) {
                    continue;
                }

                // Ekstrak timestamp dan format tanggal ramah
                $carbonDate = null;
                $formattedDate = 'Hari ini';
                $timestamp = time();
                if (!empty($rawPubDate)) {
                    try {
                        $carbonDate = Carbon::parse($rawPubDate)->setTimezone('Asia/Jakarta');
                        $formattedDate = $carbonDate->translatedFormat('d F Y, H:i') . ' WIB';
                        $timestamp = $carbonDate->timestamp;
                    } catch (\Throwable $e) {
                        $formattedDate = date('d F Y');
                    }
                }

                // Ekstrak URL gambar
                $imageUrl = null;

                // 1. Cek enclosure (Antara News / CNN)
                if (isset($item->enclosure) && !empty($item->enclosure['url'])) {
                    $imageUrl = (string)$item->enclosure['url'];
                }

                // 2. Cek media:content
                if (!$imageUrl) {
                    $media = $item->children('media', true);
                    if ($media && isset($media->content) && !empty($media->content->attributes()->url)) {
                        $imageUrl = (string)$media->content->attributes()->url;
                    }
                }

                // 3. Cek tag <img> di dalam description
                if (!$imageUrl && !empty($rawDesc)) {
                    if (preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $rawDesc, $matches)) {
                        $imageUrl = $matches[1];
                    }
                }

                // Sanitasi URL gambar: pastikan hanya protokol web valid
                if ($imageUrl && (!filter_var($imageUrl, FILTER_VALIDATE_URL) || !preg_match('/^https?:\/\//i', $imageUrl))) {
                    $imageUrl = null;
                }

                // Fallback gambar edukasi & teknologi bertema kampus
                if (!$imageUrl) {
                    $fallbackImages = [
                        'images/slides/slide1_campus.jpg',
                        'images/slides/slide2_library.jpg',
                        'images/slides/slide3_student_corner.jpg',
                        'images/slides/slide4_podcast.jpg',
                    ];
                    $imageUrl = $fallbackImages[$count % count($fallbackImages)];
                }

                // Bersihkan excerpt dari tag HTML
                $cleanExcerpt = trim(strip_tags($rawDesc));
                // Hapus tulisan "baca juga", "selengkapnya", dsb
                $cleanExcerpt = preg_replace('/(baca juga|selengkapnya|simak juga).*$/i', '', $cleanExcerpt);
                $cleanExcerpt = Str::limit($cleanExcerpt, 160, '...');
                if (empty($cleanExcerpt)) {
                    $cleanExcerpt = 'Liputan terkini dipublikasikan resmi melalui portal nasional ' . $sourceName . '. Klik baca artikel untuk membaca ulasan selengkapnya.';
                }

                // Kategori
                $category = !empty($item->category) ? trim((string)$item->category) : $defaultCategory;

                $articles[] = [
                    'id' => 'nat_' . md5($link),
                    'title' => $title,
                    'url' => $link,
                    'source' => $sourceName,
                    'date' => $formattedDate,
                    'timestamp' => $timestamp,
                    'image' => $imageUrl,
                    'excerpt' => $cleanExcerpt,
                    'category' => $category,
                    'is_national' => true,
                ];

                $count++;
            }
        } catch (\Throwable $e) {
            // Jangan gagalkan aplikasi jika koneksi internet terputus
        }

        return $articles;
    }

    /**
     * Gabungkan berita internal perpustakaan + berita portal nasional terkini
     */
    public static function getMergedNews(bool $forceRefresh = false): array
    {
        $internalNews = Setting::get('library_news', \App\Http\Controllers\OpacController::getDefaultLibraryNews());
        
        // Tandai berita internal
        foreach ($internalNews as &$item) {
            $item['is_national'] = false;
            $item['timestamp'] = isset($item['date']) ? @strtotime($item['date']) ?: time() : time();
        }
        unset($item);

        $nationalNews = self::getLatestNationalNews($forceRefresh, 15);

        // Jika berita nasional aktif dan tersedia, letakkan berita nasional terhangat berdampingan dengan berita kampus
        if (!empty($nationalNews)) {
            $merged = array_merge($nationalNews, $internalNews);
            return $merged;
        }

        return $internalNews;
    }
}
