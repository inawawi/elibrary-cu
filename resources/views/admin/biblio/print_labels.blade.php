@extends('layouts.admin')

@section('title', 'Cetak Label & Barcode Eksemplar')
@section('header_title', 'Cetak Label Punggung & Barcode')

@section('content')
<div class="space-y-6">
    <!-- Non-Print Filter & Control Header -->
    <div class="no-print bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm space-y-5">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-100 dark:border-slate-800 pb-4">
            <div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.biblio.index') }}" class="p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-500">
                        <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    </a>
                    <h2 class="text-base font-black text-slate-900 dark:text-white">Pengaturan Cetak Label & Barcode</h2>
                </div>
                <p class="text-xs text-slate-400 mt-0.5">Label punggung (call number) dan barcode Code 128 untuk ditempel pada fisik buku/skripsi/jurnal.</p>
            </div>
            <div class="flex items-center gap-3">
                <button type="button" onclick="printSelected()" class="px-5 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs shadow-md shadow-brand-500/25 transition-all flex items-center gap-2">
                    <i data-lucide="printer" class="w-4 h-4"></i>
                    <span>Cetak Sekarang (Print)</span>
                </button>
            </div>
        </div>

        <!-- Filter Form dengan Input Judul & Barcode Terpisah (Select2) -->
        <form method="GET" action="{{ route('admin.biblio.print_labels') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Select2 Input Judul -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-1 flex items-center justify-between">
                        <span>Filter Berdasarkan Judul</span>
                        <span class="text-[10px] text-brand-600 dark:text-sky-400 lowercase">ketik untuk mencari judul</span>
                    </label>
                    <select name="biblio_id" id="filter_title" class="w-full">
                        <option value="">-- Semua Judul Pustaka --</option>
                        @foreach($biblioOptions as $b)
                            <option value="{{ $b->biblio_id }}" {{ request('biblio_id', $biblioId) == $b->biblio_id ? 'selected' : '' }}>
                                {{ $b->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Select2 Input Barcode -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-1 flex items-center justify-between">
                        <span>Filter Berdasarkan Barcode / No. Eksemplar</span>
                        <span class="text-[10px] text-brand-600 dark:text-sky-400 lowercase">ketik no. barcode</span>
                    </label>
                    <select name="barcode" id="filter_barcode" class="w-full">
                        <option value="">-- Semua Nomor Barcode --</option>
                        @foreach($barcodeOptions as $code)
                            <option value="{{ $code }}" {{ request('barcode', $barcode) == $code ? 'selected' : '' }}>
                                {{ $code }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 items-end pt-1">
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-1">Kategori GMD</label>
                    <select name="gmd_id" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none">
                        <option value="">Semua GMD</option>
                        @foreach($gmds as $g)
                            <option value="{{ $g->gmd_id }}" {{ request('gmd_id', $gmdId) == $g->gmd_id ? 'selected' : '' }}>{{ $g->gmd_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-1">Mode Tampilan</label>
                    <select name="mode" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none">
                        <option value="both" {{ request('mode', $printMode) == 'both' ? 'selected' : '' }}>Label Punggung & Barcode</option>
                        <option value="spine" {{ request('mode', $printMode) == 'spine' ? 'selected' : '' }}>Hanya Label Punggung</option>
                        <option value="barcode" {{ request('mode', $printMode) == 'barcode' ? 'selected' : '' }}>Hanya Barcode Eksemplar</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-1">Jumlah Kolom Cetak</label>
                    <select name="columns" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none">
                        <option value="2" {{ request('columns', $columns) == 2 ? 'selected' : '' }}>2 Kolom (Standar)</option>
                        <option value="3" {{ request('columns', $columns) == 3 ? 'selected' : '' }}>3 Kolom (Stiker Kecil)</option>
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit" class="w-full px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs transition-colors">
                        Terapkan Filter
                    </button>
                    <a href="{{ route('admin.biblio.print_labels') }}" class="px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 text-slate-600 dark:text-slate-400 text-xs font-semibold hover:bg-slate-50 text-center">
                        Reset
                    </a>
                </div>
            </div>
        </form>

        <!-- Selection Control & Instructions Toolbar -->
        <div class="bg-slate-50 dark:bg-slate-800/60 p-3.5 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-3 border border-slate-200 dark:border-slate-700/60">
            <div class="flex items-center gap-3">
                <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Pilih Barcode untuk Dicetak:</span>
                <span id="selected-count-badge" class="px-2.5 py-1 rounded-full text-xs font-black bg-brand-100 text-brand-700 dark:bg-brand-950 dark:text-brand-300">
                    {{ $items->count() }} dari {{ $items->count() }} dipilih
                </span>
                <span class="text-[11px] text-slate-400 hidden sm:inline">(Klik kartu untuk memilih/batal)</span>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="selectAllItems()" class="px-3 py-1.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-xs font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-100">
                    Pilih Semua
                </button>
                <button type="button" onclick="deselectAllItems()" class="px-3 py-1.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-xs font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-100">
                    Batal Pilih
                </button>
                <button type="button" onclick="printSelected()" class="px-4 py-1.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs shadow-sm flex items-center gap-1.5">
                    <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                    <span>Cetak Terpilih</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Print Area -->
    @if($items->isEmpty())
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-12 text-center">
            <i data-lucide="package-search" class="w-12 h-12 text-slate-300 mx-auto mb-3"></i>
            <h3 class="text-sm font-bold text-slate-700 dark:text-slate-300">Tidak ada data eksemplar yang ditemukan</h3>
            <p class="text-xs text-slate-400 mt-1">Coba sesuaikan kata kunci pencarian judul, barcode, atau kategori GMD di atas.</p>
        </div>
    @else
        <div id="print-sheet" class="print-container grid {{ request('columns', $columns) == 3 ? 'grid-cols-1 sm:grid-cols-2 md:grid-cols-3' : 'grid-cols-1 sm:grid-cols-2' }} gap-4">
            @foreach($items as $item)
                @php
                    $biblio = $item->biblio;
                    $spine = $biblio ? $biblio->spine_label_components : [
                        'header' => 'Elibrary Cyber University',
                        'classification' => '000',
                        'author_code' => 'XXX',
                        'title_code' => 'x',
                        'full_call_number' => '000 XXX x'
                    ];
                    $currentMode = request('mode', $printMode);
                    $itemEdition = $item->edition ?: $biblio?->edition;
                @endphp
                <div class="label-card relative bg-white rounded-2xl border-2 border-brand-500 p-3 shadow-xs break-inside-avoid flex items-center justify-between gap-3 text-slate-900 cursor-pointer transition-all select-none" data-item-id="{{ $item->item_id }}">
                    <!-- Checkbox Seleksi (Hanya tampil di layar, tersembunyi saat cetak) -->
                    <div class="no-print absolute top-2 right-2 z-10">
                        <input type="checkbox" class="item-card-select w-4 h-4 rounded text-brand-600 focus:ring-brand-500 cursor-pointer" value="{{ $item->item_id }}" checked>
                    </div>

                    <!-- Spine Label (No Punggung) -->
                    @if($currentMode === 'both' || $currentMode === 'spine')
                        <div class="spine-box border-2 border-slate-900 rounded-lg p-2.5 w-36 text-center flex-shrink-0 bg-white leading-tight">
                            <div class="text-[9px] font-black uppercase tracking-tight border-b border-slate-900 pb-1 mb-1 text-slate-900">
                                {{ $spine['header'] }}
                            </div>
                            <div class="text-xs font-mono font-black text-slate-900 my-0.5">
                                {{ $spine['classification'] }}
                            </div>
                            <div class="text-xs font-mono font-black uppercase text-slate-900 my-0.5">
                                {{ $spine['author_code'] }}
                            </div>
                            <div class="text-xs font-mono font-black lowercase text-slate-900 mt-0.5">
                                {{ $spine['title_code'] }}
                            </div>
                        </div>
                    @endif

                    <!-- Barcode Eksemplar -->
                    @if($currentMode === 'both' || $currentMode === 'barcode')
                        <div class="barcode-box flex-grow text-center bg-white p-2 rounded-lg border border-slate-200 flex flex-col items-center justify-center min-w-0 pr-6">
                            <div class="w-full overflow-hidden flex justify-center mb-1">
                                {!! $item->barcode_svg !!}
                            </div>
                            <div class="text-[10px] text-slate-700 font-semibold truncate max-w-[200px]" title="{{ $biblio?->title }}">
                                {{ Str::limit($biblio?->title ?? 'Untitled', 28) }}
                            </div>
                            @if($itemEdition)
                                <div class="text-[9px] font-bold text-purple-700 bg-purple-50 dark:bg-purple-950/40 dark:text-purple-300 px-1.5 py-0.5 rounded mt-0.5 max-w-[200px] truncate" title="Edisi: {{ $itemEdition }}">
                                    Edisi: {{ $itemEdition }}
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>

<style>
@media print {
    /* Hide layout chrome and non-print items */
    body {
        background: #ffffff !important;
        color: #000000 !important;
        margin: 0 !important;
        padding: 5mm !important;
    }
    header, aside, .no-print, nav, footer, #sidebar {
        display: none !important;
    }
    main {
        padding: 0 !important;
        margin: 0 !important;
        max-width: 100% !important;
    }
    .print-container {
        display: grid !important;
        grid-template-columns: {{ request('columns', $columns) == 3 ? 'repeat(3, 1fr)' : 'repeat(2, 1fr)' }} !important;
        gap: 6mm !important;
        width: 100% !important;
    }
    .label-card {
        border: 1px dashed #444 !important;
        box-shadow: none !important;
        page-break-inside: avoid !important;
        break-inside: avoid !important;
        background: #ffffff !important;
        padding: 3mm !important;
    }
    /* Sembunyikan item yang tidak dipilih */
    .print-excluded {
        display: none !important;
    }
    .spine-box {
        border: 1.5px solid #000000 !important;
        background: #ffffff !important;
        color: #000000 !important;
    }
    .barcode-box {
        border: 1px solid #ddd !important;
        background: #ffffff !important;
        padding-right: 0.5rem !important;
    }
    @page {
        size: A4;
        margin: 8mm;
    }
}
</style>

@push('scripts')
<script>
    $(document).ready(function() {
        // Inisialisasi Select2 untuk Filter Judul & Barcode
        $('#filter_title').select2({
            placeholder: '-- Cari & Pilih Judul Pustaka --',
            allowClear: true,
            width: '100%'
        });

        $('#filter_barcode').select2({
            placeholder: '-- Cari & Pilih Nomor Barcode --',
            allowClear: true,
            width: '100%'
        });

        updateSelectionCount();
    });

    function updateSelectionCount() {
        const total = $('.item-card-select').length;
        const selected = $('.item-card-select:checked').length;
        $('#selected-count-badge').text(selected + ' dari ' + total + ' dipilih');

        $('.item-card-select').each(function() {
            const card = $(this).closest('.label-card');
            if ($(this).is(':checked')) {
                card.removeClass('print-excluded opacity-50 bg-slate-50 border-slate-300');
                card.addClass('border-brand-500 bg-white shadow-xs');
            } else {
                card.addClass('print-excluded opacity-50 bg-slate-50 border-slate-300');
                card.removeClass('border-brand-500 bg-white shadow-xs');
            }
        });
    }

    function selectAllItems() {
        $('.item-card-select').prop('checked', true);
        updateSelectionCount();
    }

    function deselectAllItems() {
        $('.item-card-select').prop('checked', false);
        updateSelectionCount();
    }

    function printSelected() {
        const count = $('.item-card-select:checked').length;
        if (count === 0) {
            alert('Silakan pilih minimal 1 eksemplar kartu barcode untuk dicetak!');
            return;
        }
        window.print();
    }

    // Klik pada seluruh area kartu untuk toggle seleksi (kecuali langsung klik input checkbox)
    $(document).on('click', '.label-card', function(e) {
        if ($(e.target).is('input[type="checkbox"]')) {
            updateSelectionCount();
            return;
        }
        const cb = $(this).find('.item-card-select');
        cb.prop('checked', !cb.is(':checked'));
        updateSelectionCount();
    });
</script>
@endpush
@endsection
