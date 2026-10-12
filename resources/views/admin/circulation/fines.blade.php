@extends('layouts.admin')

@section('title', 'Daftar Denda Sirkulasi - E-Library Cyber University')

@section('content')
<div class="space-y-6">
    <!-- Header & Action -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-1">
                <a href="{{ route('admin.circulation.index') }}" class="hover:text-brand-500">Sirkulasi</a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                <span class="text-slate-600 dark:text-slate-300">Daftar Denda</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white flex items-center gap-2.5">
                <i data-lucide="wallet" class="w-7 h-7 text-rose-500"></i>
                <span>Daftar Denda & Pelunasan</span>
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Kelola pencatatan denda keterlambatan sirkulasi, riwayat pembayaran denda, dan sisa tunggakan anggota.
            </p>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
            <a href="{{ route('admin.circulation.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition-colors shadow-sm">
                <i data-lucide="repeat" class="w-4 h-4 text-emerald-400"></i>
                <span>Meja Sirkulasi</span>
            </a>
            <a href="{{ route('admin.circulation.active', ['status' => 'overdue']) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/60 dark:hover:bg-rose-900/60 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-300 font-bold text-xs transition-colors shadow-xs">
                <i data-lucide="clock-alert" class="w-4 h-4"></i>
                <span>Pinjaman Terlambat</span>
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs font-semibold flex items-center gap-2 shadow-xs">
            <i data-lucide="check-circle" class="w-4 h-4 text-emerald-500 flex-shrink-0"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 text-xs font-semibold flex items-center gap-2 shadow-xs">
            <i data-lucide="alert-circle" class="w-4 h-4 text-rose-500 flex-shrink-0"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Statistic Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-5 shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Total Tagihan Denda</span>
                    <h3 class="text-xl font-black text-slate-900 dark:text-white">
                        Rp {{ number_format($stats['total_debet'], 0, ',', '.') }}
                    </h3>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-600 dark:text-slate-300">
                    <i data-lucide="receipt" class="w-6 h-6"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-5 shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Total Telah Dibayar</span>
                    <h3 class="text-xl font-black text-emerald-600 dark:text-emerald-400">
                        Rp {{ number_format($stats['total_credit'], 0, ',', '.') }}
                    </h3>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                    <i data-lucide="check-circle-2" class="w-6 h-6"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-rose-200 dark:border-rose-900/60 p-5 shadow-xs bg-rose-50/20">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-rose-500 block mb-1">Sisa Belum Lunas</span>
                    <h3 class="text-xl font-black text-rose-600 dark:text-rose-400">
                        Rp {{ number_format($stats['total_unpaid'], 0, ',', '.') }}
                    </h3>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-rose-100 dark:bg-rose-950/80 flex items-center justify-center text-rose-600 dark:text-rose-400">
                    <i data-lucide="alert-octagon" class="w-6 h-6"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-5 shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Kasus Belum Lunas</span>
                    <h3 class="text-xl font-black text-amber-600 dark:text-amber-400">
                        {{ number_format($stats['count_unpaid'], 0, ',', '.') }} <span class="text-xs font-normal text-slate-400">Kasus</span>
                    </h3>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/60 flex items-center justify-center text-amber-600 dark:text-amber-400">
                    <i data-lucide="clock" class="w-6 h-6"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Table Card -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 dark:border-slate-800 space-y-4">
            <!-- Tabs Status -->
            <div class="flex items-center justify-between flex-wrap gap-3">
                <div class="flex items-center gap-1.5 p-1 bg-slate-100 dark:bg-slate-800 rounded-2xl">
                    <a href="{{ route('admin.circulation.fines', ['status' => 'all', 'search' => $search, 'date_from' => $dateFrom, 'date_to' => $dateTo]) }}"
                       class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ $status === 'all' ? 'bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-xs' : 'text-slate-500 hover:text-slate-900 dark:hover:text-white' }}">
                        Semua Denda
                    </a>
                    <a href="{{ route('admin.circulation.fines', ['status' => 'unpaid', 'search' => $search, 'date_from' => $dateFrom, 'date_to' => $dateTo]) }}"
                       class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ $status === 'unpaid' ? 'bg-rose-500 text-white shadow-xs' : 'text-slate-500 hover:text-rose-500 dark:hover:text-rose-400' }}">
                        Belum Lunas ({{ $stats['count_unpaid'] }})
                    </a>
                    <a href="{{ route('admin.circulation.fines', ['status' => 'paid', 'search' => $search, 'date_from' => $dateFrom, 'date_to' => $dateTo]) }}"
                       class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ $status === 'paid' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-500 hover:text-emerald-500 dark:hover:text-emerald-400' }}">
                        Lunas
                    </a>
                </div>

                <!-- Search & Date Filter Form -->
                <form action="{{ route('admin.circulation.fines') }}" method="GET" class="flex items-center gap-2 flex-wrap">
                    <input type="hidden" name="status" value="{{ $status }}">
                    
                    <div class="relative">
                        <i data-lucide="calendar" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="date" name="date_from" value="{{ $dateFrom }}" placeholder="Dari Tgl" class="pl-9 pr-3 py-1.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white font-medium focus:outline-none" title="Filter Dari Tanggal">
                    </div>
                    <span class="text-xs text-slate-400">s/d</span>
                    <div class="relative">
                        <i data-lucide="calendar" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="date" name="date_to" value="{{ $dateTo }}" placeholder="Sampai Tgl" class="pl-9 pr-3 py-1.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white font-medium focus:outline-none" title="Filter Sampai Tanggal">
                    </div>

                    <div class="relative w-48 sm:w-64">
                        <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama, NIM, deskripsi..." class="w-full pl-9 pr-3 py-1.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white font-medium focus:outline-none">
                    </div>

                    <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs shadow-xs transition-colors">
                        Terapkan
                    </button>

                    @if(!empty($search) || !empty($dateFrom) || !empty($dateTo) || $status !== 'all')
                        <a href="{{ route('admin.circulation.fines') }}" class="px-2.5 py-1.5 rounded-xl border border-slate-300 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-100 text-xs font-semibold" title="Reset Filter">
                            Reset
                        </a>
                    @endif
                </form>
            </div>
        </div>

        <!-- Table Data -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                        <th class="py-3 px-4">Tanggal</th>
                        <th class="py-3 px-4">Anggota</th>
                        <th class="py-3 px-4">Keterangan / Eksemplar</th>
                        <th class="py-3 px-4 text-right">Tagihan (Debet)</th>
                        <th class="py-3 px-4 text-right">Dibayar (Credit)</th>
                        <th class="py-3 px-4 text-right">Sisa Saldo</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                    @forelse($fines as $fine)
                        @php
                            $member = $fine->member;
                            $balance = $fine->balance;
                            $isPaid = $fine->is_paid;
                        @endphp
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors {{ $balance > 0 ? 'bg-rose-50/10' : '' }}">
                            <td class="py-3.5 px-4 font-mono text-slate-600 dark:text-slate-300 whitespace-nowrap">
                                {{ $fine->fines_date ? \Carbon\Carbon::parse($fine->fines_date)->translatedFormat('d M Y') : '-' }}
                            </td>
                            <td class="py-3.5 px-4">
                                @if($member)
                                    <div class="flex items-center gap-2.5">
                                        <img src="{{ $member->avatar_url }}" alt="{{ $member->member_name }}" class="w-7 h-7 rounded-lg object-cover border border-slate-200 dark:border-slate-700 flex-shrink-0">
                                        <div class="min-w-0">
                                            <a href="{{ route('admin.circulation.index', ['member_id' => $member->member_id]) }}" class="font-bold text-slate-900 dark:text-white hover:text-brand-600 block truncate" title="Buka di Meja Sirkulasi">
                                                {{ $member->member_name }}
                                            </a>
                                            <div class="text-[10px] font-mono text-slate-400">
                                                {{ $member->member_id }} &bull; {{ $member->memberType?->member_type_name ?: 'Mahasiswa' }}
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="font-mono text-slate-500">
                                        ID: {{ $fine->member_id }}
                                        <span class="text-[10px] text-slate-400 block">(Data anggota terhapus/lama)</span>
                                    </div>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 max-w-xs text-slate-700 dark:text-slate-300">
                                <span class="line-clamp-2" title="{{ $fine->description }}">
                                    {{ $fine->description ?: 'Denda keterlambatan sirkulasi' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono font-bold text-slate-900 dark:text-white whitespace-nowrap">
                                Rp {{ number_format($fine->debet, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono font-bold text-emerald-600 dark:text-emerald-400 whitespace-nowrap">
                                Rp {{ number_format($fine->credit, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono font-black {{ $balance > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-slate-400' }} whitespace-nowrap">
                                Rp {{ number_format($balance, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                @if($isPaid)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                                        <i data-lucide="check" class="w-3 h-3"></i> Lunas
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300">
                                        <i data-lucide="alert-circle" class="w-3 h-3"></i> Belum Lunas
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5">
                                    @if($balance > 0)
                                        <button type="button"
                                                onclick="openPayModal({{ $fine->fines_id }}, '{{ addslashes($member?->member_name ?? $fine->member_id) }}', '{{ $fine->member_id }}', {{ $balance }}, '{{ addslashes($fine->description ?? '') }}')"
                                                class="px-2.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] shadow-xs transition-colors flex items-center gap-1">
                                            <i data-lucide="credit-card" class="w-3.5 h-3.5"></i>
                                            <span>Bayar Denda</span>
                                        </button>
                                    @endif

                                    @if($member)
                                        <a href="{{ route('admin.circulation.index', ['member_id' => $member->member_id]) }}"
                                           class="p-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 transition-colors"
                                           title="Buka Meja Sirkulasi">
                                            <i data-lucide="repeat" class="w-3.5 h-3.5"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-12 text-slate-400">
                                <i data-lucide="wallet-cards" class="w-12 h-12 text-slate-300 mx-auto mb-3"></i>
                                <h3 class="text-sm font-bold text-slate-700 dark:text-slate-300">Tidak ada catatan denda yang ditemukan</h3>
                                <p class="text-xs text-slate-400 mt-1">Coba sesuaikan kata kunci pencarian atau filter status denda di atas.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($fines->hasPages())
            <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                {{ $fines->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Pelunasan Denda -->
<div id="payFineModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-2xl max-w-md w-full animate-in fade-in zoom-in-95 duration-150">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">
            <h3 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="coins" class="w-5 h-5 text-emerald-500"></i>
                <span>Catat Pembayaran Denda</span>
            </h3>
            <button type="button" onclick="closePayModal()" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form id="payFineForm" method="POST" action="" class="space-y-4">
            @csrf
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-0.5">Anggota Peminjam</span>
                <p id="modalMemberName" class="text-sm font-extrabold text-slate-900 dark:text-white"></p>
                <p id="modalMemberId" class="text-xs font-mono text-brand-600 dark:text-sky-400"></p>
            </div>

            <div class="p-3.5 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900">
                <span class="text-[10px] font-bold uppercase tracking-wider text-rose-500 block">Sisa Tagihan Denda</span>
                <p id="modalBalanceDisplay" class="text-xl font-black text-rose-700 dark:text-rose-300 font-mono"></p>
                <p id="modalDescription" class="text-[11px] text-slate-500 dark:text-slate-400 mt-1"></p>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                    Nominal Dibayarkan (Rp) *
                </label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400 font-mono">Rp</span>
                    <input type="number" name="amount" id="modalAmountInput" required min="1" class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm font-mono font-black text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>
                <div class="flex items-center gap-2 mt-2">
                    <button type="button" id="btnFullPayment" class="px-2.5 py-1 rounded-lg text-[10px] font-bold bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 transition-colors">
                        Lunasi Penuh
                    </button>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                    Catatan / Metode Pembayaran (Opsional)
                </label>
                <input type="text" name="payment_note" placeholder="Contoh: Tunai di kasir meja sirkulasi" class="w-full px-4 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none">
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="closePayModal()" class="px-4 py-2 rounded-xl border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs hover:bg-slate-50 transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-500/25 transition-all flex items-center gap-1.5">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span>Simpan Pembayaran</span>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    let currentBalance = 0;

    function openPayModal(fineId, memberName, memberId, balance, desc) {
        currentBalance = balance;
        const form = document.getElementById('payFineForm');
        form.action = `/admin/circulation/fines/${fineId}/pay`;

        document.getElementById('modalMemberName').innerText = memberName;
        document.getElementById('modalMemberId').innerText = 'ID: ' + memberId;
        document.getElementById('modalBalanceDisplay').innerText = 'Rp ' + Number(balance).toLocaleString('id-ID');
        document.getElementById('modalDescription').innerText = desc || 'Denda keterlambatan';
        
        const amountInput = document.getElementById('modalAmountInput');
        amountInput.value = balance;
        amountInput.max = balance;

        document.getElementById('btnFullPayment').onclick = function() {
            amountInput.value = balance;
        };

        const modal = document.getElementById('payFineModal');
        modal.classList.remove('hidden');
    }

    function closePayModal() {
        const modal = document.getElementById('payFineModal');
        modal.classList.add('hidden');
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closePayModal();
        }
    });
</script>
@endpush
@endsection
