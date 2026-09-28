<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Biblio;
use App\Models\Gmd;
use App\Models\GuestBook;
use App\Models\Item;
use App\Models\Member;
use App\Models\Publisher;
use App\Models\Topic;
use Carbon\Carbon;
use Illuminate\Http\Request;

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

        $stats = [
            'total_books' => Biblio::count(),
            'total_items' => Item::count(),
            'total_members' => Member::count(),
            'total_authors' => Author::count(),
        ];

        return view('opac.index', compact('featuredBooks', 'latestBooks', 'popularTopics', 'stats'));
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
        ])->findOrFail($id);

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

        return view('opac.show', compact('book', 'relatedBooks'));
    }

    public function guestbook(Request $request)
    {
        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'id_anggota' => 'nullable|string|max:12',
                'nama' => 'required|string|max:60',
                'status' => 'required|in:Anggota,Non Anggota',
                'keperluan' => 'required|string|max:100',
            ]);

            $lastId = GuestBook::orderBy('id_bukutamu', 'desc')->value('id_bukutamu');
            $newNum = $lastId ? (intval(substr($lastId, 2)) + 1) : 1;
            $newId = 'BT' . str_pad($newNum, 6, '0', STR_PAD_LEFT);

            GuestBook::create([
                'id_bukutamu' => $newId,
                'id_kampus' => 'F1',
                'id_anggota' => $validated['id_anggota'] ?? '-',
                'nama' => $validated['nama'],
                'tgl' => Carbon::today()->toDateString(),
                'jam' => Carbon::now()->toTimeString(),
                'status' => $validated['status'],
                'keperluan' => $validated['keperluan'],
                'nomor_urut' => $newNum,
            ]);

            return back()->with('success', 'Terima kasih telah mengisi buku tamu kunjungan!');
        }

        $recentGuests = GuestBook::orderBy('tgl', 'desc')
            ->orderBy('jam', 'desc')
            ->take(15)
            ->get();

        return view('opac.guestbook', compact('recentGuests'));
    }
}
