<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Author;
use App\Models\BebasPustaka;
use App\Models\Biblio;
use App\Models\CollType;
use App\Models\Frequency;
use App\Models\Gmd;
use App\Models\Item;
use App\Models\ItemStatus;
use App\Models\Location;
use App\Models\Member;
use App\Models\Place;
use App\Models\Publisher;
use App\Models\Topic;
use App\Services\BarcodeService;
use Carbon\Carbon;
use App\Services\DataExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $gmds = Gmd::curated()->get();
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
        $gmds = Gmd::curated()->get();
        $topics = Topic::orderBy('topic')->get();
        $locations = Location::orderBy('location_name')->get();
        $frequencies = Frequency::orderBy('frequency_id')->get();

        $nextItemCode = Item::generateNextCode('B');
        $reviewerTopics = Topic::REVIEWER_SUBJECTS;

        return view('admin.biblio.create', compact('authors', 'publishers', 'places', 'gmds', 'topics', 'locations', 'frequencies', 'nextItemCode', 'reviewerTopics'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:500',
            'sor' => 'nullable|string|max:200',
            'edition' => 'nullable|string|max:50',
            'isbn_issn' => 'nullable|string|max:32',
            'publisher_id' => 'nullable|string|max:255',
            'publish_year' => 'nullable|string|max:20',
            'collation' => 'nullable|string|max:100',
            'series_title' => 'nullable|string|max:200',
            'call_number' => 'nullable|string|max:50',
            'language_id' => 'nullable|string|max:5',
            'publish_place_id' => 'nullable|string|max:255',
            'classification' => 'nullable|string|max:40',
            'notes' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'authors' => 'nullable|array',
            'topics' => 'nullable|array',
            'subjects' => 'nullable|array',
            'copies_count' => 'nullable|integer|min:1',
            'initial_item_code' => 'nullable|string|max:20',
            'location_id' => 'nullable|string|max:3',
            'gmd_id' => 'nullable|integer',
            'frequency_id' => 'nullable|integer',
            'spec_detail_info' => 'nullable|string|max:100',
        ]);

        $imageName = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $imageName = time() . '_' . Str::slug(substr($validated['title'], 0, 30)) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/docs'), $imageName);
        }

        $publisherId = $this->resolvePublisherId($request->publisher_id);
        $publishPlaceId = $this->resolvePlaceId($request->publish_place_id);

        $biblio = Biblio::create([
            'gmd_id' => $validated['gmd_id'] ?? 1,
            'title' => $validated['title'],
            'sor' => $validated['sor'] ?? null,
            'edition' => $validated['edition'] ?? null,
            'isbn_issn' => $validated['isbn_issn'] ?? null,
            'publisher_id' => $publisherId,
            'publish_year' => $validated['publish_year'] ?? null,
            'collation' => $validated['collation'] ?? null,
            'series_title' => $validated['series_title'] ?? null,
            'call_number' => $validated['call_number'] ?? null,
            'language_id' => $validated['language_id'] ?? 'id',
            'publish_place_id' => $publishPlaceId,
            'classification' => $validated['classification'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'image' => $imageName,
            'frequency_id' => $validated['frequency_id'] ?? null,
            'spec_detail_info' => $validated['spec_detail_info'] ?? null,
            'input_date' => Carbon::now(),
            'last_update' => Carbon::now(),
            'uid' => auth()->id() ?? 1,
        ]);

        if (!empty($validated['authors'])) {
            $biblio->authors()->sync($validated['authors']);
        }

        // Simpan topik / subjek
        $topicIds = [];
        if (!empty($request->subjects)) {
            foreach ($request->subjects as $subjName) {
                $t = Topic::firstOrCreate(['topic' => trim($subjName)], [
                    'topic_type' => 't',
                    'input_date' => Carbon::today()->toDateString(),
                    'last_update' => Carbon::today()->toDateString(),
                ]);
                $topicIds[] = $t->topic_id;
            }
        }
        if (!empty($validated['topics'])) {
            $topicIds = array_unique(array_merge($topicIds, $validated['topics']));
        }
        if (!empty($topicIds)) {
            $biblio->topics()->sync($topicIds);
        }

        // Tentukan prefix barcode otomatis:
        // Buku: B, Jurnal: R, Skripsi: S
        $isJurnal = false;
        $isSkripsi = false;
        if (!empty($validated['gmd_id'])) {
            $gmd = Gmd::find($validated['gmd_id']);
            if ($gmd) {
                $gName = strtolower($gmd->gmd_name);
                if (str_contains($gName, 'jurnal') || str_contains($gName, 'periodical')) {
                    $isJurnal = true;
                } elseif (str_contains($gName, 'skripsi')) {
                    $isSkripsi = true;
                }
            }
        }
        $prefix = $isJurnal ? 'R' : ($isSkripsi ? 'S' : 'B');

        // Registrasi eksemplar fisik sesuai jumlah eksemplar (copies_count)
        $copiesCount = max(1, (int) ($request->input('copies_count', 1)));
        $startingCode = !empty($validated['initial_item_code']) ? trim($validated['initial_item_code']) : null;
        $itemCodes = Item::generateMultipleNextCodes($prefix, $copiesCount, $startingCode);

        foreach ($itemCodes as $code) {
            Item::create([
                'biblio_id' => $biblio->biblio_id,
                'item_code' => $code,
                'call_number' => $validated['call_number'] ?? null,
                'edition' => $validated['edition'] ?? null,
                'coll_type_id' => 1,
                'location_id' => $validated['location_id'] ?? '001',
                'item_status_id' => '001',
                'input_date' => Carbon::now(),
                'last_update' => Carbon::now(),
                'uid' => auth()->id() ?? 1,
            ]);
        }

        $copiesInfo = $copiesCount > 1 ? " dengan {$copiesCount} eksemplar (" . implode(', ', $itemCodes) . ")" : " dengan kode {$itemCodes[0]}";
        return redirect()->route('admin.biblio.index')->with('success', 'Buku "' . $biblio->title . '" berhasil ditambahkan ke katalog' . $copiesInfo . '!');
    }

    public function edit($id)
    {
        $biblio = Biblio::with(['authors', 'topics', 'items', 'gmd', 'frequency'])->findOrFail($id);
        $authors = Author::orderBy('author_name')->get();
        $publishers = Publisher::orderBy('publisher_name')->get();
        $places = Place::orderBy('place_name')->get();
        $gmds = Gmd::curated()->get();
        $topics = Topic::orderBy('topic')->get();
        $locations = Location::orderBy('location_name')->get();
        $frequencies = Frequency::orderBy('frequency_id')->get();
        $reviewerTopics = Topic::REVIEWER_SUBJECTS;

        $isSkripsi = (int)$biblio->gmd_id === 262 || str_contains(strtolower($biblio->edition ?? ''), 'skripsi');
        $isJurnal = (int)$biblio->gmd_id === 263 || str_contains(strtolower($biblio->gmd?->gmd_name ?? ''), 'jurnal');
        $prefix = $isSkripsi ? 'S' : ($isJurnal ? 'R' : 'B');
        $nextItemCode = Item::generateNextCode($prefix);

        return view('admin.biblio.edit', compact(
            'biblio', 'authors', 'publishers', 'places', 'gmds', 'topics', 'locations', 'frequencies', 'reviewerTopics', 'nextItemCode'
        ));
    }

    public function update(Request $request, $id)
    {
        $biblio = Biblio::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:500',
            'sor' => 'nullable|string|max:200',
            'edition' => 'nullable|string|max:50',
            'isbn_issn' => 'nullable|string|max:32',
            'publisher_id' => 'nullable|string|max:255',
            'publish_year' => 'nullable|string|max:20',
            'collation' => 'nullable|string|max:100',
            'series_title' => 'nullable|string|max:200',
            'call_number' => 'nullable|string|max:50',
            'language_id' => 'nullable|string|max:5',
            'publish_place_id' => 'nullable|string|max:255',
            'classification' => 'nullable|string|max:40',
            'notes' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'authors' => 'nullable|array',
            'topics' => 'nullable|array',
            'subjects' => 'nullable|array',
            'gmd_id' => 'nullable|integer',
            'frequency_id' => 'nullable|integer',
            'spec_detail_info' => 'nullable|string|max:100',
            'additional_copies_count' => 'nullable|integer|min:0',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $imageName = time() . '_' . Str::slug(substr($validated['title'], 0, 30)) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/docs'), $imageName);
            $validated['image'] = $imageName;
        }

        if ($request->has('publisher_id')) {
            $validated['publisher_id'] = $this->resolvePublisherId($request->publisher_id);
        }
        if ($request->has('publish_place_id')) {
            $validated['publish_place_id'] = $this->resolvePlaceId($request->publish_place_id);
        }

        $validated['last_update'] = Carbon::now();

        $biblio->update($validated);

        if (isset($validated['authors'])) {
            $biblio->authors()->sync($validated['authors']);
        }

        // Simpan topik & subjek reviewer
        $topicIds = [];
        if (!empty($request->subjects)) {
            foreach ($request->subjects as $subjName) {
                $t = Topic::firstOrCreate(['topic' => trim($subjName)], [
                    'topic_type' => 't',
                    'input_date' => Carbon::today()->toDateString(),
                    'last_update' => Carbon::today()->toDateString(),
                ]);
                $topicIds[] = $t->topic_id;
            }
        }
        if (!empty($validated['topics'])) {
            $topicIds = array_unique(array_merge($topicIds, $validated['topics']));
        }
        if ($request->has('subjects') || isset($validated['topics'])) {
            $biblio->topics()->sync($topicIds);
        }

        // Tambah jumlah eksemplar baru jika diinput (tanpa mengubah eksemplar lama yang sudah ada)
        $additionalCount = (int) $request->input('additional_copies_count', 0);
        $addedCodes = [];
        if ($additionalCount > 0) {
            $isJurnal = (int)$biblio->gmd_id === 263 || str_contains(strtolower($biblio->gmd?->gmd_name ?? ''), 'jurnal');
            $isSkripsi = (int)$biblio->gmd_id === 262 || str_contains(strtolower($biblio->gmd?->gmd_name ?? ''), 'skripsi');
            $prefix = $isJurnal ? 'R' : ($isSkripsi ? 'S' : 'B');
            
            $addedCodes = Item::generateMultipleNextCodes($prefix, $additionalCount);
            foreach ($addedCodes as $code) {
                Item::create([
                    'biblio_id' => $biblio->biblio_id,
                    'item_code' => $code,
                    'call_number' => $biblio->call_number,
                    'edition' => $biblio->edition,
                    'coll_type_id' => 1,
                    'location_id' => '001',
                    'item_status_id' => '001',
                    'input_date' => Carbon::now(),
                    'last_update' => Carbon::now(),
                    'uid' => auth()->id() ?? 1,
                ]);
            }
        }

        $msg = 'Data buku "' . $biblio->title . '" berhasil diperbarui!';
        if ($additionalCount > 0) {
            $msg .= ' Dan ' . $additionalCount . ' eksemplar baru berhasil ditambahkan (' . implode(', ', $addedCodes) . ').';
        }

        return redirect()->route('admin.biblio.index')->with('success', $msg);
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
        $biblio = Biblio::with(['items.location', 'items.collType', 'items.itemStatus', 'items.activeLoan.member', 'gmd'])->findOrFail($id);

        // Tentukan prefix barcode otomatis:
        // Buku: B, Jurnal: R, Skripsi: S
        $isSkripsi = (int)$biblio->gmd_id === 262 
            || str_contains(strtolower($biblio->edition ?? ''), 'skripsi') 
            || str_contains(strtolower($biblio->spec_detail_info ?? ''), 'skripsi');
        $isJurnal = str_contains(strtolower($biblio->gmd?->gmd_name ?? ''), 'jurnal') 
            || str_contains(strtolower($biblio->gmd?->gmd_name ?? ''), 'periodical')
            || str_contains(strtolower($biblio->title ?? ''), 'jurnal');
        $prefix = $isSkripsi ? 'S' : ($isJurnal ? 'R' : 'B');
        $nextItemCode = Item::generateNextCode($prefix);

        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'item_code' => 'nullable|string|max:20|unique:item,item_code',
                'edition' => 'nullable|string|max:100',
                'location_id' => 'required|string|max:3',
                'coll_type_id' => 'required|integer',
                'price' => 'nullable|integer',
            ]);

            $finalItemCode = !empty($validated['item_code']) ? trim($validated['item_code']) : $nextItemCode;

            Item::create([
                'biblio_id' => $biblio->biblio_id,
                'item_code' => $finalItemCode,
                'call_number' => $biblio->call_number,
                'edition' => !empty($validated['edition']) ? trim($validated['edition']) : $biblio->edition,
                'location_id' => $validated['location_id'],
                'coll_type_id' => $validated['coll_type_id'],
                'item_status_id' => '001',
                'price' => $validated['price'] ?? 0,
                'input_date' => Carbon::now(),
                'last_update' => Carbon::now(),
                'uid' => auth()->id() ?? 1,
            ]);

            return back()->with('success', 'Eksemplar kode ' . $finalItemCode . ' berhasil ditambahkan!');
        }

        $locations = Location::all();
        $collTypes = CollType::all();

        return view('admin.biblio.items', compact('biblio', 'locations', 'collTypes', 'nextItemCode', 'prefix', 'isJurnal'));
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
        $reviewerTopics = Topic::REVIEWER_SUBJECTS;
        $nextItemCode = Item::generateNextCode('S');

        return view('admin.biblio.create_skripsi', compact('dosenMembers', 'authors', 'topics', 'reviewerTopics', 'nextItemCode'));
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
            'item_code' => 'nullable|string|max:20',
            'topics' => 'nullable|array',
            'subjects' => 'nullable|array',
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

        // Subjek: gunakan pilihan admin atau deteksi otomatis bila kosong
        $finalSubjects = $request->input('subjects', []);
        if (empty($finalSubjects)) {
            $finalSubjects = Topic::suggestSubjectsFromTitle($validated['title'], $validated['prodi']);
        }

        $specDetail = json_encode([
            'tipe' => 'Skripsi',
            'nim' => $validated['student_nim'],
            'prodi' => $validated['prodi'],
            'pembimbing_1' => $validated['pembimbing_1'],
            'pembimbing_2' => $validated['pembimbing_2'] ?? null,
            'subjects' => $finalSubjects,
            'status' => 'approved',
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

        // Generate barcode nomor eksemplar otomatis awalan S
        $sBarcode = !empty($validated['item_code']) ? trim($validated['item_code']) : Item::generateNextCode('S');

        Item::create([
            'biblio_id' => $biblio->biblio_id,
            'item_code' => $sBarcode,
            'call_number' => $callNumber,
            'coll_type_id' => 2, // Reference
            'location_id' => '001',
            'item_status_id' => '001',
            'input_date' => Carbon::now(),
            'last_update' => Carbon::now(),
            'uid' => auth()->id() ?? 1,
        ]);

        // Simpan topik & subjek reviewer
        $topicIds = [];
        if (!empty($finalSubjects)) {
            foreach ($finalSubjects as $subjName) {
                $t = Topic::firstOrCreate(['topic' => trim($subjName)], [
                    'topic_type' => 't',
                    'input_date' => Carbon::today()->toDateString(),
                    'last_update' => Carbon::today()->toDateString(),
                ]);
                $topicIds[] = $t->topic_id;
            }
        }
        if (!empty($validated['topics'])) {
            $topicIds = array_unique(array_merge($topicIds, $validated['topics']));
        }
        if (!empty($topicIds)) {
            $biblio->topics()->sync($topicIds);
        }

        // Simpan ke tb_skripsi
        try {
            DB::table('tb_skripsi')->updateOrInsert(
                ['nim' => $validated['student_nim']],
                [
                    'kd_skripsi' => $biblio->biblio_id,
                    'judul' => $validated['title'],
                    'dosen' => $validated['pembimbing_1'],
                    'asdos' => $validated['pembimbing_2'] ?? null,
                    'subjek' => !empty($finalSubjects) ? implode(', ', $finalSubjects) : $validated['prodi'],
                ]
            );
        } catch (\Throwable $e) {
            // Abaikan jika tabel tidak tersedia
        }

        return redirect()->route('admin.biblio.index')->with('success', 'Data Skripsi "' . $validated['title'] . '" (Barcode: ' . $sBarcode . ') berhasil ditambahkan!');
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

    public function verifySkripsiIndex(Request $request)
    {
        $status = $request->input('status', 'all');
        $search = $request->input('search');

        $query = Biblio::with(['topics', 'items'])->where(function($q) {
            $q->where('gmd_id', 262)
              ->orWhere('spec_detail_info', 'like', '%"tipe":"Skripsi"%');
        })->latest('input_date');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('sor', 'like', "%{$search}%")
                  ->orWhere('isbn_issn', 'like', "%{$search}%")
                  ->orWhere('spec_detail_info', 'like', "%{$search}%");
            });
        }

        if ($status !== 'all') {
            $query->where('spec_detail_info', 'like', '%"status":"' . $status . '"%');
        }

        $allTheses = $query->paginate(15)->withQueryString();

        $theses = $allTheses->through(function ($item) {
            $spec = json_decode($item->spec_detail_info ?? '{}', true) ?: [];
            $nim = $spec['nim'] ?? $item->isbn_issn;
            $member = $nim ? Member::find($nim) : null;
            $activeLoanCount = $member ? $member->activeLoans()->count() : 0;
            $isBebasPustaka = $nim ? BebasPustaka::where('nim', $nim)->exists() : false;

            $subjects = $spec['subjects'] ?? $item->topics->pluck('topic')->toArray();
            if (empty($subjects)) {
                $subjects = Topic::suggestSubjectsFromTitle($item->title, $spec['prodi'] ?? $member?->prodi_name);
            }

            return (object) [
                'biblio' => $item,
                'nim' => $nim,
                'student_name' => $spec['nama'] ?? ($member?->member_name ?: $item->sor),
                'prodi' => $spec['prodi'] ?? ($member?->prodi_name ?: '-'),
                'semester' => $spec['semester'] ?? ($member?->semester ?: '-'),
                'pembimbing_1' => $spec['pembimbing_1'] ?? '-',
                'pembimbing_2' => $spec['pembimbing_2'] ?? null,
                'status' => $spec['status'] ?? ($item->opac_hide ? 'pending' : 'approved'),
                'submitted_at' => $spec['submitted_at'] ?? $item->input_date,
                'notes_admin' => $spec['notes_admin'] ?? null,
                'subjects' => $subjects,
                'file_url' => $item->file_att ? asset($item->file_att) : null,
                'file_exists' => $item->file_att && file_exists(public_path($item->file_att)),
                'active_loans_count' => $activeLoanCount,
                'is_bebas_pustaka' => $isBebasPustaka,
                'member' => $member,
            ];
        });

        $pendingCount = Biblio::where(function($q) {
            $q->where('gmd_id', 262)
              ->orWhere('spec_detail_info', 'like', '%"tipe":"Skripsi"%');
        })->where('spec_detail_info', 'like', '%"status":"pending"%')->count();

        $approvedCount = Biblio::where(function($q) {
            $q->where('gmd_id', 262)
              ->orWhere('spec_detail_info', 'like', '%"tipe":"Skripsi"%');
        })->where('spec_detail_info', 'like', '%"status":"approved"%')->count();

        $reviewerTopics = Topic::REVIEWER_SUBJECTS;

        return view('admin.biblio.verify_skripsi', compact('theses', 'allTheses', 'status', 'search', 'pendingCount', 'approvedCount', 'reviewerTopics'));
    }

    public function approveSkripsi(Request $request, $id)
    {
        $biblio = Biblio::findOrFail($id);
        $spec = json_decode($biblio->spec_detail_info ?? '{}', true) ?: [];

        $spec['status'] = 'approved';
        $spec['verified_by'] = auth()->user()->realname ?? auth()->user()->username;
        $spec['verified_at'] = Carbon::now()->toDateTimeString();
        $spec['notes_admin'] = $request->input('notes_admin', 'Dokumen dan lembar pengesahan terverifikasi lengkap & valid.');

        // Simpan / update subjek yang dipilih atau dikonfirmasi oleh admin
        if ($request->has('subjects')) {
            $selectedSubjects = (array)$request->input('subjects', []);
            $spec['subjects'] = $selectedSubjects;

            $topicIds = [];
            foreach ($selectedSubjects as $subjName) {
                $t = Topic::firstOrCreate(['topic' => trim($subjName)], [
                    'topic_type' => 't',
                    'input_date' => Carbon::today()->toDateString(),
                    'last_update' => Carbon::today()->toDateString(),
                ]);
                $topicIds[] = $t->topic_id;
            }
            if (!empty($topicIds)) {
                $biblio->topics()->sync($topicIds);
            }
        }

        $biblio->spec_detail_info = json_encode($spec, JSON_UNESCAPED_UNICODE);
        $biblio->opac_hide = 0; // Publikasikan ke repositori OPAC
        $biblio->last_update = Carbon::now();
        $biblio->save();

        // Pastikan eksemplar fisik barcode berawalan 'S' otomatis dibuat jika belum ada
        if (!$biblio->items()->exists()) {
            $sBarcode = Item::generateNextCode('S');
            Item::create([
                'biblio_id' => $biblio->biblio_id,
                'item_code' => $sBarcode,
                'call_number' => $biblio->call_number,
                'coll_type_id' => 2, // Reference
                'location_id' => '001',
                'item_status_id' => '001',
                'input_date' => Carbon::now(),
                'last_update' => Carbon::now(),
                'uid' => auth()->id() ?? 1,
            ]);
        }

        $nim = $spec['nim'] ?? $biblio->isbn_issn;
        if (!empty($nim)) {
            $existingBP = BebasPustaka::where('nim', $nim)->first();
            if (!$existingBP) {
                BebasPustaka::create([
                    'nim' => $nim,
                    'tgl_in' => Carbon::today()->toDateString(),
                    'id_admin' => auth()->id() ?? 1,
                ]);
            }

            // Sync ke legacy table tb_skripsi
            try {
                $finalSubjects = $spec['subjects'] ?? [];
                DB::table('tb_skripsi')->updateOrInsert(
                    ['nim' => $nim],
                    [
                        'kd_skripsi' => $biblio->biblio_id,
                        'judul' => $biblio->title,
                        'dosen' => $spec['pembimbing_1'] ?? '-',
                        'asdos' => $spec['pembimbing_2'] ?? null,
                        'subjek' => !empty($finalSubjects) ? implode(', ', $finalSubjects) : ($spec['prodi'] ?? 'Skripsi'),
                    ]
                );
            } catch (\Throwable $e) {
                // Abaikan jika tidak tersedia
            }
        }

        return redirect()->back()->with('success', 'Skripsi atas nama ' . ($spec['nama'] ?? 'Mahasiswa') . ' (NIM: ' . $nim . ') berhasil DISETUJUI & diterbitkan Bebas Pustaka.');
    }

    public function rejectSkripsi(Request $request, $id)
    {
        $request->validate([
            'notes_admin' => 'required|string|min:5',
        ], [
            'notes_admin.required' => 'Alasan permintaan revisi/penolakan wajib diisi agar mahasiswa dapat memperbaiki.',
            'notes_admin.min' => 'Catatan revisi minimal 5 karakter.',
        ]);

        $biblio = Biblio::findOrFail($id);
        $spec = json_decode($biblio->spec_detail_info ?? '{}', true) ?: [];

        $spec['status'] = 'revision';
        $spec['verified_by'] = auth()->user()->realname ?? auth()->user()->username;
        $spec['rejected_at'] = Carbon::now()->toDateTimeString();
        $spec['notes_admin'] = $request->input('notes_admin');

        $biblio->spec_detail_info = json_encode($spec, JSON_UNESCAPED_UNICODE);
        $biblio->opac_hide = 1; // Sembunyikan dari OPAC
        $biblio->last_update = Carbon::now();
        $biblio->save();

        return redirect()->back()->with('info', 'Status skripsi diubah menjadi PERLU REVISI. Catatan telah disampaikan kepada mahasiswa.');
    }

    /**
     * Ekspor data bibliografi koleksi perpustakaan per GMD atau semua GMD (Excel, Word, PDF, CSV)
     */
    public function export(Request $request)
    {
        $format = $request->input('format', 'excel');
        $search = $request->input('search');
        $publisherId = $request->input('publisher_id');
        $gmdId = $request->input('gmd_id');
        $year = $request->input('year');
        $itemStatus = $request->input('item_status');

        $books = Biblio::with(['authors', 'publisher', 'items', 'gmd', 'place', 'topics']);

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

        $gmdLabel = 'Semua Format / GMD';
        if (!empty($gmdId) && $gmdId !== 'all') {
            if ($gmdId == '1' || $gmdId === 'buku') {
                $books->where('gmd_id', 1);
                $gmdLabel = 'Buku';
            } elseif ($gmdId == '262' || $gmdId === 'skripsi') {
                $books->where('gmd_id', 262);
                $gmdLabel = 'Skripsi';
            } elseif ($gmdId == '263' || $gmdId === 'jurnal') {
                $books->where('gmd_id', 263);
                $gmdLabel = 'Jurnal';
            } elseif ($gmdId == '30' || $gmdId === 'ebook') {
                $books->where(function($q) {
                    $q->where('gmd_id', 30)->orWhereNotNull('file_att');
                });
                $gmdLabel = 'e-Book';
            } elseif ($gmdId === 'prosiding') {
                $books->where(function($q) {
                    $q->where('title', 'like', '%prosiding%')
                      ->orWhere('title', 'like', '%proceedings%');
                });
                $gmdLabel = 'Prosiding';
            } elseif ($gmdId === 'lainnya') {
                $books->whereNotIn('gmd_id', [1, 262, 263, 30])
                      ->whereNull('file_att');
                $gmdLabel = 'Koleksi Lainnya';
            } elseif (is_numeric($gmdId)) {
                $books->where('gmd_id', $gmdId);
                $selectedGmd = Gmd::find($gmdId);
                if ($selectedGmd) $gmdLabel = $selectedGmd->gmd_name;
            }
        }

        if (!empty($year)) {
            $books->where('publish_year', 'like', "%{$year}%");
        }

        if ($itemStatus === 'has_items') {
            $books->has('items');
        } elseif ($itemStatus === 'no_items') {
            $books->doesntHave('items');
        }

        $collection = $books->orderBy('biblio_id', 'desc')->get();

        $title = $gmdLabel !== 'Semua Format / GMD' ? 'Laporan Koleksi ' . $gmdLabel : 'Laporan Katalog Koleksi Bibliografi';
        $filename = 'Laporan_Koleksi_' . Str::slug($gmdLabel, '_') . '_' . date('Ymd_His');

        $headers = [
            'No',
            'Kode / Barcode Eksemplar',
            'Judul Dokumen / Buku',
            'Format (GMD)',
            'Pengarang / Penulis',
            'Penerbit',
            'Tempat Terbit',
            'Tahun',
            'ISBN / ISSN',
            'No. Panggil',
            'Klasifikasi (DDC)',
            'Subjek / Topik',
            'Total Eksemplar',
            'Status / Akses'
        ];

        $rows = [];
        $no = 1;
        foreach ($collection as $b) {
            $itemCodes = $b->items->pluck('item_code')->filter()->implode(', ');
            $topics = $b->topics->pluck('topic')->filter()->implode(', ');
            $author = $b->author_names;
            $statusText = $b->items->count() > 0 ? $b->items->count() . ' Eksemplar' : (!empty($b->file_att) ? 'Digital (e-Resource)' : 'Tersedia');

            $rows[] = [
                'no'           => $no++,
                'item_code'    => $itemCodes ?: '-',
                'title'        => $b->title,
                'gmd'          => $b->gmd?->gmd_name ?? '-',
                'author'       => $author ?: '-',
                'publisher'    => $b->publisher?->publisher_name ?? '-',
                'place'        => $b->place?->place_name ?? '-',
                'year'         => $b->publish_year ?: '-',
                'isbn'         => $b->isbn_issn ?: '-',
                'call_number'  => $b->call_number ?: '-',
                'ddc'          => $b->classification ?: '-',
                'topics'       => $topics ?: '-',
                'total_items'  => $b->items->count(),
                'status'       => $statusText,
            ];
        }

        $metadata = [
            'Jenis Dokumen'  => 'Katalog Koleksi Perpustakaan',
            'Kategori GMD'   => $gmdLabel,
            'Tahun Terbit'   => !empty($year) ? $year : 'Semua Tahun',
            'Jumlah Data'    => count($rows) . ' Judul Koleksi',
            'Tanggal Cetak'  => Carbon::now()->translatedFormat('d F Y, H:i') . ' WIB',
            'Dicetak Oleh'   => auth()->user()->username ?? 'Petugas Perpustakaan'
        ];

        return DataExportService::export(
            $format,
            $filename,
            $title,
            $metadata,
            $headers,
            $rows,
            'landscape'
        );
    }

    public function createJurnal()
    {
        $publishers = Publisher::orderBy('publisher_name')->get();
        $places = Place::orderBy('place_name')->get();
        $gmds = Gmd::curated()->get();
        $topics = Topic::orderBy('topic')->get();
        $locations = Location::orderBy('location_name')->get();
        $frequencies = Frequency::orderBy('frequency_id')->get();

        // Default prefix R untuk Jurnal (Reviewer: Pilihan eksemplar sama dengan buku dengan kode R)
        $nextItemCode = Item::generateNextCode('R');
        $reviewerTopics = Topic::REVIEWER_SUBJECTS;
        $jurnalLevels = [
            'Jurnal Nasional',
            'Jurnal Nasional Terakreditasi',
            'Jurnal Internasional',
            'Jurnal Internasional Bereputasi'
        ];

        return view('admin.biblio.create_jurnal', compact(
            'publishers', 'places', 'gmds', 'topics', 'locations', 'frequencies', 'nextItemCode', 'reviewerTopics', 'jurnalLevels'
        ));
    }

    public function storeJurnal(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:500',
            'edition' => 'required|string|max:50', // Kolom edisi (Vol & No)
            'frequency_id' => 'required|integer', // Kolom kala terbit (annually, 3 times a year, quarterly, monthly)
            'spec_detail_info' => 'required|string|max:100', // Dropdown tingkat jurnal
            'isbn_issn' => 'nullable|string|max:32',
            'publisher_id' => 'nullable|string|max:255',
            'publish_place_id' => 'nullable|string|max:255',
            'publish_year' => 'nullable|string|max:20',
            'sor' => 'nullable|string|max:200',
            'call_number' => 'nullable|string|max:50',
            'classification' => 'nullable|string|max:40',
            'collation' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'subjects' => 'nullable|array',
            'copies_count' => 'nullable|integer|min:1',
            'initial_item_code' => 'nullable|string|max:20',
        ]);

        $imageName = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $imageName = time() . '_' . Str::slug(substr($validated['title'], 0, 30)) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/docs'), $imageName);
        }

        $publisherId = $this->resolvePublisherId($request->publisher_id);
        $publishPlaceId = $this->resolvePlaceId($request->publish_place_id);

        $biblio = Biblio::create([
            'gmd_id' => 263, // Jurnal
            'title' => $validated['title'],
            'edition' => $validated['edition'],
            'frequency_id' => $validated['frequency_id'],
            'spec_detail_info' => $validated['spec_detail_info'],
            'isbn_issn' => $validated['isbn_issn'] ?? null,
            'publisher_id' => $publisherId,
            'publish_place_id' => $publishPlaceId,
            'publish_year' => $validated['publish_year'] ?? null,
            'sor' => $validated['sor'] ?? null,
            'call_number' => $validated['call_number'] ?? null,
            'classification' => $validated['classification'] ?? null,
            'collation' => $validated['collation'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'image' => $imageName,
            'language_id' => 'id',
            'input_date' => Carbon::now(),
            'last_update' => Carbon::now(),
            'uid' => auth()->id() ?? 1,
        ]);

        // Simpan topik / subjek
        $topicIds = [];
        if (!empty($request->subjects)) {
            foreach ($request->subjects as $subjName) {
                $t = Topic::firstOrCreate(['topic' => trim($subjName)], [
                    'topic_type' => 't',
                    'input_date' => Carbon::today()->toDateString(),
                    'last_update' => Carbon::today()->toDateString(),
                ]);
                $topicIds[] = $t->topic_id;
            }
        }
        if (!empty($topicIds)) {
            $biblio->topics()->sync($topicIds);
        }

        // Pilihan eksemplar sama dengan buku dengan kode awalan R
        $copiesCount = max(1, (int) ($request->input('copies_count', 1)));
        $startingCode = !empty($validated['initial_item_code']) ? trim($validated['initial_item_code']) : null;
        $itemCodes = Item::generateMultipleNextCodes('R', $copiesCount, $startingCode);

        foreach ($itemCodes as $code) {
            Item::create([
                'biblio_id' => $biblio->biblio_id,
                'item_code' => $code,
                'call_number' => $validated['call_number'] ?? null,
                'edition' => $validated['edition'] ?? null,
                'coll_type_id' => 1,
                'location_id' => '001',
                'item_status_id' => '001',
                'input_date' => Carbon::now(),
                'last_update' => Carbon::now(),
                'uid' => auth()->id() ?? 1,
            ]);
        }

        $copiesInfo = $copiesCount > 1 ? " dengan {$copiesCount} eksemplar (" . implode(', ', $itemCodes) . ")" : " dengan kode {$itemCodes[0]}";
        return redirect()->route('admin.biblio.index', ['gmd_id' => 263])->with('success', 'Jurnal "' . $biblio->title . '" berhasil ditambahkan ke katalog' . $copiesInfo . '!');
    }

    public function printLabels(Request $request)
    {
        $search = $request->input('search');
        $biblioId = $request->input('biblio_id');
        $barcode = $request->input('barcode');
        $gmdId = $request->input('gmd_id');
        $selectedItems = $request->input('items', []);
        $printMode = $request->input('mode', 'both'); // 'both', 'spine', 'barcode'
        $columns = (int) $request->input('columns', 2); // 2 or 3

        $query = Item::with(['biblio.authors', 'biblio.publisher', 'biblio.gmd']);

        if (!empty($selectedItems)) {
            $query->whereIn('item_id', (array) $selectedItems);
        } else {
            if (!empty($biblioId)) {
                if (is_numeric($biblioId)) {
                    $query->where('biblio_id', $biblioId);
                } else {
                    $query->whereHas('biblio', function($bq) use ($biblioId) {
                        $bq->where('title', 'like', "%{$biblioId}%");
                    });
                }
            }

            if (!empty($barcode)) {
                $query->where('item_code', 'like', "%{$barcode}%");
            }

            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->where('item_code', 'like', "%{$search}%")
                      ->orWhereHas('biblio', function($bq) use ($search) {
                          $bq->where('title', 'like', "%{$search}%")
                             ->orWhere('classification', 'like', "%{$search}%")
                             ->orWhere('call_number', 'like', "%{$search}%");
                      });
                });
            }

            if (!empty($gmdId)) {
                $query->whereHas('biblio', function($bq) use ($gmdId) {
                    $bq->where('gmd_id', $gmdId);
                });
            }
        }

        $items = $query->orderBy('item_id', 'desc')->take(100)->get();
        $gmds = Gmd::curated()->get();
        $biblioOptions = Biblio::select('biblio_id', 'title')->orderBy('title')->get();
        $barcodeOptions = Item::select('item_code')->distinct()->orderBy('item_code')->pluck('item_code');

        return view('admin.biblio.print_labels', compact(
            'items', 'gmds', 'search', 'biblioId', 'barcode', 'gmdId', 'selectedItems', 'printMode', 'columns', 'biblioOptions', 'barcodeOptions'
        ));
    }

    public function printSingleLabel($id, Request $request)
    {
        $biblio = Biblio::with(['items.biblio.authors', 'authors', 'gmd'])->findOrFail($id);
        $items = $biblio->items;
        $printMode = $request->input('mode', 'both');
        $columns = (int) $request->input('columns', 2);
        $gmds = Gmd::curated()->get();
        $biblioOptions = Biblio::select('biblio_id', 'title')->orderBy('title')->get();
        $barcodeOptions = Item::select('item_code')->distinct()->orderBy('item_code')->pluck('item_code');

        return view('admin.biblio.print_labels', [
            'items' => $items,
            'gmds' => $gmds,
            'search' => '',
            'biblioId' => $biblio->biblio_id,
            'barcode' => '',
            'gmdId' => '',
            'selectedItems' => $items->pluck('item_id')->toArray(),
            'printMode' => $printMode,
            'columns' => $columns,
            'singleBiblio' => $biblio,
            'biblioOptions' => $biblioOptions,
            'barcodeOptions' => $barcodeOptions,
        ]);
    }

    private function resolvePublisherId($input)
    {
        if (empty($input)) return null;
        if (is_numeric($input) && Publisher::where('publisher_id', $input)->exists()) {
            return (int) $input;
        }
        $pub = Publisher::firstOrCreate(
            ['publisher_name' => trim($input)],
            [
                'input_date' => Carbon::today()->toDateString(),
                'last_update' => Carbon::today()->toDateString(),
            ]
        );
        return $pub->publisher_id;
    }

    private function resolvePlaceId($input)
    {
        if (empty($input)) return null;
        if (is_numeric($input) && Place::where('place_id', $input)->exists()) {
            return (int) $input;
        }
        $plc = Place::firstOrCreate(
            ['place_name' => trim($input)],
            [
                'input_date' => Carbon::today()->toDateString(),
                'last_update' => Carbon::today()->toDateString(),
            ]
        );
        return $plc->place_id;
    }
}


