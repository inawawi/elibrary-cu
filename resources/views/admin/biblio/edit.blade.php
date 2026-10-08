@extends('layouts.admin')

@section('title', 'Ubah Buku - ' . $biblio->title)
@section('header_title', 'Perbarui Data Bibliografi')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.biblio.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-slate-800 dark:hover:text-white">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Kembali ke Daftar Buku</span>
        </a>
    </div>

    <form action="{{ route('admin.biblio.update', $biblio->biblio_id) }}" method="POST" enctype="multipart/form-data" class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-8 shadow-sm space-y-6">
        @csrf
        @method('PUT')

        <div class="border-b border-slate-100 dark:border-slate-800 pb-4 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-black text-slate-900 dark:text-white">Ubah Metadata Buku</h2>
                <p class="text-xs text-slate-400">ID Bibliografi: {{ $biblio->biblio_id }}</p>
            </div>
            <a href="{{ route('admin.biblio.items', $biblio->biblio_id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-sky-50 text-sky-700 dark:bg-sky-950 dark:text-sky-300 text-xs font-bold">
                <i data-lucide="barcode" class="w-3.5 h-3.5"></i>
                <span>Kelola {{ $biblio->items->count() }} Eksemplar</span>
            </a>
        </div>

        <div class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Judul Buku *</label>
                <input type="text" name="title" value="{{ old('title', $biblio->title) }}" required class="w-full px-4 py-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-brand-500 focus:outline-none">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Pernyataan Tanggung Jawab (SOR)</label>
                    <input type="text" name="sor" value="{{ old('sor', $biblio->sor) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white font-medium focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Edisi</label>
                    <input type="text" name="edition" value="{{ old('edition', $biblio->edition) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white font-medium focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Nomor Panggil</label>
                    <input type="text" name="call_number" value="{{ old('call_number', $biblio->call_number) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-mono text-slate-900 dark:text-white font-bold focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Klasifikasi (DDC)</label>
                    <input type="text" name="classification" value="{{ old('classification', $biblio->classification) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-mono text-slate-900 dark:text-white font-medium focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">ISBN / ISSN</label>
                    <input type="text" name="isbn_issn" value="{{ old('isbn_issn', $biblio->isbn_issn) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-mono text-slate-900 dark:text-white font-medium focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Penerbit</label>
                    <select name="publisher_id" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none">
                        <option value="">Pilih Penerbit</option>
                        @foreach($publishers as $p)
                            <option value="{{ $p->publisher_id }}" {{ old('publisher_id', $biblio->publisher_id) == $p->publisher_id ? 'selected' : '' }}>{{ $p->publisher_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Tahun Terbit</label>
                    <input type="text" name="publish_year" value="{{ old('publish_year', $biblio->publish_year) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white font-medium focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Tempat Terbit</label>
                    <select name="publish_place_id" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none">
                        <option value="">Pilih Kota Terbit</option>
                        @foreach($places as $pl)
                            <option value="{{ $pl->place_id }}" {{ old('publish_place_id', $biblio->publish_place_id) == $pl->place_id ? 'selected' : '' }}>{{ $pl->place_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Pengarang Utama</label>
                    <select name="authors[]" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none">
                        <option value="">Pilih Pengarang</option>
                        @php $currentAuthorId = $biblio->authors->first()?->author_id; @endphp
                        @foreach($authors as $a)
                            <option value="{{ $a->author_id }}" {{ $currentAuthorId == $a->author_id ? 'selected' : '' }}>{{ $a->author_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Bentuk Karya (GMD)</label>
                    <select name="gmd_id" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none">
                        <option value="">Pilih GMD</option>
                        @foreach($gmds as $g)
                            <option value="{{ $g->gmd_id }}" {{ old('gmd_id', $biblio->gmd_id) == $g->gmd_id ? 'selected' : '' }}>{{ $g->gmd_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Subjek Dropdown (Reviewer Requirement: Multiple Select) -->
            <div>
                @php
                    $existingTopics = $biblio->topics->pluck('topic')->toArray();
                @endphp
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5 flex items-center justify-between">
                    <span>Subjek / Bidang Ilmu (Bisa pilih lebih dari 1)</span>
                    <span class="text-[11px] font-normal text-brand-600 dark:text-sky-400">Tahan tombol Ctrl / Cmd untuk memilih lebih dari 1</span>
                </label>
                <select name="subjects[]" multiple size="4" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500">
                    @foreach($reviewerTopics as $sub)
                        <option value="{{ $sub }}" {{ (is_array(old('subjects')) ? in_array($sub, old('subjects')) : in_array($sub, $existingTopics)) ? 'selected' : '' }}>
                            {{ $sub }}
                        </option>
                    @endforeach
                </select>
                <p class="text-[11px] text-slate-400 mt-1">Pilihan rekomendasi kurikulum: Sistem Informasi, STI, TI, Bisnis Digital, Kewirausahaan, Metodologi Penelitian, Agama, Ekonomi & Keuangan, Pancasila & Kewarganegaraan.</p>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Deskripsi Fisik / Kolasi</label>
                <input type="text" name="collation" value="{{ old('collation', $biblio->collation) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white font-medium focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Sinopsis / Catatan</label>
                <textarea name="notes" rows="4" class="w-full px-4 py-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white font-medium focus:outline-none">{{ old('notes', $biblio->notes) }}</textarea>
            </div>

            <!-- Link URL Jurnal / Sumber Digital Online -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5 flex items-center justify-between">
                    <span>Link URL Jurnal / OJS / Sumber Daring Online</span>
                    <span class="text-[10px] text-purple-600 dark:text-purple-400 font-semibold">Akses Daring / OJS</span>
                </label>
                <input type="url" name="file_att" value="{{ old('file_att', $biblio->file_att) }}" placeholder="Contoh: https://journal.cyber-univ.ac.id/index.php/..." class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-purple-500">
                <p class="text-[10px] text-slate-400 mt-1">Tautan langsung ke web jurnal atau OJS untuk akses naskah publikasi secara daring (online).</p>
            </div>

            <!-- Cover Preview & Upload -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Gambar Sampul</label>
                <div class="flex items-center gap-6">
                    <div class="w-20 h-28 rounded-xl overflow-hidden bg-slate-100 dark:bg-slate-800 flex-shrink-0 border border-slate-200 dark:border-slate-700 shadow-sm">
                        <img src="{{ $biblio->cover_url }}" alt="Cover" class="w-full h-full object-cover">
                    </div>
                    <div class="flex-grow">
                        <input type="file" name="image" accept="image/*" class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-brand-50 file:text-brand-700 dark:file:bg-sky-950 dark:file:text-sky-300 hover:file:bg-brand-100">
                        <p class="text-[11px] text-slate-400 mt-1">Kosongkan jika tidak ingin mengganti cover yang sudah ada.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Eksemplar Fisik & Tambah Eksemplar Baru Masuk (Reviewer Requirement) -->
        <div class="border-t border-slate-100 dark:border-slate-800 pt-6 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">Eksemplar Fisik & Tambah Koleksi Baru Masuk</h3>
                    <p class="text-xs text-slate-400">Total saat ini: <b>{{ $biblio->items->count() }} eksemplar</b> terdaftar di perpustakaan.</p>
                </div>
                <a href="{{ route('admin.biblio.print_single', $biblio->biblio_id) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-sky-50 dark:bg-sky-950/60 text-sky-700 dark:text-sky-300 border border-sky-200 dark:border-sky-800 text-xs font-bold hover:bg-sky-100 transition-colors">
                    <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                    <span>Cetak Label & Barcode Buku Ini</span>
                </a>
            </div>

            <!-- Daftar Eksemplar Lama yang Sudah Ada -->
            <div class="bg-slate-50 dark:bg-slate-800/60 rounded-2xl p-4 border border-slate-200 dark:border-slate-800">
                <p class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2.5">Daftar Eksemplar Lama (Tetap Tersimpan & Tidak Berubah)</p>
                @if($biblio->items->isEmpty())
                    <p class="text-xs text-slate-400 italic">Belum ada eksemplar fisik yang terdaftar untuk judul ini.</p>
                @else
                    <div class="flex flex-wrap gap-2">
                        @foreach($biblio->items as $it)
                            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-xs font-mono">
                                <span class="font-bold text-slate-800 dark:text-white">{{ $it->item_code }}</span>
                                <span class="text-[10px] px-1.5 py-0.5 rounded-md font-sans {{ $it->item_status_id === '001' ? 'bg-emerald-50 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400' : 'bg-amber-50 dark:bg-amber-950 text-amber-600 dark:text-amber-400' }}">
                                    {{ $it->item_status_id === '001' ? 'Tersedia' : 'Dipinjam' }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Form Tambah Eksemplar Baru Masuk -->
            <div class="bg-brand-50/50 dark:bg-brand-950/20 rounded-2xl p-4 border border-brand-100 dark:border-brand-900/40 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-center">
                    <div>
                        <label class="block text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider mb-1">
                            Tambah Jumlah Eksemplar Baru Masuk
                        </label>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">
                            Masukkan jumlah eksemplar tambahan yang baru masuk untuk judul ini. Eksemplar lama tidak akan diubah.
                        </p>
                    </div>
                    <div>
                        <input type="number" id="additional_copies_count" name="additional_copies_count" min="0" max="100" value="0" class="w-full px-4 py-2.5 rounded-xl border border-brand-300 dark:border-brand-700 bg-white dark:bg-slate-900 text-sm font-bold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500">
                        <p id="additional-preview" class="text-[11px] text-brand-600 dark:text-sky-400 font-medium mt-1">
                            (Isi dengan angka jika ada tambahan eksemplar baru masuk)
                        </p>
                    </div>
                </div>

                <!-- Input Tambahan Edisi Baru untuk Eksemplar Baru -->
                <div class="pt-3 border-t border-brand-200/60 dark:border-brand-900/40 grid grid-cols-1 sm:grid-cols-2 gap-4 items-center">
                    <div>
                        <label class="block text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider mb-1">
                            Edisi Eksemplar Baru (Opsional)
                        </label>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">
                            Bisa diisi jika eksemplar yang baru masuk memiliki edisi berbeda/terbaru (misal: Cetakan ke-2 / Vol. 2 No. 1). Jika dikosongkan, akan mengikuti edisi katalog ({{ $biblio->edition ?: '-' }}).
                        </p>
                    </div>
                    <div>
                        <input type="text" name="additional_edition" value="{{ old('additional_edition') }}" placeholder="Contoh: Cetakan ke-2 / Vol. 2 No. 1 (2025)" class="w-full px-4 py-2.5 rounded-xl border border-brand-300 dark:border-brand-700 bg-white dark:bg-slate-900 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500">
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const addInput = document.getElementById('additional_copies_count');
                const addPreview = document.getElementById('additional-preview');
                const nextCode = '{{ $nextItemCode }}';

                if (addInput && addPreview) {
                    addInput.addEventListener('input', function() {
                        const count = parseInt(this.value) || 0;
                        if (count > 0) {
                            const match = nextCode.match(/^([A-Za-z]+)(\d+)$/);
                            if (match && count > 1) {
                                const prefix = match[1];
                                const startNum = parseInt(match[2]);
                                const padLen = match[2].length;
                                const endNum = startNum + count - 1;
                                const endCode = prefix + String(endNum).padStart(padLen, '0');
                                addPreview.innerHTML = `Sistem akan otomatis membuat <b>${count} eksemplar baru</b>: <span class="font-mono font-bold text-slate-900 dark:text-white">${nextCode}</span> s/d <span class="font-mono font-bold text-slate-900 dark:text-white">${endCode}</span>`;
                            } else {
                                addPreview.innerHTML = `Sistem akan otomatis membuat <b>1 eksemplar baru</b>: <span class="font-mono font-bold text-slate-900 dark:text-white">${nextCode}</span>`;
                            }
                        } else {
                            addPreview.innerHTML = '(Isi dengan angka jika ada tambahan eksemplar baru masuk)';
                        }
                    });
                }
            });
        </script>

        <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100 dark:border-slate-800">
            <a href="{{ route('admin.biblio.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs hover:bg-slate-50 transition-colors">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs shadow-md shadow-brand-500/25 transition-all">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
