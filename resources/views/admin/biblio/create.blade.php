@extends('layouts.admin')

@section('title', 'Tambah Buku Baru')
@section('header_title', 'Katalogisasi Bibliografi Baru')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.biblio.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-slate-800 dark:hover:text-white">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Kembali ke Daftar Buku</span>
        </a>
    </div>

    <form action="{{ route('admin.biblio.store') }}" method="POST" enctype="multipart/form-data" class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-8 shadow-sm space-y-6">
        @csrf

        <div class="border-b border-slate-100 dark:border-slate-800 pb-4">
            <h2 class="text-lg font-black text-slate-900 dark:text-white">Informasi Dasar Bibliografi</h2>
            <p class="text-xs text-slate-400">Masukkan detail metadata buku yang akan ditambahkan ke katalog</p>
        </div>

        <div class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Judul Buku *</label>
                <input type="text" name="title" value="{{ old('title') }}" required placeholder="Masukkan judul buku..." class="w-full px-4 py-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-brand-500 focus:outline-none">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Pernyataan Tanggung Jawab (SOR)</label>
                    <input type="text" name="sor" value="{{ old('sor') }}" placeholder="Contoh: ditulis oleh Dr. John Doe..." class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white font-medium focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Edisi</label>
                    <input type="text" name="edition" value="{{ old('edition') }}" placeholder="Contoh: Cetakan ke-2 / Edisi Revisi" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white font-medium focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Nomor Panggil *</label>
                    <input type="text" name="call_number" value="{{ old('call_number') }}" placeholder="Contoh: 005.1 IND s" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-mono text-slate-900 dark:text-white font-bold focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Klasifikasi (DDC)</label>
                    <input type="text" name="classification" value="{{ old('classification') }}" placeholder="Contoh: 005.1" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-mono text-slate-900 dark:text-white font-medium focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">ISBN / ISSN</label>
                    <input type="text" name="isbn_issn" value="{{ old('isbn_issn') }}" placeholder="Contoh: 978-602-xxx" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-mono text-slate-900 dark:text-white font-medium focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5 flex items-center justify-between">
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
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Tahun Terbit</label>
                    <input type="text" name="publish_year" value="{{ old('publish_year') }}" placeholder="Contoh: 2023" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white font-medium focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5 flex items-center justify-between">
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
            </div>

            <!-- Pengarang & GMD -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-start">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5 flex items-center justify-between">
                        <span>Nama Pengarang & Peran</span>
                        <span class="text-[10px] text-purple-600 dark:text-purple-400 font-semibold">Bisa tambah lebih dari 1 pengarang</span>
                    </label>
                    <div id="author-inputs-container" class="space-y-2.5">
                        <div class="author-row flex flex-col sm:flex-row items-stretch sm:items-center gap-2 p-2 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700">
                            <div class="relative flex-grow">
                                <input type="text" name="author_names[]" list="authors-datalist" value="{{ old('author_names.0') }}" placeholder="Ketik nama pengarang utama..." class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-xs font-semibold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
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
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Bentuk Karya (GMD)</label>
                    <select name="gmd_id" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none">
                        <option value="">Pilih GMD</option>
                        @foreach($gmds as $g)
                            <option value="{{ $g->gmd_id }}" {{ old('gmd_id', 1) == $g->gmd_id ? 'selected' : '' }}>{{ $g->gmd_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Subjek Dropdown (Select2 Tags Creatable & Multiple Select) -->
            <!-- Subjek Dropdown (Select2 Tags Creatable & Multiple Select) -->
            <div>
                @php
                    $allSelectableTopics = array_values(array_unique(array_filter(array_merge(
                        $reviewerTopics ?? [],
                        isset($topics) ? $topics->pluck('topic')->toArray() : []
                    ))));
                @endphp
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5 flex items-center justify-between">
                    <span>Subjek / Bidang Ilmu (Bisa pilih lebih dari 1 atau ketik baru)</span>
                    <span class="text-[11px] font-normal text-brand-600 dark:text-sky-400">Pilih atau ketik topik baru lalu Enter</span>
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
                <p class="text-[11px] text-slate-400 mt-1">Pilihan rekomendasi: Sistem Informasi, TI, Bisnis Digital, Kewirausahaan, Metodologi Penelitian, Agama, dll.</p>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Deskripsi Fisik / Kolasi</label>
                <input type="text" name="collation" value="{{ old('collation') }}" placeholder="Contoh: xx, 240 hlm. : ilus. ; 25 cm." class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white font-medium focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Sinopsis / Catatan</label>
                <textarea name="notes" rows="4" placeholder="Tuliskan ringkasan isi buku atau catatan bibliografis..." class="w-full px-4 py-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white font-medium focus:outline-none">{{ old('notes') }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Unggah Gambar Sampul (Cover)</label>
                <input type="file" name="image" accept="image/*" class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-brand-50 file:text-brand-700 dark:file:bg-sky-950 dark:file:text-sky-300 hover:file:bg-brand-100">
            </div>
        </div>

        {{-- Physical Copy Section (Reviewer Request: Ubah Lokasi Rak jadi Jumlah Eksemplar) --}}
        <div class="border-t border-slate-100 dark:border-slate-800 pt-6">
            <div class="flex items-center justify-between mb-1">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white">Registrasi Eksemplar Fisik (Penomoran Barcode Otomatis)</h3>
                <span class="text-xs px-2.5 py-1 bg-brand-50 dark:bg-brand-950/50 text-brand-700 dark:text-sky-300 rounded-lg font-semibold border border-brand-200/60 dark:border-sky-800/40">Awalan B</span>
            </div>
            <p class="text-xs text-slate-400 mb-4">Barcode akan digenerate otomatis secara berurutan sesuai dengan jumlah eksemplar buku yang dimasukkan.</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5 flex items-center justify-between">
                        <span>Kode Eksemplar Pertama / Barcode</span>
                        <span class="text-[10px] font-normal text-brand-600 dark:text-sky-400 font-mono">Auto: {{ $nextItemCode }}</span>
                    </label>
                    <input type="text" id="initial_item_code" name="initial_item_code" value="{{ old('initial_item_code', $nextItemCode) }}" placeholder="Contoh: {{ $nextItemCode }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-mono text-slate-900 dark:text-white font-bold focus:outline-none focus:ring-2 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Jumlah Eksemplar Buku Masuk *</label>
                    <input type="number" id="copies_count" name="copies_count" min="1" max="100" value="{{ old('copies_count', 1) }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-bold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500">
                    <p id="barcode-preview" class="text-[11px] text-brand-600 dark:text-sky-400 font-medium mt-1">
                        Akan membuat 1 eksemplar: <span class="font-mono font-bold">{{ $nextItemCode }}</span>
                    </p>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const countInput = document.getElementById('copies_count');
                const codeInput = document.getElementById('initial_item_code');
                const previewEl = document.getElementById('barcode-preview');

                function updatePreview() {
                    const startCode = codeInput.value.trim() || '{{ $nextItemCode }}';
                    const count = parseInt(countInput.value) || 1;
                    
                    // Ekstrak prefix dan nomor
                    const match = startCode.match(/^([A-Za-z]+)(\d+)$/);
                    if (match && count > 1) {
                        const prefix = match[1];
                        const startNum = parseInt(match[2]);
                        const padLen = match[2].length;
                        const endNum = startNum + count - 1;
                        const endCode = prefix + String(endNum).padStart(padLen, '0');
                        previewEl.innerHTML = `Akan membuat <b>${count} eksemplar</b> berurutan: <span class="font-mono font-bold text-slate-900 dark:text-white">${startCode}</span> s/d <span class="font-mono font-bold text-slate-900 dark:text-white">${endCode}</span>`;
                    } else {
                        previewEl.innerHTML = `Akan membuat 1 eksemplar: <span class="font-mono font-bold text-slate-900 dark:text-white">${startCode}</span>`;
                    }
                }

                if (countInput && codeInput && previewEl) {
                    countInput.addEventListener('input', updatePreview);
                    codeInput.addEventListener('input', updatePreview);
                }

                // Dynamic Add/Remove Authors
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

        <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100 dark:border-slate-800">
            <a href="{{ route('admin.biblio.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs hover:bg-slate-50 transition-colors">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs shadow-md shadow-brand-500/25 transition-all">
                Simpan Bibliografi
            </button>
        </div>
    </form>
</div>
@endsection
