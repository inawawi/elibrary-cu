@extends('layouts.admin')

@section('title', 'Katalog Bibliografi')
@section('header_title', 'Manajemen Katalog Buku (Bibliografi)')

@section('content')
<div class="space-y-6">
    <!-- Header with Add Button & Search -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
        <form action="{{ route('admin.biblio.index') }}" method="GET" class="flex flex-wrap items-center gap-3 w-full sm:w-auto flex-grow max-w-2xl">
            <div class="relative flex-grow">
                <i data-lucide="search" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text"
                       name="search"
                       value="{{ $search }}"
                       placeholder="Cari judul, pengarang, ISBN, atau no. panggil..."
                       class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-brand-500">
            </div>

            <button type="submit" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-xl transition-colors">
                Cari
            </button>
            @if($search || $publisherId || $gmdId)
                <a href="{{ route('admin.biblio.index') }}" class="text-xs text-rose-500 hover:underline">Reset</a>
            @endif
        </form>

        <a href="{{ route('admin.biblio.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs shadow-md shadow-brand-500/25 transition-all flex-shrink-0">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Tambah Buku Baru</span>
        </a>
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
