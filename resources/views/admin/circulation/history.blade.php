@extends('layouts.admin')

@section('title', 'Riwayat Sirkulasi')
@section('header_title', 'Catatan Riwayat Pengembalian Buku')

@section('content')
<div class="space-y-6">
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
        <form action="{{ route('admin.circulation.history') }}" method="GET" class="flex items-center gap-3 w-full sm:w-auto flex-grow max-w-md">
            <div class="relative flex-grow">
                <i data-lucide="search" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text"
                       name="search"
                       value="{{ $search }}"
                       placeholder="Cari kode buku, nama peminjam, atau NIM..."
                       class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white font-medium focus:outline-none">
            </div>
            <button type="submit" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-xl transition-colors">
                Cari
            </button>
            @if($search)
                <a href="{{ route('admin.circulation.history') }}" class="text-xs text-rose-500 hover:underline">Reset</a>
            @endif
        </form>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-600 dark:text-slate-300 font-bold uppercase tracking-wider border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="py-4 px-6">Barcode / Buku</th>
                        <th class="py-4 px-6">Peminjam</th>
                        <th class="py-4 px-6">Tgl Pinjam</th>
                        <th class="py-4 px-6">Batas Waktu</th>
                        <th class="py-4 px-6">Tgl Kembali</th>
                        <th class="py-4 px-6">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                    @forelse($histories as $hist)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                            <td class="py-3 px-6 max-w-xs">
                                <div class="font-mono font-bold text-brand-600 dark:text-sky-400">{{ $hist->item_code }}</div>
                                <div class="font-bold text-slate-900 dark:text-white line-clamp-1" title="{{ $hist->item?->biblio?->title }}">
                                    {{ $hist->item?->biblio?->title ?: 'Judul Buku' }}
                                </div>
                            </td>
                            <td class="py-3 px-6">
                                <div class="font-bold text-slate-900 dark:text-white">{{ $hist->member?->member_name ?: $hist->member_id }}</div>
                                <div class="font-mono text-[10px] text-slate-400">{{ $hist->member_id }}</div>
                            </td>
                            <td class="py-3 px-6 text-slate-500">
                                {{ $hist->loan_date ? \Carbon\Carbon::parse($hist->loan_date)->format('d/m/Y') : '-' }}
                            </td>
                            <td class="py-3 px-6 text-slate-500">
                                {{ $hist->due_date ? \Carbon\Carbon::parse($hist->due_date)->format('d/m/Y') : '-' }}
                            </td>
                            <td class="py-3 px-6 font-bold text-emerald-600">
                                {{ $hist->return_date ? \Carbon\Carbon::parse($hist->return_date)->format('d/m/Y') : '-' }}
                            </td>
                            <td class="py-3 px-6">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">
                                    Sudah Selesai
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400">Tidak ada riwayat sirkulasi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100 dark:border-slate-800">
            {{ $histories->links() }}
        </div>
    </div>
</div>
@endsection
