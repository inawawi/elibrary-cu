@extends('layouts.admin')

@section('title', 'Daftar Pinjaman Aktif')
@section('header_title', 'Pinjaman Berjalan & Monitoring Keterlambatan')

@section('content')
<div class="space-y-6">
    <!-- Header with Filter Tabs -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.circulation.active', ['status' => 'all']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $status === 'all' ? 'bg-brand-600 text-white shadow-md' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200' }}">
                Semua Pinjaman Aktif
            </a>
            <a href="{{ route('admin.circulation.active', ['status' => 'overdue']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $status === 'overdue' ? 'bg-rose-600 text-white shadow-md' : 'bg-slate-100 dark:bg-slate-800 text-rose-600 hover:bg-rose-50' }}">
                Hanya Terlambat
            </a>
        </div>

        <a href="{{ route('admin.circulation.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs transition-colors">
            <i data-lucide="repeat" class="w-4 h-4"></i>
            <span>Buka Meja Sirkulasi</span>
        </a>
    </div>

    <!-- Active Loans Table -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-600 dark:text-slate-300 font-bold uppercase tracking-wider border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="py-4 px-6">Barcode / Buku</th>
                        <th class="py-4 px-6">Peminjam</th>
                        <th class="py-4 px-6">Tgl Pinjam</th>
                        <th class="py-4 px-6">Batas Waktu</th>
                        <th class="py-4 px-6">Status / Denda</th>
                        <th class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                    @forelse($loans as $loan)
                        @php
                            $isOverdue = $loan->isOverdue();
                            $days = $loan->overdueDays();
                            $fine = $loan->calculateFine();
                        @endphp
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors {{ $isOverdue ? 'bg-rose-50/30 dark:bg-rose-950/20' : '' }}">
                            <td class="py-3.5 px-6 max-w-xs">
                                <div class="font-mono font-bold text-brand-600 dark:text-sky-400">{{ $loan->item_code }}</div>
                                <div class="font-bold text-slate-900 dark:text-white line-clamp-1 text-sm mt-0.5" title="{{ $loan->item?->biblio?->title }}">
                                    {{ $loan->item?->biblio?->title ?: 'Judul Buku' }}
                                </div>
                            </td>
                            <td class="py-3.5 px-6">
                                <div class="font-bold text-slate-900 dark:text-white">{{ $loan->member?->member_name ?: $loan->member_id }}</div>
                                <div class="font-mono text-[10px] text-slate-400">{{ $loan->member_id }} • {{ $loan->member?->memberType?->member_type_name }}</div>
                            </td>
                            <td class="py-3.5 px-6 text-slate-500">
                                {{ \Carbon\Carbon::parse($loan->loan_date)->format('d/m/Y') }}
                            </td>
                            <td class="py-3.5 px-6 font-bold {{ $isOverdue ? 'text-rose-600' : 'text-slate-800 dark:text-slate-200' }}">
                                {{ \Carbon\Carbon::parse($loan->due_date)->format('d/m/Y') }}
                            </td>
                            <td class="py-3.5 px-6">
                                @if($isOverdue)
                                    <div class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300 inline-block">
                                        Terlambat {{ $days }} Hari
                                    </div>
                                    <div class="text-[11px] font-black text-rose-600 mt-0.5">Denda: Rp {{ number_format($fine, 0, ',', '.') }}</div>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">
                                        Masa Pinjam Aktif
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-6 text-right">
                                <form action="{{ route('admin.circulation.return') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="item_code" value="{{ $loan->item_code }}">
                                    <button type="submit" class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm transition-colors inline-flex items-center gap-1.5">
                                        <i data-lucide="check" class="w-3.5 h-3.5"></i>
                                        <span>Kembalikan</span>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400">Tidak ada data pinjaman aktif.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100 dark:border-slate-800">
            {{ $loans->links() }}
        </div>
    </div>
</div>
@endsection
