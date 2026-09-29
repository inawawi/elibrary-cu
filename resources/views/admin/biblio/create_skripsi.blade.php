@extends('layouts.admin')

@section('title', 'Tambah Data Skripsi Mahasiswa')
@section('header_title', 'Unggah Data Karya Ilmiah & Skripsi')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.biblio.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-slate-800 dark:hover:text-white">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Kembali ke Katalog</span>
        </a>
    </div>

    <form action="{{ route('admin.skripsi.store') }}" method="POST" enctype="multipart/form-data" class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-8 shadow-sm space-y-6">
        @csrf

        <div class="flex items-center gap-4 border-b border-slate-100 dark:border-slate-800 pb-5">
            <div class="w-12 h-12 rounded-2xl bg-purple-50 dark:bg-purple-950 text-purple-600 dark:text-purple-400 flex items-center justify-center font-bold flex-shrink-0">
                <i data-lucide="graduation-cap" class="w-6 h-6"></i>
            </div>
            <div>
                <h2 class="text-xl font-black text-slate-900 dark:text-white">Formulir Tambah Data Skripsi / Tugas Akhir</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Arsipkan karya ilmiah sivitas akademika Universitas Siber Indonesia ke repositori perpustakaan</p>
            </div>
        </div>

        <div class="space-y-5 text-xs">
            <!-- Judul Skripsi -->
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Judul Lengkap Skripsi *</label>
                <textarea name="title" rows="2" required placeholder="Contoh: Rancang Bangun Sistem Informasi Perpustakaan Berbasis Web Menggunakan Framework Laravel..."
                    class="w-full px-4 py-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-purple-500">{{ old('title') }}</textarea>
            </div>

            <!-- Mahasiswa: Nama, NIM, Prodi -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Nama Mahasiswa (Penulis) *</label>
                    <input type="text" name="student_name" value="{{ old('student_name') }}" required placeholder="Nama lengkap mahasiswa..."
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 font-semibold text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">NIM Mahasiswa *</label>
                    <input type="text" name="student_nim" value="{{ old('student_nim') }}" required placeholder="Contoh: 12220001"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 font-mono font-bold text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Program Studi *</label>
                    <select name="prodi" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 font-semibold text-slate-900 dark:text-white">
                        <option value="">-- Pilih Program Studi --</option>
                        <option value="Teknologi Informasi" {{ old('prodi') === 'Teknologi Informasi' ? 'selected' : '' }}>Teknologi Informasi (S1)</option>
                        <option value="Sistem Informasi" {{ old('prodi') === 'Sistem Informasi' ? 'selected' : '' }}>Sistem Informasi (S1)</option>
                        <option value="Sistem dan Teknologi Informasi" {{ old('prodi') === 'Sistem dan Teknologi Informasi' ? 'selected' : '' }}>Sistem dan Teknologi Informasi (S1)</option>
                        <option value="Bisnis Digital" {{ old('prodi') === 'Bisnis Digital' ? 'selected' : '' }}>Bisnis Digital (S1)</option>
                        <option value="Kewirausahaan" {{ old('prodi') === 'Kewirausahaan' ? 'selected' : '' }}>Kewirausahaan (S1)</option>
                    </select>
                </div>
            </div>

            <!-- Dosen Pembimbing (Link ke Dosen) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 rounded-2xl bg-purple-50/50 dark:bg-purple-950/20 border border-purple-100 dark:border-purple-900/40">
                <div>
                    <label class="block font-bold text-purple-900 dark:text-purple-300 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                        <i data-lucide="user-check" class="w-4 h-4 text-purple-600"></i>
                        Dosen Pembimbing 1 *
                    </label>
                    <input type="text" list="dosenList1" name="pembimbing_1" value="{{ old('pembimbing_1') }}" required placeholder="Pilih atau ketik nama dosen beserta gelar..."
                        class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200 dark:border-purple-800 bg-white dark:bg-slate-900 font-semibold text-slate-900 dark:text-white">
                    <datalist id="dosenList1">
                        @foreach($dosenMembers as $dm)
                            <option value="{{ $dm->member_name }}">{{ $dm->member_name }} (NIP: {{ $dm->member_id }})</option>
                        @endforeach
                    </datalist>
                    <span class="text-[10px] text-purple-700/70 dark:text-purple-400/60 mt-1 block">Terkoneksi dengan data Dosen / Sivitas Akademika</span>
                </div>

                <div>
                    <label class="block font-bold text-purple-900 dark:text-purple-300 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                        <i data-lucide="user-check" class="w-4 h-4 text-purple-600"></i>
                        Dosen Pembimbing 2 (Opsional)
                    </label>
                    <input type="text" list="dosenList2" name="pembimbing_2" value="{{ old('pembimbing_2') }}" placeholder="Pilih atau ketik nama dosen ke-2..."
                        class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200 dark:border-purple-800 bg-white dark:bg-slate-900 font-semibold text-slate-900 dark:text-white">
                    <datalist id="dosenList2">
                        @foreach($dosenMembers as $dm)
                            <option value="{{ $dm->member_name }}">{{ $dm->member_name }} (NIP: {{ $dm->member_id }})</option>
                        @endforeach
                    </datalist>
                </div>
            </div>

            <!-- Tahun & Nomor Panggil -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Tahun Kelulusan / Skripsi *</label>
                    <input type="number" min="2000" max="2099" name="publish_year" value="{{ old('publish_year', date('Y')) }}" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 font-bold text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Nomor Panggil (Opsional, Otomatis)</label>
                    <input type="text" name="call_number" value="{{ old('call_number') }}" placeholder="Auto: SKR-[PRODI]-[TAHUN]-[NIM]"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 font-mono text-slate-900 dark:text-white">
                </div>
            </div>

            <!-- Abstrak Skripsi -->
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Abstrak Skripsi</label>
                <textarea name="abstract" rows="4" placeholder="Ringkasan / intisari penelitian skripsi..."
                    class="w-full px-4 py-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 leading-relaxed">{{ old('abstract') }}</textarea>
            </div>

            <!-- File Upload & Sampul -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="p-4 rounded-2xl border border-dashed border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/40">
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                        <i data-lucide="file-up" class="w-4 h-4 text-brand-500"></i>
                        Unggah Berkas Skripsi (PDF / Dokumen)
                    </label>
                    <input type="file" name="skripsi_file" accept=".pdf,.doc,.docx,.zip,.rar" class="mt-2 text-xs">
                    <span class="text-[10px] text-slate-400 block mt-1">Maksimal 20 MB (Format PDF disarankan)</span>
                </div>

                <div class="p-4 rounded-2xl border border-dashed border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/40">
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                        <i data-lucide="image" class="w-4 h-4 text-brand-500"></i>
                        Sampul / Cover Skripsi (Opsional)
                    </label>
                    <input type="file" name="cover_image" accept="image/*" class="mt-2 text-xs">
                    <span class="text-[10px] text-slate-400 block mt-1">Format JPG, PNG, WEBP (Maksimal 2 MB)</span>
                </div>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <span class="text-[11px] text-slate-400">Data otomatis diklasifikasikan sebagai GMD: Skripsi</span>
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs shadow-md shadow-purple-500/25 transition-all">
                <i data-lucide="check" class="w-4 h-4"></i>
                <span>Simpan Data Skripsi</span>
            </button>
        </div>
    </form>
</div>
@endsection
