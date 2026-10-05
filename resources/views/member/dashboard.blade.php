@extends('layouts.opac')

@section('title', 'Dasbor Anggota - ' . $member->member_name)

@section('content')
<div class="{{ !empty($isContactIncomplete) ? 'filter blur-[2px] pointer-events-none select-none opacity-50 transition-all' : '' }}">
<div class="bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white">Area Mandiri Anggota</h1>
            <p class="text-xs text-slate-500">Selamat datang di portal informasi keanggotaan Anda</p>
        </div>

        <form action="{{ route('member.logout') }}" method="POST">
            @csrf
            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-rose-200 dark:border-rose-900/60 bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 text-xs font-bold hover:bg-rose-100 transition-colors">
                <i data-lucide="log-out" class="w-4 h-4"></i>
                <span>Keluar Akun</span>
            </button>
        </form>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <!-- Announcement / Broadcast Banner -->
    @if(!empty($announcement))
        @php
            $aType = $announcement['type'] ?? 'info';
            $bannerColors = match($aType) {
                'warning' => 'bg-amber-500/10 border-amber-400/40 text-amber-900 dark:text-amber-200',
                'danger' => 'bg-rose-500/10 border-rose-400/40 text-rose-900 dark:text-rose-200',
                'success' => 'bg-emerald-500/10 border-emerald-400/40 text-emerald-900 dark:text-emerald-200',
                default => 'bg-sky-500/10 border-sky-400/40 text-sky-900 dark:text-sky-200',
            };
            $icon = match($aType) {
                'warning' => 'alert-triangle',
                'danger' => 'alert-circle',
                'success' => 'check-circle-2',
                default => 'megaphone',
            };
        @endphp
        <div class="mb-8 rounded-3xl border p-6 shadow-sm relative overflow-hidden {{ $bannerColors }}">
            <div class="flex items-start gap-4">
                <div class="p-3 rounded-2xl bg-white/70 dark:bg-slate-900/70 shadow-sm flex-shrink-0">
                    <i data-lucide="{{ $icon }}" class="w-6 h-6"></i>
                </div>
                <div class="flex-grow min-w-0">
                    <div class="flex items-center justify-between gap-2 mb-1.5 flex-wrap">
                        <h3 class="font-black text-base tracking-tight leading-snug">{{ $announcement['title'] }}</h3>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-white/80 dark:bg-slate-800/80 uppercase">
                            Informasi Perpustakaan
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm leading-relaxed opacity-95 whitespace-pre-line">{{ $announcement['content'] }}</p>
                    <div class="mt-3 flex items-center justify-between text-[11px] opacity-75 pt-2.5 border-t border-current/15">
                        <span class="flex items-center gap-1.5">
                            <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                            Pemberitahuan Resmi Perpustakaan Universitas Siber Indonesia
                        </span>
                        @if(!empty($announcement['updated_at']))
                            <span>{{ $announcement['updated_at'] }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Left: Digital Member Card -->
        <div class="lg:col-span-5 space-y-6">
            <!-- Card Front -->
            <div class="rounded-3xl p-6 bg-gradient-to-tr from-brand-900 via-brand-800 to-indigo-900 text-white shadow-2xl relative overflow-hidden border border-white/10">
                <div class="absolute -right-8 -top-8 w-44 h-44 rounded-full bg-white/10 blur-xl pointer-events-none"></div>
                <div class="absolute -left-8 -bottom-8 w-44 h-44 rounded-full bg-brand-500/20 blur-xl pointer-events-none"></div>

                <!-- Card Header -->
                <div class="flex items-center justify-between mb-6 relative">
                    <div class="flex items-center gap-2.5">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo Universitas Siber Indonesia" class="w-10 h-10 object-contain">
                        <div>
                            <div class="text-xs font-extrabold tracking-wider uppercase text-sky-300">KARTU ANGGOTA PERPUSTAKAAN</div>
                            <div class="text-[10px] text-white/70">UNIVERSITAS SIBER INDONESIA</div>
                        </div>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-white/20 backdrop-blur">
                        {{ $member->memberType?->member_type_name ?: 'Mahasiswa' }}
                    </span>
                </div>

                <!-- Card Body -->
                <div class="flex items-center gap-5 mb-6 relative">
                    <img src="{{ $member->avatar_url }}" alt="{{ $member->member_name }}" class="w-20 h-20 rounded-2xl object-cover border-2 border-white/30 shadow-md">
                    <div>
                        <div class="text-lg font-black tracking-tight leading-snug">{{ $member->member_name }}</div>
                        <div class="font-mono text-sm text-sky-200 font-bold mt-0.5 tracking-wider">{{ $member->member_id }}</div>
                        <div class="text-xs text-white/80 mt-1">{{ $member->inst_name ?: 'Universitas Siber Indonesia' }}</div>
                    </div>
                </div>

                <!-- Card Footer -->
                <div class="pt-4 border-t border-white/15 flex items-center justify-between text-[11px] relative">
                    <div>
                        <span class="text-white/60 block text-[9px] uppercase tracking-wider">Masa Berlaku</span>
                        <span class="font-semibold">{{ $member->isLecturer() ? 'Selama Bertugas' : ($member->expire_date ? \Carbon\Carbon::parse($member->expire_date)->translatedFormat('d F Y') : 'Seumur Hidup') }}</span>
                    </div>
                    <div>
                        <span class="text-white/60 block text-[9px] uppercase tracking-wider">Status</span>
                        @if($member->is_pending == 1)
                            <span class="px-2 py-0.5 rounded bg-rose-500/80 text-[10px] font-bold">Non-Aktif</span>
                        @elseif($member->isExpired())
                            <span class="px-2 py-0.5 rounded bg-amber-500/80 text-[10px] font-bold">Kedaluwarsa</span>
                        @else
                            <span class="px-2 py-0.5 rounded bg-emerald-500/80 text-[10px] font-bold">Aktif</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Member Details Card -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm">
                <h3 class="font-bold text-sm text-slate-900 dark:text-white uppercase tracking-wider mb-4 flex items-center gap-2">
                    <i data-lucide="user-check" class="w-4 h-4 text-brand-500"></i>
                    Informasi & Ketentuan Pinjam
                </h3>
                <div class="space-y-3 text-xs">
                    <div class="flex justify-between py-2 border-b border-slate-100 dark:border-slate-800">
                        <span class="text-slate-400">Email:</span>
                        <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $member->member_email ?: '-' }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-slate-100 dark:border-slate-800">
                        <span class="text-slate-400">No. Telepon/WA:</span>
                        <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $member->member_phone ?: '-' }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-slate-100 dark:border-slate-800">
                        <span class="text-slate-400">Maks. Peminjaman:</span>
                        <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $member->memberType?->loan_limit ?? 3 }} Buku</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-slate-100 dark:border-slate-800">
                        <span class="text-slate-400">Durasi Pinjam:</span>
                        <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $member->memberType?->loan_periode ?? 7 }} Hari / Peminjaman</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-slate-100 dark:border-slate-800">
                        <span class="text-slate-400">Denda Keterlambatan:</span>
                        <span class="font-bold text-rose-600 dark:text-rose-400">Rp {{ number_format($member->memberType?->fine_each_day ?? 1000, 0, ',', '.') }} / hari / buku</span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-slate-400">Toleransi Keterlambatan:</span>
                        <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $member->memberType?->grace_periode ?? 0 }} Hari</span>
                    </div>
                </div>
            </div>

            <!-- Tata Tertib Card -->
            @if(!empty($libraryRules))
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm">
                <h3 class="font-bold text-sm text-slate-900 dark:text-white uppercase tracking-wider mb-3 flex items-center gap-2">
                    <i data-lucide="scroll-text" class="w-4 h-4 text-brand-500"></i>
                    Tata Tertib Perpustakaan
                </h3>
                <div class="text-xs text-slate-600 dark:text-slate-400 whitespace-pre-line leading-relaxed">
{{ $libraryRules }}
                </div>
            </div>
            @endif
        </div>

        <!-- Right: Active Loans & History -->
        <div class="lg:col-span-7 space-y-8">
            <!-- Khusus Mahasiswa Semester >= 7: Layanan Skripsi & Bebas Pustaka -->
            @if($member->isStudent() && $isSenior)
                @php
                    $spec = $thesis ? (json_decode($thesis->spec_detail_info ?? '{}', true) ?: []) : [];
                    $status = $spec['status'] ?? ($thesis ? ($thesis->opac_hide ? 'pending' : 'approved') : null);
                    $hasActiveLoans = $activeLoans->isNotEmpty();
                @endphp
                <div class="rounded-3xl p-6 sm:p-8 border shadow-sm relative overflow-hidden bg-gradient-to-br from-purple-50 via-white to-sky-50 dark:from-purple-950/30 dark:via-slate-900 dark:to-sky-950/30 border-purple-200/80 dark:border-purple-900/50">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-purple-100 dark:border-purple-900/40">
                        <div class="flex items-center gap-3">
                            <span class="w-12 h-12 rounded-2xl bg-purple-600 text-white flex items-center justify-center shadow-lg shadow-purple-500/25 flex-shrink-0">
                                <i data-lucide="graduation-cap" class="w-6 h-6"></i>
                            </span>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="text-base font-black text-slate-900 dark:text-white">Layanan Skripsi & Bebas Pustaka</h3>
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-purple-100 dark:bg-purple-950 text-purple-700 dark:text-purple-300">
                                        Semester {{ $member->semester }}
                                    </span>
                                </div>
                                <p class="text-xs text-slate-500 mt-0.5">Persyaratan administrasi calon wisudawan Universitas Siber Indonesia</p>
                            </div>
                        </div>

                        <div>
                            @if(!$thesis)
                                <a href="{{ route('member.skripsi') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs shadow-md transition-all">
                                    <i data-lucide="upload" class="w-4 h-4"></i>
                                    <span>Unggah Berkas Skripsi</span>
                                </a>
                            @elseif($status === 'approved' && !$hasActiveLoans)
                                <a href="{{ route('member.bebas-pustaka.print') }}" target="_blank" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md transition-all">
                                    <i data-lucide="printer" class="w-4 h-4"></i>
                                    <span>Cetak Surat Bebas Pustaka</span>
                                </a>
                            @else
                                <a href="{{ route('member.skripsi') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 font-bold text-xs transition-all">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                    <span>Lihat Detail Pengajuan</span>
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Progress & Status Info -->
                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                        <div class="p-3.5 rounded-2xl bg-white/80 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/80">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Status Skripsi</span>
                            @if(!$thesis)
                                <span class="font-bold text-slate-500 flex items-center gap-1.5">
                                    <i data-lucide="circle-dashed" class="w-4 h-4 text-slate-400"></i>
                                    Belum Diunggah
                                </span>
                            @elseif($status === 'approved')
                                <span class="font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-1.5">
                                    <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-500"></i>
                                    Terverifikasi & Disetujui
                                </span>
                            @elseif($status === 'revision')
                                <span class="font-bold text-rose-600 dark:text-rose-400 flex items-center gap-1.5">
                                    <i data-lucide="alert-octagon" class="w-4 h-4 text-rose-500"></i>
                                    Perlu Perbaikan
                                </span>
                            @else
                                <span class="font-bold text-amber-600 dark:text-amber-400 flex items-center gap-1.5">
                                    <i data-lucide="clock" class="w-4 h-4 text-amber-500"></i>
                                    Menunggu Verifikasi
                                </span>
                            @endif
                        </div>

                        <div class="p-3.5 rounded-2xl bg-white/80 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/80">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Tanggungan Pinjaman</span>
                            @if($hasActiveLoans)
                                <span class="font-bold text-rose-600 dark:text-rose-400 flex items-center gap-1.5">
                                    <i data-lucide="alert-triangle" class="w-4 h-4 text-rose-500"></i>
                                    Ada {{ $activeLoans->count() }} Buku Belum Kembali
                                </span>
                            @else
                                <span class="font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-1.5">
                                    <i data-lucide="check" class="w-4 h-4 text-emerald-500"></i>
                                    Bebas Pinjaman (Nol)
                                </span>
                            @endif
                        </div>

                        <div class="p-3.5 rounded-2xl bg-white/80 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/80">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Status Bebas Pustaka</span>
                            @if($isBebasPustakaEligible)
                                <span class="font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-1.5">
                                    <i data-lucide="award" class="w-4 h-4 text-emerald-500"></i>
                                    Siap Dicetak
                                </span>
                            @else
                                <span class="font-bold text-slate-500 flex items-center gap-1.5">
                                    <i data-lucide="lock" class="w-4 h-4 text-slate-400"></i>
                                    Belum Memenuhi Syarat
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Banner Informasi Ketentuan Watermark Skripsi -->
                    <div class="mt-4 p-4 rounded-2xl bg-white/90 dark:bg-slate-900/90 border border-purple-200/90 dark:border-purple-800/60 flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-sm">
                        <div class="flex items-start gap-3">
                            <div class="p-2.5 rounded-xl bg-purple-100 dark:bg-purple-950/80 text-purple-600 dark:text-purple-300 flex-shrink-0">
                                <i data-lucide="stamp" class="w-5 h-5"></i>
                            </div>
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <h4 class="font-extrabold text-xs text-slate-900 dark:text-white">Ketentuan Pemberian Watermark File Skripsi</h4>
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-black bg-purple-100 dark:bg-purple-950 text-purple-700 dark:text-purple-300">Wajib Wisuda</span>
                                </div>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-snug">
                                    File skripsi wajib memuat watermark logo resmi universitas (format PNG transparan, opacity 10-20%, 1 file PDF utuh max 10MB pada cover s.d. lampiran).
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <a href="{{ route('member.watermark.download') }}"
                               download="Watermark_Universitas_Siber_Indonesia.png"
                               class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/40 dark:hover:bg-emerald-950/70 text-emerald-700 dark:text-emerald-400 font-bold text-xs transition-colors border border-emerald-200 dark:border-emerald-800 cursor-pointer"
                               title="Unduh Logo Watermark Resmi (PNG)">
                                <i data-lucide="download" class="w-3.5 h-3.5"></i>
                                <span>Unduh Logo PNG</span>
                            </a>
                            <a href="{{ route('member.skripsi') }}#ketentuan-watermark"
                               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs shadow-sm transition-all">
                                <i data-lucide="info" class="w-3.5 h-3.5"></i>
                                <span>Lihat Ketentuan Lengkap</span>
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Active Loans -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 sm:p-8 shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-xl font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <i data-lucide="book-marked" class="w-5 h-5 text-brand-500"></i>
                            Pinjaman Buku Sedang Berjalan
                        </h2>
                        <p class="text-xs text-slate-500">Buku yang sedang Anda pinjam saat ini</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-brand-50 dark:bg-sky-950 text-brand-700 dark:text-sky-300">
                        {{ $activeLoans->count() }} Buku
                    </span>
                </div>

                @if($activeLoans->isEmpty())
                    <div class="p-8 text-center bg-slate-50 dark:bg-slate-800/40 rounded-2xl border border-slate-200 dark:border-slate-800">
                        <i data-lucide="check-circle-2" class="w-10 h-10 mx-auto mb-2 text-emerald-500"></i>
                        <h4 class="font-bold text-sm text-slate-900 dark:text-white">Tidak Ada Tanggungan Pinjaman</h4>
                        <p class="text-xs text-slate-500 mt-1">Anda sedang tidak meminjam buku. Silakan cari dan pinjam buku menarik di katalog!</p>
                        <a href="{{ route('opac.search') }}" class="inline-flex items-center gap-2 mt-4 px-4 py-2 rounded-xl bg-brand-600 text-white text-xs font-bold hover:bg-brand-700 transition-colors">
                            <i data-lucide="search" class="w-3.5 h-3.5"></i>
                            <span>Jelajahi Katalog Buku</span>
                        </a>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($activeLoans as $loan)
                            @php
                                $biblio = $loan->item?->biblio;
                                $isOverdue = $loan->isOverdue();
                                $daysOverdue = $loan->overdueDays();
                                $fine = $loan->calculateFine();
                            @endphp
                            <div class="p-4 rounded-2xl border {{ $isOverdue ? 'border-rose-300 bg-rose-50/50 dark:bg-rose-950/30 dark:border-rose-900/60' : 'border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40' }} flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                <div class="flex items-center gap-4">
                                    <div class="w-14 h-20 rounded-xl overflow-hidden bg-slate-200 dark:bg-slate-700 flex-shrink-0 shadow">
                                        <img src="{{ $biblio?->cover_url ?: asset('images/default_cover.svg') }}" class="w-full h-full object-cover">
                                    </div>
                                    <div>
                                        <span class="text-[10px] font-mono font-bold text-brand-600 dark:text-sky-400">
                                            KODE: {{ $loan->item_code }}
                                        </span>
                                        <h4 class="font-bold text-sm text-slate-900 dark:text-white line-clamp-1">
                                            {{ $biblio?->title ?: 'Judul Buku' }}
                                        </h4>
                                        <p class="text-xs text-slate-500">
                                            Dipinjam: {{ \Carbon\Carbon::parse($loan->loan_date)->format('d/m/Y') }}
                                        </p>
                                    </div>
                                </div>

                                <div class="text-left sm:text-right w-full sm:w-auto pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-200 dark:border-slate-700">
                                    <div class="text-[11px] text-slate-400">Batas Waktu Pengembalian:</div>
                                    <div class="text-sm font-black {{ $isOverdue ? 'text-rose-600 dark:text-rose-400' : 'text-slate-900 dark:text-white' }}">
                                        {{ \Carbon\Carbon::parse($loan->due_date)->format('d F Y') }}
                                    </div>
                                    @if($isOverdue)
                                        <div class="mt-1 px-2.5 py-0.5 rounded-full bg-rose-100 dark:bg-rose-950 text-rose-700 dark:text-rose-300 text-[10px] font-bold inline-block">
                                            Terlambat {{ $daysOverdue }} Hari (Denda: Rp {{ number_format($fine, 0, ',', '.') }})
                                        </div>
                                    @else
                                        <div class="mt-1 text-[11px] text-emerald-600 font-semibold">
                                            Tepat Waktu
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Loan History -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 sm:p-8 shadow-sm">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                    <i data-lucide="history" class="w-5 h-5 text-indigo-500"></i>
                    Riwayat Pengembalian Terakhir
                </h2>

                @if($loanHistories->isEmpty())
                    <div class="p-6 text-center text-xs text-slate-400 bg-slate-50 dark:bg-slate-800/30 rounded-xl">
                        Belum ada riwayat peminjaman masa lalu.
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="text-slate-400 font-bold uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 pb-2">
                                <tr>
                                    <th class="py-2.5">Judul Buku</th>
                                    <th class="py-2.5">Tgl Pinjam</th>
                                    <th class="py-2.5">Tgl Kembali</th>
                                    <th class="py-2.5">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                                @foreach($loanHistories as $hist)
                                    <tr>
                                        <td class="py-3 font-semibold text-slate-800 dark:text-slate-200 max-w-xs truncate">
                                            {{ $hist->item?->biblio?->title ?: $hist->item_code }}
                                        </td>
                                        <td class="py-3 text-slate-500">
                                            {{ $hist->loan_date ? \Carbon\Carbon::parse($hist->loan_date)->format('d/m/Y') : '-' }}
                                        </td>
                                        <td class="py-3 text-slate-500">
                                            {{ $hist->return_date ? \Carbon\Carbon::parse($hist->return_date)->format('d/m/Y') : '-' }}
                                        </td>
                                        <td class="py-3">
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">
                                                Sudah Kembali
                                            </span>
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
</div>
</div>

