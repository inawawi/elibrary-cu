@extends('layouts.admin')

@section('title', 'Tambah Jurnal Baru')
@section('header_title', 'Katalogisasi Jurnal Ilmiah')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.biblio.index', ['gmd_id' => 263]) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-slate-800 dark:hover:text-white">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Kembali ke Daftar Koleksi</span>
        </a>
        <div class="inline-flex items-center gap-2 px-3 py-1 bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 rounded-full text-xs font-bold border border-purple-200 dark:border-purple-800">
            <i data-lucide="book-open-check" class="w-3.5 h-3.5"></i>
            <span>Koleksi GMD: Jurnal (Kode R)</span>
        </div>
    </div>

    <form action="{{ route('admin.jurnal.store') }}" method="POST" enctype="multipart/form-data" class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-8 shadow-sm space-y-6">
        @csrf

        <div class="border-b border-slate-100 dark:border-slate-800 pb-4">
            <h2 class="text-lg font-black text-slate-900 dark:text-white">Informasi Dasar Jurnal</h2>
            <p class="text-xs text-slate-400">Masukkan detail metadata jurnal ilmiah sesuai standar katalog perpustakaan</p>
        </div>

        @if ($errors->any())
            <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-300 text-xs space-y-1">
                <p class="font-bold">Periksa kembali isian formulir:</p>
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="space-y-4">
            <!-- Judul Jurnal -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Nama / Judul Jurnal *</label>
                <input type="text" name="title" value="{{ old('title') }}" required placeholder="Contoh: Jurnal Teknologi dan Sistem Informasi (JTSI)..." class="w-full px-4 py-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-brand-500 focus:outline-none">
            </div>

            <!-- Reviewer Requirements: Kolom Edisi & Kategori Akreditasi Jurnal -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5 flex items-center justify-between">
                        <span>Kolom Edisi (Volume & Nomor) *</span>
                        <span class="text-[10px] text-purple-600 dark:text-purple-400 font-semibold">Reviewer Catatan #1</span>
                    </label>
                    <input type="text" name="edition" value="{{ old('edition') }}" required placeholder="Contoh: Vol. 8 No. 2, 2026" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5 flex items-center justify-between">
                        <span>Tingkat / Kategori Jurnal *</span>
                        <span class="text-[10px] text-purple-600 dark:text-purple-400 font-semibold">Reviewer Catatan #4</span>
                    </label>
                    <select name="spec_detail_info" required class="w-full px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-brand-500">
                        <option value="">-- Pilih Tingkat Akreditasi Jurnal --</option>
                        @foreach($jurnalLevels as $lvl)
                            <option value="{{ $lvl }}" {{ old('spec_detail_info') == $lvl ? 'selected' : '' }}>{{ $lvl }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Reviewer Requirements: Kolom Kala Terbit & ISSN -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5 flex items-center justify-between">
                        <span>Kala Terbit *</span>
                        <span class="text-[10px] text-purple-600 dark:text-purple-400 font-semibold">Reviewer Catatan #2</span>
                    </label>
                    <select name="frequency_id" required class="w-full px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-brand-500">
                        <option value="">-- Pilih Kala Terbit --</option>
                        @php
                            $targetFrequencies = [
                                8 => 'Annually (Tahunan)',
                                7 => '3 Times a Year (3 kali setahun)',
                                6 => 'Quarterly (Triwulan)',
                                4 => 'Monthly (Bulanan)'
                            ];
                        @endphp
                        @foreach($targetFrequencies as $fId => $fLabel)
                            <option value="{{ $fId }}" {{ old('frequency_id') == $fId ? 'selected' : '' }}>{{ $fLabel }}</option>
                        @endforeach
                        @foreach($frequencies as $f)
                            @if(!array_key_exists($f->frequency_id, $targetFrequencies))
                                <option value="{{ $f->frequency_id }}" {{ old('frequency_id') == $f->frequency_id ? 'selected' : '' }}>{{ $f->frequency }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">ISSN / e-ISSN</label>
                    <input type="text" name="isbn_issn" value="{{ old('isbn_issn') }}" placeholder="Contoh: 2723-3863" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-mono text-slate-900 dark:text-white font-medium focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Tahun Terbit</label>
                    <input type="text" name="publish_year" value="{{ old('publish_year', date('Y')) }}" placeholder="Contoh: {{ date('Y') }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white font-medium focus:outline-none">
                </div>
            </div>

            <!-- Call Number, Klasifikasi, Penanggung Jawab -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Nomor Panggil (Call Number)</label>
                    <input type="text" name="call_number" value="{{ old('call_number') }}" placeholder="Contoh: R 005.1 JUR" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-mono text-slate-900 dark:text-white font-bold focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Klasifikasi DDC</label>
                    <input type="text" name="classification" value="{{ old('classification') }}" placeholder="Contoh: 005.1" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-mono text-slate-900 dark:text-white font-medium focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Dewan Redaksi / Editor (SOR)</label>
                    <input type="text" name="sor" value="{{ old('sor') }}" placeholder="Contoh: Editor in Chief: Prof. Dr. ..." class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white font-medium focus:outline-none">
                </div>
            </div>

            <!-- Penerbit & Kota Terbit -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Penerbit / Institusi Pengelola</label>
                    <select name="publisher_id" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none">
                        <option value="">Pilih Penerbit / Institusi</option>
                        @foreach($publishers as $p)
                            <option value="{{ $p->publisher_id }}" {{ old('publisher_id') == $p->publisher_id ? 'selected' : '' }}>{{ $p->publisher_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Kota Terbit</label>
                    <select name="publish_place_id" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none">
                        <option value="">Pilih Kota Terbit</option>
                        @foreach($places as $pl)
                            <option value="{{ $pl->place_id }}" {{ old('publish_place_id') == $pl->place_id ? 'selected' : '' }}>{{ $pl->place_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Subjek / Bidang Ilmu -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5 flex items-center justify-between">
                    <span>Subjek / Bidang Ilmu (Bisa pilih lebih dari 1)</span>
                    <span class="text-[11px] font-normal text-brand-600 dark:text-sky-400">Tahan tombol Ctrl / Cmd untuk memilih multi-subjek</span>
                </label>
                <select name="subjects[]" multiple size="4" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500">
                    @foreach($reviewerTopics as $sub)
                        <option value="{{ $sub }}" {{ (is_array(old('subjects')) && in_array($sub, old('subjects'))) ? 'selected' : '' }}>
                            {{ $sub }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Deskripsi Fisik & Catatan / Abstrak -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Deskripsi Fisik (Kolasi)</label>
                    <input type="text" name="collation" value="{{ old('collation') }}" placeholder="Contoh: 120 hlm. : ilus. ; 29 cm." class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white font-medium focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Unggah Cover / Sampul Jurnal</label>
                    <input type="file" name="image" accept="image/*" class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-purple-50 file:text-purple-700 dark:file:bg-purple-950 dark:file:text-purple-300 hover:file:bg-purple-100">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Catatan / Abstrak / URL Indeksasi</label>
                <textarea name="notes" rows="3" placeholder="Tuliskan fokus & ruang lingkup (scope) jurnal, pengindeks (SINTA/Scopus), atau tautan OJS jurnal..." class="w-full px-4 py-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white font-medium focus:outline-none">{{ old('notes') }}</textarea>
            </div>
        </div>

        <!-- Reviewer Requirement #3: Registrasi Eksemplar Fisik dengan Kode Awalan R -->
        <div class="border-t border-slate-100 dark:border-slate-800 pt-6">
            <div class="flex items-center justify-between mb-1">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white">Registrasi Eksemplar Fisik Jurnal (Otomatis Kode Awalan R)</h3>
                <span class="text-xs px-2.5 py-1 bg-purple-50 dark:bg-purple-950/50 text-purple-700 dark:text-purple-300 rounded-lg font-semibold border border-purple-200/60 dark:border-purple-800/40 font-mono">Prefix R</span>
            </div>
            <p class="text-xs text-slate-400 mb-4">Pilihan eksemplar sama dengan buku dengan kode barcode awalan R (Referensi/Jurnal) yang digenerate otomatis berurutan.</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5 flex items-center justify-between">
                        <span>Kode Eksemplar Pertama / Barcode</span>
                        <span class="text-[10px] font-normal text-purple-600 dark:text-purple-400 font-mono">Auto: {{ $nextItemCode }}</span>
                    </label>
                    <input type="text" id="jurnal_item_code" name="initial_item_code" value="{{ old('initial_item_code', $nextItemCode) }}" placeholder="Contoh: {{ $nextItemCode }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-mono text-slate-900 dark:text-white font-bold focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Jumlah Eksemplar Jurnal Masuk *</label>
                    <input type="number" id="jurnal_copies_count" name="copies_count" min="1" max="100" value="{{ old('copies_count', 1) }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-bold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <p id="jurnal-barcode-preview" class="text-[11px] text-purple-600 dark:text-purple-400 font-medium mt-1">
                        Akan membuat 1 eksemplar: <span class="font-mono font-bold">{{ $nextItemCode }}</span>
                    </p>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const countInput = document.getElementById('jurnal_copies_count');
                const codeInput = document.getElementById('jurnal_item_code');
                const previewEl = document.getElementById('jurnal-barcode-preview');

                function updateJurnalPreview() {
                    const startCode = codeInput.value.trim() || '{{ $nextItemCode }}';
                    const count = parseInt(countInput.value) || 1;
                    
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
                    countInput.addEventListener('input', updateJurnalPreview);
                    codeInput.addEventListener('input', updateJurnalPreview);
                }
            });
        </script>

        <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100 dark:border-slate-800">
            <a href="{{ route('admin.biblio.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs hover:bg-slate-50 transition-colors">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs shadow-md shadow-purple-500/25 transition-all flex items-center gap-2">
                <i data-lucide="check" class="w-4 h-4"></i>
                <span>Simpan Jurnal</span>
            </button>
        </div>
    </form>
</div>
@endsection
