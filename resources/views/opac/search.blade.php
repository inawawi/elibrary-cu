@extends('layouts.opac')

@section('title', 'Pencarian Katalog Buku')

@section('content')
<div class="bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white mb-4">Pencarian Katalog Perpustakaan</h1>

        <!-- Search Bar -->
        <form action="{{ route('opac.search') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-grow">
                <i data-lucide="search" class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text"
                       name="q"
                       value="{{ $query }}"
                       placeholder="Cari berdasarkan judul, nama pengarang, ISBN, atau nomor panggil..."
                       class="w-full pl-12 pr-4 py-3.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 font-medium">
            </div>

            <!-- Sort Dropdown -->
            <select name="sort" class="px-4 py-3.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm font-medium focus:outline-none">
                <option value="newest" {{ $sort === 'newest' ? 'selected' : '' }}>Terbaru Masuk</option>
                <option value="title_asc" {{ $sort === 'title_asc' ? 'selected' : '' }}>Judul A - Z</option>
                <option value="title_desc" {{ $sort === 'title_desc' ? 'selected' : '' }}>Judul Z - A</option>
                <option value="year_desc" {{ $sort === 'year_desc' ? 'selected' : '' }}>Tahun Terbaru</option>
                <option value="oldest" {{ $sort === 'oldest' ? 'selected' : '' }}>Koleksi Terlama</option>
            </select>

            <button type="submit" class="px-6 py-3.5 bg-brand-600 hover:bg-brand-700 text-white font-bold text-sm rounded-xl shadow-md transition-colors flex items-center justify-center gap-2">
                <i data-lucide="filter" class="w-4 h-4"></i>
                <span>Terapkan</span>
            </button>
        </form>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Sidebar Filters -->
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm sticky top-28">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800 mb-5">
                    <span class="font-bold text-sm text-slate-900 dark:text-white flex items-center gap-2">
                        <i data-lucide="sliders-horizontal" class="w-4 h-4 text-brand-500"></i>
                        Filter Koleksi
                    </span>
                    @if($query || $topicId || $publisherId || $gmdId || $year)
                        <a href="{{ route('opac.search') }}" class="text-xs font-semibold text-rose-500 hover:underline">Reset</a>
                    @endif
                </div>

                <form action="{{ route('opac.search') }}" method="GET" class="space-y-5">
                    @if($query)
                        <input type="hidden" name="q" value="{{ $query }}">
                    @endif

                    <!-- Topic Filter -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Subjek / Topik</label>
                        <select name="topic" onchange="this.form.submit()" class="w-full p-2.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-800 dark:text-slate-200 focus:outline-none">
                            <option value="">Semua Subjek</option>
                            @foreach($topics as $t)
                                <option value="{{ $t->topic_id }}" {{ $topicId == $t->topic_id ? 'selected' : '' }}>{{ $t->topic }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Publisher Filter -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Penerbit</label>
                        <select name="publisher" onchange="this.form.submit()" class="w-full p-2.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-800 dark:text-slate-200 focus:outline-none">
                            <option value="">Semua Penerbit</option>
                            @foreach($publishers as $p)
                                <option value="{{ $p->publisher_id }}" {{ $publisherId == $p->publisher_id ? 'selected' : '' }}>{{ $p->publisher_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- GMD Filter -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Bentuk Karya (GMD)</label>
                        <select name="gmd" onchange="this.form.submit()" class="w-full p-2.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-800 dark:text-slate-200 focus:outline-none">
                            <option value="">Semua Jenis Koleksi</option>
                            @foreach($gmds as $g)
                                <option value="{{ $g->gmd_id }}" {{ $gmdId == $g->gmd_id ? 'selected' : '' }}>{{ $g->gmd_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Year -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Tahun Terbit</label>
                        <input type="text" name="year" value="{{ $year }}" placeholder="Contoh: 2022" class="w-full p-2.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-800 dark:text-slate-200 focus:outline-none">
                    </div>

                    <button type="submit" class="w-full py-2.5 rounded-lg bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs transition-colors">
                        Saring Hasil
                    </button>
                </form>
            </div>
        </div>

        <!-- Book Results Grid -->
        <div class="lg:col-span-3">
            <div class="flex items-center justify-between mb-6">
                <span class="text-sm font-semibold text-slate-600 dark:text-slate-400">
                    Menampilkan <span class="font-bold text-slate-900 dark:text-white">{{ $results->total() }}</span> judul buku
                    @if($query) untuk pencarian "<span class="font-bold text-brand-600 dark:text-sky-400">{{ $query }}</span>" @endif
                </span>
            </div>

            @if($results->isEmpty())
                <div class="p-12 text-center bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400">
                        <i data-lucide="book-x" class="w-8 h-8"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Koleksi Tidak Ditemukan</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 max-w-md mx-auto mb-6">
                        Maaf, kami tidak menemukan buku yang cocok dengan kriteria pencarian Anda. Coba periksa ejaan kata kunci atau hapus beberapa filter pencarian.
                    </p>
                    <a href="{{ route('opac.search') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-600 text-white text-sm font-semibold hover:bg-brand-700 transition-colors">
                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                        <span>Reset Pencarian</span>
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($results as $book)
                        <div class="group flex flex-col bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden hover:border-brand-300 dark:hover:border-sky-700 transition-all duration-300 hover:-translate-y-1 shadow-sm hover:shadow-xl">
                            <!-- Cover -->
                            <div class="relative aspect-[3/4] bg-slate-100 dark:bg-slate-800 overflow-hidden">
                                <img src="{{ $book->cover_url }}"
                                     alt="{{ $book->title }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                     loading="lazy"
                                     onerror="this.src='{{ asset('images/default_cover.svg') }}'">

                                <div class="absolute top-3 right-3">
                                    @if($book->available_copies > 0)
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-500/90 text-white backdrop-blur shadow-sm">
                                            Tersedia ({{ $book->available_copies }})
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-500/90 text-white backdrop-blur shadow-sm">
                                            Dipinjam
                                        </span>
                                    @endif
                                </div>

                                @if($book->publish_year)
                                    <div class="absolute bottom-3 left-3">
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-900/80 text-white backdrop-blur">
                                            {{ $book->publish_year }}
                                        </span>
                                    </div>
                                @endif
                            </div>

                            <!-- Content -->
                            <div class="p-4 flex flex-col flex-grow">
                                <div class="text-[11px] font-semibold text-brand-600 dark:text-sky-400 mb-1 truncate">
                                    {{ $book->call_number ?: 'Koleksi Umum' }}
                                </div>
                                <h3 class="font-bold text-slate-900 dark:text-white line-clamp-2 text-sm leading-snug group-hover:text-brand-600 dark:group-hover:text-sky-400 transition-colors mb-2" title="{{ $book->title }}">
                                    <a href="{{ route('opac.show', $book->biblio_id) }}">
                                        {{ $book->title }}
                                    </a>
                                </h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-1 mb-3">
                                    {{ $book->author_names }}
                                </p>
                                <div class="mt-auto pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs text-slate-400">
                                    <span>{{ $book->publisher?->publisher_name ?: 'Penerbit Umum' }}</span>
                                    <a href="{{ route('opac.show', $book->biblio_id) }}" class="font-semibold text-brand-600 dark:text-sky-400 hover:underline inline-flex items-center gap-1">
                                        <span>Detail</span>
                                        <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-10">
                    {{ $results->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
