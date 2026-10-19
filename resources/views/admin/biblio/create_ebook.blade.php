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
            <!-- Judul e-Book & Jenis e-Book -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="sm:col-span-2">
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Judul Buku Elektronik *</label>
                    <input type="text" name="title" value="{{ old('title') }}" required placeholder="Contoh: Pemrograman Python untuk Kecerdasan Buatan dan Data Science..."
                        class="w-full px-4 py-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-cyan-500">
                </div>
                <div>
                    <label class="block font-bold text-cyan-900 dark:text-cyan-300 uppercase tracking-wider mb-1.5 flex items-center justify-between">
                        <span>Jenis e-Book *</span>
                        <span class="text-[10px] text-cyan-600 dark:text-cyan-400 font-semibold">Khusus e-Book</span>
                    </label>
                    <input type="text" name="ebook_type" list="ebook-types-list" value="{{ old('ebook_type', 'Buku Teks Kuliah') }}" required placeholder="Pilih / ketik jenis e-book..."
                        class="w-full px-3.5 py-3 rounded-xl border border-cyan-300 dark:border-cyan-700 bg-cyan-50/50 dark:bg-cyan-950/30 text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-cyan-500">
                    <datalist id="ebook-types-list">
                        <option value="Buku Teks Kuliah"></option>
                        <option value="Modul Pembelajaran / Praktikum"></option>
                        <option value="Buku Referensi / Handbook"></option>
                        <option value="Monograf & Hasil Penelitian"></option>
                        <option value="Buku Populer / Umum"></option>
                    </datalist>
                </div>
            </div>

            <!-- Pengarang & Peran -->
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5 flex items-center justify-between">
                    <span>Nama Pengarang & Peran *</span>
                    <span class="text-[10px] text-purple-600 dark:text-purple-400 font-semibold">Bisa tambah lebih dari 1 pengarang</span>
                </label>
                <div id="author-inputs-container" class="space-y-2.5">
                    <div class="author-row flex flex-col sm:flex-row items-stretch sm:items-center gap-2 p-2 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700">
                        <div class="relative flex-grow">
                            <input type="text" name="author_names[]" list="authors-datalist" value="{{ old('author_names.0') }}" required placeholder="Ketik nama pengarang utama..." class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-xs font-semibold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                        </div>
                        <div class="w-full sm:w-44 flex-shrink-0">
                            <select name="author_types[]" class="w-full px-2.5 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-xs text-slate-900 dark:text-white focus:outline-none font-medium">
                                <option value="p" {{ old('author_types.0', 'p') == 'p' ? 'selected' : '' }}>Nama Orang</option>
                                <option value="o" {{ old('author_types.0') == 'o' ? 'selected' : '' }}>Nama Lembaga</option>
                                <option value="c" {{ old('author_types.0') == 'c' ? 'selected' : '' }}>Badan Organisasi</option>
                            </select>
                        </div>
                        <div class="w-full sm:w-44 flex-shrink-0">
                            <select name="author_levels[]" class="w-full px-2.5 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-xs text-slate-900 dark:text-white focus:outline-none font-medium">
                                <option value="1" {{ old('author_levels.0', 1) == 1 ? 'selected' : '' }}>Pengarang Utama</option>
                                <option value="2" {{ old('author_levels.0') == 2 ? 'selected' : '' }}>Editor</option>
                                <option value="3" {{ old('author_levels.0') == 3 ? 'selected' : '' }}>Penerjemah</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="mt-2.5 flex items-center justify-between">
                    <button type="button" id="btn-add-author" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-purple-50 dark:bg-purple-950/60 hover:bg-purple-100 dark:hover:bg-purple-900/60 text-purple-700 dark:text-purple-300 font-bold text-xs border border-purple-200/60 dark:border-purple-800/40 transition-colors cursor-pointer">
                        <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                        <span>+ Tambah Pengarang Lain</span>
                    </button>
                    <p class="text-[11px] text-slate-400">Pilih tipe nama dan peran untuk setiap pengarang.</p>
                </div>

                <datalist id="authors-datalist">
                    @foreach($authors as $a)
                        <option value="{{ $a->author_name }}"></option>
                    @endforeach
                </datalist>
            </div>

            <!-- Penerbit, Tempat Terbit, Tahun Terbit -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5 flex items-center justify-between">
                        <span>Penerbit</span>
                        <span class="text-[10px] text-brand-600 dark:text-sky-400 font-semibold">Bisa ketik baru</span>
                    </label>
                    <select id="publisher_id" name="publisher_id" class="w-full">
                        <option value="">-- Pilih atau Ketik Penerbit Baru --</option>
                        @foreach($publishers as $p)
                            <option value="{{ $p->publisher_id }}" {{ old('publisher_id') == $p->publisher_id ? 'selected' : '' }}>{{ $p->publisher_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5 flex items-center justify-between">
                        <span>Tempat Terbit</span>
                        <span class="text-[10px] text-brand-600 dark:text-sky-400 font-semibold">Bisa ketik baru</span>
                    </label>
                    <select id="publish_place_id" name="publish_place_id" class="w-full">
                        <option value="">-- Pilih atau Ketik Kota Terbit Baru --</option>
                        @foreach($places as $pl)
                            <option value="{{ $pl->place_id }}" {{ old('publish_place_id') == $pl->place_id ? 'selected' : '' }}>{{ $pl->place_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Tahun Terbit</label>
                    <input type="number" min="1900" max="2099" name="publish_year" value="{{ old('publish_year', date('Y')) }}"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 font-bold text-slate-900 dark:text-white">
                </div>
            </div>

            <!-- ISBN, Call Number & Bahasa -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
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
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Bahasa</label>
                    <select name="language_id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 font-bold text-slate-900 dark:text-white focus:outline-none">
                        <option value="id" {{ old('language_id', 'id') == 'id' ? 'selected' : '' }}>Indonesia (id)</option>
                        <option value="en" {{ old('language_id') == 'en' ? 'selected' : '' }}>English (en)</option>
                        <option value="ar" {{ old('language_id') == 'ar' ? 'selected' : '' }}>Arab / Arabic (ar)</option>
                    </select>
                </div>
            </div>

            <!-- Subjek / Bidang Ilmu (Select2 Multiple Tags) -->
            <div>
                @php
                    $allSelectableTopics = array_values(array_unique(array_filter(array_merge(
                        $reviewerTopics ?? [],
                        isset($topics) ? $topics->pluck('topic')->toArray() : []
                    ))));
                @endphp
                <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5 flex items-center justify-between">
                    <span>Subjek / Bidang Ilmu (Bisa pilih lebih dari 1 atau ketik baru)</span>
                    <span class="text-[10px] text-brand-600 dark:text-sky-400 font-semibold">Pilih atau ketik topik baru lalu Enter</span>
                </label>
                <select id="subjects" name="subjects[]" multiple class="w-full">
                    @foreach($allSelectableTopics as $sub)
                        <option value="{{ $sub }}" {{ (is_array(old('subjects')) && in_array($sub, old('subjects'))) ? 'selected' : '' }}>
                            {{ $sub }}
                        </option>
                    @endforeach
                    @if(is_array(old('subjects')))
                        @foreach(old('subjects') as $oldSub)
                            @if(!in_array($oldSub, $allSelectableTopics))
                                <option value="{{ $oldSub }}" selected>{{ $oldSub }}</option>
                            @endif
                        @endforeach
                    @endif
                </select>
                <p class="text-[11px] text-slate-400 mt-1">Pilihan rekomendasi kurikulum: Sistem Informasi, TI, Bisnis Digital, Kewirausahaan, dll.</p>
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnAddAuthor = document.getElementById('btn-add-author');
        const authorContainer = document.getElementById('author-inputs-container');
        if (btnAddAuthor && authorContainer) {
            btnAddAuthor.addEventListener('click', function(e) {
                e.preventDefault();
                const currentRows = authorContainer.querySelectorAll('.author-row').length + 1;
                const row = document.createElement('div');
                row.className = 'author-row flex flex-col sm:flex-row items-stretch sm:items-center gap-2 p-2 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700';
                row.innerHTML = `
                    <div class="relative flex-grow">
                        <input type="text" name="author_names[]" list="authors-datalist" placeholder="Nama pengarang ke-${currentRows}..." class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-xs font-semibold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>
                    <div class="w-full sm:w-44 flex-shrink-0">
                        <select name="author_types[]" class="w-full px-2.5 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-xs text-slate-900 dark:text-white focus:outline-none font-medium">
                            <option value="p">Nama Orang</option>
                            <option value="o">Nama Lembaga</option>
                            <option value="c">Badan Organisasi</option>
                        </select>
                    </div>
                    <div class="w-full sm:w-44 flex-shrink-0">
                        <select name="author_levels[]" class="w-full px-2.5 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-xs text-slate-900 dark:text-white focus:outline-none font-medium">
                            <option value="2">Editor</option>
                            <option value="1">Pengarang Utama</option>
                            <option value="3">Penerjemah</option>
                        </select>
                    </div>
                    <button type="button" class="btn-remove-author p-2 rounded-lg text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/50 transition-colors flex-shrink-0" title="Hapus Pengarang Ini">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                `;
                authorContainer.appendChild(row);
                const inp = row.querySelector('input');
                if (inp) inp.focus();
            });

            authorContainer.addEventListener('click', function(e) {
                const btnRemove = e.target.closest('.btn-remove-author');
                if (btnRemove) {
                    e.preventDefault();
                    const row = btnRemove.closest('.author-row');
                    if (row) row.remove();
                }
            });
        }
    });

    // Select2 Init
    $(document).ready(function() {
        $('#publisher_id').select2({
            placeholder: '-- Pilih atau Ketik Penerbit Baru --',
            allowClear: true,
            tags: true,
            width: '100%',
            createTag: function (params) {
                var term = $.trim(params.term);
                if (term === '') return null;
                return {
                    id: term,
                    text: term + ' (Penerbit Baru)',
                    newTag: true
                };
            }
        });

        $('#publish_place_id').select2({
            placeholder: '-- Pilih atau Ketik Kota Terbit Baru --',
            allowClear: true,
            tags: true,
            width: '100%',
            createTag: function (params) {
                var term = $.trim(params.term);
                if (term === '') return null;
                return {
                    id: term,
                    text: term + ' (Kota Baru)',
                    newTag: true
                };
            }
        });

        $('#subjects').select2({
            placeholder: '-- Pilih atau Ketik Subjek / Topik (Bisa lebih dari 1) --',
            allowClear: true,
            tags: true,
            width: '100%',
            multiple: true
        });
    });
</script>
@endpush
@endsection
