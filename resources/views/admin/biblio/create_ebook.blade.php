@extends('layouts.admin')

@section('title', 'Tambah Data e-Book')
@section('header_title', 'Unggah Koleksi Buku Elektronik (e-Book)')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.biblio.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-slate-800 dark:hover:text-white">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Kembali ke Katalog</span>
        </a>
    </div>

    <form action="{{ route('admin.ebook.store') }}" method="POST" enctype="multipart/form-data" class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-8 shadow-sm space-y-6">
        @csrf

        <div class="flex items-center gap-4 border-b border-slate-100 dark:border-slate-800 pb-5">
            <div class="w-12 h-12 rounded-2xl bg-cyan-50 dark:bg-cyan-950 text-cyan-600 dark:text-cyan-400 flex items-center justify-center font-bold flex-shrink-0">
                <i data-lucide="tablet" class="w-6 h-6"></i>
            </div>
            <div>
                <h2 class="text-xl font-black text-slate-900 dark:text-white">Formulir Tambah Data Buku Elektronik (e-Book)</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Tambahkan koleksi digital e-Book yang dapat diakses secara daring oleh mahasiswa dan dosen</p>
            </div>
        </div>

        <div class="space-y-5 text-xs">
            <!-- Judul e-Book -->
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Judul Buku Elektronik *</label>
                <input type="text" name="title" value="{{ old('title') }}" required placeholder="Contoh: Pemrograman Python untuk Kecerdasan Buatan dan Data Science..."
                    class="w-full px-4 py-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-cyan-500">
            </div>

            <!-- Pengarang & Penerbit -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Nama Pengarang / Penulis *</label>
                    <input type="text" name="author_name" value="{{ old('author_name') }}" required placeholder="Nama pengarang buku..."
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 font-semibold text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Penerbit</label>
                    <input type="text" name="publisher_name" value="{{ old('publisher_name') }}" placeholder="Contoh: Informatika, Andi Offset, dll..."
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white">
                </div>
            </div>

            <!-- Tahun, ISBN, Call Number -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Tahun Terbit</label>
                    <input type="number" min="1900" max="2099" name="publish_year" value="{{ old('publish_year', date('Y')) }}"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 font-bold text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">ISBN (Opsional)</label>
                    <input type="text" name="isbn_issn" value="{{ old('isbn_issn') }}" placeholder="Contoh: 978-602-..."
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 font-mono text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Nomor Panggil (Opsional)</label>
                    <input type="text" name="call_number" value="{{ old('call_number') }}" placeholder="Auto: EB-[TAHUN]-[RANDOM]"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 font-mono text-slate-900 dark:text-white">
                </div>
            </div>

            <!-- Akses e-Book: File PDF atau Link URL -->
            <div class="p-5 rounded-2xl bg-cyan-50/50 dark:bg-cyan-950/20 border border-cyan-100 dark:border-cyan-900/40 space-y-4">
                <div class="font-bold text-cyan-900 dark:text-cyan-300 uppercase tracking-wider flex items-center gap-2">
                    <i data-lucide="download-cloud" class="w-4 h-4 text-cyan-600"></i>
                    Berkas / Akses e-Book Digital
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="p-4 rounded-xl border border-dashed border-cyan-300 dark:border-cyan-800 bg-white dark:bg-slate-900">
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Pilihan 1: Unggah Berkas e-Book (PDF / EPUB)
                        </label>
                        <input type="file" name="ebook_file" accept=".pdf,.epub" class="mt-2 text-xs">
                        <span class="text-[10px] text-slate-400 block mt-1">Maksimal 50 MB (File disimpan aman di server perpustakaan)</span>
                    </div>

                    <div class="p-4 rounded-xl border border-cyan-200 dark:border-cyan-800/80 bg-white dark:bg-slate-900 flex flex-col justify-between">
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                                Pilihan 2: Tautan / URL Baca Online
                            </label>
                            <input type="url" name="ebook_url" value="{{ old('ebook_url') }}" placeholder="https://drive.google.com/... atau https://openlibrary.org/..."
                                class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs">
                        </div>
                        <span class="text-[10px] text-slate-400 block mt-1">Gunakan tautan jika buku berada di repositori eksternal</span>
                    </div>
                </div>
            </div>

            <!-- Sinopsis -->
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Sinopsis / Ringkasan Buku</label>
                <textarea name="synopsis" rows="4" placeholder="Ringkasan atau daftar bab buku elektronik..."
                    class="w-full px-4 py-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 leading-relaxed">{{ old('synopsis') }}</textarea>
            </div>

            <!-- Cover Image -->
            <div class="p-4 rounded-2xl border border-dashed border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/40">
                <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                    <i data-lucide="image" class="w-4 h-4 text-brand-500"></i>
                    Sampul / Cover e-Book
                </label>
                <input type="file" name="cover_image" accept="image/*" class="mt-2 text-xs">
                <span class="text-[10px] text-slate-400 block mt-1">Format JPG, PNG, WEBP (Maksimal 2 MB)</span>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <span class="text-[11px] text-slate-400">Data otomatis diklasifikasikan sebagai Media Elektronik (e-Book)</span>
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-cyan-600 hover:bg-cyan-700 text-white font-bold text-xs shadow-md shadow-cyan-500/25 transition-all">
                <i data-lucide="check" class="w-4 h-4"></i>
                <span>Simpan Data e-Book</span>
            </button>
        </div>
    </form>
</div>
@endsection
