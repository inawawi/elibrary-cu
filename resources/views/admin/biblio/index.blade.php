@extends('layouts.admin')

@section('title', 'Katalog Bibliografi')
@section('header_title', 'Manajemen Katalog Buku (Bibliografi)')

@section('content')
<div class="space-y-6">
    <!-- Search & Filter Card -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm" x-data="{ showFilters: {{ $activeFiltersCount > 0 ? 'true' : 'false' }} }">
        <form action="{{ route('admin.biblio.index') }}" method="GET" id="biblioFilterForm" class="space-y-4">
            
            <!-- Top Row: Search Input + Filter Toggle + Add Button -->
            <div class="flex flex-col lg:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-2.5 w-full lg:max-w-3xl">
                    <div class="relative flex-grow">
                        <i data-lucide="search" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text"
                               name="search"
                               value="{{ $search }}"
                               placeholder="Cari judul buku, nama pengarang, nomor ISBN, atau no. panggil..."
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-brand-500">
                    </div>

                    <button type="submit" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-900 dark:bg-brand-600 dark:hover:bg-brand-700 text-white font-bold text-xs rounded-xl transition-colors flex items-center gap-1.5 flex-shrink-0">
                        <i data-lucide="search" class="w-3.5 h-3.5"></i>
                        <span>Cari</span>
                    </button>

                    <button type="button" @click="showFilters = !showFilters"
                        class="px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-bold flex items-center gap-2 transition-colors flex-shrink-0"
                        :class="showFilters || {{ $activeFiltersCount }} > 0 ? 'bg-brand-50 text-brand-700 border-brand-300 dark:bg-sky-950/60 dark:text-sky-300 dark:border-sky-800' : 'bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100'">
                        <i data-lucide="sliders-horizontal" class="w-3.5 h-3.5"></i>
                        <span>Filter</span>
                        @if($activeFiltersCount > 0)
                            <span class="w-5 h-5 rounded-full bg-brand-600 text-white text-[10px] font-black flex items-center justify-center">
                                {{ $activeFiltersCount }}
                            </span>
                        @endif
                    </button>

                    @if($search || $activeFiltersCount > 0)
                        <a href="{{ route('admin.biblio.index') }}" class="px-3 py-2 rounded-xl text-xs font-semibold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition-colors flex-shrink-0" title="Reset semua filter">
                            Reset
                        </a>
                    @endif
                </div>

                <div class="flex items-center gap-2 w-full lg:w-auto justify-end">
                    <a href="{{ route('admin.biblio.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs shadow-md shadow-brand-500/25 transition-all flex-shrink-0">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        <span>Tambah Buku Baru</span>
                    </a>
                </div>
            </div>

            <!-- Filter Panel (Collapsible or visible if active) -->
            <div x-show="showFilters" x-collapse x-cloak class="pt-4 border-t border-slate-100 dark:border-slate-800">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 text-xs">
                    
                    <!-- Format / GMD -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Format (GMD)</label>
                        <select name="gmd_id" onchange="this.form.submit()" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-brand-500">
                            <option value="">Semua Format</option>
                            @foreach($gmds as $g)
                                <option value="{{ $g->gmd_id }}" {{ $gmdId == $g->gmd_id ? 'selected' : '' }}>
                                    {{ $g->gmd_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Penerbit -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Penerbit</label>
                        <select name="publisher_id" onchange="this.form.submit()" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-brand-500">
                            <option value="">Semua Penerbit</option>
                            @foreach($publishers as $p)
                                <option value="{{ $p->publisher_id }}" {{ $publisherId == $p->publisher_id ? 'selected' : '' }}>
                                    {{ Str::limit($p->publisher_name, 22) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tahun Terbit -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Tahun Terbit</label>
                        <select name="year" onchange="this.form.submit()" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-brand-500">
                            <option value="">Semua Tahun</option>
                            @foreach($years as $y)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Ketersediaan Eksemplar -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Koleksi Fisik</label>
                        <select name="item_status" onchange="this.form.submit()" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-brand-500">
                            <option value="">Semua Koleksi</option>
                            <option value="has_items" {{ $itemStatus === 'has_items' ? 'selected' : '' }}>Ada Eksemplar Fisik</option>
                            <option value="no_items" {{ $itemStatus === 'no_items' ? 'selected' : '' }}>Tanpa Eksemplar Fisik</option>
                        </select>
                    </div>

                    <!-- Lampiran Digital / e-Book -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">File Digital</label>
                        <select name="has_file" onchange="this.form.submit()" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-brand-500">
                            <option value="">Semua Tipe</option>
                            <option value="yes" {{ $hasFile === 'yes' ? 'selected' : '' }}>Ada File PDF / e-Book</option>
                            <option value="no" {{ $hasFile === 'no' ? 'selected' : '' }}>Hanya Fisik (Tanpa File)</option>
                        </select>
                    </div>

                    <!-- Urutkan / Sort -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Urutkan</label>
                        <select name="sort" onchange="this.form.submit()" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-brand-500">
                            <option value="latest" {{ $sort === 'latest' ? 'selected' : '' }}>Terbaru Ditambahkan</option>
                            <option value="oldest" {{ $sort === 'oldest' ? 'selected' : '' }}>Terlama Ditambahkan</option>
                            <option value="title_asc" {{ $sort === 'title_asc' ? 'selected' : '' }}>Judul (A - Z)</option>
                            <option value="title_desc" {{ $sort === 'title_desc' ? 'selected' : '' }}>Judul (Z - A)</option>
                            <option value="year_desc" {{ $sort === 'year_desc' ? 'selected' : '' }}>Tahun (Terbaru)</option>
                            <option value="year_asc" {{ $sort === 'year_asc' ? 'selected' : '' }}>Tahun (Terlama)</option>
                        </select>
                    </div>

                </div>

                <!-- Active Filters Tags summary -->
                @if($activeFiltersCount > 0)
                    <div class="mt-3.5 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs flex-wrap gap-2">
                        <div class="flex items-center gap-2 flex-wrap text-[11px]">
                            <span class="text-slate-400 font-semibold">Filter aktif:</span>
                            @if($gmdId && $selectedGmd = $gmds->firstWhere('gmd_id', $gmdId))
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-brand-50 dark:bg-sky-950/80 text-brand-700 dark:text-sky-300 font-semibold">
                                    Format: {{ $selectedGmd->gmd_name }}
                                </span>
                            @endif
                            @if($publisherId && $selectedPub = $publishers->firstWhere('publisher_id', $publisherId))
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-brand-50 dark:bg-sky-950/80 text-brand-700 dark:text-sky-300 font-semibold">
                                    Penerbit: {{ Str::limit($selectedPub->publisher_name, 20) }}
                                </span>
                            @endif
                            @if($year)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-brand-50 dark:bg-sky-950/80 text-brand-700 dark:text-sky-300 font-semibold">
                                    Tahun: {{ $year }}
                                </span>
                            @endif
                            @if($itemStatus)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-brand-50 dark:bg-sky-950/80 text-brand-700 dark:text-sky-300 font-semibold">
                                    {{ $itemStatus === 'has_items' ? 'Ada Eksemplar Fisik' : 'Tanpa Eksemplar Fisik' }}
                                </span>
                            @endif
                            @if($hasFile)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-brand-50 dark:bg-sky-950/80 text-brand-700 dark:text-sky-300 font-semibold">
                                    {{ $hasFile === 'yes' ? 'Ada File Digital' : 'Hanya Fisik' }}
                                </span>
                            @endif
                        </div>

                        <a href="{{ route('admin.biblio.index') }}" class="text-rose-600 dark:text-rose-400 font-bold hover:underline text-[11px]">
                            Hapus Semua Filter
                        </a>
                    </div>
                @endif
            </div>

        </form>
    </div>

    <!-- Catalog Table Info -->
    <div class="flex items-center justify-between text-xs text-slate-500 px-1">
        <div>
            Menampilkan <strong>{{ $biblios->firstItem() ?? 0 }} - {{ $biblios->lastItem() ?? 0 }}</strong> dari total <strong>{{ number_format($biblios->total(), 0, ',', '.') }}</strong> koleksi katalog
        </div>
    </div>

    <!-- Catalog Table -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-600 dark:text-slate-300 font-bold uppercase tracking-wider border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="py-4 px-6 w-16">Cover</th>
                        <th class="py-4 px-6">Informasi Bibliografi</th>
                        <th class="py-4 px-6">No. Panggil / ISBN</th>
                        <th class="py-4 px-6">Penerbit & Tahun</th>
                        <th class="py-4 px-6 text-center">Eksemplar</th>
                        <th class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                    @forelse($biblios as $b)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                            <td class="py-3 px-6">
                                <div class="w-12 h-16 rounded-lg overflow-hidden bg-slate-100 dark:bg-slate-800 flex-shrink-0 shadow-sm">
                                    <img src="{{ $b->cover_url }}" alt="{{ $b->title }}" class="w-full h-full object-cover" onerror="this.src='{{ asset('images/default_cover.svg') }}'">
                                </div>
                            </td>
                            <td class="py-3 px-6 max-w-xs">
                                <div class="flex items-center gap-1.5 flex-wrap mb-1">
                                    @if($b->gmd)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                                            {{ $b->gmd->gmd_name }}
                                        </span>
                                    @endif
                                    @if(!empty($b->file_att))
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-50 dark:bg-indigo-950/80 text-indigo-700 dark:text-indigo-300">
                                            <i data-lucide="file-text" class="w-3 h-3"></i>
                                            <span>PDF</span>
                                        </span>
                                    @endif
                                </div>
                                <a href="{{ route('opac.show', $b->biblio_id) }}" target="_blank" class="font-bold text-sm text-slate-900 dark:text-white hover:text-brand-600 dark:hover:text-sky-400 transition-colors line-clamp-1">
                                    {{ $b->title }}
                                </a>
                                <div class="text-slate-500 text-[11px] truncate mt-0.5">
                                    {{ $b->author_names }}
                                </div>
                            </td>
                            <td class="py-3 px-6">
                                <div class="font-mono font-bold text-slate-800 dark:text-slate-200">{{ $b->call_number ?: '-' }}</div>
                                <div class="text-[10px] text-slate-400 font-mono">{{ $b->isbn_issn ?: '-' }}</div>
                            </td>
                            <td class="py-3 px-6 text-slate-600 dark:text-slate-400">
                                <div>{{ $b->publisher?->publisher_name ?: '-' }}</div>
                                <div class="text-[11px] text-slate-400">{{ $b->publish_year ?: '-' }}</div>
                            </td>
                            <td class="py-3 px-6 text-center">
                                <a href="{{ route('admin.biblio.items', $b->biblio_id) }}" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-slate-100 dark:bg-slate-800 hover:bg-brand-50 text-slate-700 dark:text-slate-300 hover:text-brand-600 transition-colors">
                                    <i data-lucide="barcode" class="w-3.5 h-3.5"></i>
                                    <span>{{ $b->items->count() }} Eks</span>
                                </a>
                            </td>
                            <td class="py-3 px-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.biblio.items', $b->biblio_id) }}" class="p-2 rounded-lg bg-sky-50 dark:bg-sky-950/60 text-sky-600 dark:text-sky-400 hover:bg-sky-100 transition-colors" title="Kelola Eksemplar">
                                        <i data-lucide="barcode" class="w-4 h-4"></i>
                                    </a>
                                    <a href="{{ route('admin.biblio.edit', $b->biblio_id) }}" class="p-2 rounded-lg bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 hover:bg-amber-100 transition-colors" title="Ubah">
                                        <i data-lucide="pencil" class="w-4 h-4"></i>
                                    </a>
                                    <form action="{{ route('admin.biblio.destroy', $b->biblio_id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus buku ini beserta seluruh eksemplarnya?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 rounded-lg bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 hover:bg-rose-100 transition-colors" title="Hapus">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400">Tidak ada data katalog buku.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100 dark:border-slate-800">
            {{ $biblios->links() }}
        </div>
    </div>
</div>
@endsection
