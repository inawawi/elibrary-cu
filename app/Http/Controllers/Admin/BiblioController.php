<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Author;
use App\Models\Biblio;
use App\Models\CollType;
use App\Models\Gmd;
use App\Models\Item;
use App\Models\ItemStatus;
use App\Models\Location;
use App\Models\Member;
use App\Models\Place;
use App\Models\Publisher;
use App\Models\Topic;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BiblioController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $publisherId = $request->input('publisher_id');
        $gmdId = $request->input('gmd_id');
        $year = $request->input('year');
        $itemStatus = $request->input('item_status');
        $hasFile = $request->input('has_file');
        $sort = $request->input('sort', 'latest');

        $books = Biblio::with(['authors', 'publisher', 'items', 'gmd']);

        if (!empty($search)) {
            $books->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('isbn_issn', 'like', "%{$search}%")
                  ->orWhere('call_number', 'like', "%{$search}%")
                  ->orWhere('classification', 'like', "%{$search}%")
                  ->orWhereHas('authors', function ($aq) use ($search) {
                      $aq->where('author_name', 'like', "%{$search}%");
                  });
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

        if ($itemStatus === 'has_items') {
            $books->has('items');
        } elseif ($itemStatus === 'no_items') {
            $books->doesntHave('items');
        }

        if ($hasFile === 'yes') {
            $books->whereNotNull('file_att')->where('file_att', '!=', '');
        } elseif ($hasFile === 'no') {
            $books->where(function($q) {
                $q->whereNull('file_att')->orWhere('file_att', '');
            });
        }

        // Sorting
        match ($sort) {
            'oldest' => $books->orderBy('biblio_id', 'asc'),
            'title_asc' => $books->orderBy('title', 'asc'),
            'title_desc' => $books->orderBy('title', 'desc'),
            'year_desc' => $books->orderBy('publish_year', 'desc'),
            'year_asc' => $books->orderBy('publish_year', 'asc'),
            default => $books->orderBy('biblio_id', 'desc'),
        };

        $biblios = $books->paginate(15)->withQueryString();

        $publishers = Publisher::has('biblios')->orderBy('publisher_name')->get();
        $gmds = Gmd::has('biblios')->orderBy('gmd_name')->get();
        $years = Biblio::selectRaw('publish_year')
            ->whereNotNull('publish_year')
            ->whereRaw('publish_year REGEXP "^[0-9]{4}$"')
            ->distinct()
            ->orderByDesc('publish_year')
            ->take(25)
            ->pluck('publish_year');

        $activeFiltersCount = collect([$publisherId, $gmdId, $year, $itemStatus, $hasFile, $sort !== 'latest' ? $sort : null])->filter()->count();

        return view('admin.biblio.index', compact(
            'biblios', 'search', 'publishers', 'gmds', 'years',
            'publisherId', 'gmdId', 'year', 'itemStatus', 'hasFile', 'sort', 'activeFiltersCount'
        ));
    }

    public function create()
    {
        $authors = Author::orderBy('author_name')->get();
        $publishers = Publisher::orderBy('publisher_name')->get();
        $places = Place::orderBy('place_name')->get();
        $gmds = Gmd::orderBy('gmd_name')->get();
        $topics = Topic::orderBy('topic')->get();
        $locations = Location::orderBy('location_name')->get();
        $collTypes = CollType::orderBy('coll_type_name')->get();

        return view('admin.biblio.create', compact('authors', 'publishers', 'places', 'gmds', 'topics', 'locations', 'collTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:500',
            'sor' => 'nullable|string|max:200',
            'edition' => 'nullable|string|max:50',
            'isbn_issn' => 'nullable|string|max:32',
            'publisher_id' => 'nullable|integer',
            'publish_year' => 'nullable|string|max:20',
            'collation' => 'nullable|string|max:100',
            'series_title' => 'nullable|string|max:200',
            'call_number' => 'nullable|string|max:50',
            'language_id' => 'nullable|string|max:5',
            'publish_place_id' => 'nullable|integer',
            'classification' => 'nullable|string|max:40',
            'notes' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'authors' => 'nullable|array',
            'topics' => 'nullable|array',
            'initial_item_code' => 'nullable|string|max:20',
            'location_id' => 'nullable|string|max:3',
            'coll_type_id' => 'nullable|integer',
        ]);

        $imageName = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $imageName = time() . '_' . Str::slug(substr($validated['title'], 0, 30)) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/docs'), $imageName);
        }

        $biblio = Biblio::create([
            'title' => $validated['title'],
            'sor' => $validated['sor'] ?? null,
            'edition' => $validated['edition'] ?? null,
            'isbn_issn' => $validated['isbn_issn'] ?? null,
            'publisher_id' => $validated['publisher_id'] ?? null,
            'publish_year' => $validated['publish_year'] ?? null,
            'collation' => $validated['collation'] ?? null,
            'series_title' => $validated['series_title'] ?? null,
            'call_number' => $validated['call_number'] ?? null,
            'language_id' => $validated['language_id'] ?? 'id',
            'publish_place_id' => $validated['publish_place_id'] ?? null,
            'classification' => $validated['classification'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'image' => $imageName,
            'input_date' => Carbon::now(),
            'last_update' => Carbon::now(),
            'uid' => auth()->id() ?? 1,
        ]);

        if (!empty($validated['authors'])) {
            $biblio->authors()->sync($validated['authors']);
        }

        if (!empty($validated['topics'])) {
            $biblio->topics()->sync($validated['topics']);
        }

        // Add physical copy if specified
        if (!empty($validated['initial_item_code'])) {
            Item::create([
                'biblio_id' => $biblio->biblio_id,
                'item_code' => $validated['initial_item_code'],
                'call_number' => $validated['call_number'] ?? null,
                'coll_type_id' => $validated['coll_type_id'] ?? 1,
                'location_id' => $validated['location_id'] ?? '001',
                'item_status_id' => '001',
                'input_date' => Carbon::now(),
                'last_update' => Carbon::now(),
                'uid' => auth()->id() ?? 1,
            ]);
        }

        return redirect()->route('admin.biblio.index')->with('success', 'Buku "' . $biblio->title . '" berhasil ditambahkan ke katalog!');
    }

    public function edit($id)
    {
        $biblio = Biblio::with(['authors', 'topics', 'items'])->findOrFail($id);
        $authors = Author::orderBy('author_name')->get();
        $publishers = Publisher::orderBy('publisher_name')->get();
        $places = Place::orderBy('place_name')->get();
        $gmds = Gmd::orderBy('gmd_name')->get();
        $topics = Topic::orderBy('topic')->get();
        $locations = Location::orderBy('location_name')->get();
        $collTypes = CollType::orderBy('coll_type_name')->get();

        return view('admin.biblio.edit', compact('biblio', 'authors', 'publishers', 'places', 'gmds', 'topics', 'locations', 'collTypes'));
    }

    public function update(Request $request, $id)
    {
        $biblio = Biblio::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:500',
            'sor' => 'nullable|string|max:200',
            'edition' => 'nullable|string|max:50',
            'isbn_issn' => 'nullable|string|max:32',
            'publisher_id' => 'nullable|integer',
            'publish_year' => 'nullable|string|max:20',
            'collation' => 'nullable|string|max:100',
            'series_title' => 'nullable|string|max:200',
            'call_number' => 'nullable|string|max:50',
            'language_id' => 'nullable|string|max:5',
            'publish_place_id' => 'nullable|integer',
            'classification' => 'nullable|string|max:40',
            'notes' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'authors' => 'nullable|array',
            'topics' => 'nullable|array',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $imageName = time() . '_' . Str::slug(substr($validated['title'], 0, 30)) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/docs'), $imageName);
            $validated['image'] = $imageName;
        }

        $validated['last_update'] = Carbon::now();

        $biblio->update($validated);

        if (isset($validated['authors'])) {
            $biblio->authors()->sync($validated['authors']);
        }

        if (isset($validated['topics'])) {
            $biblio->topics()->sync($validated['topics']);
        }

        return redirect()->route('admin.biblio.index')->with('success', 'Data buku "' . $biblio->title . '" berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $biblio = Biblio::with('items.activeLoan')->findOrFail($id);

        // Check if any items have active loans
        $hasActiveLoans = $biblio->items->some(function ($item) {
            return $item->activeLoan !== null;
        });

        if ($hasActiveLoans) {
            return back()->with('error', 'Buku tidak dapat dihapus karena masih ada eksemplar yang sedang dipinjam!');
        }

        // Delete items
        $biblio->items()->delete();
        $biblio->authors()->detach();
        $biblio->topics()->detach();
        $biblio->delete();

        return redirect()->route('admin.biblio.index')->with('success', 'Katalog buku berhasil dihapus.');
    }

    public function manageItems($id, Request $request)
    {
        $biblio = Biblio::with(['items.location', 'items.collType', 'items.itemStatus', 'items.activeLoan.member'])->findOrFail($id);

        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'item_code' => 'required|string|max:20|unique:item,item_code',
                'location_id' => 'required|string|max:3',
                'coll_type_id' => 'required|integer',
                'price' => 'nullable|integer',
            ]);

            Item::create([
                'biblio_id' => $biblio->biblio_id,
                'item_code' => $validated['item_code'],
                'call_number' => $biblio->call_number,
                'location_id' => $validated['location_id'],
                'coll_type_id' => $validated['coll_type_id'],
                'item_status_id' => '001',
                'price' => $validated['price'] ?? 0,
                'input_date' => Carbon::now(),
                'last_update' => Carbon::now(),
                'uid' => auth()->id() ?? 1,
            ]);

            return back()->with('success', 'Eksemplar kode ' . $validated['item_code'] . ' berhasil ditambahkan!');
        }

        $locations = Location::all();
        $collTypes = CollType::all();

        return view('admin.biblio.items', compact('biblio', 'locations', 'collTypes'));
    }

    public function deleteItem($itemId)
    {
        $item = Item::with('activeLoan')->findOrFail($itemId);

        if ($item->activeLoan) {
            return back()->with('error', 'Eksemplar sedang dalam status dipinjam, tidak dapat dihapus!');
        }

        $item->delete();
        return back()->with('success', 'Eksemplar berhasil dihapus.');
    }

    public function createSkripsi()
    {
        $dosenMembers = Member::where('member_type_id', 2)->orderBy('member_name')->get();
        $authors = Author::orderBy('author_name')->get();
        $topics = Topic::orderBy('topic')->get();

        return view('admin.biblio.create_skripsi', compact('dosenMembers', 'authors', 'topics'));
    }

    public function storeSkripsi(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:500',
            'student_name' => 'required|string|max:100',
            'student_nim' => 'required|string|max:20',
            'prodi' => 'required|string|max:60',
            'pembimbing_1' => 'required|string|max:100',
            'pembimbing_2' => 'nullable|string|max:100',
            'publish_year' => 'required|string|max:4',
            'abstract' => 'nullable|string',
            'skripsi_file' => 'nullable|file|mimes:pdf,zip,rar,doc,docx|max:20480',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'call_number' => 'nullable|string|max:50',
            'topics' => 'nullable|array',
        ]);

        $gmdSkripsi = Gmd::where('gmd_code', 'SR')->orWhere('gmd_name', 'like', '%Skripsi%')->first();
        $gmdId = $gmdSkripsi ? $gmdSkripsi->gmd_id : 262;

        $publisher = Publisher::firstOrCreate(
            ['publisher_name' => 'Universitas Siber Indonesia'],
            ['input_date' => Carbon::today()->toDateString(), 'last_update' => Carbon::today()->toDateString()]
        );

        $place = Place::firstOrCreate(
            ['place_name' => 'Jakarta'],
            ['input_date' => Carbon::today()->toDateString(), 'last_update' => Carbon::today()->toDateString()]
        );

        // Upload file attachment
        $fileName = null;
        if ($request->hasFile('skripsi_file')) {
            $file = $request->file('skripsi_file');
            $fileName = 'skripsi_' . $validated['student_nim'] . '_' . time() . '.' . $file->getClientOriginalExtension();
            if (!file_exists(public_path('files/skripsi'))) {
                mkdir(public_path('files/skripsi'), 0755, true);
            }
            $file->move(public_path('files/skripsi'), $fileName);
        }

        // Upload cover
        $imageName = null;
        if ($request->hasFile('cover_image')) {
            $cfile = $request->file('cover_image');
            $imageName = 'cover_skripsi_' . time() . '.' . $cfile->getClientOriginalExtension();
            $cfile->move(public_path('images/docs'), $imageName);
        }

        $prodiCode = match($validated['prodi']) {
            'Teknologi Informasi' => 'TI',
            'Sistem Informasi' => 'SI',
            'Sistem dan Teknologi Informasi' => 'STI',
            'Bisnis Digital' => 'BD',
            'Kewirausahaan' => 'KW',
            default => 'SKR'
        };
        $callNumber = $validated['call_number'] ?: ('SKR-' . $prodiCode . '-' . $validated['publish_year'] . '-' . substr($validated['student_nim'], -4));

        $specDetail = json_encode([
            'tipe' => 'Skripsi',
            'nim' => $validated['student_nim'],
            'prodi' => $validated['prodi'],
            'pembimbing_1' => $validated['pembimbing_1'],
            'pembimbing_2' => $validated['pembimbing_2'] ?? null,
        ], JSON_UNESCAPED_UNICODE);

        $biblio = Biblio::create([
            'gmd_id' => $gmdId,
            'title' => $validated['title'],
            'sor' => $validated['student_name'] . ' (NIM: ' . $validated['student_nim'] . ') ; Pembimbing: ' . $validated['pembimbing_1'],
            'edition' => 'Skripsi',
            'publisher_id' => $publisher->publisher_id,
            'publish_year' => $validated['publish_year'],
            'collation' => 'xx, 120 hlm. : ilus. ; 30 cm',
            'series_title' => 'Skripsi Program Studi ' . $validated['prodi'],
            'call_number' => $callNumber,
            'language_id' => 'id',
            'publish_place_id' => $place->place_id,
            'classification' => '004',
            'notes' => $validated['abstract'] ?? null,
            'image' => $imageName,
            'file_att' => $fileName ? ('files/skripsi/' . $fileName) : null,
            'spec_detail_info' => $specDetail,
            'input_date' => Carbon::now(),
            'last_update' => Carbon::now(),
            'uid' => auth()->id() ?? 1,
        ]);

        // Register student as author
        $studentAuthor = Author::firstOrCreate(
            ['author_name' => $validated['student_name']],
            ['authority_type' => 'p', 'input_date' => Carbon::today()->toDateString(), 'last_update' => Carbon::today()->toDateString()]
        );
        $biblio->authors()->attach($studentAuthor->author_id, ['level' => 1]);

        // Register advisor 1 as author
        if (!empty($validated['pembimbing_1'])) {
            $adv1 = Author::firstOrCreate(
                ['author_name' => $validated['pembimbing_1']],
                ['authority_type' => 'p', 'input_date' => Carbon::today()->toDateString(), 'last_update' => Carbon::today()->toDateString()]
            );
            $biblio->authors()->attach($adv1->author_id, ['level' => 2]);
        }

        // Register advisor 2 if any
        if (!empty($validated['pembimbing_2'])) {
            $adv2 = Author::firstOrCreate(
                ['author_name' => $validated['pembimbing_2']],
                ['authority_type' => 'p', 'input_date' => Carbon::today()->toDateString(), 'last_update' => Carbon::today()->toDateString()]
            );
            $biblio->authors()->attach($adv2->author_id, ['level' => 3]);
        }

        // Create Item physical archive copy
        Item::create([
            'biblio_id' => $biblio->biblio_id,
            'item_code' => 'SKR' . str_pad($biblio->biblio_id, 5, '0', STR_PAD_LEFT),
            'call_number' => $callNumber,
            'coll_type_id' => 2, // Reference
            'location_id' => '001',
            'item_status_id' => '001',
            'input_date' => Carbon::now(),
            'last_update' => Carbon::now(),
            'uid' => auth()->id() ?? 1,
        ]);

        if (!empty($validated['topics'])) {
            $biblio->topics()->sync($validated['topics']);
        }

        return redirect()->route('admin.biblio.index')->with('success', 'Data Skripsi "' . $validated['title'] . '" karya ' . $validated['student_name'] . ' berhasil ditambahkan!');
    }

    public function createEbook()
    {
        $authors = Author::orderBy('author_name')->get();
        $publishers = Publisher::orderBy('publisher_name')->get();
        $topics = Topic::orderBy('topic')->get();

        return view('admin.biblio.create_ebook', compact('authors', 'publishers', 'topics'));
    }

    public function storeEbook(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:500',
            'author_name' => 'required|string|max:200',
            'publisher_id' => 'nullable|integer',
            'publisher_name' => 'nullable|string|max:100',
            'publish_year' => 'nullable|string|max:4',
            'isbn_issn' => 'nullable|string|max:32',
            'ebook_file' => 'nullable|file|mimes:pdf,epub|max:51200',
            'ebook_url' => 'nullable|url|max:500',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'synopsis' => 'nullable|string',
            'topics' => 'nullable|array',
            'call_number' => 'nullable|string|max:50',
        ]);

        $gmdEbook = Gmd::where('gmd_name', 'like', '%e-Book%')
            ->orWhere('gmd_name', 'like', '%Electronic%')
            ->first();
        $gmdId = $gmdEbook ? $gmdEbook->gmd_id : 30;

        // Publisher
        $pubId = $validated['publisher_id'] ?? null;
        if (!$pubId && !empty($validated['publisher_name'])) {
            $p = Publisher::firstOrCreate(
                ['publisher_name' => $validated['publisher_name']],
                ['input_date' => Carbon::today()->toDateString(), 'last_update' => Carbon::today()->toDateString()]
            );
            $pubId = $p->publisher_id;
        }

        // Upload file
        $fileName = null;
        if ($request->hasFile('ebook_file')) {
            $file = $request->file('ebook_file');
            $fileName = 'ebook_' . time() . '_' . Str::slug(substr($validated['title'], 0, 30)) . '.' . $file->getClientOriginalExtension();
            if (!file_exists(public_path('files/ebooks'))) {
                mkdir(public_path('files/ebooks'), 0755, true);
            }
            $file->move(public_path('files/ebooks'), $fileName);
        }

        // Upload cover
        $imageName = null;
        if ($request->hasFile('cover_image')) {
            $cfile = $request->file('cover_image');
            $imageName = 'cover_ebook_' . time() . '.' . $cfile->getClientOriginalExtension();
            $cfile->move(public_path('images/docs'), $imageName);
        }

        $specDetail = json_encode([
            'tipe' => 'e-Book',
            'url' => $validated['ebook_url'] ?? null,
            'format' => $fileName ? pathinfo($fileName, PATHINFO_EXTENSION) : 'Online Link',
        ], JSON_UNESCAPED_UNICODE);

        $callNumber = $validated['call_number'] ?: ('EB-' . ($validated['publish_year'] ?? date('Y')) . '-' . rand(1000, 9999));

        $biblio = Biblio::create([
            'gmd_id' => $gmdId,
            'title' => $validated['title'],
            'sor' => $validated['author_name'],
            'edition' => 'Edisi Digital (e-Book)',
            'isbn_issn' => $validated['isbn_issn'] ?? null,
            'publisher_id' => $pubId,
            'publish_year' => $validated['publish_year'] ?? date('Y'),
            'call_number' => $callNumber,
            'language_id' => 'id',
            'notes' => $validated['synopsis'] ?? null,
            'image' => $imageName,
            'file_att' => $fileName ? ('files/ebooks/' . $fileName) : ($validated['ebook_url'] ?? null),
            'spec_detail_info' => $specDetail,
            'input_date' => Carbon::now(),
            'last_update' => Carbon::now(),
            'uid' => auth()->id() ?? 1,
        ]);

        // Register author
        $author = Author::firstOrCreate(
            ['author_name' => $validated['author_name']],
            ['authority_type' => 'p', 'input_date' => Carbon::today()->toDateString(), 'last_update' => Carbon::today()->toDateString()]
        );
        $biblio->authors()->attach($author->author_id, ['level' => 1]);

        if (!empty($validated['topics'])) {
            $biblio->topics()->sync($validated['topics']);
        }

        return redirect()->route('admin.biblio.index')->with('success', 'Data e-Book "' . $validated['title'] . '" berhasil ditambahkan ke katalog digital!');
    }
}
