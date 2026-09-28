@extends('layouts.admin')

@section('title', 'Master Pengarang')
@section('header_title', 'Kelola Master Data Pengarang')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
    <div class="lg:col-span-4">
        <form action="{{ route('admin.master.authors.store') }}" method="POST" class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm space-y-4">
            @csrf
            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="plus" class="w-4 h-4 text-brand-500"></i>
                Tambah Pengarang Baru
            </h3>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Nama Pengarang *</label>
                <input type="text" name="author_name" required placeholder="Contoh: Prof. Dr. Andi Wijaya" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-medium text-slate-900 dark:text-white focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Tipe Otoritas</label>
                <select name="authority_type" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none">
                    <option value="p">Orang Pribadi (Personal Name)</option>
                    <option value="o">Organisasi / Lembaga (Organizational Body)</option>
                    <option value="c">Konferensi / Pertemuan (Conference)</option>
                </select>
            </div>

            <button type="submit" class="w-full py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs shadow-md transition-colors">
                Simpan Pengarang
            </button>
        </form>
    </div>

    <div class="lg:col-span-8 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm">
        <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white">Daftar Pengarang ({{ $authors->total() }})</h3>
            <form action="{{ route('admin.master.authors') }}" method="GET" class="flex items-center gap-2">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama pengarang..." class="px-3 py-1.5 rounded-lg border border-slate-300 dark:border-slate-700 text-xs bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white">
                <button type="submit" class="px-3 py-1.5 bg-slate-800 text-white text-xs font-bold rounded-lg">Cari</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-600 dark:text-slate-300 font-bold uppercase tracking-wider border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="py-3.5 px-6">Nama Pengarang</th>
                        <th class="py-3.5 px-6">Tipe</th>
                        <th class="py-3.5 px-6 text-center">Jumlah Buku</th>
                        <th class="py-3.5 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                    @forelse($authors as $a)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                            <td class="py-3 px-6 font-bold text-slate-900 dark:text-white">{{ $a->author_name }}</td>
                            <td class="py-3 px-6 text-slate-500">
                                {{ $a->authority_type == 'p' ? 'Pribadi' : ($a->authority_type == 'o' ? 'Organisasi' : 'Konferensi') }}
                            </td>
                            <td class="py-3 px-6 text-center font-bold text-brand-600 dark:text-sky-400">
                                {{ $a->biblios_count }}
                            </td>
                            <td class="py-3 px-6 text-right">
                                @if($a->biblios_count == 0)
                                    <form action="{{ route('admin.master.authors.delete', $a->author_id) }}" method="POST" onsubmit="return confirm('Hapus pengarang ini?')">
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
                            <td colspan="4" class="p-6 text-center text-slate-400">Tidak ada data pengarang.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100 dark:border-slate-800">
            {{ $authors->links() }}
        </div>
    </div>
</div>
@endsection
