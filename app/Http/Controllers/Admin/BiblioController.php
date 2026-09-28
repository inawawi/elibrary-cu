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

        $books = Biblio::with(['authors', 'publisher', 'items']);

        if (!empty($search)) {
            $books->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('isbn_issn', 'like', "%{$search}%")
                  ->orWhere('call_number', 'like', "%{$search}%")
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

        $biblios = $books->orderBy('biblio_id', 'desc')->paginate(15)->withQueryString();

        $publishers = Publisher::has('biblios')->orderBy('publisher_name')->get();
        $gmds = Gmd::all();

        return view('admin.biblio.index', compact('biblios', 'search', 'publishers', 'gmds', 'publisherId', 'gmdId'));
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
}
