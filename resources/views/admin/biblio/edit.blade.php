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

            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
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
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Bahasa</label>
                    <select name="language_id" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-bold text-slate-900 dark:text-white focus:outline-none">
                        <option value="id" {{ old('language_id', $biblio->language_id ?? 'id') == 'id' ? 'selected' : '' }}>Indonesia (id)</option>
                        <option value="en" {{ old('language_id', $biblio->language_id) == 'en' ? 'selected' : '' }}>English (en)</option>
                        <option value="ar" {{ old('language_id', $biblio->language_id) == 'ar' ? 'selected' : '' }}>Arab / Arabic (ar)</option>
                    </select>
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
                            <option value="{{ $p->publisher_id }}" {{ old('publisher_id', $biblio->publisher_id) == $p->publisher_id ? 'selected' : '' }}>{{ $p->publisher_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Tahun Terbit</label>
                    <input type="text" name="publish_year" value="{{ old('publish_year', $biblio->publish_year) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white font-medium focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5 flex items-center justify-between">
                        <span>Tempat Terbit</span>
                        <span class="text-[10px] text-brand-600 dark:text-sky-400 font-semibold">Bisa ketik baru</span>
                    </label>
                    <select id="publish_place_id" name="publish_place_id" class="w-full">
                        <option value="">-- Pilih atau Ketik Kota Terbit Baru --</option>
                        @foreach($places as $pl)
                            <option value="{{ $pl->place_id }}" {{ old('publish_place_id', $biblio->publish_place_id) == $pl->place_id ? 'selected' : '' }}>{{ $pl->place_name }}</option>
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
                        @php
                            $existingAuthorData = [];
                            if ($biblio->authors->isNotEmpty()) {
                                foreach ($biblio->authors as $a) {
                                    $existingAuthorData[] = [
                                        'name' => $a->author_name,
                                        'type' => $a->authority_type ?: 'p',
                                        'level' => $a->pivot->level ?: 1,
                                    ];
                                }
                            } elseif (!empty($biblio->sor)) {
                                $names = array_filter(array_map('trim', explode(';', $biblio->sor)));
                                foreach ($names as $idx => $n) {
                                    $existingAuthorData[] = [
                                        'name' => $n,
                                        'type' => 'p',
                                        'level' => $idx === 0 ? 1 : 2,
                                    ];
                                }
                            }
                            if (empty($existingAuthorData)) {
                                $existingAuthorData = [['name' => '', 'type' => 'p', 'level' => 1]];
                            }
                        @endphp
                        @foreach($existingAuthorData as $idx => $authItem)
                            <div class="author-row flex flex-col sm:flex-row items-stretch sm:items-center gap-2 p-2 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700">
                                <div class="relative flex-grow">
                                    <input type="text" name="author_names[]" list="authors-datalist" value="{{ $authItem['name'] }}" placeholder="Nama pengarang..." class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-xs font-semibold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                                </div>
                                <div class="w-full sm:w-44 flex-shrink-0">
                                    <select name="author_types[]" class="w-full px-2.5 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-xs text-slate-900 dark:text-white focus:outline-none font-medium">
                                        <option value="p" {{ $authItem['type'] === 'p' ? 'selected' : '' }}>Nama Orang</option>
                                        <option value="o" {{ $authItem['type'] === 'o' ? 'selected' : '' }}>Nama Lembaga</option>
                                        <option value="c" {{ $authItem['type'] === 'c' ? 'selected' : '' }}>Badan Organisasi</option>
                                    </select>
                                </div>
                                <div class="w-full sm:w-44 flex-shrink-0">
                                    <select name="author_levels[]" class="w-full px-2.5 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-xs text-slate-900 dark:text-white focus:outline-none font-medium">
                                        <option value="1" {{ $authItem['level'] == 1 ? 'selected' : '' }}>Pengarang Utama</option>
                                        <option value="2" {{ $authItem['level'] == 2 ? 'selected' : '' }}>Editor</option>
                                        <option value="3" {{ $authItem['level'] == 3 ? 'selected' : '' }}>Penerjemah</option>
                                    </select>
                                </div>
                                @if($idx > 0)
                                    <button type="button" class="btn-remove-author p-2 rounded-lg text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/50 transition-colors flex-shrink-0" title="Hapus Pengarang Ini">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                @endif
                            </div>
                        @endforeach
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
                            <option value="{{ $g->gmd_id }}" {{ old('gmd_id', $biblio->gmd_id) == $g->gmd_id ? 'selected' : '' }}>{{ $g->gmd_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            @php
                $isSkripsiBiblio = $biblio->isSkripsi();
                $isJournalBiblio = $biblio->isJournal();
                $isEbookBiblio = $biblio->isEbook() || $biblio->gmd_id == 30;
                $specData = json_decode($biblio->spec_detail_info ?? '{}', true) ?: [];
                $curEbookType = $specData['jenis_ebook'] ?? (str_contains($biblio->edition ?? '', 'e-Book (') ? trim(str_replace(['e-Book (', ')'], '', $biblio->edition)) : '');
                
                $jurnalLevels = [
                    'Jurnal Nasional',
                    'Jurnal Nasional Terakreditasi',
                    'Jurnal Internasional',
                    'Jurnal Internasional Bereputasi'
                ];
                $curJurnalLevel = $biblio->spec_detail_info;

                $curNim = $specData['nim'] ?? '';
                $curProdi = $specData['prodi'] ?? '';
                $curPembimbing1 = $specData['pembimbing_1'] ?? '';
                $curPembimbing2 = $specData['pembimbing_2'] ?? '';
                if ($isSkripsiBiblio && (empty($curNim) || empty($curPembimbing1))) {
                    $skripsiRow = \Illuminate\Support\Facades\DB::table('tb_skripsi')->where('kd_skripsi', $biblio->biblio_id)->first();
                    if ($skripsiRow) {
                        $curNim = $curNim ?: $skripsiRow->nim;
                        $curPembimbing1 = $curPembimbing1 ?: $skripsiRow->dosen;
                        $curPembimbing2 = $curPembimbing2 ?: $skripsiRow->asdos;
                    }
                }
            @endphp

            <!-- Khusus e-Book: Input Jenis e-Book -->
            @if($isEbookBiblio)
                <div class="p-4 rounded-2xl bg-cyan-50/50 dark:bg-cyan-950/30 border border-cyan-200 dark:border-cyan-800/60">
                    <label class="block text-xs font-bold text-cyan-900 dark:text-cyan-300 uppercase tracking-wider mb-1.5 flex items-center justify-between">
                        <span>Jenis / Tipe Koleksi e-Book</span>
                        <span class="text-[10px] text-cyan-600 dark:text-cyan-400 font-semibold">Khusus e-Book</span>
                    </label>
                    <input type="text" name="ebook_type" list="ebook-types-list" value="{{ old('ebook_type', $curEbookType ?: 'Buku Teks Kuliah') }}" placeholder="Contoh: Buku Teks Kuliah, Modul Praktikum, Monograf, Buku Referensi..." class="w-full px-4 py-2.5 rounded-xl border border-cyan-300 dark:border-cyan-700 bg-white dark:bg-slate-900 text-xs font-bold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-cyan-500">
                    <datalist id="ebook-types-list">
                        <option value="Buku Teks Kuliah"></option>
                        <option value="Modul Pembelajaran / Praktikum"></option>
                        <option value="Buku Referensi / Handbook"></option>
                        <option value="Monograf & Hasil Penelitian"></option>
                        <option value="Buku Populer / Umum"></option>
                    </datalist>
                </div>
            @endif

            <!-- Khusus Jurnal: Tingkat Akreditasi & Kala Terbit -->
            @if($isJournalBiblio)
                <div class="p-4 rounded-2xl bg-purple-50/50 dark:bg-purple-950/20 border border-purple-200 dark:border-purple-800/60 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-purple-900 dark:text-purple-300 uppercase tracking-wider mb-1.5 flex items-center justify-between">
                            <span>Tingkat / Kategori Akreditasi Jurnal</span>
                            <span class="text-[10px] text-purple-600 dark:text-purple-400 font-semibold">Khusus Jurnal</span>
                        </label>
                        <select name="spec_detail_info" class="w-full px-3 py-2.5 rounded-xl border border-purple-300 dark:border-purple-700 bg-white dark:bg-slate-900 text-xs text-slate-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-purple-500">
                            <option value="">-- Pilih Tingkat Akreditasi Jurnal --</option>
                            @foreach($jurnalLevels as $lvl)
                                <option value="{{ $lvl }}" {{ old('spec_detail_info', $curJurnalLevel) == $lvl ? 'selected' : '' }}>{{ $lvl }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-purple-900 dark:text-purple-300 uppercase tracking-wider mb-1.5">Kala Terbit</label>
                        <select name="frequency_id" class="w-full px-3 py-2.5 rounded-xl border border-purple-300 dark:border-purple-700 bg-white dark:bg-slate-900 text-xs text-slate-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-purple-500">
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
                                <option value="{{ $fId }}" {{ old('frequency_id', $biblio->frequency_id) == $fId ? 'selected' : '' }}>{{ $fLabel }}</option>
                            @endforeach
                            @foreach($frequencies as $f)
                                @if(!array_key_exists($f->frequency_id, $targetFrequencies))
                                    <option value="{{ $f->frequency_id }}" {{ old('frequency_id', $biblio->frequency_id) == $f->frequency_id ? 'selected' : '' }}>{{ $f->frequency }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
            @endif

            <!-- Khusus Skripsi: NIM, Prodi, Dosen Pembimbing -->
            @if($isSkripsiBiblio)
                <div class="p-4 rounded-2xl bg-purple-50/50 dark:bg-purple-950/20 border border-purple-200 dark:border-purple-800/60 space-y-4">
                    <p class="text-xs font-bold text-purple-900 dark:text-purple-300 uppercase tracking-wider">Data Khusus Skripsi / Tugas Akhir</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">NIM Mahasiswa</label>
                            <input type="text" name="student_nim" value="{{ old('student_nim', $curNim) }}" placeholder="Contoh: 12220001" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-xs font-mono font-bold text-slate-900 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Program Studi</label>
                            <select name="prodi" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-xs text-slate-900 dark:text-white">
                                <option value="">-- Pilih Program Studi --</option>
                                <option value="Teknologi Informasi" {{ old('prodi', $curProdi) === 'Teknologi Informasi' ? 'selected' : '' }}>Teknologi Informasi (S1)</option>
                                <option value="Sistem Informasi" {{ old('prodi', $curProdi) === 'Sistem Informasi' ? 'selected' : '' }}>Sistem Informasi (S1)</option>
                                <option value="Sistem dan Teknologi Informasi" {{ old('prodi', $curProdi) === 'Sistem dan Teknologi Informasi' ? 'selected' : '' }}>Sistem dan Teknologi Informasi (S1)</option>
                                <option value="Bisnis Digital" {{ old('prodi', $curProdi) === 'Bisnis Digital' ? 'selected' : '' }}>Bisnis Digital (S1)</option>
                                <option value="Kewirausahaan" {{ old('prodi', $curProdi) === 'Kewirausahaan' ? 'selected' : '' }}>Kewirausahaan (S1)</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Dosen Pembimbing 1</label>
                            <input type="text" name="pembimbing_1" value="{{ old('pembimbing_1', $curPembimbing1) }}" placeholder="Nama dosen pembimbing 1 beserta gelar..." class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-xs font-semibold text-slate-900 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Dosen Pembimbing 2 (Opsional)</label>
                            <input type="text" name="pembimbing_2" value="{{ old('pembimbing_2', $curPembimbing2) }}" placeholder="Nama dosen pembimbing 2..." class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-xs font-semibold text-slate-900 dark:text-white">
                        </div>
                    </div>
                </div>
            @endif

            <!-- Subjek Dropdown (Select2 Tags Creatable & Multiple Select) -->
            <div>
                @php
                    $existingTopics = $biblio->topics->pluck('topic')->toArray();
                    $allSelectableTopics = array_values(array_unique(array_filter(array_merge(
                        $reviewerTopics ?? [],
                        isset($topics) ? $topics->pluck('topic')->toArray() : [],
                        $existingTopics
                    ))));
                @endphp
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5 flex items-center justify-between">
                    <span>Subjek / Bidang Ilmu (Bisa pilih lebih dari 1 atau ketik baru)</span>
                    <span class="text-[11px] font-normal text-brand-600 dark:text-sky-400">Pilih atau ketik topik baru lalu Enter</span>
                </label>
                <select id="subjects" name="subjects[]" multiple class="w-full">
                    @foreach($allSelectableTopics as $sub)
                        <option value="{{ $sub }}" {{ (is_array(old('subjects')) ? in_array($sub, old('subjects')) : in_array($sub, $existingTopics)) ? 'selected' : '' }}>
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
                <p class="text-[11px] text-slate-400 mt-1">Pilihan rekomendasi kurikulum: Sistem Informasi, TI, Bisnis Digital, Kewirausahaan, Metodologi Penelitian, Agama, dll.</p>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Deskripsi Fisik / Kolasi</label>
                <input type="text" name="collation" value="{{ old('collation', $biblio->collation) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white font-medium focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Sinopsis / Catatan</label>
                <textarea name="notes" rows="4" class="w-full px-4 py-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white font-medium focus:outline-none">{{ old('notes', $biblio->notes) }}</textarea>
            </div>

            <!-- Berkas Digital / Link Sumber Sesuai Tipe Koleksi -->
            @if($isJournalBiblio)
                <div class="p-4 rounded-2xl bg-purple-50/50 dark:bg-purple-950/20 border border-purple-200 dark:border-purple-800">
                    <label class="block text-xs font-bold text-purple-900 dark:text-purple-300 uppercase tracking-wider mb-1.5 flex items-center justify-between">
                        <span>Link URL Jurnal / OJS / Sumber Daring Online</span>
                        <span class="text-[10px] text-purple-600 dark:text-purple-400 font-semibold">Akses Daring / OJS</span>
                    </label>
                    <input type="text" name="file_att" value="{{ old('file_att', $biblio->file_att) }}" placeholder="Contoh: https://journal.cyber-univ.ac.id/index.php/..." class="w-full px-4 py-2.5 rounded-xl border border-purple-300 dark:border-purple-700 bg-white dark:bg-slate-900 text-xs text-slate-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <p class="text-[10px] text-slate-400 mt-1">Tautan langsung ke web jurnal atau OJS untuk akses naskah publikasi secara daring (online).</p>
                </div>
            @elseif($isEbookBiblio)
                <div class="p-4 rounded-2xl bg-cyan-50/50 dark:bg-cyan-950/20 border border-cyan-200 dark:border-cyan-800 space-y-3">
                    <label class="block text-xs font-bold text-cyan-900 dark:text-cyan-300 uppercase tracking-wider flex items-center justify-between">
                        <span>Berkas Digital e-Book & Tautan Daring</span>
                        <span class="text-[10px] text-cyan-600 dark:text-cyan-400 font-semibold">Khusus e-Book</span>
                    </label>
                    @if(!empty($biblio->file_att))
                        <div class="flex items-center justify-between p-3 rounded-xl bg-white dark:bg-slate-900 border border-cyan-200 dark:border-cyan-700/60">
                            <div class="flex items-center gap-2 overflow-hidden">
                                <i data-lucide="file-text" class="w-4 h-4 text-cyan-600 flex-shrink-0"></i>
                                <span class="text-xs font-mono text-slate-700 dark:text-slate-300 truncate">{{ $biblio->file_att }}</span>
                            </div>
                            <a href="{{ $biblio->digital_url }}" target="_blank" class="px-3 py-1 rounded-lg bg-cyan-600 text-white font-bold text-xs hover:bg-cyan-700 flex-shrink-0 ml-3">
                                Buka / Unduh Berkas
                            </a>
                        </div>
                    @endif
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Ganti Berkas e-Book (Upload PDF / ePub Baru)</label>
                            <input type="file" name="ebook_file" accept=".pdf,.epub" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-cyan-100 file:text-cyan-700">
                            <p class="text-[10px] text-slate-400 mt-0.5">Biarkan kosong jika tidak ingin mengganti berkas e-book.</p>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Path File / URL Tautan e-Book</label>
                            <input type="text" name="file_att" value="{{ old('file_att', $biblio->file_att) }}" placeholder="Contoh: files/ebooks/... atau https://..." class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-xs font-mono text-slate-900 dark:text-white">
                        </div>
                    </div>
                </div>
            @elseif($isSkripsiBiblio)
                <div class="p-4 rounded-2xl bg-purple-50/50 dark:bg-purple-950/20 border border-purple-200 dark:border-purple-800 space-y-3">
                    <label class="block text-xs font-bold text-purple-900 dark:text-purple-300 uppercase tracking-wider flex items-center justify-between">
                        <span>Berkas Naskah Skripsi / Tugas Akhir</span>
                        <span class="text-[10px] text-purple-600 dark:text-purple-400 font-semibold">Khusus Skripsi</span>
                    </label>
                    @if(!empty($biblio->file_att))
                        <div class="flex items-center justify-between p-3 rounded-xl bg-white dark:bg-slate-900 border border-purple-200 dark:border-purple-700/60">
                            <div class="flex items-center gap-2 overflow-hidden">
                                <i data-lucide="file-text" class="w-4 h-4 text-purple-600 flex-shrink-0"></i>
                                <span class="text-xs font-mono text-slate-700 dark:text-slate-300 truncate">{{ $biblio->file_att }}</span>
                            </div>
                            <a href="{{ $biblio->digital_url }}" target="_blank" class="px-3 py-1 rounded-lg bg-purple-600 text-white font-bold text-xs hover:bg-purple-700 flex-shrink-0 ml-3">
                                Buka / Unduh Berkas
                            </a>
                        </div>
                    @endif
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Ganti Berkas Skripsi (Upload PDF Baru)</label>
                            <input type="file" name="skripsi_file" accept=".pdf,.doc,.docx,.zip,.rar" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-purple-100 file:text-purple-700">
                            <p class="text-[10px] text-slate-400 mt-0.5">Biarkan kosong jika tidak ingin mengganti berkas skripsi.</p>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Path File / URL Naskah</label>
                            <input type="text" name="file_att" value="{{ old('file_att', $biblio->file_att) }}" placeholder="Contoh: files/skripsi/..." class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-xs font-mono text-slate-900 dark:text-white">
                        </div>
                    </div>
                </div>
            @else
                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5 flex items-center justify-between">
                        <span>Lampiran Berkas / Tautan Digital Pelengkap (Opsional)</span>
                        <span class="text-[10px] text-slate-500 font-semibold">Tautan Digital</span>
                    </label>
                    <input type="text" name="file_att" value="{{ old('file_att', $biblio->file_att) }}" placeholder="Contoh: files/buku/... atau tautan e-resource eksternal" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-xs text-slate-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-brand-500">
                    <p class="text-[10px] text-slate-400 mt-1">Opsional untuk buku teks/referensi jika memiliki suplemen berkas atau CD/akses daring.</p>
                </div>
            @endif

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
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