@if(!empty($isContactIncomplete))
<!-- BLOCKING MANDATORY CONTACT COMPLETION MODAL -->
<div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-950/85 backdrop-blur-md overflow-y-auto">
    <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-200 dark:border-slate-800 relative my-8">
        
        <!-- Header -->
        <div class="text-center mb-6">
            <div class="w-16 h-16 rounded-2xl bg-amber-50 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-800/80 text-amber-600 dark:text-amber-400 mx-auto flex items-center justify-center shadow-inner mb-4">
                <i data-lucide="phone-call" class="w-8 h-8"></i>
            </div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-amber-100 dark:bg-amber-950/80 text-amber-800 dark:text-amber-300 mb-2">
                <i data-lucide="alert-triangle" class="w-3.5 h-3.5"></i>
                Wajib Dilengkapi
            </span>
            <h2 class="text-xl font-black text-slate-900 dark:text-white">Lengkapi Nomor WhatsApp & Email</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5 leading-relaxed max-w-sm mx-auto">
                Halo <strong>{{ $member->member_name }}</strong>, sesuai ketentuan perpustakaan digital Universitas Siber Indonesia, Anda wajib melengkapi data kontak aktif sebelum dapat mengakses seluruh fitur portal.
            </p>
        </div>

        <!-- Warning Notice -->
        <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 text-xs text-slate-600 dark:text-slate-300 mb-6 flex items-start gap-3">
            <i data-lucide="lock" class="w-4 h-4 text-brand-500 mt-0.5 flex-shrink-0"></i>
            <span class="text-[11px] leading-relaxed">
                Fitur kartu anggota digital, peminjaman buku, dan riwayat sirkulasi dikunci sementara sampai Anda mengisi data nomor WhatsApp dan email di bawah ini.
            </span>
        </div>

        @if($errors->any())
            <div class="mb-4 p-3.5 rounded-2xl bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-900/60 text-xs text-rose-700 dark:text-rose-300">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('member.update-contact') }}" method="POST" class="space-y-4">
            @csrf

            <!-- Identity Preview -->
            <div class="p-3 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-between text-xs font-semibold text-slate-700 dark:text-slate-300">
                <span class="text-slate-400">NIM / Anggota:</span>
                <span class="font-mono text-brand-600 dark:text-sky-400 font-bold">{{ $member->member_id }}</span>
            </div>

            <!-- Phone / WA -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 flex items-center gap-1.5">
                    <i data-lucide="phone" class="w-3.5 h-3.5 text-brand-500"></i>
                    <span>Nomor Telepon / WhatsApp (Aktif) *</span>
                </label>
                <div class="relative">
                    <input type="tel" name="member_phone" required autofocus
                        value="{{ old('member_phone', $member->member_phone) }}"
                        placeholder="Contoh: 081234567890"
                        class="w-full pl-3.5 pr-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-brand-500 focus:outline-none">
                </div>
                <span class="text-[10px] text-slate-400 mt-1 block">Digunakan untuk notifikasi peminjaman & batas waktu pengembalian buku.</span>
            </div>

            <!-- Email -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 flex items-center gap-1.5">
                    <i data-lucide="mail" class="w-3.5 h-3.5 text-brand-500"></i>
                    <span>Alamat Email (Aktif) *</span>
                </label>
                <div class="relative">
                    <input type="email" name="member_email" required
                        value="{{ old('member_email', $member->member_email) }}"
                        placeholder="Contoh: nama@cyber-univ.ac.id atau nama@gmail.com"
                        class="w-full pl-3.5 pr-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-brand-500 focus:outline-none">
                </div>
                <span class="text-[10px] text-slate-400 mt-1 block">Digunakan untuk pengiriman bukti peminjaman dan pengumuman resmi.</span>
            </div>

            <div class="pt-3 space-y-2">
                <button type="submit" class="w-full py-3 rounded-xl bg-gradient-to-r from-brand-600 to-indigo-600 hover:from-brand-700 hover:to-indigo-700 text-white font-bold text-xs shadow-lg shadow-brand-500/25 flex items-center justify-center gap-2 transition-all">
                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                    <span>Simpan & Aktifkan Seluruh Layanan</span>
                </button>
            </div>
        </form>

        <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800 text-center">
            <form action="{{ route('member.logout') }}" method="POST">
                @csrf
                <button type="submit" class="text-[11px] text-slate-400 hover:text-rose-600 transition-colors inline-flex items-center gap-1">
                    <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                    <span>Batalkan sesi dan keluar akun</span>
                </button>
            </form>
        </div>

    </div>
</div>
@endif
@endsection
