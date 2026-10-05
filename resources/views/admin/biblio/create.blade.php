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
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Penerbit</label>
                    <select name="publisher_id" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none">
                        <option value="">Pilih Penerbit</option>
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
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Tempat Terbit</label>
                    <select name="publish_place_id" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none">
                        <option value="">Pilih Kota Terbit</option>
                        @foreach($places as $pl)
                            <option value="{{ $pl->place_id }}" {{ old('publish_place_id') == $pl->place_id ? 'selected' : '' }}>{{ $pl->place_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Pengarang Utama</label>
                    <select name="authors[]" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none">
                        <option value="">Pilih Pengarang</option>
                        @foreach($authors as $a)
                            <option value="{{ $a->author_id }}">{{ $a->author_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Bentuk Karya (GMD)</label>
                    <select name="gmd_id" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none">
                        <option value="">Pilih GMD</option>
                        @foreach($gmds as $g)
                            <option value="{{ $g->gmd_id }}" {{ old('gmd_id') == $g->gmd_id ? 'selected' : '' }}>{{ $g->gmd_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Subjek Dropdown (Reviewer Requirement: Multiple Select) -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5 flex items-center justify-between">
                    <span>Subjek / Bidang Ilmu (Bisa pilih lebih dari 1)</span>
                    <span class="text-[11px] font-normal text-brand-600 dark:text-sky-400">Tahan tombol Ctrl / Cmd untuk memilih lebih dari 1</span>
                </label>
                <select name="subjects[]" multiple size="4" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500">
                    @foreach($reviewerTopics as $sub)
                        <option value="{{ $sub }}" {{ (is_array(old('subjects')) && in_array($sub, old('subjects'))) ? 'selected' : '' }}>
                            {{ $sub }}
                        </option>
                    @endforeach
                </select>
                <p class="text-[11px] text-slate-400 mt-1">Pilihan rekomendasi kurikulum: Sistem Informasi, STI, TI, Bisnis Digital, Kewirausahaan, Metodologi Penelitian, Agama, Ekonomi & Keuangan, Pancasila & Kewarganegaraan.</p>
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
