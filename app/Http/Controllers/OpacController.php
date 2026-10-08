<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Biblio;
use App\Models\Gmd;
use App\Models\GuestBook;
use App\Models\Item;
use App\Models\Member;
use App\Models\Publisher;
use App\Models\Reserve;
use App\Models\Setting;
use App\Models\Topic;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OpacController extends Controller
{
    public function index(Request $request)
    {
        $featuredBooks = Biblio::with(['authors', 'publisher'])
            ->where('opac_hide', 0)
            ->where(function ($query) {
                $query->where('promoted', 1)
                      ->orWhereNotNull('image');
            })
            ->whereNotNull('image')
            ->orderBy('biblio_id', 'desc')
            ->take(8)
            ->get();

        if ($featuredBooks->isEmpty()) {
            $featuredBooks = Biblio::with(['authors', 'publisher'])
                ->where('opac_hide', 0)
                ->orderBy('biblio_id', 'desc')
                ->take(8)
                ->get();
        }

        $latestBooks = Biblio::with(['authors', 'publisher'])
            ->where('opac_hide', 0)
            ->orderBy('biblio_id', 'desc')
            ->take(12)
            ->get();

        $popularTopics = Topic::has('biblios')
            ->withCount('biblios')
            ->orderBy('biblios_count', 'desc')
            ->take(8)
            ->get();

        $gmdSkripsiIds = Gmd::where('gmd_name', 'like', '%skripsi%')->orWhere('gmd_name', 'like', '%tugas akhir%')->pluck('gmd_id')->toArray() ?: [262];
        $gmdJurnalIds = Gmd::where('gmd_name', 'like', '%jurnal%')->pluck('gmd_id')->toArray() ?: [263];
        $gmdEbookIds = Gmd::where('gmd_name', 'like', '%electronic%')->orWhere('gmd_name', 'like', '%ebook%')->pluck('gmd_id')->toArray() ?: [30];

        $stats = [
            'total_books' => Biblio::whereNotIn('gmd_id', array_merge($gmdSkripsiIds, $gmdJurnalIds, $gmdEbookIds))->count(),
            'total_ebooks' => Biblio::whereIn('gmd_id', $gmdEbookIds)->count(),
            'total_jurnals' => Biblio::whereIn('gmd_id', $gmdJurnalIds)->count(),
            'total_skripsi' => Biblio::whereIn('gmd_id', $gmdSkripsiIds)->count(),
            // Preserved for internal/admin usage
            'total_items' => Item::count(),
            'total_members' => Member::count(),
            'total_authors' => Author::count(),
            'gmd_ids' => [
                'skripsi' => $gmdSkripsiIds[0] ?? 262,
                'jurnal' => $gmdJurnalIds[0] ?? 263,
                'ebook' => $gmdEbookIds[0] ?? 30,
            ],
        ];

        $newsArticles = self::getNewsArticles();

        $defaultSlides = \App\Http\Controllers\Admin\SettingController::getDefaultSlides();
        $storedSlides = Setting::get('hero_slides', $defaultSlides);
        $heroSlides = collect($storedSlides)
            ->where('is_active', 1)
            ->map(function ($s) {
                return [
                    'img' => str_starts_with($s['image'], 'http') ? $s['image'] : asset($s['image']),
                    'tag' => $s['tag'],
                    'title' => $s['title'],
                    'desc' => $s['desc'],
                ];
            })
            ->values()
            ->all();

        if (empty($heroSlides)) {
            $heroSlides = collect($defaultSlides)->map(function ($s) {
                return [
                    'img' => asset($s['image']),
                    'tag' => $s['tag'],
                    'title' => $s['title'],
                    'desc' => $s['desc'],
                ];
            })->values()->all();
        }

        return view('opac.index', compact('featuredBooks', 'latestBooks', 'popularTopics', 'stats', 'newsArticles', 'heroSlides'));
    }

    public function search(Request $request)
    {
        $query = $request->input('q');
        $topicId = $request->input('topic');
        $publisherId = $request->input('publisher');
        $gmdId = $request->input('gmd');
        $year = $request->input('year');
        $sort = $request->input('sort', 'newest');

        $books = Biblio::with(['authors', 'publisher', 'gmd', 'items'])
            ->where('opac_hide', 0);

        if (!empty($query)) {
            $books->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('isbn_issn', 'like', "%{$query}%")
                  ->orWhere('call_number', 'like', "%{$query}%")
                  ->orWhere('notes', 'like', "%{$query}%")
                  ->orWhere('sor', 'like', "%{$query}%")
                  ->orWhereHas('authors', function ($aq) use ($query) {
                      $aq->where('author_name', 'like', "%{$query}%");
                  });
            });
        }

        if (!empty($topicId)) {
            $books->whereHas('topics', function ($tq) use ($topicId) {
                $tq->where('mst_topic.topic_id', $topicId);
            });
        }

        if (!empty($publisherId)) {
            $books->where('publisher_id', $publisherId);
        }

        if (!empty($gmdId)) {
            $books->where('gmd_id', $gmdId);
        }

        if (!empty($year)) {
            $books->where('publish_year', 'like', "%{$year}%");
        }

        switch ($sort) {
            case 'title_asc':
                $books->orderBy('title', 'asc');
                break;
            case 'title_desc':
                $books->orderBy('title', 'desc');
                break;
            case 'year_desc':
                $books->orderBy('publish_year', 'desc');
                break;
            case 'oldest':
                $books->orderBy('biblio_id', 'asc');
                break;
            case 'newest':
            default:
                $books->orderBy('biblio_id', 'desc');
                break;
        }

        $results = $books->paginate(16)->withQueryString();

        $topics = Topic::has('biblios')->orderBy('topic')->get();
        $publishers = Publisher::has('biblios')->orderBy('publisher_name')->take(50)->get();
        $gmds = Gmd::has('biblios')->orderBy('gmd_name')->get();

        return view('opac.search', compact('results', 'query', 'topics', 'publishers', 'gmds', 'sort', 'topicId', 'publisherId', 'gmdId', 'year'));
    }

    public function show($id)
    {
        $book = Biblio::with([
            'authors',
            'publisher',
            'place',
            'gmd',
            'topics',
            'items.location',
            'items.collType',
            'items.itemStatus',
            'items.activeLoan.member',
            'items.reserve.member',
        ])->findOrFail($id);

        $member = Auth::guard('member')->user();
        $userReserve = null;
        if ($member) {
            $userReserve = Reserve::where('member_id', $member->member_id)
                ->where('biblio_id', $book->biblio_id)
                ->first();
        }

        $availableItems = $book->items->filter(function ($item) {
            return !$item->activeLoan && !$item->reserve;
        });

        // Related books (same author or same topic)
        $topicIds = $book->topics->pluck('topic_id')->toArray();
        $authorIds = $book->authors->pluck('author_id')->toArray();

        $relatedBooks = Biblio::with(['authors', 'publisher'])
            ->where('biblio_id', '!=', $book->biblio_id)
            ->where('opac_hide', 0)
            ->where(function ($q) use ($topicIds, $authorIds) {
                if (!empty($topicIds)) {
                    $q->orWhereHas('topics', function ($tq) use ($topicIds) {
                        $tq->whereIn('mst_topic.topic_id', $topicIds);
                    });
                }
                if (!empty($authorIds)) {
                    $q->orWhereHas('authors', function ($aq) use ($authorIds) {
                        $aq->whereIn('mst_author.author_id', $authorIds);
                    });
                }
            })
            ->take(4)
            ->get();

        return view('opac.show', compact('book', 'relatedBooks', 'userReserve', 'availableItems'));
    }

    public function guestbook(Request $request)
    {
        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'id_anggota' => 'nullable|string|max:12',
                'nama' => 'required|string|max:60',
                'status' => 'required|in:Anggota,Non Anggota',
                'prodi' => 'nullable|string|max:60',
                'tujuan' => 'required|string|max:50',
                'keperluan' => 'required|string|max:100',
                'keperluan_lainnya' => 'nullable|string|max:100',
            ]);

            $finalKeperluan = $validated['keperluan'];
            if ($finalKeperluan === 'Lainnya' && !empty($validated['keperluan_lainnya'])) {
                $finalKeperluan = 'Lainnya: ' . $validated['keperluan_lainnya'];
            }

            // Ambil nomor urut tertinggi secara numerik agar tidak tertipu sorting alfabetis ('V' > 'B')
            $maxNomorUrut = (int) (GuestBook::selectRaw('MAX(CAST(nomor_urut AS UNSIGNED)) as max_no')->value('max_no') ?? 0);
            $maxBtNum = (int) (GuestBook::where('id_bukutamu', 'like', 'BT%')
                ->selectRaw('MAX(CAST(SUBSTRING(id_bukutamu, 3) AS UNSIGNED)) as max_bt')
                ->value('max_bt') ?? 0);
            $maxVNum = (int) (GuestBook::where('id_bukutamu', 'like', 'V%')
                ->selectRaw('MAX(CAST(SUBSTRING(id_bukutamu, 2) AS UNSIGNED)) as max_v')
                ->value('max_v') ?? 0);

            $newNum = max($maxNomorUrut, $maxBtNum, $maxVNum) + 1;

            $inserted = false;
            for ($attempt = 0; $attempt < 10; $attempt++) {
                $newId = 'BT' . str_pad($newNum, 6, '0', STR_PAD_LEFT);
                if (GuestBook::where('id_bukutamu', $newId)->exists()) {
                    $newNum++;
                    continue;
                }

                try {
                    GuestBook::create([
                        'id_bukutamu' => $newId,
                        'id_kampus' => 'F1',
                        'id_anggota' => $validated['id_anggota'] ?? '-',
                        'nama' => $validated['nama'],
                        'tgl' => Carbon::today()->toDateString(),
                        'jam' => Carbon::now()->toTimeString(),
                        'status' => $validated['status'],
                        'prodi' => $validated['prodi'] ?? null,
                        'tujuan' => $validated['tujuan'],
                        'keperluan' => $finalKeperluan,
                        'nomor_urut' => $newNum,
                    ]);
                    $inserted = true;
                    break;
                } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
                    $newNum++;
                } catch (\Exception $e) {
                    if (str_contains($e->getMessage(), 'Duplicate entry') || str_contains($e->getMessage(), '1062')) {
                        $newNum++;
                    } else {
                        throw $e;
                    }
                }
            }

            if (!$inserted) {
                return back()->with('error', 'Gagal menyimpan buku tamu, silakan coba lagi.');
            }

            return back()->with('success', 'Terima kasih telah mengisi buku tamu kunjungan!');
        }

        $recentGuests = GuestBook::orderBy('tgl', 'desc')
            ->orderBy('jam', 'desc')
            ->take(15)
            ->get();

        return view('opac.guestbook', compact('recentGuests'));
    }

    public function news(Request $request)
    {
        $newsArticles = self::getNewsArticles();
        $tab = $request->query('tab', 'all');

        $nationalArticles = array_values(array_filter($newsArticles, fn($item) => !empty($item['is_national'])));
        $internalArticles = array_values(array_filter($newsArticles, fn($item) => empty($item['is_national'])));

        $filteredArticles = match($tab) {
            'national' => $nationalArticles,
            'internal' => $internalArticles,
            default => $newsArticles
        };

        $counts = [
            'all' => count($newsArticles),
            'national' => count($nationalArticles),
            'internal' => count($internalArticles),
        ];

        return view('opac.news', compact('newsArticles', 'filteredArticles', 'tab', 'counts'));
    }

    public static function getNewsArticles(bool $forceRefresh = false): array
    {
        return \App\Services\NationalNewsService::getMergedNews($forceRefresh);
    }

    public static function getDefaultLibraryNews(): array
    {
        return [
            [
                'id' => 1,
                'title' => 'Perpustakaan Universitas Siber Indonesia Perluas Akses Koleksi Digital & Layanan Sirkulasi Modern',
                'source' => 'Cyber University News',
                'url' => 'https://cyber-univ.ac.id',
                'date' => '24 September 2026',
                'image' => 'images/slides/slide2_library.jpg',
                'excerpt' => 'Universitas Siber Indonesia resmi meluncurkan pembaruan sistem informasi perpustakaan berbasis teknologi web modern dengan integrasi katalog digital dan area mandiri anggota.',
                'category' => 'Layanan & Inovasi',
            ],
            [
                'id' => 2,
                'title' => 'Cyber University Resmikan Student Corner & Podcast Studio Kreatif di Perpustakaan',
                'source' => 'Portal Berita Kampus',
                'url' => 'https://cyber-univ.ac.id',
                'date' => '18 September 2026',
                'image' => 'images/slides/slide4_podcast.jpg',
                'excerpt' => 'Fasilitas Student Corner dan Podcast Studio kini hadir di Perpustakaan Cyber University untuk mendukung kreativitas, diskusi kolaboratif, serta produksi konten literasi mahasiswa.',
                'category' => 'Fasilitas Kampus',
            ],
            [
                'id' => 3,
                'title' => 'Tingkatkan Mutu Akademik, Perpustakaan Cyber University Tambah Ribuan Koleksi Buku & e-Book Terkini',
                'source' => 'Media Pendidikan Online',
                'url' => 'https://cyber-univ.ac.id',
                'date' => '10 September 2026',
                'image' => 'images/slides/slide1_campus.jpg',
                'excerpt' => 'Komitmen penguatan literasi ilmiah diwujudkan melalui penambahan ribuan judul literatur, e-book, dan repositori skripsi untuk lima program studi unggulan.',
                'category' => 'Akademik & Riset',
            ],
            [
                'id' => 4,
                'title' => 'Kunjungan Studi Literasi & Kolaborasi Riset Mahasiswa di Student Lounge Perpustakaan',
                'source' => 'Info Kampus Nasional',
                'url' => 'https://cyber-univ.ac.id',
                'date' => '02 September 2026',
                'image' => 'images/slides/slide3_student_corner.jpg',
                'excerpt' => 'Antusiasme mahasiswa memanfaatkan area Student Corner perpustakaan untuk bedah jurnal ilmiah, perancangan proposal skripsi, dan kegiatan belajar kelompok.',
                'category' => 'Aktivitas Mahasiswa',
            ],
        ];
    }
}
