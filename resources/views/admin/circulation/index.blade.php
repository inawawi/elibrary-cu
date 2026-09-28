@extends('layouts.admin')

@section('title', 'Transaksi Sirkulasi')
@section('header_title', 'Meja Layanan Sirkulasi (Peminjaman & Pengembalian)')

@section('content')
<div class="space-y-8">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Left: Quick Return Barcode Scanner & Member Lookup -->
        <div class="lg:col-span-5 space-y-6">
            <!-- Quick Return Box (Pengembalian Kilat) -->
            <div class="bg-gradient-to-tr from-emerald-600 to-teal-700 text-white rounded-3xl p-6 shadow-xl relative overflow-hidden">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                        <i data-lucide="check-check" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-extrabold">Pengembalian Kilat</h2>
                        <p class="text-xs text-emerald-100">Scan barcode buku yang dikembalikan</p>
                    </div>
                </div>

                <form action="{{ route('admin.circulation.return') }}" method="POST" class="space-y-3">
                    @csrf
                    @if($member)
                        <input type="hidden" name="redirect_member_id" value="{{ $member->member_id }}">
                    @endif
                    <div class="relative">
                        <i data-lucide="barcode" class="w-5 h-5 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text"
                               name="item_code"
                               required
                               placeholder="Scan / Masukkan Kode Eksemplar..."
                               class="w-full pl-11 pr-4 py-3 rounded-xl bg-white text-slate-900 font-mono font-bold text-sm focus:outline-none shadow-inner"
                               autofocus>
                    </div>
                    <button type="submit" class="w-full py-2.5 rounded-xl bg-white/20 hover:bg-white/30 text-white font-bold text-xs transition-colors flex items-center justify-center gap-2">
                        <i data-lucide="corner-down-left" class="w-4 h-4"></i>
                        <span>Proses Pengembalian</span>
                    </button>
                </form>
            </div>

            <!-- Member Lookup Form -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                    <i data-lucide="user-search" class="w-4 h-4 text-brand-500"></i>
                    Pilih Anggota Peminjam
                </h3>

                <form action="{{ route('admin.circulation.index') }}" method="GET" class="space-y-3">
                    <div class="relative">
                        <i data-lucide="badge-info" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text"
                               name="member_id"
                               value="{{ $memberId }}"
                               required
                               placeholder="Ketik ID / NIM Anggota..."
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-mono font-bold text-slate-900 dark:text-white focus:outline-none">
                    </div>
                    <button type="submit" class="w-full py-2.5 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs transition-colors">
                        Buka Profil Sirkulasi Anggota
                    </button>
                </form>
            </div>

            <!-- Member Summary if loaded -->
            @if($member)
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm space-y-4">
                    <div class="flex items-center gap-4">
                        <img src="{{ $member->avatar_url }}" alt="{{ $member->member_name }}" class="w-14 h-14 rounded-2xl object-cover border border-slate-200 dark:border-slate-700 shadow-sm">
                        <div class="min-w-0">
                            <span class="text-[10px] font-mono font-bold text-brand-600 dark:text-sky-400">{{ $member->member_id }}</span>
                            <h3 class="font-extrabold text-sm text-slate-900 dark:text-white truncate">{{ $member->member_name }}</h3>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                                {{ $member->memberType?->member_type_name ?: 'Mahasiswa' }}
                            </span>
                        </div>
                    </div>

                    @if(!$canBorrow)
                        <div class="p-3.5 rounded-xl bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-900 text-rose-800 dark:text-rose-300 text-xs font-semibold flex items-center gap-2.5">
                            <i data-lucide="alert-circle" class="w-4 h-4 text-rose-500 flex-shrink-0"></i>
                            <div>{{ $borrowBlockReason }}</div>
                        </div>
                    @endif

                    <div class="pt-3 border-t border-slate-100 dark:border-slate-800 grid grid-cols-2 gap-2 text-xs">
                        <div>
                            <span class="text-slate-400 block text-[10px]">Masa Berlaku:</span>
                            <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $member->expire_date ? \Carbon\Carbon::parse($member->expire_date)->format('d/m/Y') : '-' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[10px]">Limit Pinjaman:</span>
                            <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $activeLoans->count() }} / {{ $member->memberType?->loan_limit ?? 3 }} Buku</span>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Right: Loan Form & Member Active Loans -->
        <div class="lg:col-span-7 space-y-6">
            @if($member)
                <!-- Form Borrow Item -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 sm:p-8 shadow-sm">
                    <h3 class="font-bold text-base text-slate-900 dark:text-white mb-2 flex items-center gap-2">
                        <i data-lucide="book-plus" class="w-5 h-5 text-brand-500"></i>
                        Pinjamkan Buku ke {{ $member->member_name }}
                    </h3>
                    <p class="text-xs text-slate-400 mb-5">Scan atau ketik kode barcode buku untuk menambahkan ke transaksi</p>

                    <form action="{{ route('admin.circulation.loan') }}" method="POST" class="flex flex-col sm:flex-row gap-3">
                        @csrf
                        <input type="hidden" name="member_id" value="{{ $member->member_id }}">
                        <div class="relative flex-grow">
                            <i data-lucide="barcode" class="w-5 h-5 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input type="text"
                                   name="item_code"
                                   required
                                   {{ !$canBorrow ? 'disabled' : '' }}
                                   placeholder="Scan barcode buku (Contoh: B00123)..."
                                   class="w-full pl-11 pr-4 py-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white font-mono font-bold text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 disabled:opacity-50">
                        </div>
                        <button type="submit" {{ !$canBorrow ? 'disabled' : '' }} class="px-6 py-3 bg-brand-600 hover:bg-brand-700 disabled:bg-slate-400 text-white font-bold text-xs rounded-xl shadow-md transition-all flex items-center justify-center gap-2 flex-shrink-0">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                            <span>Pinjamkan</span>
                        </button>
                    </form>
                </div>

                <!-- Active Loans for this Member -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 sm:p-8 shadow-sm">
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="font-bold text-base text-slate-900 dark:text-white">Pinjaman Aktif Anggota Ini</h3>
                        <span class="text-xs font-bold px-2.5 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800">
                            {{ $activeLoans->count() }} Buku Sedang Dipinjam
                        </span>
                    </div>

                    @if($activeLoans->isEmpty())
                        <div class="p-6 text-center text-xs text-slate-400 bg-slate-50 dark:bg-slate-800/40 rounded-2xl">
                            Anggota ini sedang tidak memiliki pinjaman buku.
                        </div>
                    @else
                        <div class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach($activeLoans as $loan)
                                @php
                                    $isOverdue = $loan->isOverdue();
                                    $fine = $loan->calculateFine();
                                @endphp
                                <div class="py-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-16 rounded-lg overflow-hidden bg-slate-100 dark:bg-slate-800 flex-shrink-0">
                                            <img src="{{ $loan->item?->biblio?->cover_url ?: asset('images/default_cover.svg') }}" class="w-full h-full object-cover">
                                        </div>
                                        <div>
                                            <div class="font-mono text-[11px] font-bold text-brand-600 dark:text-sky-400">{{ $loan->item_code }}</div>
                                            <h4 class="font-bold text-sm text-slate-900 dark:text-white line-clamp-1 max-w-sm">{{ $loan->item?->biblio?->title ?: 'Judul Buku' }}</h4>
                                            <div class="text-[11px] text-slate-400">
                                                Pinjam: {{ \Carbon\Carbon::parse($loan->loan_date)->format('d/m/Y') }} •
                                                Batas: <span class="font-bold {{ $isOverdue ? 'text-rose-600' : 'text-slate-700 dark:text-slate-300' }}">{{ \Carbon\Carbon::parse($loan->due_date)->format('d/m/Y') }}</span>
                                            </div>
                                            @if($isOverdue)
                                                <div class="text-[10px] text-rose-600 font-bold mt-0.5">
                                                    Terlambat {{ $loan->overdueDays() }} hari (Denda: Rp {{ number_format($fine, 0, ',', '.') }})
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <form action="{{ route('admin.circulation.return') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="item_code" value="{{ $loan->item_code }}">
                                        <input type="hidden" name="redirect_member_id" value="{{ $member->member_id }}">
                                        <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 text-xs font-bold flex items-center gap-1.5 transition-colors">
                                            <i data-lucide="check" class="w-3.5 h-3.5"></i>
                                            <span>Kembalikan</span>
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @else
                <div class="p-12 text-center bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm">
                    <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400 mx-auto mb-4">
                        <i data-lucide="user-search" class="w-8 h-8"></i>
                    </div>
                    <h3 class="font-bold text-base text-slate-900 dark:text-white mb-2">Pilih Anggota Terlebih Dahulu</h3>
                    <p class="text-xs text-slate-400 max-w-sm mx-auto">
                        Masukkan ID / NIM anggota pada formulir di sebelah kiri untuk memulai transaksi peminjaman buku baru atau melihat pinjaman aktif.
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
