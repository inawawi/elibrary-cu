@extends('layouts.admin')

@section('title', 'Master Penerbit')
@section('header_title', 'Kelola Master Data Penerbit')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
    <div class="lg:col-span-4">
        <form action="{{ route('admin.master.publishers.store') }}" method="POST" class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm space-y-4">
            @csrf
            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="plus" class="w-4 h-4 text-brand-500"></i>
                Tambah Penerbit Baru
            </h3>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Nama Penerbit *</label>
                <input type="text" name="publisher_name" required placeholder="Contoh: Andi Offset / Gramedia" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-medium text-slate-900 dark:text-white focus:outline-none">
            </div>

            <button type="submit" class="w-full py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs shadow-md transition-colors">
                Simpan Penerbit
            </button>
        </form>
    </div>

    <div class="lg:col-span-8 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm">
        <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white">Daftar Penerbit ({{ $publishers->total() }})</h3>
            <form action="{{ route('admin.master.publishers') }}" method="GET" class="flex items-center gap-2">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama penerbit..." class="px-3 py-1.5 rounded-lg border border-slate-300 dark:border-slate-700 text-xs bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white">
                <button type="submit" class="px-3 py-1.5 bg-slate-800 text-white text-xs font-bold rounded-lg">Cari</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-600 dark:text-slate-300 font-bold uppercase tracking-wider border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="py-3.5 px-6">Nama Penerbit</th>
                        <th class="py-3.5 px-6 text-center">Jumlah Buku</th>
                        <th class="py-3.5 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                    @forelse($publishers as $p)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                            <td class="py-3 px-6 font-bold text-slate-900 dark:text-white">{{ $p->publisher_name }}</td>
                            <td class="py-3 px-6 text-center font-bold text-brand-600 dark:text-sky-400">
                                {{ $p->biblios_count }}
                            </td>
                            <td class="py-3 px-6 text-right">
                                @if($p->biblios_count == 0)
                                    <form action="{{ route('admin.master.publishers.delete', $p->publisher_id) }}" method="POST" onsubmit="return confirm('Hapus penerbit ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1 text-rose-500 hover:text-rose-700">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                @else
                                    <span class="text-slate-300 text-[10px]">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="p-6 text-center text-slate-400">Tidak ada data penerbit.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100 dark:border-slate-800">
            {{ $publishers->links() }}
        </div>
    </div>
</div>
@endsection
