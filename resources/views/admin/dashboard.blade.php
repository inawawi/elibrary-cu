@extends('layouts.admin')

@section('title', 'Dashboard Perpustakaan')
@section('header_title', 'Ringkasan & Dasbor Eksekutif')

@section('content')
<div class="space-y-8">
    <!-- Stat Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-7 gap-4">
        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="w-10 h-10 rounded-xl bg-sky-100 dark:bg-sky-950 text-sky-600 dark:text-sky-400 flex items-center justify-center mb-3">
                <i data-lucide="book-copy" class="w-5 h-5"></i>
            </div>
            <div class="text-2xl font-black text-slate-900 dark:text-white">{{ number_format($stats['total_biblio']) }}</div>
            <div class="text-xs text-slate-400 font-medium">Judul Buku</div>
        </div>

        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="w-10 h-10 rounded-xl bg-indigo-100 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 flex items-center justify-center mb-3">
                <i data-lucide="copy" class="w-5 h-5"></i>
            </div>
            <div class="text-2xl font-black text-slate-900 dark:text-white">{{ number_format($stats['total_items']) }}</div>
            <div class="text-xs text-slate-400 font-medium">Eksemplar Fisik</div>
        </div>

        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mb-3">
                <i data-lucide="users" class="w-5 h-5"></i>
            </div>
            <div class="text-2xl font-black text-slate-900 dark:text-white">{{ number_format($stats['total_members']) }}</div>
            <div class="text-xs text-slate-400 font-medium">Anggota Terdaftar</div>
        </div>

        <a href="{{ route('admin.circulation.reserves') }}" class="p-5 rounded-2xl bg-white dark:bg-slate-900 border {{ ($stats['pending_reserves'] ?? 0) > 0 ? 'border-amber-300 dark:border-amber-700 bg-amber-50/30' : 'border-slate-200 dark:border-slate-800' }} shadow-sm hover:border-amber-400 transition-all block">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-950 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                    <i data-lucide="bookmark-check" class="w-5 h-5"></i>
                </div>
                @if(($stats['pending_reserves'] ?? 0) > 0)
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-ping"></span>
                @endif
            </div>
            <div class="text-2xl font-black text-amber-600 dark:text-amber-400">{{ number_format($stats['pending_reserves'] ?? 0) }}</div>
            <div class="text-xs text-slate-400 font-medium">Buku Direservasi</div>
        </a>

        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-950 text-amber-600 dark:text-amber-400 flex items-center justify-center mb-3">
                <i data-lucide="repeat" class="w-5 h-5"></i>
            </div>
            <div class="text-2xl font-black text-slate-900 dark:text-white">{{ number_format($stats['active_loans']) }}</div>
            <div class="text-xs text-slate-400 font-medium">Pinjaman Berjalan</div>
        </div>

        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="w-10 h-10 rounded-xl bg-rose-100 dark:bg-rose-950 text-rose-600 dark:text-rose-400 flex items-center justify-center mb-3">
                <i data-lucide="alert-triangle" class="w-5 h-5"></i>
            </div>
            <div class="text-2xl font-black text-rose-600 dark:text-rose-400">{{ number_format($stats['overdue_loans']) }}</div>
            <div class="text-xs text-slate-400 font-medium">Keterlambatan</div>
        </div>

        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="w-10 h-10 rounded-xl bg-purple-100 dark:bg-purple-950 text-purple-600 dark:text-purple-400 flex items-center justify-center mb-3">
                <i data-lucide="clipboard-list" class="w-5 h-5"></i>
            </div>
            <div class="text-2xl font-black text-slate-900 dark:text-white">{{ number_format($stats['today_visitors']) }}</div>
            <div class="text-xs text-slate-400 font-medium">Pengunjung Hari Ini</div>
        </div>
    </div>

    <!-- Quick Actions Banner -->
    <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Aksi Cepat Pustakawan</h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-7 gap-3">
            <a href="{{ route('admin.circulation.index') }}" class="p-3.5 rounded-2xl bg-brand-50 hover:bg-brand-100 dark:bg-sky-950/50 dark:hover:bg-sky-900/50 border border-brand-200 dark:border-sky-800 text-brand-700 dark:text-sky-300 font-bold text-xs flex flex-col sm:flex-row items-center gap-2.5 transition-colors">
                <div class="w-8 h-8 rounded-xl bg-brand-600 text-white flex items-center justify-center flex-shrink-0">
                    <i data-lucide="repeat" class="w-4 h-4"></i>
                </div>
                <span>Sirkulasi</span>
            </a>

            <a href="{{ route('admin.circulation.reserves') }}" class="p-3.5 rounded-2xl bg-amber-50 hover:bg-amber-100 dark:bg-amber-950/50 dark:hover:bg-amber-900/50 border border-amber-200 dark:border-amber-800 text-amber-700 dark:text-amber-300 font-bold text-xs flex flex-col sm:flex-row items-center gap-2.5 transition-colors relative">
                <div class="w-8 h-8 rounded-xl bg-amber-500 text-white flex items-center justify-center flex-shrink-0">
                    <i data-lucide="bookmark-check" class="w-4 h-4"></i>
                </div>
                <span>Booking Buku</span>
                @if(($stats['pending_reserves'] ?? 0) > 0)
                    <span class="px-1.5 py-0.2 rounded-full text-[9px] font-black bg-amber-600 text-white ml-auto">
                        {{ $stats['pending_reserves'] }}
                    </span>
                @endif
            </a>

            <a href="{{ route('admin.biblio.create') }}" class="p-3.5 rounded-2xl bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-950/50 dark:hover:bg-indigo-900/50 border border-indigo-200 dark:border-indigo-800 text-indigo-700 dark:text-indigo-300 font-bold text-xs flex flex-col sm:flex-row items-center gap-2.5 transition-colors">
                <div class="w-8 h-8 rounded-xl bg-indigo-600 text-white flex items-center justify-center flex-shrink-0">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i>
                </div>
                <span>Buku Baru</span>
            </a>

            <a href="{{ route('admin.skripsi.create') }}" class="p-3.5 rounded-2xl bg-purple-50 hover:bg-purple-100 dark:bg-purple-950/50 dark:hover:bg-purple-900/50 border border-purple-200 dark:border-purple-800 text-purple-700 dark:text-purple-300 font-bold text-xs flex flex-col sm:flex-row items-center gap-2.5 transition-colors">
                <div class="w-8 h-8 rounded-xl bg-purple-600 text-white flex items-center justify-center flex-shrink-0">
                    <i data-lucide="graduation-cap" class="w-4 h-4"></i>
                </div>
                <span>Data Skripsi</span>
            </a>

            <a href="{{ route('admin.ebook.create') }}" class="p-3.5 rounded-2xl bg-teal-50 hover:bg-teal-100 dark:bg-teal-950/50 dark:hover:bg-teal-900/50 border border-teal-200 dark:border-teal-800 text-teal-700 dark:text-teal-300 font-bold text-xs flex flex-col sm:flex-row items-center gap-2.5 transition-colors">
                <div class="w-8 h-8 rounded-xl bg-teal-600 text-white flex items-center justify-center flex-shrink-0">
                    <i data-lucide="tablet" class="w-4 h-4"></i>
                </div>
                <span>Data e-Book</span>
            </a>

            <a href="{{ route('admin.member.create') }}" class="p-3.5 rounded-2xl bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/50 dark:hover:bg-emerald-900/50 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 font-bold text-xs flex flex-col sm:flex-row items-center gap-2.5 transition-colors">
                <div class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center flex-shrink-0">
                    <i data-lucide="user-plus" class="w-4 h-4"></i>
                </div>
                <span>Anggota Baru</span>
            </a>

            <a href="{{ route('admin.circulation.active', ['status' => 'overdue']) }}" class="p-3.5 rounded-2xl bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/50 dark:hover:bg-rose-900/50 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-300 font-bold text-xs flex flex-col sm:flex-row items-center gap-2.5 transition-colors">
                <div class="w-8 h-8 rounded-xl bg-rose-600 text-white flex items-center justify-center flex-shrink-0">
                    <i data-lucide="alert-circle" class="w-4 h-4"></i>
                </div>
                <span>Cek Denda</span>
            </a>
        </div>
    </div>

    <!-- Recent Bookings Alert & Action -->
    @if(isset($recentReserves) && $recentReserves->isNotEmpty())
        <div class="p-6 rounded-3xl bg-gradient-to-r from-amber-500 to-amber-600 text-white shadow-lg relative overflow-hidden">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-white/20 flex items-center justify-center">
                        <i data-lucide="bookmark-check" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <h3 class="font-black text-base text-white">Booking / Reservasi Buku Terbaru</h3>
                        <p class="text-xs text-amber-100">Ada {{ $stats['pending_reserves'] }} pemesanan buku dari member yang menunggu pengambilan</p>
                    </div>
                </div>
                <a href="{{ route('admin.circulation.reserves') }}" class="px-4 py-2 rounded-xl bg-white text-amber-900 hover:bg-amber-50 text-xs font-black shadow transition-all flex items-center gap-1.5 self-start sm:self-auto">
                    <span>Lihat Semua Reservasi</span>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($recentReserves as $res)
                    <div class="p-3.5 rounded-2xl bg-white/10 backdrop-blur-sm border border-white/15 flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <div class="flex items-center gap-1.5 mb-0.5">
                                <span class="font-mono text-xs font-black bg-white text-amber-900 px-2 py-0.5 rounded">
                                    {{ $res->item_code }}
                                </span>
                                <span class="text-[10px] text-amber-100 truncate">
                                    {{ $res->member?->member_name ?: $res->member_id }}
                                </span>
                            </div>
                            <h4 class="font-bold text-xs text-white truncate max-w-[200px]" title="{{ $res->biblio?->title }}">
                                {{ $res->biblio?->title ?: 'Judul Buku' }}
                            </h4>
                            <div class="text-[10px] text-amber-200">
                                {{ $res->reserve_date ? \Carbon\Carbon::parse($res->reserve_date)->diffForHumans() : '-' }}
                            </div>
                        </div>

                        <form action="{{ route('admin.circulation.loan') }}" method="POST" class="flex-shrink-0">
                            @csrf
                            <input type="hidden" name="member_id" value="{{ $res->member_id }}">
                            <input type="hidden" name="item_code" value="{{ $res->item_code }}">
                            <button type="submit" class="px-3 py-1.5 rounded-xl bg-white text-amber-900 hover:bg-amber-50 text-xs font-black shadow transition-all flex items-center gap-1 cursor-pointer" title="Langsung proses pinjam">
                                <i data-lucide="zap" class="w-3 h-3 text-amber-600"></i>
                                <span>Pinjamkan</span>
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Tables: Recent Loans & Overdues -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Recent Loans -->
        <div class="lg:col-span-7 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="font-bold text-base text-slate-900 dark:text-white">Aktivitas Peminjaman Terbaru</h3>
                    <p class="text-xs text-slate-400">Transaksi sirkulasi terakhir yang tercatat</p>
                </div>
                <a href="{{ route('admin.circulation.active') }}" class="text-xs font-bold text-brand-600 dark:text-sky-400 hover:underline">Lihat Semua</a>
            </div>

            @if($recentLoans->isEmpty())
                <div class="p-8 text-center text-xs text-slate-400">Belum ada transaksi peminjaman.</div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="text-slate-400 uppercase tracking-wider font-bold border-b border-slate-100 dark:border-slate-800 pb-2">
                            <tr>
                                <th class="py-2.5">Peminjam</th>
                                <th class="py-2.5">Buku Dipinjam</th>
                                <th class="py-2.5">Tgl Pinjam</th>
                                <th class="py-2.5">Batas Kembali</th>
                                <th class="py-2.5">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                            @foreach($recentLoans as $loan)
                                <tr>
                                    <td class="py-3 font-semibold text-slate-800 dark:text-slate-200">
                                        {{ $loan->member?->member_name ?: $loan->member_id }}
                                        <div class="font-mono text-[10px] text-slate-400">{{ $loan->member_id }}</div>
                                    </td>
                                    <td class="py-3 max-w-[200px] truncate text-slate-700 dark:text-slate-300" title="{{ $loan->item?->biblio?->title }}">
                                        {{ $loan->item?->biblio?->title ?: $loan->item_code }}
                                        <div class="font-mono text-[10px] text-brand-600">{{ $loan->item_code }}</div>
                                    </td>
                                    <td class="py-3 text-slate-500">{{ \Carbon\Carbon::parse($loan->loan_date)->format('d/m/Y') }}</td>
                                    <td class="py-3 font-bold {{ $loan->isOverdue() ? 'text-rose-600' : 'text-slate-700 dark:text-slate-300' }}">
                                        {{ \Carbon\Carbon::parse($loan->due_date)->format('d/m/Y') }}
                                    </td>
                                    <td class="py-3">
                                        @if($loan->is_return)
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400">Kembali</span>
                                        @elseif($loan->isOverdue())
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 dark:bg-rose-950 text-rose-700 dark:text-rose-300">Terlambat</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300">Dipinjam</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Overdue Loans Alert -->
        <div class="lg:col-span-5 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="font-bold text-base text-slate-900 dark:text-white flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-500 animate-pulse"></span>
                        Perhatian Keterlambatan
                    </h3>
                    <p class="text-xs text-slate-400">Anggota dengan buku lewat batas waktu</p>
                </div>
                <a href="{{ route('admin.circulation.active', ['status' => 'overdue']) }}" class="text-xs font-bold text-rose-600 dark:text-rose-400 hover:underline">Kelola</a>
            </div>

            @if($overdueLoansList->isEmpty())
                <div class="p-8 text-center bg-slate-50 dark:bg-slate-800/40 rounded-2xl text-xs text-slate-400">
                    <i data-lucide="check-circle" class="w-8 h-8 text-emerald-500 mx-auto mb-2"></i>
                    Semua pinjaman buku tepat waktu!
                </div>
            @else
                <div class="space-y-3">
                    @foreach($overdueLoansList as $od)
                        <div class="p-3.5 rounded-2xl bg-rose-50/60 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-900/60 flex items-center justify-between text-xs">
                            <div class="max-w-[200px]">
                                <div class="font-bold text-slate-900 dark:text-white">{{ $od->member?->member_name ?: $od->member_id }}</div>
                                <div class="text-[11px] text-slate-500 truncate" title="{{ $od->item?->biblio?->title }}">{{ $od->item?->biblio?->title }}</div>
                                <div class="text-[10px] font-mono text-rose-600 font-bold">Terlambat {{ $od->overdueDays() }} hari</div>
                            </div>
                            <div class="text-right">
                                <div class="text-[10px] text-slate-400">Estimasi Denda:</div>
                                <div class="font-bold text-rose-700 dark:text-rose-300">Rp {{ number_format($od->calculateFine(), 0, ',', '.') }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
