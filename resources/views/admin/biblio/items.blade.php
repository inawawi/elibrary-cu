@extends('layouts.admin')

@section('title', 'Kelola Eksemplar - ' . $biblio->title)
@section('header_title', 'Eksemplar Fisik & Barcode')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.biblio.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-slate-800 dark:hover:text-white">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Kembali ke Katalog Buku</span>
        </a>
    </div>

    <!-- Book Summary Card -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm flex items-center gap-5">
        <div class="w-16 h-20 rounded-xl overflow-hidden bg-slate-100 dark:bg-slate-800 flex-shrink-0 shadow">
            <img src="{{ $biblio->cover_url }}" alt="Cover" class="w-full h-full object-cover">
        </div>
        <div>
            <span class="text-xs font-mono font-bold text-brand-600 dark:text-sky-400">NO. PANGGIL: {{ $biblio->call_number ?: '-' }}</span>
            <h1 class="text-lg font-black text-slate-900 dark:text-white line-clamp-1">{{ $biblio->title }}</h1>
            <p class="text-xs text-slate-400 mt-0.5">{{ $biblio->author_names }} • {{ $biblio->publisher?->publisher_name }} ({{ $biblio->publish_year }})</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Add Copy Form -->
        <div class="lg:col-span-4">
            <form action="{{ route('admin.biblio.items', $biblio->biblio_id) }}" method="POST" class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm space-y-4">
                @csrf
                <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="plus" class="w-4 h-4 text-brand-500"></i>
                    Tambah Eksemplar Baru
                </h3>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Kode Barcode *</label>
                    <input type="text" name="item_code" required placeholder="Contoh: B00123" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-mono text-slate-900 dark:text-white font-bold focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Lokasi Rak *</label>
                    <select name="location_id" required class="w-full px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none">
                        @foreach($locations as $l)
                            <option value="{{ $l->location_id }}">{{ $l->location_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Tipe Koleksi *</label>
                    <select name="coll_type_id" required class="w-full px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none">
                        @foreach($collTypes as $ct)
                            <option value="{{ $ct->coll_type_id }}">{{ $ct->coll_type_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Harga Buku (Rp)</label>
                    <input type="number" name="price" placeholder="0" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white font-medium focus:outline-none">
                </div>

                <button type="submit" class="w-full py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs shadow-md transition-colors">
                    Simpan Eksemplar
                </button>
            </form>
        </div>

        <!-- Copies List Table -->
        <div class="lg:col-span-8 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-4 flex items-center justify-between">
                <span>Daftar Salinan di Rak Perpustakaan</span>
                <span class="text-xs font-normal text-slate-400">Total {{ $biblio->items->count() }} Eksemplar</span>
            </h3>

            @if($biblio->items->isEmpty())
                <div class="p-8 text-center bg-slate-50 dark:bg-slate-800/40 rounded-2xl text-xs text-slate-400">
                    Belum ada eksemplar fisik untuk judul ini. Silakan gunakan formulir di samping untuk menambahkan barcode.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="text-slate-400 font-bold uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 pb-2">
                            <tr>
                                <th class="py-2.5">Kode Eksemplar</th>
                                <th class="py-2.5">Lokasi Rak</th>
                                <th class="py-2.5">Tipe Koleksi</th>
                                <th class="py-2.5">Status Pinjam</th>
                                <th class="py-2.5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                            @foreach($biblio->items as $item)
                                <tr>
                                    <td class="py-3 font-mono font-bold text-slate-900 dark:text-white">
                                        {{ $item->item_code }}
                                    </td>
                                    <td class="py-3 text-slate-600 dark:text-slate-400">
                                        {{ $item->location?->location_name ?: '-' }}
                                    </td>
                                    <td class="py-3 text-slate-600 dark:text-slate-400">
                                        {{ $item->collType?->coll_type_name ?: '-' }}
                                    </td>
                                    <td class="py-3">
                                        @if($item->activeLoan)
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300">
                                                Dipinjam ({{ $item->activeLoan->member?->member_name ?: $item->activeLoan->member_id }})
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">
                                                Tersedia
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 text-right">
                                        <form action="{{ route('admin.biblio.item.delete', $item->item_id) }}" method="POST" onsubmit="return confirm('Hapus eksemplar ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/60" title="Hapus">
                                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
