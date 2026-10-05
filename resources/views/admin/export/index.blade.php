@extends('layouts.admin')

@section('title', 'Pusat Ekspor Data & Laporan')
@section('header_title', 'Pusat Ekspor Data Perpustakaan (Multi-Format)')

@section('content')
<div class="space-y-8">

    <!-- Hero Banner -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-slate-900 via-brand-950 to-slate-900 p-8 text-white shadow-xl border border-slate-800">
        <div class="relative z-10 max-w-3xl">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-brand-500/20 text-brand-300 border border-brand-500/30 text-xs font-bold uppercase tracking-wider mb-3">
                <i data-lucide="file-spreadsheet" class="w-3.5 h-3.5"></i>
                Modul Ekspor & Pelaporan Terintegrasi
            </span>
            <h2 class="text-2xl lg:text-3xl font-black tracking-tight text-white mb-2">
                Pusat Unduh & Ekspor Data Perpustakaan
            </h2>
            <p class="text-slate-300 text-sm leading-relaxed">
                Unduh rekapitulasi data koleksi dari masing-masing GMD (Buku Teks, Skripsi, Jurnal, e-Book), data keanggotaan mahasiswa/dosen, serta buku tamu kunjungan dalam format <strong class="text-white">Microsoft Excel (.xls)</strong>, <strong class="text-white">Microsoft Word (.doc)</strong>, <strong class="text-white">Cetak / Simpan PDF</strong>, atau <strong class="text-white">CSV Universal</strong>.
            </p>
        </div>
        <div class="absolute right-0 top-0 bottom-0 opacity-10 flex items-center pr-12 pointer-events-none">
            <i data-lucide="download-cloud" class="w-64 h-64 text-white"></i>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center flex-shrink-0">
                <i data-lucide="book" class="w-5 h-5"></i>
            </div>
            <div>
                <div class="text-[10px] uppercase font-bold text-slate-400">Total Koleksi</div>
                <div class="text-lg font-black text-slate-800 dark:text-white">{{ number_format($stats['total_biblio']) }} Judul</div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center flex-shrink-0">
                <i data-lucide="graduation-cap" class="w-5 h-5"></i>
            </div>
            <div>
                <div class="text-[10px] uppercase font-bold text-slate-400">Koleksi Skripsi</div>
                <div class="text-lg font-black text-purple-600 dark:text-purple-400">{{ number_format($stats['total_skripsi']) }} Dokumen</div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center flex-shrink-0">
                <i data-lucide="users" class="w-5 h-5"></i>
            </div>
            <div>
                <div class="text-[10px] uppercase font-bold text-slate-400">Total Anggota</div>
                <div class="text-lg font-black text-emerald-600 dark:text-emerald-400">{{ number_format($stats['total_member']) }} Orang</div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center flex-shrink-0">
                <i data-lucide="book-open-check" class="w-5 h-5"></i>
            </div>
            <div>
                <div class="text-[10px] uppercase font-bold text-slate-400">Buku Tamu</div>
                <div class="text-lg font-black text-amber-600 dark:text-amber-400">{{ number_format($stats['total_visits']) }} Kunjungan</div>
            </div>
        </div>
    </div>

    <!-- 3 Export Section Cards -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- CARD 1: EKSPOR KOLEKSI BIBLIOGRAFI PER GMD -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm flex flex-col justify-between" x-data="{ gmdId: '', year: '' }">
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-sky-50 dark:bg-sky-950/60 text-brand-600 dark:text-sky-400 flex items-center justify-center">
                        <i data-lucide="book-copy" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <span class="text-[10px] font-black uppercase tracking-wider text-brand-600 dark:text-sky-400">Modul 1</span>
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Ekspor Koleksi per GMD</h3>
                    </div>
                </div>

                <p class="text-xs text-slate-500 dark:text-slate-400 mb-5 leading-relaxed">
                    Pilih format koleksi (GMD) seperti Buku Teks, Skripsi, Jurnal, atau e-Book untuk mengunduh daftar katalog bibliografi lengkap dengan barcode dan no. panggil.
                </p>

                <!-- Filter Controls -->
                <div class="space-y-3 mb-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Pilih Format (GMD):
                        </label>
                        <select x-model="gmdId" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-semibold text-slate-900 dark:text-white">
                            <option value="">Semua Format / GMD (Katalog Lengkap)</option>
                            @foreach($gmdCustomList as $g)
                                <option value="{{ $g['id'] }}">
                                    {{ $g['name'] }} ({{ number_format($g['count']) }} Judul)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Filter Tahun Terbit (Opsional):
                        </label>
                        <input type="text" x-model="year" placeholder="Contoh: 2024 atau kosongkan..."
                               class="w-full px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white">
                    </div>
                </div>
            </div>

            <!-- Download Buttons -->
            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 space-y-2">
                <span class="block text-[10px] font-black uppercase tracking-wider text-slate-400 mb-2">Unduh Format Dokumen</span>
                
                <div class="grid grid-cols-2 gap-2">
                    <!-- Excel -->
                    <a :href="`{{ route('admin.biblio.export') }}?gmd_id=${gmdId}&year=${year}&format=excel`"
                       class="flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/40 dark:hover:bg-emerald-950/70 text-emerald-700 dark:text-emerald-400 font-bold text-xs transition-colors border border-emerald-200 dark:border-emerald-800/60">
                        <i data-lucide="sheet" class="w-4 h-4"></i>
                        <span>Excel (.xls)</span>
                    </a>

                    <!-- Word -->
                    <a :href="`{{ route('admin.biblio.export') }}?gmd_id=${gmdId}&year=${year}&format=word`"
                       class="flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl bg-blue-50 hover:bg-blue-100 dark:bg-blue-950/40 dark:hover:bg-blue-950/70 text-blue-700 dark:text-blue-400 font-bold text-xs transition-colors border border-blue-200 dark:border-blue-800/60">
                        <i data-lucide="file-text" class="w-4 h-4"></i>
                        <span>Word (.doc)</span>
                    </a>

                    <!-- PDF / Print -->
                    <a :href="`{{ route('admin.biblio.export') }}?gmd_id=${gmdId}&year=${year}&format=pdf`" target="_blank"
                       class="flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/40 dark:hover:bg-rose-950/70 text-rose-700 dark:text-rose-400 font-bold text-xs transition-colors border border-rose-200 dark:border-rose-800/60">
                        <i data-lucide="printer" class="w-4 h-4"></i>
                        <span>Cetak / PDF</span>
                    </a>

                    <!-- CSV -->
                    <a :href="`{{ route('admin.biblio.export') }}?gmd_id=${gmdId}&year=${year}&format=csv`"
                       class="flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs transition-colors border border-slate-300 dark:border-slate-700">
                        <i data-lucide="file-code" class="w-4 h-4"></i>
                        <span>CSV Data</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- CARD 2: EKSPOR DATA ANGGOTA PERPUSTAKAAN -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm flex flex-col justify-between" x-data="{ memberType: 'all', memberStatus: 'all' }">
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center">
                        <i data-lucide="users" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <span class="text-[10px] font-black uppercase tracking-wider text-purple-600 dark:text-purple-400">Modul 2</span>
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Ekspor Data Anggota</h3>
                    </div>
                </div>

                <p class="text-xs text-slate-500 dark:text-slate-400 mb-5 leading-relaxed">
                    Unduh data anggota perpustakaan yang dapat difilter berdasarkan tipe anggota (Mahasiswa atau Dosen/Staff) dan status masa berlaku.
                </p>

                <!-- Filter Controls -->
                <div class="space-y-3 mb-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Kategori Anggota:
                        </label>
                        <select x-model="memberType" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-semibold text-slate-900 dark:text-white">
                            <option value="all">Semua Kategori Anggota ({{ $stats['total_member'] }} Orang)</option>
                            <option value="mahasiswa">Khusus Mahasiswa S-1 ({{ $stats['student_count'] }} Orang)</option>
                            <option value="non-mahasiswa">Khusus Dosen & Karyawan ({{ $stats['staff_count'] }} Orang)</option>
                            @foreach($memberTypes as $mt)
                                <option value="{{ $mt->member_type_id }}">{{ $mt->member_type_name }} ({{ $mt->members_count }} Orang)</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Status Keaktifan:
                        </label>
                        <select x-model="memberStatus" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-semibold text-slate-900 dark:text-white">
                            <option value="all">Semua Status</option>
                            <option value="active">Aktif</option>
                            <option value="expired">Masa Berlaku Habis (Kedaluwarsa)</option>
                            <option value="inactive">Ditangguhkan / Pending</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Download Buttons -->
            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 space-y-2">
                <span class="block text-[10px] font-black uppercase tracking-wider text-slate-400 mb-2">Unduh Format Dokumen</span>
                
                <div class="grid grid-cols-2 gap-2">
                    <!-- Excel -->
                    <a :href="`{{ route('admin.member.export') }}?type=${memberType}&status=${memberStatus}&format=excel`"
                       class="flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/40 dark:hover:bg-emerald-950/70 text-emerald-700 dark:text-emerald-400 font-bold text-xs transition-colors border border-emerald-200 dark:border-emerald-800/60">
                        <i data-lucide="sheet" class="w-4 h-4"></i>
                        <span>Excel (.xls)</span>
                    </a>

                    <!-- Word -->
                    <a :href="`{{ route('admin.member.export') }}?type=${memberType}&status=${memberStatus}&format=word`"
                       class="flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl bg-blue-50 hover:bg-blue-100 dark:bg-blue-950/40 dark:hover:bg-blue-950/70 text-blue-700 dark:text-blue-400 font-bold text-xs transition-colors border border-blue-200 dark:border-blue-800/60">
                        <i data-lucide="file-text" class="w-4 h-4"></i>
                        <span>Word (.doc)</span>
                    </a>

                    <!-- PDF / Print -->
                    <a :href="`{{ route('admin.member.export') }}?type=${memberType}&status=${memberStatus}&format=pdf`" target="_blank"
                       class="flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/40 dark:hover:bg-rose-950/70 text-rose-700 dark:text-rose-400 font-bold text-xs transition-colors border border-rose-200 dark:border-rose-800/60">
                        <i data-lucide="printer" class="w-4 h-4"></i>
                        <span>Cetak / PDF</span>
                    </a>

                    <!-- CSV -->
                    <a :href="`{{ route('admin.member.export') }}?type=${memberType}&status=${memberStatus}&format=csv`"
                       class="flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs transition-colors border border-slate-300 dark:border-slate-700">
                        <i data-lucide="file-code" class="w-4 h-4"></i>
                        <span>CSV Data</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- CARD 3: EKSPOR BUKU TAMU & KUNJUNGAN -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm flex flex-col justify-between" x-data="{ startDate: '', endDate: '', guestStatus: 'all' }">
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                        <i data-lucide="book-open-check" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <span class="text-[10px] font-black uppercase tracking-wider text-amber-600 dark:text-amber-400">Modul 3</span>
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Ekspor Buku Tamu</h3>
                    </div>
                </div>

                <p class="text-xs text-slate-500 dark:text-slate-400 mb-5 leading-relaxed">
                    Unduh data statistik kunjungan perpustakaan harian/bulanan dengan filter rentang tanggal, program studi, dan kategori status tamu.
                </p>

                <!-- Filter Controls -->
                <div class="space-y-3 mb-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Rentang Tanggal Kunjungan:
                        </label>
                        <div class="grid grid-cols-2 gap-2">
                            <input type="date" x-model="startDate" title="Dari Tanggal"
                                   class="px-2.5 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white">
                            <input type="date" x-model="endDate" title="Sampai Tanggal"
                                   class="px-2.5 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Kategori Pengunjung:
                        </label>
                        <select x-model="guestStatus" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-semibold text-slate-900 dark:text-white">
                            <option value="all">Semua Kategori (Anggota & Tamu Luar)</option>
                            <option value="Anggota">Khusus Anggota Perpustakaan</option>
                            <option value="Non Anggota">Khusus Non Anggota / Tamu Luar</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Download Buttons -->
            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 space-y-2">
                <span class="block text-[10px] font-black uppercase tracking-wider text-slate-400 mb-2">Unduh Format Dokumen</span>
                
                <div class="grid grid-cols-2 gap-2">
                    <!-- Excel -->
                    <a :href="`{{ route('admin.guestbook.export') }}?start_date=${startDate}&end_date=${endDate}&status=${guestStatus}&format=excel`"
                       class="flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/40 dark:hover:bg-emerald-950/70 text-emerald-700 dark:text-emerald-400 font-bold text-xs transition-colors border border-emerald-200 dark:border-emerald-800/60">
                        <i data-lucide="sheet" class="w-4 h-4"></i>
                        <span>Excel (.xls)</span>
                    </a>

                    <!-- Word -->
                    <a :href="`{{ route('admin.guestbook.export') }}?start_date=${startDate}&end_date=${endDate}&status=${guestStatus}&format=word`"
                       class="flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl bg-blue-50 hover:bg-blue-100 dark:bg-blue-950/40 dark:hover:bg-blue-950/70 text-blue-700 dark:text-blue-400 font-bold text-xs transition-colors border border-blue-200 dark:border-blue-800/60">
                        <i data-lucide="file-text" class="w-4 h-4"></i>
                        <span>Word (.doc)</span>
                    </a>

                    <!-- PDF / Print -->
                    <a :href="`{{ route('admin.guestbook.export') }}?start_date=${startDate}&end_date=${endDate}&status=${guestStatus}&format=pdf`" target="_blank"
                       class="flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/40 dark:hover:bg-rose-950/70 text-rose-700 dark:text-rose-400 font-bold text-xs transition-colors border border-rose-200 dark:border-rose-800/60">
                        <i data-lucide="printer" class="w-4 h-4"></i>
                        <span>Cetak / PDF</span>
                    </a>

                    <!-- CSV -->
                    <a :href="`{{ route('admin.guestbook.export') }}?start_date=${startDate}&end_date=${endDate}&status=${guestStatus}&format=csv`"
                       class="flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs transition-colors border border-slate-300 dark:border-slate-700">
                        <i data-lucide="file-code" class="w-4 h-4"></i>
                        <span>CSV Data</span>
                    </a>
                </div>
            </div>
        </div>

    </div>

    <!-- ========================================================================= -->
    <!-- MODUL KHUSUS AKREDITASI: VISUALISASI GRAFIK & LAPORAN TS-2, TS-1, TS      -->
    <!-- ========================================================================= -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 sm:p-8 shadow-sm space-y-6"
         x-data="{ 
            akreditasiTab: 'visits',
            selectedTs: '{{ $selectedTs }}',
            isExportingWord: false,
            downloadWordWithCharts() {
                this.isExportingWord = true;
                
                // Ambil base64 dari canvas Chart.js
                try {
                    const canvasSemester = document.getElementById('chartSemesterVisits');
                    if (canvasSemester) {
                        document.getElementById('inputChartSemester').value = canvasSemester.toDataURL('image/png');
                    }
                    const canvasMonthly = document.getElementById('chartMonthlyVisits');
                    if (canvasMonthly) {
                        document.getElementById('inputChartMonthly').value = canvasMonthly.toDataURL('image/png');
                    }
                    const canvasMember = document.getElementById('chartMemberGrowth');
                    if (canvasMember) {
                        document.getElementById('inputChartMember').value = canvasMember.toDataURL('image/png');
                    }
                } catch(e) {
                    console.error('Error generating chart image:', e);
                }
                
                document.getElementById('formExportAkreditasiWord').submit();
                setTimeout(() => { this.isExportingWord = false; }, 3000);
            },
            exportChartPng(canvasId, filename) {
                const canvas = document.getElementById(canvasId);
                if (!canvas) return;
                const link = document.createElement('a');
                link.download = filename + '_' + this.selectedTs.replace('/', '-') + '.png';
                link.href = canvas.toDataURL('image/png');
                link.click();
            }
         }">
        
        <!-- Header & Filter TS -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-6 border-b border-slate-100 dark:border-slate-800">
            <div>
                <div class="flex items-center gap-2 mb-1.5">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 text-[11px] font-extrabold uppercase tracking-wider">
                        <i data-lucide="award" class="w-3.5 h-3.5"></i>
                        Standar Instrumen Akreditasi (BAN-PT / LAM)
                    </span>
                    <span class="text-xs text-slate-400 font-semibold">•</span>
                    <span class="text-xs text-slate-500 font-medium">Semester Ganjil (Sep - Feb) & Genap (Mar - Agu)</span>
                </div>
                <h3 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="bar-chart-3" class="w-6 h-6 text-brand-600 dark:text-sky-400"></i>
                    <span>Visualisasi Grafik Akreditasi (TS-2, TS-1, TS)</span>
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-2xl leading-relaxed">
                    Visualisasi data statistik kunjungan perpustakaan dan keanggotaan mahasiswa/dosen dalam 3 tahun akademik berturut-turut. Dilengkapi ekspor laporan resmi Microsoft Word (.doc) lengkap dengan kop surat institusi, grafik, dan tabel matriks borang.
                </p>
            </div>

            <!-- Penentuan TS & Tombol Ekspor Word Akreditasi -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                <!-- Dropdown Penentuan TS Fleksibel -->
                <div class="flex items-center gap-2">
                    <label class="text-xs font-extrabold text-slate-700 dark:text-slate-300 whitespace-nowrap">
                        Tahun TS:
                    </label>
                    <select x-model="selectedTs" @change="window.location.href = '{{ route('admin.export.index') }}?ts=' + selectedTs"
                            class="px-3 py-2 rounded-xl border border-brand-300 dark:border-brand-700 bg-brand-50/50 dark:bg-brand-950/40 text-brand-900 dark:text-sky-200 text-xs font-bold focus:ring-2 focus:ring-brand-500">
                        @foreach($availableTsOptions as $tsVal => $tsLabel)
                            <option value="{{ $tsVal }}" {{ $selectedTs === $tsVal ? 'selected' : '' }}>
                                {{ $tsLabel }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Tombol Ekspor Word Lengkap dengan Grafik -->
                <button type="button" @click="downloadWordWithCharts()"
                        :disabled="isExportingWord"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-extrabold text-xs shadow-md shadow-blue-500/20 transition-all cursor-pointer disabled:opacity-50">
                    <i data-lucide="file-text" class="w-4 h-4"></i>
                    <span x-text="isExportingWord ? 'Menyiapkan Word...' : 'Unduh Laporan Akreditasi (Word .doc)'"></span>
                </button>
            </div>
        </div>

        <!-- Hidden Form untuk Submit ke Backend Ekspor Word Akreditasi -->
        <form id="formExportAkreditasiWord" action="{{ route('admin.export.akreditasi.word') }}" method="POST" style="display: none;">
            @csrf
            <input type="hidden" name="ts" value="{{ $selectedTs }}">
            <input type="hidden" name="chart_image_semester" id="inputChartSemester">
            <input type="hidden" name="chart_image_monthly" id="inputChartMonthly">
            <input type="hidden" name="chart_image_member" id="inputChartMember">
        </form>

        <!-- Navigation Tabs Akreditasi -->
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
            <div class="flex items-center gap-2">
                <button type="button" @click="akreditasiTab = 'visits'"
                        :class="akreditasiTab === 'visits' 
                            ? 'bg-brand-600 text-white shadow-sm' 
                            : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700'"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer">
                    <i data-lucide="user-check" class="w-4 h-4"></i>
                    <span>Grafik Kunjungan Buku Tamu</span>
                </button>

                <button type="button" @click="akreditasiTab = 'members'"
                        :class="akreditasiTab === 'members' 
                            ? 'bg-brand-600 text-white shadow-sm' 
                            : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700'"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer">
                    <i data-lucide="users" class="w-4 h-4"></i>
                    <span>Grafik Keanggotaan Perpustakaan</span>
                </button>
            </div>

            <!-- Quick Export as Image (PNG) Button -->
            <div class="flex items-center gap-2">
                <button type="button" @click="exportChartPng(akreditasiTab === 'visits' ? 'chartSemesterVisits' : 'chartMemberGrowth', 'Grafik_Akreditasi')"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-900 text-white text-[11px] font-bold shadow-sm transition-colors cursor-pointer"
                        title="Unduh file gambar grafik beresolusi tinggi (.png)">
                    <i data-lucide="image" class="w-3.5 h-3.5 text-sky-400"></i>
                    <span>Export as Grafik (PNG)</span>
                </button>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TAB 1: KUNJUNGAN BUKU TAMU                -->
        <!-- ========================================== -->
        <div x-show="akreditasiTab === 'visits'" class="space-y-6">
            
            <!-- 3 Ringkasan Angka Borang Akreditasi Kunjungan -->
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/40">
                    <div class="text-[10px] font-black uppercase tracking-wider text-slate-400 mb-1">
                        TS-2 ({{ $akreditasiVisits['TS-2']['label'] }})
                    </div>
                    <div class="text-xl font-black text-slate-900 dark:text-white">
                        {{ number_format($akreditasiVisits['TS-2']['total']) }} <span class="text-xs font-semibold text-slate-500">Kunjungan</span>
                    </div>
                    <div class="text-[11px] text-slate-500 mt-1">
                        Ganjil: <strong>{{ $akreditasiVisits['TS-2']['ganjil'] }}</strong> | Genap: <strong>{{ $akreditasiVisits['TS-2']['genap'] }}</strong>
                    </div>
                </div>

                <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/40">
                    <div class="text-[10px] font-black uppercase tracking-wider text-slate-400 mb-1">
                        TS-1 ({{ $akreditasiVisits['TS-1']['label'] }})
                    </div>
                    <div class="text-xl font-black text-slate-900 dark:text-white">
                        {{ number_format($akreditasiVisits['TS-1']['total']) }} <span class="text-xs font-semibold text-slate-500">Kunjungan</span>
                    </div>
                    <div class="text-[11px] text-slate-500 mt-1">
                        Ganjil: <strong>{{ $akreditasiVisits['TS-1']['ganjil'] }}</strong> | Genap: <strong>{{ $akreditasiVisits['TS-1']['genap'] }}</strong>
                    </div>
                </div>

                <div class="p-4 rounded-2xl border border-brand-200 dark:border-brand-800 bg-brand-50/60 dark:bg-brand-950/30">
                    <div class="text-[10px] font-black uppercase tracking-wider text-brand-600 dark:text-sky-400 mb-1">
                        TS ({{ $akreditasiVisits['TS']['label'] }})
                    </div>
                    <div class="text-xl font-black text-brand-700 dark:text-sky-300">
                        {{ number_format($akreditasiVisits['TS']['total']) }} <span class="text-xs font-semibold text-brand-600 dark:text-sky-400">Kunjungan</span>
                    </div>
                    <div class="text-[11px] text-brand-600 dark:text-sky-400 mt-1">
                        Ganjil: <strong>{{ $akreditasiVisits['TS']['ganjil'] }}</strong> | Genap: <strong>{{ $akreditasiVisits['TS']['genap'] }}</strong>
                    </div>
                </div>

                <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/40">
                    <div class="text-[10px] font-black uppercase tracking-wider text-slate-400 mb-1">
                        Rata-Rata Bulanan (TS)
                    </div>
                    <div class="text-xl font-black text-emerald-600 dark:text-emerald-400">
                        {{ $akreditasiVisits['TS']['monthly_avg'] }} <span class="text-xs font-semibold text-slate-500">Kunjungan/Bln</span>
                    </div>
                    <div class="text-[11px] text-slate-500 mt-1">
                        Konsistensi layanan perpustakaan
                    </div>
                </div>
            </div>

            <!-- GRAFIK 1: PERBANDINGAN KUNJUNGAN SEMESTER (TS-2 vs TS-1 vs TS) -->
            <div class="p-5 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/60 space-y-3">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="font-bold text-sm text-slate-900 dark:text-white flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-brand-500"></span>
                            <span>Grafik 1: Perbandingan Kunjungan Semester Ganjil vs Genap (TS-2, TS-1, TS)</span>
                        </h4>
                        <p class="text-[11px] text-slate-400 mt-0.5">Grafik utama untuk evaluasi pemanfaatan perpustakaan civitas akademika</p>
                    </div>
                    <button type="button" @click="exportChartPng('chartSemesterVisits', 'Grafik_Semester_Visits')"
                            class="text-xs font-bold text-brand-600 dark:text-sky-400 hover:underline">
                        Unduh PNG
                    </button>
                </div>
                <div class="relative w-full h-72 sm:h-80">
                    <canvas id="chartSemesterVisits"></canvas>
                </div>
            </div>

            <!-- GRAFIK 2: GRAFIK TOTAL PENGUNJUNG & PER TUJUAN BULANAN (SEPERTI GAMBAR 2 DARI USER) -->
            <div class="p-5 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/60 space-y-6">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                    <div>
                        <h4 class="font-bold text-sm text-slate-900 dark:text-white flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-indigo-500"></span>
                            <span>Grafik 2: Rincian Kunjungan Bulanan & Kategori Tujuan pada TS {{ $selectedTs }}</span>
                        </h4>
                        <p class="text-[11px] text-slate-400 mt-0.5">Format visualisasi breakdown bulanan (Sep s/d Agu) per tujuan / kategori pengunjung</p>
                    </div>
                    <button type="button" @click="exportChartPng('chartMonthlyVisits', 'Grafik_Bulanan_Visits')"
                            class="text-xs font-bold text-brand-600 dark:text-sky-400 hover:underline">
                        Unduh PNG
                    </button>
                </div>

                <!-- Canvas Chart Bulanan & Per Tujuan -->
                <div class="relative w-full h-80 sm:h-96">
                    <canvas id="chartMonthlyVisits"></canvas>
                </div>
            </div>

            <!-- TABEL MATRIKS REKAPITULASI KUNJUNGAN BORANG LKPS -->
            <div class="p-5 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-800/40 space-y-3">
                <div class="flex items-center justify-between">
                    <h4 class="font-extrabold text-xs uppercase tracking-wider text-slate-700 dark:text-slate-300 flex items-center gap-2">
                        <i data-lucide="table" class="w-4 h-4 text-emerald-500"></i>
                        <span>Tabel Rekapitulasi Kunjungan Borang Akreditasi (LKPS / LED)</span>
                    </h4>
                    <span class="text-[11px] font-semibold text-slate-500">Standar 3 Tahun Terakhir</span>
                </div>
                
                <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-700">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-extrabold border-b border-slate-200 dark:border-slate-700">
                            <tr>
                                <th class="p-3">Tahun Akademik</th>
                                <th class="p-3 text-center">Semester Ganjil (Sep - Feb)</th>
                                <th class="p-3 text-center">Semester Genap (Mar - Agu)</th>
                                <th class="p-3 text-center">Total Kunjungan Tahunan</th>
                                <th class="p-3 text-center">Rata-Rata / Bulan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-700 bg-white dark:bg-slate-900 font-medium">
                            @foreach($akreditasiVisits as $k => $v)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                                    <td class="p-3 font-bold text-slate-900 dark:text-white">
                                        {{ $k }} <span class="text-[11px] font-normal text-slate-500">({{ $v['label'] }})</span>
                                    </td>
                                    <td class="p-3 text-center text-slate-700 dark:text-slate-300">{{ number_format($v['ganjil']) }} Orang</td>
                                    <td class="p-3 text-center text-slate-700 dark:text-slate-300">{{ number_format($v['genap']) }} Orang</td>
                                    <td class="p-3 text-center font-bold text-brand-600 dark:text-sky-400">{{ number_format($v['total']) }} Orang</td>
                                    <td class="p-3 text-center font-bold text-emerald-600 dark:text-emerald-400">{{ $v['monthly_avg'] }} Orang/Bln</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- ========================================== -->
        <!-- TAB 2: KEANGGOTAAN PERPUSTAKAAN           -->
        <!-- ========================================== -->
        <div x-show="akreditasiTab === 'members'" class="space-y-6" style="display: none;">
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Grafik Pertumbuhan Anggota Baru -->
                <div class="p-5 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/60 space-y-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="font-bold text-sm text-slate-900 dark:text-white flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-purple-500"></span>
                                <span>Pertumbuhan Anggota Baru (TS-2, TS-1, TS)</span>
                            </h4>
                            <p class="text-[11px] text-slate-400">Pendaftaran anggota civitas akademika tiap semester</p>
                        </div>
                        <button type="button" @click="exportChartPng('chartMemberGrowth', 'Grafik_Member_Growth')"
                                class="text-xs font-bold text-brand-600 dark:text-sky-400 hover:underline">
                            Unduh PNG
                        </button>
                    </div>
                    <div class="relative w-full h-72">
                        <canvas id="chartMemberGrowth"></canvas>
                    </div>
                </div>

                <!-- Grafik Distribusi Anggota per Program Studi -->
                <div class="p-5 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/60 space-y-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="font-bold text-sm text-slate-900 dark:text-white flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                <span>Distribusi Anggota per Program Studi & Unit</span>
                            </h4>
                            <p class="text-[11px] text-slate-400">Proporsi keaktifan anggota dari tiap program studi</p>
                        </div>
                        <button type="button" @click="exportChartPng('chartMemberProdi', 'Grafik_Member_Prodi')"
                                class="text-xs font-bold text-brand-600 dark:text-sky-400 hover:underline">
                            Unduh PNG
                        </button>
                    </div>
                    <div class="relative w-full h-72">
                        <canvas id="chartMemberProdi"></canvas>
                    </div>
                </div>
            </div>

            <!-- Tabel Rekapitulasi Anggota Baru -->
            <div class="p-5 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-800/40 space-y-3">
                <div class="flex items-center justify-between">
                    <h4 class="font-extrabold text-xs uppercase tracking-wider text-slate-700 dark:text-slate-300 flex items-center gap-2">
                        <i data-lucide="table" class="w-4 h-4 text-purple-500"></i>
                        <span>Tabel Rekapitulasi Anggota Baru Perpustakaan (TS-2 s.d. TS)</span>
                    </h4>
                </div>
                
                <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-700">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-extrabold border-b border-slate-200 dark:border-slate-700">
                            <tr>
                                <th class="p-3">Tahun Akademik</th>
                                <th class="p-3 text-center">Semester Ganjil (Sep - Feb)</th>
                                <th class="p-3 text-center">Semester Genap (Mar - Agu)</th>
                                <th class="p-3 text-center">Total Anggota Baru</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-700 bg-white dark:bg-slate-900 font-medium">
                            @foreach($akreditasiMembers as $k => $m)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                                    <td class="p-3 font-bold text-slate-900 dark:text-white">
                                        {{ $k }} <span class="text-[11px] font-normal text-slate-500">({{ $m['label'] }})</span>
                                    </td>
                                    <td class="p-3 text-center text-slate-700 dark:text-slate-300">{{ number_format($m['ganjil']) }} Orang</td>
                                    <td class="p-3 text-center text-slate-700 dark:text-slate-300">{{ number_format($m['genap']) }} Orang</td>
                                    <td class="p-3 text-center font-bold text-purple-600 dark:text-purple-400">{{ number_format($m['total']) }} Orang</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // ----------------------------------------------------
    // 1. DATA UNTUK GRAFIK KUNJUNGAN SEMESTER (TS-2, TS-1, TS)
    // ----------------------------------------------------
    const tsLabels = ['TS-2 ({{ $akreditasiVisits["TS-2"]["label"] }})', 'TS-1 ({{ $akreditasiVisits["TS-1"]["label"] }})', 'TS ({{ $akreditasiVisits["TS"]["label"] }})'];
    const dataGanjil = [{{ $akreditasiVisits['TS-2']['ganjil'] }}, {{ $akreditasiVisits['TS-1']['ganjil'] }}, {{ $akreditasiVisits['TS']['ganjil'] }}];
    const dataGenap  = [{{ $akreditasiVisits['TS-2']['genap'] }}, {{ $akreditasiVisits['TS-1']['genap'] }}, {{ $akreditasiVisits['TS']['genap'] }}];
    const dataTotal  = [{{ $akreditasiVisits['TS-2']['total'] }}, {{ $akreditasiVisits['TS-1']['total'] }}, {{ $akreditasiVisits['TS']['total'] }}];

    const ctxSemester = document.getElementById('chartSemesterVisits');
    if (ctxSemester) {
        new Chart(ctxSemester, {
            type: 'bar',
            data: {
                labels: tsLabels,
                datasets: [
                    {
                        label: 'Semester Ganjil (Sep - Feb)',
                        data: dataGanjil,
                        backgroundColor: '#0284c7', // Brand blue
                        borderRadius: 6,
                    },
                    {
                        label: 'Semester Genap (Mar - Agu)',
                        data: dataGenap,
                        backgroundColor: '#10b981', // Emerald green
                        borderRadius: 6,
                    },
                    {
                        type: 'line',
                        label: 'Total Tahunan (Trend)',
                        data: dataTotal,
                        borderColor: '#f59e0b', // Amber
                        backgroundColor: '#f59e0b',
                        borderWidth: 3,
                        pointRadius: 6,
                        pointHoverRadius: 8,
                        fill: false,
                        tension: 0.2
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: { family: "'Plus Jakarta Sans', sans-serif", size: 11, weight: 'bold' }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y + ' Kunjungan';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 10 }
                    }
                }
            }
        });
    }

    // ----------------------------------------------------
    // 2. DATA UNTUK GRAFIK BULANAN & PER TUJUAN (TS TERPILIH)
    // ----------------------------------------------------
    const monthlyLabels = [
        @foreach($monthlyVisits as $m)
            '{{ $m["name"] }}',
        @endforeach
    ];
    const monthlyTotal = [
        @foreach($monthlyVisits as $m)
            {{ $m["total"] }},
        @endforeach
    ];
    const monthlyLibrary = [
        @foreach($monthlyVisits as $m)
            {{ $m["library"] }},
        @endforeach
    ];
    const monthlyCorner = [
        @foreach($monthlyVisits as $m)
            {{ $m["student_corner"] }},
        @endforeach
    ];

    const ctxMonthly = document.getElementById('chartMonthlyVisits');
    if (ctxMonthly) {
        new Chart(ctxMonthly, {
            type: 'bar',
            data: {
                labels: monthlyLabels,
                datasets: [
                    {
                        label: 'Total Pengunjung',
                        data: monthlyTotal,
                        backgroundColor: '#64748b', // Slate gray seperti di Gambar 2
                        borderRadius: 6,
                        order: 2
                    },
                    {
                        label: 'Library / Anggota',
                        data: monthlyLibrary,
                        backgroundColor: '#f97316', // Orange seperti di Gambar 2
                        borderRadius: 6,
                        order: 3
                    },
                    {
                        label: 'Student Corner / Tamu',
                        data: monthlyCorner,
                        backgroundColor: '#60a5fa', // Light blue seperti di Gambar 2
                        borderRadius: 6,
                        order: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: { family: "'Plus Jakarta Sans', sans-serif", size: 11, weight: 'bold' }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y + ' Orang';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // ----------------------------------------------------
    // 3. GRAFIK PERTUMBUHAN ANGGOTA (TS-2, TS-1, TS)
    // ----------------------------------------------------
    const memberGanjil = [{{ $akreditasiMembers['TS-2']['ganjil'] }}, {{ $akreditasiMembers['TS-1']['ganjil'] }}, {{ $akreditasiMembers['TS']['ganjil'] }}];
    const memberGenap  = [{{ $akreditasiMembers['TS-2']['genap'] }}, {{ $akreditasiMembers['TS-1']['genap'] }}, {{ $akreditasiMembers['TS']['genap'] }}];
    const memberTotal  = [{{ $akreditasiMembers['TS-2']['total'] }}, {{ $akreditasiMembers['TS-1']['total'] }}, {{ $akreditasiMembers['TS']['total'] }}];

    const ctxMember = document.getElementById('chartMemberGrowth');
    if (ctxMember) {
        new Chart(ctxMember, {
            type: 'bar',
            data: {
                labels: tsLabels,
                datasets: [
                    {
                        label: 'Semester Ganjil',
                        data: memberGanjil,
                        backgroundColor: '#8b5cf6', // Purple
                        borderRadius: 6,
                    },
                    {
                        label: 'Semester Genap',
                        data: memberGenap,
                        backgroundColor: '#ec4899', // Pink
                        borderRadius: 6,
                    },
                    {
                        type: 'line',
                        label: 'Total Anggota Baru',
                        data: memberTotal,
                        borderColor: '#6366f1',
                        backgroundColor: '#6366f1',
                        borderWidth: 3,
                        pointRadius: 5,
                        fill: false,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top' }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }

    // ----------------------------------------------------
    // 4. GRAFIK DISTRIBUSI PROGRAM STUDI ANGGOTA
    // ----------------------------------------------------
    const prodiLabels = [
        @foreach($prodiCounts as $pName => $pCount)
            '{{ $pName }}',
        @endforeach
    ];
    const prodiData = [
        @foreach($prodiCounts as $pName => $pCount)
            {{ $pCount }},
        @endforeach
    ];

    const ctxProdi = document.getElementById('chartMemberProdi');
    if (ctxProdi) {
        new Chart(ctxProdi, {
            type: 'doughnut',
            data: {
                labels: prodiLabels,
                datasets: [{
                    data: prodiData,
                    backgroundColor: [
                        '#0284c7', // Sky
                        '#3b82f6', // Blue
                        '#8b5cf6', // Violet
                        '#10b981', // Emerald
                        '#f59e0b', // Amber
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }

});
</script>
@endpush
