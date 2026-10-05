@extends('layouts.opac')

@section('title', 'Unggah Berkas Skripsi & Bebas Pustaka - ' . $member->member_name)

@section('content')
<div class="bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 py-6">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <a href="{{ route('member.dashboard') }}" class="text-xs font-bold text-brand-600 dark:text-sky-400 hover:underline flex items-center gap-1">
                        <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                        <span>Kembali ke Dasbor</span>
                    </a>
                    <span class="text-slate-300 dark:text-slate-700">/</span>
                    <span class="text-xs font-semibold text-slate-500">Layanan Calon Wisudawan</span>
                </div>
                <h1 class="text-2xl font-black text-slate-900 dark:text-white flex items-center gap-2.5">
                    <span class="p-2 rounded-xl bg-purple-100 dark:bg-purple-950/80 text-purple-600 dark:text-purple-300">
                        <i data-lucide="graduation-cap" class="w-6 h-6"></i>
                    </span>
                    <span>Pengunggahan Skripsi & Bebas Pustaka</span>
                </h1>
                <p class="text-xs text-slate-500 mt-1">Syarat penerbitan Surat Keterangan Bebas Pustaka untuk pendaftaran Wisuda</p>
            </div>

            @if($isBebasPustakaEligible)
                <a href="{{ route('member.bebas-pustaka.print') }}" target="_blank"
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-bold text-sm shadow-lg shadow-emerald-500/20 hover:from-emerald-500 hover:to-teal-500 transition-all transform hover:-translate-y-0.5">
                    <i data-lucide="printer" class="w-4 h-4"></i>
                    <span>Cetak Surat Bebas Pustaka</span>
                </a>
            @endif
        </div>
    </div>
