<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Author;
use App\Models\Location;
use App\Models\Publisher;
use App\Models\Topic;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MasterDataController extends Controller
{
    public function authors(Request $request)
    {
        $search = $request->input('search');
        $query = Author::withCount('biblios');

        if (!empty($search)) {
            $query->where('author_name', 'like', "%{$search}%");
        }

        $authors = $query->orderBy('author_name')->paginate(20)->withQueryString();

        return view('admin.master.authors', compact('authors', 'search'));
    }

    public function storeAuthor(Request $request)
    {
        $request->validate([
            'author_name' => 'required|string|max:100',
            'authority_type' => 'nullable|in:p,o,c',
        ]);

        Author::create([
            'author_name' => $request->author_name,
            'authority_type' => $request->authority_type ?? 'p',
            'input_date' => Carbon::today(),
            'last_update' => Carbon::today(),
        ]);

        return back()->with('success', 'Pengarang berhasil ditambahkan!');
    }

    public function deleteAuthor($id)
    {
        $author = Author::withCount('biblios')->findOrFail($id);
        if ($author->biblios_count > 0) {
            return back()->with('error', 'Pengarang tidak dapat dihapus karena terhubung dengan ' . $author->biblios_count . ' buku!');
        }
        $author->delete();
        return back()->with('success', 'Pengarang berhasil dihapus.');
    }

    public function publishers(Request $request)
    {
        $search = $request->input('search');
        $query = Publisher::withCount('biblios');

        if (!empty($search)) {
            $query->where('publisher_name', 'like', "%{$search}%");
        }

        $publishers = $query->orderBy('publisher_name')->paginate(20)->withQueryString();

        return view('admin.master.publishers', compact('publishers', 'search'));
    }

    public function storePublisher(Request $request)
    {
        $request->validate([
            'publisher_name' => 'required|string|max:100|unique:mst_publisher,publisher_name',
        ]);

        Publisher::create([
            'publisher_name' => $request->publisher_name,
            'input_date' => Carbon::today(),
            'last_update' => Carbon::today(),
        ]);

        return back()->with('success', 'Penerbit berhasil ditambahkan!');
    }

    public function deletePublisher($id)
    {
        $pub = Publisher::withCount('biblios')->findOrFail($id);
        if ($pub->biblios_count > 0) {
            return back()->with('error', 'Penerbit tidak dapat dihapus karena terhubung dengan ' . $pub->biblios_count . ' buku!');
        }
        $pub->delete();
        return back()->with('success', 'Penerbit berhasil dihapus.');
    }

    public function topics(Request $request)
    {
        $search = $request->input('search');
        $query = Topic::withCount('biblios');

        if (!empty($search)) {
            $query->where('topic', 'like', "%{$search}%");
        }

        $topics = $query->orderBy('topic')->paginate(20)->withQueryString();

        return view('admin.master.topics', compact('topics', 'search'));
    }

    public function storeTopic(Request $request)
    {
        $request->validate([
            'topic' => 'required|string|max:50',
            'classification' => 'nullable|string|max:50',
        ]);

        Topic::create([
            'topic' => $request->topic,
            'topic_type' => 't',
            'classification' => $request->classification ?? '',
            'input_date' => Carbon::today(),
            'last_update' => Carbon::today(),
        ]);

        return back()->with('success', 'Subjek/Topik berhasil ditambahkan!');
    }

    public function deleteTopic($id)
    {
        $topic = Topic::withCount('biblios')->findOrFail($id);
        if ($topic->biblios_count > 0) {
            return back()->with('error', 'Topik tidak dapat dihapus karena terhubung dengan ' . $topic->biblios_count . ' buku!');
        }
        $topic->delete();
        return back()->with('success', 'Subjek berhasil dihapus.');
    }
}
