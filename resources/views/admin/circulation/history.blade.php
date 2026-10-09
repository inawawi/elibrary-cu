@extends('layouts.admin')

@section('title', 'Riwayat Sirkulasi')
@section('header_title', 'Catatan Riwayat & Transaksi Sirkulasi')

@section('content')
<div class="space-y-6">
    <!-- Header Controls: Tabs & Search -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
        <!-- Status Tabs -->
        <div class="flex items-center gap-1.5 p-1 bg-slate-100 dark:bg-slate-800/80 rounded-2xl w-full md:w-auto overflow-x-auto">
            <a href="{{ route('admin.circulation.history', ['status' => 'all', 'search' => $search]) }}"
               class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ ($status ?? 'all') === 'all' ? 'bg-white dark:bg-slate-900 text-brand-600 dark:text-sky-400 shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900' }}">
                Semua ({{ $stats['total'] ?? 0 }})
            </a>
            <a href="{{ route('admin.circulation.history', ['status' => 'returned', 'search' => $search]) }}"
               class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ ($status ?? '') === 'returned' ? 'bg-white dark:bg-slate-900 text-emerald-600 dark:text-emerald-400 shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900' }}">
                Sudah Kembali ({{ $stats['returned'] ?? 0 }})
            </a>
            <a href="{{ route('admin.circulation.history', ['status' => 'renewed', 'search' => $search]) }}"
               class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ ($status ?? '') === 'renewed' ? 'bg-white dark:bg-slate-900 text-purple-600 dark:text-purple-400 shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900' }}">
                Diperpanjang ({{ $stats['renewed'] ?? 0 }})
            </a>
            <a href="{{ route('admin.circulation.history', ['status' => 'active', 'search' => $search]) }}"
               class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ ($status ?? '') === 'active' ? 'bg-white dark:bg-slate-900 text-sky-600 dark:text-sky-400 shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900' }}">
                Sedang Dipinjam ({{ $stats['active'] ?? 0 }})
            </a>
        </div>

        <!-- Search Form -->
        <form action="{{ route('admin.circulation.history') }}" method="GET" class="flex items-center gap-3 w-full md:w-auto flex-grow max-w-md">
            <input type="hidden" name="status" value="{{ $status }}">
            <div class="relative flex-grow">
                <i data-lucide="search" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text"
                       name="search"
                       value="{{ $search }}"
                       placeholder="Cari kode buku, nama peminjam, atau NIM..."
                       class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-brand-500">
            </div>
            <button type="submit" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-xl transition-colors">
                Cari
            </button>
            @if($search)
                <a href="{{ route('admin.circulation.history', ['status' => $status]) }}" class="text-xs text-rose-500 hover:underline">Reset</a>
            @endif
        </form>
    </div>

    <!-- Data Table -->
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
                        <th class="py-4 px-6">Status & Perpanjangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                    @forelse($histories as $hist)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                            <td class="py-3 px-6 max-w-sm">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-14 rounded-lg overflow-hidden bg-slate-100 dark:bg-slate-800 flex-shrink-0 border border-slate-200 dark:border-slate-700">
                                        <img src="{{ $hist->item?->biblio?->cover_url ?: asset('images/default_cover.svg') }}" class="w-full h-full object-cover">
                                    </div>
                                    <div>
                                        <span class="font-mono font-bold text-brand-600 dark:text-sky-400">{{ $hist->item_code }}</span>
                                        <div class="font-bold text-slate-900 dark:text-white line-clamp-1" title="{{ $hist->item?->biblio?->title ?: 'Judul Buku' }}">
                                            {{ $hist->item?->biblio?->title ?: 'Judul Buku' }}
                                        </div>
                                    </div>
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
                            <td class="py-3 px-6 font-bold {{ $hist->is_return ? 'text-emerald-600' : 'text-slate-400' }}">
                                {{ $hist->return_date ? \Carbon\Carbon::parse($hist->return_date)->format('d/m/Y') : ($hist->is_return ? 'Sudah Kembali' : '-') }}
                            </td>
                            <td class="py-3 px-6">
                                <div class="flex flex-col gap-1 items-start">
                                    @if($hist->is_return)
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-950/80 dark:text-emerald-300 inline-flex items-center gap-1">
                                            <i data-lucide="check" class="w-3 h-3"></i> Sudah Kembali
                                        </span>
                                    @elseif($hist->isOverdue())
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700 dark:bg-rose-950/80 dark:text-rose-300 inline-flex items-center gap-1">
                                            <i data-lucide="alert-circle" class="w-3 h-3"></i> Terlambat
                                        </span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-sky-100 text-sky-700 dark:bg-sky-950/80 dark:text-sky-300 inline-flex items-center gap-1">
                                            <i data-lucide="clock" class="w-3 h-3"></i> Sedang Dipinjam
                                        </span>
                                    @endif

                                    @if($hist->renewed > 0)
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-purple-700 dark:bg-purple-950/80 dark:text-purple-300 inline-flex items-center gap-1" title="Pernah diperpanjang {{ $hist->renewed }} kali">
                                            <i data-lucide="rotate-cw" class="w-3 h-3"></i> Diperpanjang {{ $hist->renewed }}x
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400">Tidak ada catatan sirkulasi ditemukan.</td>
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