</div>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-900 dark:text-emerald-200 flex items-start gap-3 shadow-sm">
            <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600 dark:text-emerald-400 flex-shrink-0 mt-0.5"></i>
            <div class="text-sm font-semibold leading-relaxed">{{ session('success') }}</div>
        </div>
    @endif

    @if(session('warning'))
        <div class="p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-amber-900 dark:text-amber-200 flex items-start gap-3 shadow-sm">
            <i data-lucide="alert-triangle" class="w-5 h-5 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5"></i>
            <div class="text-sm font-semibold leading-relaxed">{{ session('warning') }}</div>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-900 dark:text-rose-200 shadow-sm">
            <div class="flex items-center gap-2 font-bold text-sm mb-1.5">
                <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600"></i>
                <span>Terdapat kesalahan pengisian formulir:</span>
            </div>
            <ul class="list-disc list-inside text-xs space-y-1 pl-1">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Status Card (If already submitted) -->
    @if($thesis)
        @php
            $spec = json_decode($thesis->spec_detail_info ?? '{}', true) ?: [];
            $status = $spec['status'] ?? ($thesis->opac_hide ? 'pending' : 'approved');
        @endphp

        <div class="rounded-3xl border p-6 sm:p-8 shadow-sm transition-all
            {{ $status === 'approved' ? 'bg-emerald-50/60 dark:bg-emerald-950/20 border-emerald-200 dark:border-emerald-800' : '' }}
            {{ $status === 'revision' ? 'bg-rose-50/60 dark:bg-rose-950/20 border-rose-200 dark:border-rose-800' : '' }}
            {{ $status === 'pending' ? 'bg-amber-50/60 dark:bg-amber-950/20 border-amber-200 dark:border-amber-800' : '' }}">
            
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-current/15">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center font-bold text-white shadow-md
                        {{ $status === 'approved' ? 'bg-emerald-600 shadow-emerald-500/20' : '' }}
                        {{ $status === 'revision' ? 'bg-rose-600 shadow-rose-500/20' : '' }}
                        {{ $status === 'pending' ? 'bg-amber-500 shadow-amber-500/20' : '' }}">
                        @if($status === 'approved')
                            <i data-lucide="badge-check" class="w-7 h-7"></i>
                        @elseif($status === 'revision')
                            <i data-lucide="alert-octagon" class="w-7 h-7"></i>
                        @else
                            <i data-lucide="clock" class="w-7 h-7"></i>
                        @endif
                    </div>
                    <div>
                        <div class="text-[11px] font-bold uppercase tracking-wider opacity-70">Status Verifikasi Berkas Skripsi</div>
                        <div class="text-lg font-black tracking-tight">
                            @if($status === 'approved')
                                <span class="text-emerald-700 dark:text-emerald-300">TERVERIFIKASI & DISETUJUI PUSTAKAWAN</span>
                            @elseif($status === 'revision')
                                <span class="text-rose-700 dark:text-rose-300">PERLU PERBAIKAN / REVISI DOKUMEN</span>
                            @else
                                <span class="text-amber-700 dark:text-amber-300">MENUNGGU VERIFIKASI PUSTAKAWAN</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div>
                    @if($status === 'approved')
                        @if(!$hasActiveLoans)
                            <a href="{{ route('member.bebas-pustaka.print') }}" target="_blank"
                               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md transition-all">
                                <i data-lucide="printer" class="w-4 h-4"></i>
                                <span>Cetak Surat Bebas Pustaka</span>
                            </a>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-200 text-xs font-bold">
                                <i data-lucide="alert-triangle" class="w-3.5 h-3.5"></i>
                                Selesaikan Pinjaman Buku Terlebih Dahulu
                            </span>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Detail submission -->
            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                <div>
                    <span class="text-slate-400 block mb-0.5">Judul Skripsi yang Diajukan:</span>
                    <span class="font-bold text-slate-800 dark:text-slate-200 leading-relaxed block">{{ $thesis->title }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block mb-0.5">Dosen Pembimbing:</span>
                    <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $spec['pembimbing_1'] ?? '-' }}</span>
                    @if(!empty($spec['pembimbing_2']))
                        <span class="text-slate-500"> / {{ $spec['pembimbing_2'] }}</span>
                    @endif
                </div>
                <div>
                    <span class="text-slate-400 block mb-0.5">Waktu Pengajuan:</span>
                    <span class="font-semibold text-slate-800 dark:text-slate-200">{{ \Carbon\Carbon::parse($spec['submitted_at'] ?? $thesis->input_date)->translatedFormat('d F Y, H:i') }} WIB</span>
                </div>
                <div>
                    <span class="text-slate-400 block mb-0.5">Dokumen Digital Skripsi:</span>
                    @if(!empty($thesis->file_att))
                        <a href="{{ asset($thesis->file_att) }}" target="_blank" class="inline-flex items-center gap-1.5 text-brand-600 dark:text-sky-400 font-bold hover:underline">
                            <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
                            <span>Buka Berkas PDF Terunggah</span>
                        </a>
                    @else
                        <span class="text-slate-400">Belum ada file terlampir</span>
                    @endif
                </div>
            </div>

            @if(!empty($spec['notes_admin']))
                <div class="mt-6 p-4 rounded-2xl bg-white/80 dark:bg-slate-900/80 border border-current/20">
                    <span class="text-[11px] font-bold uppercase tracking-wider block mb-1 opacity-75 flex items-center gap-1.5">
                        <i data-lucide="message-square" class="w-3.5 h-3.5"></i>
                        Catatan Pustakawan / Petugas:
                    </span>
                    <p class="text-xs font-medium leading-relaxed">{{ $spec['notes_admin'] }}</p>
                </div>
            @endif

            @if($status === 'approved' && $hasActiveLoans)
                <div class="mt-6 p-4 rounded-2xl bg-amber-100/80 dark:bg-amber-950/60 border border-amber-300 dark:border-amber-800 text-amber-900 dark:text-amber-200 flex items-start gap-3">
                    <i data-lucide="alert-circle" class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5"></i>
                    <div class="text-xs leading-relaxed">
                        <strong class="font-bold block mb-1">Perhatian Penting:</strong>
                        Skripsi Anda telah disetujui, namun pada sistem sirkulasi tercatat Anda masih memiliki pinjaman buku aktif yang belum dikembalikan. Harap segera melakukan pengembalian buku di loket perpustakaan agar tombol <strong>Cetak Surat Keterangan Bebas Pustaka</strong> dapat diaktifkan.
                    </div>
                </div>
            @endif
        </div>
    @endif

    <!-- Highlight Persyaratan Wajib (Requirement Box) -->
    <div class="rounded-3xl p-6 sm:p-8 bg-gradient-to-br from-indigo-900 via-brand-900 to-slate-900 text-white shadow-xl relative overflow-hidden border border-white/10">
        <div class="absolute -right-10 -bottom-10 w-60 h-60 bg-brand-500/10 rounded-full blur-2xl pointer-events-none"></div>

        <div class="flex items-center gap-3 mb-4">
            <span class="p-2 rounded-xl bg-white/10 backdrop-blur text-sky-300">
                <i data-lucide="shield-alert" class="w-5 h-5"></i>
            </span>
            <div>
                <h3 class="font-black text-base text-white tracking-tight">Persyaratan Wajib Dokumen Skripsi</h3>
                <p class="text-xs text-slate-300">Pastikan seluruh butir di bawah ini telah terpenuhi sebelum mengunggah dokumen:</p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-6">
            <!-- Requirement 1 -->
            <div class="p-4 rounded-2xl bg-white/10 backdrop-blur border border-white/10 flex items-start gap-3">
                <div class="p-2 rounded-xl bg-sky-500/30 text-sky-300 flex-shrink-0 mt-0.5">
                    <i data-lucide="file-check-2" class="w-4 h-4"></i>
                </div>
                <div>
                    <span class="font-bold text-xs text-white block mb-0.5">1 File Utuh PDF</span>
                    <p class="text-[11px] text-slate-300 leading-snug">Dokumen wajib disatukan menjadi <strong>1 file tunggal PDF</strong> (tidak terpisah-pisah per bab).</p>
                </div>
            </div>

            <!-- Requirement 2 -->
            <div class="p-4 rounded-2xl bg-white/10 backdrop-blur border border-white/10 flex items-start gap-3">
                <div class="p-2 rounded-xl bg-purple-500/30 text-purple-300 flex-shrink-0 mt-0.5">
                    <i data-lucide="hard-drive" class="w-4 h-4"></i>
                </div>
                <div>
                    <span class="font-bold text-xs text-white block mb-0.5">Ukuran Maksimal 10 MB</span>
                    <p class="text-[11px] text-slate-300 leading-snug">Kapasitas ukuran berkas tidak boleh melebihi batas <strong>10 MB</strong>.</p>
                </div>
            </div>

            <!-- Requirement 3 -->
            <div class="p-4 rounded-2xl bg-white/10 backdrop-blur border border-white/10 flex items-start gap-3">
                <div class="p-2 rounded-xl bg-emerald-500/30 text-emerald-300 flex-shrink-0 mt-0.5">
                    <i data-lucide="book-open-check" class="w-4 h-4"></i>
                </div>
                <div>
                    <span class="font-bold text-xs text-white block mb-0.5">Cover s.d. Lampiran Lengkap</span>
                    <p class="text-[11px] text-slate-300 leading-snug">Dimulai dari halaman judul/cover, kata pengantar, abstrak, seluruh bab, daftar pustaka, hingga lampiran.</p>
                </div>
            </div>

            <!-- Requirement 4 -->
            <div class="p-4 rounded-2xl bg-white/10 backdrop-blur border border-white/10 flex items-start gap-3">
                <div class="p-2 rounded-xl bg-amber-500/30 text-amber-300 flex-shrink-0 mt-0.5">
                    <i data-lucide="pen-tool" class="w-4 h-4"></i>
                </div>
                <div>
                    <span class="font-bold text-xs text-white block mb-0.5">Lembar Pengesahan Bertanda Tangan</span>
                    <p class="text-[11px] text-slate-300 leading-snug">Lembar pengesahan <strong>wajib sudah ditandatangani lengkap</strong> oleh Pembimbing, Penguji, dan Dekan/Kaprodi.</p>
                </div>
            </div>

            <!-- Requirement 5 -->
            <div class="p-4 rounded-2xl bg-white/10 backdrop-blur border border-white/10 flex items-start gap-3">
                <div class="p-2 rounded-xl bg-cyan-500/30 text-cyan-300 flex-shrink-0 mt-0.5">
                    <i data-lucide="droplet" class="w-4 h-4"></i>
                </div>
                <div>
                    <span class="font-bold text-xs text-white block mb-0.5">Memuat Watermark Resmi</span>
                    <p class="text-[11px] text-slate-300 leading-snug">Setiap halaman dokumen naskah wajib telah diberi <strong>watermark resmi kampus Universitas Siber Indonesia</strong>.</p>
                </div>
            </div>

            <!-- Requirement 6 -->
            <div class="p-4 rounded-2xl bg-white/10 backdrop-blur border border-white/10 flex items-start gap-3">
                <div class="p-2 rounded-xl bg-rose-500/30 text-rose-300 flex-shrink-0 mt-0.5">
                    <i data-lucide="check-square" class="w-4 h-4"></i>
                </div>
                <div>
                    <span class="font-bold text-xs text-white block mb-0.5">Bebas Tanggungan Buku</span>
                    <p class="text-[11px] text-slate-300 leading-snug">Tidak memiliki buku pinjaman aktif maupun denda yang belum diselesaikan di perpustakaan.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- PANDUAN RESMI KETENTUAN PEMBERIAN WATERMARK SKRIPSI CALON WISUDAWAN -->
    <div id="ketentuan-watermark" class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 sm:p-8 shadow-sm space-y-6">
        
        <!-- Header Dokumen Resmi -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-slate-200 dark:border-slate-800">
            <div class="flex items-start gap-4">
                <div class="w-14 h-14 rounded-2xl bg-brand-50 dark:bg-sky-950/60 text-brand-600 dark:text-sky-400 flex items-center justify-center flex-shrink-0 border border-brand-200 dark:border-sky-800 shadow-sm">
                    <i data-lucide="stamp" class="w-7 h-7"></i>
                </div>
                <div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-brand-100 dark:bg-brand-950/80 text-brand-700 dark:text-brand-300 border border-brand-200 dark:border-brand-800 mb-1.5">
                        <i data-lucide="file-check" class="w-3.5 h-3.5"></i>
                        Ketentuan Institusional Perpustakaan
                    </span>
                    <h2 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                        Ketentuan Pemberian Watermark File Skripsi Calon Wisudawan
                    </h2>
                    <p class="text-xs font-bold text-slate-500 dark:text-slate-400 mt-0.5">
                        UPT Perpustakaan & Institutional Repository Universitas Siber Indonesia (Cyber University)
                    </p>
                </div>
            </div>

            <!-- Tombol Unduh Logo Watermark Resmi -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 flex-shrink-0">
                <a href="{{ route('member.watermark.download') }}"
                   download="Watermark_Universitas_Siber_Indonesia.png"
                   class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-xs shadow-md shadow-emerald-500/20 transition-all cursor-pointer"
                   title="Unduh logo watermark resmi berlatar transparan untuk skripsi">
                    <i data-lucide="download" class="w-4 h-4"></i>
                    <span>Unduh Logo Watermark Resmi (PNG)</span>
                </a>
            </div>
        </div>

        <!-- Paragraf Pengantar -->
        <div class="p-4 rounded-2xl bg-sky-50 dark:bg-sky-950/40 border border-sky-200 dark:border-sky-800 text-sky-900 dark:text-sky-200 text-xs leading-relaxed">
            Untuk menjaga identitas institusi, memberikan penanda kepemilikan institusional, serta mendukung pengelolaan dan penyimpanan karya ilmiah pada <strong>Institutional Repository Universitas Siber Indonesia</strong>, setiap file skripsi yang akan diunggah melalui sistem Perpustakaan <strong>wajib menggunakan watermark</strong> sesuai ketentuan di bawah ini:
        </div>

        <!-- 4 Grid Bagian Utama Panduan -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- BAGIAN A & C: KETENTUAN UMUM & FILE LOGO -->
            <div class="space-y-4">
                <!-- A. Ketentuan Umum -->
                <div class="p-5 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/40 space-y-3">
                    <h3 class="font-extrabold text-sm text-slate-900 dark:text-white flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-brand-600 text-white font-black text-xs flex items-center justify-center">A</span>
                        <span>Ketentuan Umum Watermark</span>
                    </h3>
                    <ul class="text-xs text-slate-600 dark:text-slate-300 space-y-2 list-none pl-1">
                        <li class="flex items-start gap-2">
                            <i data-lucide="check" class="w-4 h-4 text-emerald-500 flex-shrink-0 mt-0.5"></i>
                            <span><strong>Wajib dicantumkan:</strong> Dicantumkan pada setiap halaman file skripsi yang diunggah ke sistem Perpustakaan.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i data-lucide="check" class="w-4 h-4 text-emerald-500 flex-shrink-0 mt-0.5"></i>
                            <span><strong>Logo Resmi:</strong> Menggunakan logo resmi Universitas Siber Indonesia yang telah ditetapkan oleh Universitas.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i data-lucide="check" class="w-4 h-4 text-emerald-500 flex-shrink-0 mt-0.5"></i>
                            <span><strong>Sumber Logo:</strong> Wajib menggunakan file logo watermark resmi yang disediakan oleh Perpustakaan/Universitas (dilarang menggunakan logo unduhan internet lain).</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i data-lucide="check" class="w-4 h-4 text-emerald-500 flex-shrink-0 mt-0.5"></i>
                            <span><strong>Keterbacaan:</strong> Watermark harus tetap terlihat, tetapi tidak boleh mengganggu keterbacaan teks, tabel, gambar, grafik, maupun unsur akademik lainnya.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i data-lucide="check" class="w-4 h-4 text-emerald-500 flex-shrink-0 mt-0.5"></i>
                            <span><strong>Permanen di PDF:</strong> Merupakan bagian dari file PDF final yang diunggah dan harus tetap terlihat ketika dokumen dibuka maupun dicetak.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i data-lucide="x-circle" class="w-4 h-4 text-rose-500 flex-shrink-0 mt-0.5"></i>
                            <span><strong>Tidak Boleh Menutupi:</strong> Teks utama, nomor halaman, tabel, gambar/grafik, tanda tangan, stempel, barcode/QR Code, atau elemen penting lainnya.</span>
                        </li>
                    </ul>
                </div>

                <!-- C. File Logo Watermark Resmi -->
                <div class="p-5 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/40 space-y-3">
                    <h3 class="font-extrabold text-sm text-slate-900 dark:text-white flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-brand-600 text-white font-black text-xs flex items-center justify-center">C</span>
                        <span>File Logo Watermark Resmi</span>
                    </h3>
                    <div class="text-xs text-slate-600 dark:text-slate-300 space-y-2">
                        <p>Format file yang disediakan UPT Perpustakaan:</p>
                        <div class="p-3 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 font-mono text-[11px] text-slate-800 dark:text-slate-200">
                            Nama File: <strong>Watermark_Universitas_Siber_Indonesia.png</strong>
                        </div>
                        <ul class="space-y-1.5 pl-1 list-disc list-inside text-[11px] text-slate-500 dark:text-slate-400">
                            <li>Background transparan (PNG Alpha Channel)</li>
                            <li>Resolusi tinggi (crisp & tajam saat dicetak)</li>
                            <li>Proporsi logo tidak berubah & tidak mengalami distorsi</li>
                            <li>Tidak diberi tambahan teks atau efek lain oleh mahasiswa</li>
                        </ul>
                        <div class="pt-2">
                            <a href="{{ route('member.watermark.download') }}"
                               download="Watermark_Universitas_Siber_Indonesia.png"
                               class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-900 dark:bg-slate-700 dark:hover:bg-slate-600 text-white font-bold text-[11px] cursor-pointer">
                                <i data-lucide="download" class="w-3.5 h-3.5 text-emerald-400"></i>
                                <span>Klik di Sini untuk Unduh File Logo PNG</span>
                            </a>
                        </div>

                        <!-- Preview Logo Watermark Transparan & Opsi Simpan Langsung -->
                        <div class="mt-3 p-3 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700">
                            <div class="flex items-center gap-3">
                                <div class="w-16 h-16 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 flex items-center justify-center p-1.5 flex-shrink-0" style="background-image: linear-gradient(45deg, #cbd5e1 25%, transparent 25%), linear-gradient(-45deg, #cbd5e1 25%, transparent 25%), linear-gradient(45deg, transparent 75%, #cbd5e1 75%), linear-gradient(-45deg, transparent 75%, #cbd5e1 75%); background-size: 10px 10px; background-position: 0 0, 0 5px, 5px -5px, -5px 0;">
                                    <img src="{{ asset('images/Watermark_Universitas_Siber_Indonesia.png') }}" 
                                         alt="Watermark Universitas Siber Indonesia" 
                                         class="max-w-full max-h-full object-contain">
                                </div>
                                <div class="text-[11px] text-slate-600 dark:text-slate-300 leading-snug">
                                    <p class="font-bold text-slate-800 dark:text-white">Pratinjau File Watermark Transparan:</p>
                                    <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-0.5">
                                        Jika tombol unduh terkendala ekstensi di peramban Anda, <strong>klik kanan</strong> gambar logo di samping lalu pilih <em>"Simpan gambar sebagai..."</em> (Save image as...) untuk menyimpan langsung sebagai <strong class="text-emerald-600 dark:text-emerald-400">.png</strong>.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BAGIAN B: TABEL SPESIFIKASI TEKNIS WATERMARK -->
            <div class="p-5 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/40 space-y-3 flex flex-col justify-between">
                <div>
                    <h3 class="font-extrabold text-sm text-slate-900 dark:text-white flex items-center gap-2 mb-3">
                        <span class="w-6 h-6 rounded-lg bg-brand-600 text-white font-black text-xs flex items-center justify-center">B</span>
                        <span>Spesifikasi Teknis Watermark</span>
                    </h3>
                    
                    <div class="overflow-hidden rounded-xl border border-slate-200 dark:border-slate-700">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 font-bold border-b border-slate-200 dark:border-slate-700">
                                <tr>
                                    <th class="px-3.5 py-2.5 w-1/3">Komponen</th>
                                    <th class="px-3.5 py-2.5">Ketentuan Standar</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-slate-700 font-medium">
                                <tr class="bg-white dark:bg-slate-900">
                                    <td class="px-3.5 py-2 font-bold text-slate-700 dark:text-slate-300">Objek</td>
                                    <td class="px-3.5 py-2 text-slate-600 dark:text-slate-300">Logo resmi Universitas Siber Indonesia</td>
                                </tr>
                                <tr class="bg-slate-50/50 dark:bg-slate-800/50">
                                    <td class="px-3.5 py-2 font-bold text-slate-700 dark:text-slate-300">Format Logo</td>
                                    <td class="px-3.5 py-2 text-slate-600 dark:text-slate-300">PNG dengan latar belakang transparan</td>
                                </tr>
                                <tr class="bg-white dark:bg-slate-900">
                                    <td class="px-3.5 py-2 font-bold text-slate-700 dark:text-slate-300">Posisi</td>
                                    <td class="px-3.5 py-2 text-slate-600 dark:text-slate-300">Tepat di tengah halaman (Center)</td>
                                </tr>
                                <tr class="bg-slate-50/50 dark:bg-slate-800/50">
                                    <td class="px-3.5 py-2 font-bold text-slate-700 dark:text-slate-300">Orientasi</td>
                                    <td class="px-3.5 py-2 text-slate-600 dark:text-slate-300">Horizontal normal, tidak diputar (0 derajat)</td>
                                </tr>
                                <tr class="bg-white dark:bg-slate-900">
                                    <td class="px-3.5 py-2 font-bold text-slate-700 dark:text-slate-300">Ukuran</td>
                                    <td class="px-3.5 py-2 text-slate-600 dark:text-slate-300">Proporsional, sekitar <strong>25 - 35%</strong> lebar area halaman</td>
                                </tr>
                                <tr class="bg-slate-50/50 dark:bg-slate-800/50">
                                    <td class="px-3.5 py-2 font-bold text-slate-700 dark:text-slate-300">Transparansi</td>
                                    <td class="px-3.5 py-2 font-bold text-brand-600 dark:text-sky-400">Sekitar 80% - 90% transparan</td>
                                </tr>
                                <tr class="bg-white dark:bg-slate-900">
                                    <td class="px-3.5 py-2 font-bold text-slate-700 dark:text-slate-300">Opacity</td>
                                    <td class="px-3.5 py-2 font-bold text-brand-600 dark:text-sky-400">Sekitar 10% - 20%</td>
                                </tr>
                                <tr class="bg-slate-50/50 dark:bg-slate-800/50">
                                    <td class="px-3.5 py-2 font-bold text-slate-700 dark:text-slate-300">Warna</td>
                                    <td class="px-3.5 py-2 text-slate-600 dark:text-slate-300">Mengikuti warna asli resmi logo Universitas</td>
                                </tr>
                                <tr class="bg-white dark:bg-slate-900">
                                    <td class="px-3.5 py-2 font-bold text-slate-700 dark:text-slate-300">Kemiringan</td>
                                    <td class="px-3.5 py-2 text-slate-600 dark:text-slate-300">Tidak diputar / 0&deg;</td>
                                </tr>
                                <tr class="bg-slate-50/50 dark:bg-slate-800/50">
                                    <td class="px-3.5 py-2 font-bold text-slate-700 dark:text-slate-300">Halaman</td>
                                    <td class="px-3.5 py-2 text-slate-600 dark:text-slate-300">Seluruh halaman dokumen naskah</td>
                                </tr>
                                <tr class="bg-white dark:bg-slate-900">
                                    <td class="px-3.5 py-2 font-bold text-slate-700 dark:text-slate-300">Lapisan (Layer)</td>
                                    <td class="px-3.5 py-2 text-slate-600 dark:text-slate-300">Di belakang teks (Behind text)</td>
                                </tr>
                                <tr class="bg-slate-50/50 dark:bg-slate-800/50">
                                    <td class="px-3.5 py-2 font-bold text-slate-700 dark:text-slate-300">Gangguan Isi</td>
                                    <td class="px-3.5 py-2 text-rose-600 dark:text-rose-400 font-bold">Tidak boleh mengurangi keterbacaan dokumen</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="text-[11px] text-slate-400 italic mt-3">
                    * Catatan: Nilai transparansi/opacity di atas merupakan standar teknis internal Universitas Siber Indonesia.
                </div>
            </div>

        </div>

        <!-- BAGIAN D, E, F, G: CAKUPAN HALAMAN & FORMAT AKHIR -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 pt-4 border-t border-slate-200 dark:border-slate-800">

            <!-- BAGIAN D & E: CAKUPAN 14 HALAMAN SKRIPSI -->
            <div class="p-5 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/40 space-y-3">
                <h3 class="font-extrabold text-sm text-slate-900 dark:text-white flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-brand-600 text-white font-black text-xs flex items-center justify-center">D</span>
                    <span>Cakupan Halaman Skripsi yang Wajib Diberi Watermark</span>
                </h3>
                <p class="text-xs text-slate-500">Watermark wajib diterapkan pada <strong>seluruh halaman skripsi tanpa terlewat</strong>, termasuk:</p>
                
                <div class="grid grid-cols-2 gap-2 text-xs text-slate-700 dark:text-slate-300 font-medium">
                    <div class="space-y-1">
                        <div class="flex items-center gap-1.5"><span class="w-5 h-5 rounded-full bg-slate-200 dark:bg-slate-700 text-[10px] font-bold flex items-center justify-center">1</span> Cover / Sampul Depan</div>
                        <div class="flex items-center gap-1.5"><span class="w-5 h-5 rounded-full bg-slate-200 dark:bg-slate-700 text-[10px] font-bold flex items-center justify-center">2</span> Halaman Judul</div>
                        <div class="flex items-center gap-1.5"><span class="w-5 h-5 rounded-full bg-slate-200 dark:bg-slate-700 text-[10px] font-bold flex items-center justify-center">3</span> Abstrak</div>
                        <div class="flex items-center gap-1.5"><span class="w-5 h-5 rounded-full bg-slate-200 dark:bg-slate-700 text-[10px] font-bold flex items-center justify-center">4</span> Kata Pengantar</div>
                        <div class="flex items-center gap-1.5"><span class="w-5 h-5 rounded-full bg-slate-200 dark:bg-slate-700 text-[10px] font-bold flex items-center justify-center">5</span> Daftar Isi</div>
                        <div class="flex items-center gap-1.5"><span class="w-5 h-5 rounded-full bg-slate-200 dark:bg-slate-700 text-[10px] font-bold flex items-center justify-center">6</span> Daftar Tabel</div>
                        <div class="flex items-center gap-1.5"><span class="w-5 h-5 rounded-full bg-slate-200 dark:bg-slate-700 text-[10px] font-bold flex items-center justify-center">7</span> Daftar Gambar</div>
                    </div>
                    <div class="space-y-1">
                        <div class="flex items-center gap-1.5"><span class="w-5 h-5 rounded-full bg-slate-200 dark:bg-slate-700 text-[10px] font-bold flex items-center justify-center">8</span> Bab I (Pendahuluan)</div>
                        <div class="flex items-center gap-1.5"><span class="w-5 h-5 rounded-full bg-slate-200 dark:bg-slate-700 text-[10px] font-bold flex items-center justify-center">9</span> Bab II (Landasan Teori)</div>
                        <div class="flex items-center gap-1.5"><span class="w-5 h-5 rounded-full bg-slate-200 dark:bg-slate-700 text-[10px] font-bold flex items-center justify-center">10</span> Bab III (Metodologi)</div>
                        <div class="flex items-center gap-1.5"><span class="w-5 h-5 rounded-full bg-slate-200 dark:bg-slate-700 text-[10px] font-bold flex items-center justify-center">11</span> Bab IV (Hasil & Pembahasan)</div>
                        <div class="flex items-center gap-1.5"><span class="w-5 h-5 rounded-full bg-slate-200 dark:bg-slate-700 text-[10px] font-bold flex items-center justify-center">12</span> Bab V (Penutup)</div>
                        <div class="flex items-center gap-1.5"><span class="w-5 h-5 rounded-full bg-slate-200 dark:bg-slate-700 text-[10px] font-bold flex items-center justify-center">13</span> Daftar Pustaka</div>
                        <div class="flex items-center gap-1.5"><span class="w-5 h-5 rounded-full bg-slate-200 dark:bg-slate-700 text-[10px] font-bold flex items-center justify-center">14</span> Lampiran & DRH</div>
                    </div>
                </div>

                <!-- Bagian E: Pengecualian -->
                <div class="mt-4 pt-3 border-t border-slate-200 dark:border-slate-700 text-[11px] text-slate-500 dark:text-slate-400">
                    <strong class="text-slate-700 dark:text-slate-200">E. Pengecualian:</strong> Apabila terdapat dokumen/lampiran yang memiliki karakter khusus (seperti formulir resmi, sertifikat, dokumen pihak ketiga, atau dokumen yang memiliki ketentuan reproduksi tertentu), mahasiswa dapat berkonsultasi dengan Pustakawan sebelum melakukan pengunggahan.
                </div>
            </div>

            <!-- BAGIAN F & G: FORMAT FILE AKHIR & VERIFIKASI PUSTAKAWAN -->
            <div class="p-5 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/40 space-y-3">
                <h3 class="font-extrabold text-sm text-slate-900 dark:text-white flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-brand-600 text-white font-black text-xs flex items-center justify-center">F</span>
                    <span>Format File Akhir & Kriteria Verifikasi</span>
                </h3>
                
                <div class="space-y-2 text-xs text-slate-600 dark:text-slate-300">
                    <div class="p-3 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 space-y-1.5">
                        <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block">7 Syarat File Akhir:</span>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-1 text-[11px]">
                            <div>1. Format file <strong>PDF (.pdf)</strong></div>
                            <div>2. Digabung jadi <strong>1 file utuh</strong></div>
                            <div>3. Terbaca dengan baik</div>
                            <div>4. Watermark tampak jelas</div>
                            <div>5. TTD pengesahan jelas</div>
                            <div>6. <strong>Ukuran maks. 10 MB</strong></div>
                            <div class="col-span-2">7. Versi final skripsi yang telah disahkan</div>
                        </div>
                    </div>

                    <div class="p-3 rounded-xl bg-purple-50 dark:bg-purple-950/40 border border-purple-200 dark:border-purple-800 text-purple-900 dark:text-purple-200">
                        <span class="text-[10px] font-black uppercase tracking-wider text-purple-700 dark:text-purple-300 block mb-1">
                            G. Pemeriksaan oleh Pustakawan:
                        </span>
                        <div class="grid grid-cols-2 gap-1 text-[11px]">
                            <div>&check; Kelengkapan dokumen</div>
                            <div>&check; Kesesuaian susunan</div>
                            <div>&check; Format file PDF</div>
                            <div>&check; Tanda tangan/pengesahan</div>
                            <div>&check; Watermark resmi</div>
                            <div>&check; Posisi & keterbacaan</div>
                            <div class="col-span-2">&check; Ukuran file maksimal 10 MB</div>
                        </div>
                    </div>
                </div>

                <div class="p-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-900 dark:text-emerald-200 text-[11px] leading-relaxed">
                    <strong>Penerbitan Surat Bebas Pustaka:</strong> Setelah status <strong>TERVERIFIKASI</strong> diberikan oleh Pustakawan, maka fitur <strong>Cetak Keterangan Bebas Pustaka</strong> akan otomatis aktif dan dapat langsung dicetak oleh calon wisudawan/i.
                </div>
            </div>

        </div>

        <!-- PENTING - Alert Box -->
        <div class="p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border-2 border-amber-300 dark:border-amber-700 text-amber-950 dark:text-amber-200 flex items-start gap-3 shadow-sm">
            <i data-lucide="alert-triangle" class="w-6 h-6 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5"></i>
            <div class="text-xs leading-relaxed">
                <strong class="font-extrabold text-amber-900 dark:text-amber-100 block mb-1 uppercase tracking-wide">
                    PENTING: Jangan Gunakan Logo dari Google atau Media Sosial!
                </strong>
                Jangan menggunakan logo Universitas Siber Indonesia yang diperoleh dari Google, media sosial, atau sumber internet lainnya sebagai watermark. Gunakanlah <strong>file watermark resmi</strong> yang disediakan oleh Perpustakaan Universitas Siber Indonesia untuk memastikan keseragaman identitas visual dan kualitas dokumen ilmiah institusi.
            </div>
        </div>

    </div>

    <!-- Upload Form -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 sm:p-8 shadow-sm">
        <div class="mb-6 pb-4 border-b border-slate-100 dark:border-slate-800">
            <h2 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="upload-cloud" class="w-5 h-5 text-brand-600 dark:text-sky-400"></i>
                <span>{{ $thesis ? 'Formulir Pembaruan / Pengajuan Berkas Skripsi' : 'Formulir Pengajuan Skripsi Calon Wisudawan' }}</span>
            </h2>
            <p class="text-xs text-slate-500 mt-0.5">Informasi biodata mahasiswa otomatis terisi dari sistem.</p>
        </div>

        <form action="{{ route('member.skripsi.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Section 1: Readonly Identity Fields -->
            <div class="p-4 sm:p-5 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-800">
                <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block mb-3 flex items-center gap-1.5">
                    <i data-lucide="user-check" class="w-3.5 h-3.5 text-brand-500"></i>
                    Data Mahasiswa (Otomatis Terisi dari Sistem)
                </span>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1">NIM Mahasiswa</label>
                        <input type="text" value="{{ $member->member_id }}" readonly
                               class="w-full px-3 py-2 rounded-xl bg-slate-200/70 dark:bg-slate-700/60 border border-slate-300 dark:border-slate-600 text-xs font-mono font-bold text-slate-800 dark:text-slate-200 cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1">Nama Mahasiswa</label>
                        <input type="text" value="{{ $member->member_name }}" readonly
                               class="w-full px-3 py-2 rounded-xl bg-slate-200/70 dark:bg-slate-700/60 border border-slate-300 dark:border-slate-600 text-xs font-bold text-slate-800 dark:text-slate-200 cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1">Program Studi</label>
                        <input type="text" value="{{ $member->prodi_name }}" readonly
                               class="w-full px-3 py-2 rounded-xl bg-slate-200/70 dark:bg-slate-700/60 border border-slate-300 dark:border-slate-600 text-xs font-bold text-slate-800 dark:text-slate-200 cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1">Semester</label>
                        <input type="text" value="Semester {{ $member->semester }}" readonly
                               class="w-full px-3 py-2 rounded-xl bg-slate-200/70 dark:bg-slate-700/60 border border-slate-300 dark:border-slate-600 text-xs font-bold text-brand-600 dark:text-sky-300 cursor-not-allowed">
                    </div>
                </div>
            </div>

            <!-- Section 2: Thesis Details -->
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                        Judul Lengkap Skripsi / Tugas Akhir *
                    </label>
                    <textarea name="title" rows="3" required placeholder="Tuliskan judul lengkap skripsi Anda sesuai lembar pengesahan..."
                              class="w-full px-4 py-3 rounded-2xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold text-slate-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-brand-500 leading-relaxed">{{ old('title', $thesis?->title) }}</textarea>
                    <div class="mt-1.5 flex items-center gap-1.5 text-[11px] text-slate-500 dark:text-slate-400">
                        <i data-lucide="sparkles" class="w-3.5 h-3.5 text-purple-500"></i>
                        <span>Klasifikasi <strong>Subjek & Bidang Ilmu</strong> skripsi Anda akan direkomendasikan & ditetapkan secara otomatis oleh sistem perpustakaan sesuai judul dan program studi Anda.</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                            Dosen Pembimbing 1 *
                        </label>
                        @php
                            $curSpec = json_decode($thesis?->spec_detail_info ?? '{}', true) ?: [];
                        @endphp
                        <input type="text" list="dosenList1" name="pembimbing_1" value="{{ old('pembimbing_1', $curSpec['pembimbing_1'] ?? '') }}" required
                               placeholder="Ketik atau pilih nama Dosen Pembimbing 1..."
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold text-slate-900 dark:text-white">
                        <datalist id="dosenList1">
                            @foreach($dosenMembers as $dm)
                                <option value="{{ $dm->member_name }}">{{ $dm->member_name }}</option>
                            @endforeach
                        </datalist>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                            Dosen Pembimbing 2 (Opsional)
                        </label>
                        <input type="text" list="dosenList2" name="pembimbing_2" value="{{ old('pembimbing_2', $curSpec['pembimbing_2'] ?? '') }}"
                               placeholder="Ketik atau pilih nama Dosen Pembimbing 2 (jika ada)..."
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold text-slate-900 dark:text-white">
                        <datalist id="dosenList2">
                            @foreach($dosenMembers as $dm)
                                <option value="{{ $dm->member_name }}">{{ $dm->member_name }}</option>
                            @endforeach
                        </datalist>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                            Tahun Lulus / Skripsi *
                        </label>
                        <input type="number" name="publish_year" min="2018" max="2099" value="{{ old('publish_year', $thesis?->publish_year ?? date('Y')) }}" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold text-slate-900 dark:text-white">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider flex items-center justify-between">
                            <span>Berkas Naskah Skripsi (PDF) {{ $thesis ? '(Opsional jika tidak ganti berkas)' : '*' }}</span>
                            <span class="text-[10px] text-brand-600 dark:text-sky-400 font-bold lowercase">maksimal 10 mb</span>
                        </label>
                        <input type="file" name="skripsi_file" accept=".pdf" {{ $thesis ? '' : 'required' }}
                               class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-900 dark:text-white file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-brand-50 file:text-brand-700 dark:file:bg-sky-950 dark:file:text-sky-300">
                        @if($thesis && !empty($thesis->file_att))
                            <span class="text-[10px] text-slate-500 mt-1 block">
                                Berkas tersimpan saat ini: <a href="{{ asset($thesis->file_att) }}" target="_blank" class="text-brand-600 underline font-bold">{{ basename($thesis->file_att) }}</a>. Unggah file baru untuk memperbarui.
                            </span>
                        @endif
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                        Abstrak / Ringkasan Skripsi *
                    </label>
                    <textarea name="abstract" rows="4" required placeholder="Tuliskan teks abstrak skripsi dalam bahasa Indonesia..."
                              class="w-full px-4 py-3 rounded-2xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-brand-500 leading-relaxed">{{ old('abstract', $thesis?->notes) }}</textarea>
                </div>
            </div>

            <!-- Section 3: Interactive Checklist Requirements Confirmation -->
            <div class="p-5 rounded-2xl bg-amber-50/70 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900/60 space-y-3">
                <span class="text-xs font-black text-amber-900 dark:text-amber-200 uppercase tracking-wider block flex items-center gap-1.5">
                    <i data-lucide="check-circle" class="w-4 h-4 text-amber-600"></i>
                    Konfirmasi Kelengkapan Persyaratan Naskah Skripsi
                </span>
                <p class="text-[11px] text-amber-800 dark:text-amber-300/80">
                    Beri tanda centang pada setiap butir untuk memastikan berkas yang Anda unggah telah memenuhi seluruh kriteria verifikasi:
                </p>

                <div class="space-y-2.5 pt-1 text-xs text-slate-800 dark:text-slate-200 font-medium">
                    <label class="flex items-start gap-2.5 cursor-pointer">
                        <input type="checkbox" name="agree_single_pdf" value="1" required {{ old('agree_single_pdf') ? 'checked' : '' }}
                               class="w-4 h-4 rounded text-brand-600 focus:ring-brand-500 mt-0.5">
                        <span>Saya mengonfirmasi naskah adalah <strong>1 file utuh bertipe PDF</strong>.</span>
                    </label>

                    <label class="flex items-start gap-2.5 cursor-pointer">
                        <input type="checkbox" name="agree_max_10mb" value="1" required {{ old('agree_max_10mb') ? 'checked' : '' }}
                               class="w-4 h-4 rounded text-brand-600 focus:ring-brand-500 mt-0.5">
                        <span>Saya mengonfirmasi ukuran file tidak melebihi <strong>10 MB</strong>.</span>
                    </label>

                    <label class="flex items-start gap-2.5 cursor-pointer">
                        <input type="checkbox" name="agree_complete" value="1" required {{ old('agree_complete') ? 'checked' : '' }}
                               class="w-4 h-4 rounded text-brand-600 focus:ring-brand-500 mt-0.5">
                        <span>Saya mengonfirmasi naskah lengkap <strong>dimulai dari halaman Cover hingga Daftar Pustaka & Lampiran</strong>.</span>
                    </label>

                    <label class="flex items-start gap-2.5 cursor-pointer">
                        <input type="checkbox" name="agree_signed" value="1" required {{ old('agree_signed') ? 'checked' : '' }}
                               class="w-4 h-4 rounded text-brand-600 focus:ring-brand-500 mt-0.5">
                        <span>Saya mengonfirmasi <strong>Lembar Pengesahan telah ditandatangani lengkap</strong> oleh Pembimbing, Penguji, dan Dekan/Kaprodi.</span>
                    </label>

                    <label class="flex items-start gap-2.5 cursor-pointer">
                        <input type="checkbox" name="agree_watermark" value="1" required {{ old('agree_watermark') ? 'checked' : '' }}
                               class="w-4 h-4 rounded text-brand-600 focus:ring-brand-500 mt-0.5">
                        <span>Saya mengonfirmasi seluruh halaman dokumen telah memuat <strong>Watermark resmi Universitas Siber Indonesia</strong>.</span>
                    </label>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-between pt-4 border-t border-slate-100 dark:border-slate-800 flex-wrap gap-4">
                <a href="{{ route('member.dashboard') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800">
                    Batal
                </a>

                <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-brand-600 hover:bg-brand-700 text-white font-black text-xs uppercase tracking-wider shadow-lg shadow-brand-500/25 transition-all transform hover:-translate-y-0.5">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span>{{ $thesis ? 'Simpan Pembaruan Naskah Skripsi' : 'Unggah & Ajukan Verifikasi Skripsi' }}</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
