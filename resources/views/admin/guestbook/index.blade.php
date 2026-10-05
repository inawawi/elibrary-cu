@extends('layouts.admin')

@section('title', 'Buku Tamu & Kunjungan')
@section('header_title', 'Manajemen Buku Tamu & Kunjungan Perpustakaan')

@section('content')
<div class="space-y-6">

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Kunjungan</span>
                    <h3 class="text-2xl font-black text-slate-800 dark:text-white mt-1">{{ number_format($stats['total']) }}</h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-brand-50 dark:bg-sky-950/60 flex items-center justify-center text-brand-600 dark:text-sky-400">
                    <i data-lucide="book-open-check" class="w-6 h-6"></i>
                </div>
            </div>
            <span class="text-[11px] text-slate-400 mt-2 block">Keseluruhan catatan kunjungan</span>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Hari Ini</span>
                    <h3 class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ number_format($stats['today']) }}</h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                    <i data-lucide="calendar-check" class="w-6 h-6"></i>
                </div>
            </div>
            <span class="text-[11px] text-slate-400 mt-2 block">{{ \Carbon\Carbon::today()->translatedFormat('d F Y') }}</span>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Bulan Ini</span>
                    <h3 class="text-2xl font-black text-brand-600 dark:text-sky-400 mt-1">{{ number_format($stats['this_month']) }}</h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-brand-50 dark:bg-sky-950/60 flex items-center justify-center text-brand-600 dark:text-sky-400">
                    <i data-lucide="calendar" class="w-6 h-6"></i>
                </div>
            </div>
            <span class="text-[11px] text-slate-400 mt-2 block">Bulan {{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</span>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Anggota / Umum</span>
                    <h3 class="text-xl font-black text-purple-600 dark:text-purple-400 mt-1">
                        {{ $stats['anggota'] }} <span class="text-xs font-normal text-slate-400">/ {{ $stats['non_anggota'] }}</span>
                    </h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-purple-50 dark:bg-purple-950/60 flex items-center justify-center text-purple-600 dark:text-purple-400">
                    <i data-lucide="users" class="w-6 h-6"></i>
                </div>
            </div>
            <span class="text-[11px] text-slate-400 mt-2 block">Komposisi tipe pengunjung</span>
        </div>
    </div>

    <!-- Filter & Action Card -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm">
        <form action="{{ route('admin.guestbook.index') }}" method="GET" class="space-y-4">
            <div class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4">
                
                <!-- Search & Filters -->
                <div class="flex flex-wrap items-center gap-3 flex-grow">
                    <div class="relative flex-grow min-w-[240px]">
                        <i data-lucide="search" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text"
                               name="search"
                               value="{{ $search }}"
                               placeholder="Cari nama pengunjung, NIM, atau prodi..."
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-brand-500">
                    </div>

                    <!-- Date Range -->
                    <div class="flex items-center gap-2">
                        <input type="date"
                               name="start_date"
                               value="{{ $startDate }}"
                               title="Dari Tanggal"
                               class="px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white font-medium">
                        <span class="text-xs text-slate-400 font-bold">s/d</span>
                        <input type="date"
                               name="end_date"
                               value="{{ $endDate }}"
                               title="Sampai Tanggal"
                               class="px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white font-medium">
                    </div>

                    <!-- Status Filter -->
                    <select name="status" class="px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white font-medium">
                        <option value="all">Semua Status</option>
                        <option value="Anggota" {{ $status === 'Anggota' ? 'selected' : '' }}>Anggota</option>
                        <option value="Non Anggota" {{ $status === 'Non Anggota' ? 'selected' : '' }}>Non Anggota (Tamu Luar)</option>
                    </select>

                    <button type="submit" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-900 dark:bg-brand-600 dark:hover:bg-brand-700 text-white font-bold text-xs rounded-xl transition-colors flex items-center gap-1.5">
                        <i data-lucide="filter" class="w-3.5 h-3.5"></i>
                        <span>Filter</span>
                    </button>

                    @if($search || $startDate || $endDate || ($status && $status !== 'all'))
                        <a href="{{ route('admin.guestbook.index') }}" class="px-3 py-2 rounded-xl text-xs font-semibold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition-colors">
                            Reset
                        </a>
                    @endif
                </div>

                <!-- Export Multi-Format Dropdown -->
                <div class="flex items-center gap-2 justify-end" x-data="{ exportOpen: false }">
                    <div class="relative">
                        <button type="button" @click="exportOpen = !exportOpen"
                                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-500/20 transition-all">
                            <i data-lucide="download" class="w-4 h-4"></i>
                            <span>Ekspor Data Kunjungan</span>
                            <i data-lucide="chevron-down" class="w-3.5 h-3.5"></i>
                        </button>

                        <div x-show="exportOpen" @click.away="exportOpen = false" x-cloak
                             class="absolute right-0 mt-2 w-56 bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-100 dark:border-slate-700 p-2 z-50 space-y-1">
                            
                            <div class="px-3 py-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100 dark:border-slate-700 mb-1">
                                Pilih Format Dokumen
                            </div>

                            <!-- Excel -->
                            <a href="{{ route('admin.guestbook.export', array_merge(request()->query(), ['format' => 'excel'])) }}"
                               class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-emerald-50 hover:text-emerald-700 dark:hover:bg-emerald-950/50 transition-colors">
                                <span class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-700 dark:bg-emerald-900/60 dark:text-emerald-400 flex items-center justify-center font-bold text-[10px]">XLS</span>
                                <div>
                                    <div class="font-bold">Microsoft Excel</div>
                                    <div class="text-[10px] text-slate-400">File Spreadsheet (.xls)</div>
                                </div>
                            </a>

                            <!-- Word -->
                            <a href="{{ route('admin.guestbook.export', array_merge(request()->query(), ['format' => 'word'])) }}"
                               class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-blue-50 hover:text-blue-700 dark:hover:bg-blue-950/50 transition-colors">
                                <span class="w-6 h-6 rounded-lg bg-blue-100 text-blue-700 dark:bg-blue-900/60 dark:text-blue-400 flex items-center justify-center font-bold text-[10px]">DOC</span>
                                <div>
                                    <div class="font-bold">Microsoft Word</div>
                                    <div class="text-[10px] text-slate-400">Dokumen Resmi (.doc)</div>
                                </div>
                            </a>

                            <!-- PDF / Print -->
                            <a href="{{ route('admin.guestbook.export', array_merge(request()->query(), ['format' => 'pdf'])) }}" target="_blank"
                               class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-rose-50 hover:text-rose-700 dark:hover:bg-rose-950/50 transition-colors">
                                <span class="w-6 h-6 rounded-lg bg-rose-100 text-rose-700 dark:bg-rose-900/60 dark:text-rose-400 flex items-center justify-center font-bold text-[10px]">PDF</span>
                                <div>
                                    <div class="font-bold">Cetak / Simpan PDF</div>
                                    <div class="text-[10px] text-slate-400">Layout Kop Surat Resmi</div>
                                </div>
                            </a>

                            <!-- CSV -->
                            <a href="{{ route('admin.guestbook.export', array_merge(request()->query(), ['format' => 'csv'])) }}"
                               class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-100 hover:text-slate-900 dark:hover:bg-slate-700 transition-colors">
                                <span class="w-6 h-6 rounded-lg bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-300 flex items-center justify-center font-bold text-[10px]">CSV</span>
                                <div>
                                    <div class="font-bold">File CSV</div>
                                    <div class="text-[10px] text-slate-400">Universal Raw Data (.csv)</div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>

    <!-- Table of Visitors -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-400 uppercase font-black text-[10px] tracking-wider border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-6 py-4">No</th>
                        <th class="px-6 py-4">Waktu Kunjungan</th>
                        <th class="px-6 py-4">Nama Pengunjung</th>
                        <th class="px-6 py-4">NIM / ID Anggota</th>
                        <th class="px-6 py-4">Status & Prodi</th>
                        <th class="px-6 py-4">Keperluan</th>
                        <th class="px-6 py-4">Tujuan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($visitors as $index => $v)
                    <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-colors">
                        <td class="px-6 py-4 font-bold text-slate-400">
                            {{ $visitors->firstItem() + $index }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-bold text-slate-800 dark:text-white">
                                {{ !empty($v->tgl) ? \Carbon\Carbon::parse($v->tgl)->translatedFormat('d M Y') : '-' }}
                            </div>
                            <div class="text-[11px] text-slate-400 font-mono">
                                {{ $v->jam ?? '-' }} WIB
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-extrabold text-slate-900 dark:text-white text-sm">
                                {{ $v->nama }}
                            </div>
                            @if($v->member)
                                <span class="text-[10px] text-brand-600 dark:text-sky-400 font-bold">Terverifikasi Anggota</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap font-mono font-bold text-slate-700 dark:text-slate-300">
                            {{ $v->id_anggota ?: '-' }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black {{ $v->status === 'Anggota' ? 'bg-sky-100 text-sky-700 dark:bg-sky-950/60 dark:text-sky-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300' }}">
                                    {{ $v->status ?? 'Anggota' }}
                                </span>
                            </div>
                            <div class="text-[11px] text-slate-500 dark:text-slate-400 font-medium mt-1">
                                {{ $v->prodi ?: ($v->member?->prodi_name ?? 'Umum') }}
                            </div>
                        </td>
                        <td class="px-6 py-4 text-slate-700 dark:text-slate-300 font-medium">
                            {{ $v->keperluan ?? 'Kunjungan Perpustakaan' }}
                        </td>
                        <td class="px-6 py-4 text-slate-500 dark:text-slate-400">
                            {{ $v->tujuan ?: '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-16 text-center text-slate-400">
                            <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mx-auto mb-3 text-slate-400">
                                <i data-lucide="inbox" class="w-6 h-6"></i>
                            </div>
                            <p class="font-bold">Belum ada catatan buku tamu.</p>
                            <p class="text-xs text-slate-400 mt-1">Data akan otomatis terisi saat pengunjung mengisi buku tamu di OPAC.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($visitors->hasPages())
        <div class="p-6 border-t border-slate-100 dark:border-slate-800">
            {{ $visitors->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
