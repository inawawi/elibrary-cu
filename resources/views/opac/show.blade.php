@extends('layouts.opac')

@section('title', $book->title)

@section('content')
<!-- Breadcrumbs -->
<div class="bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 py-3.5">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex items-center gap-2 text-xs font-semibold text-slate-500">
            <a href="{{ route('opac.index') }}" class="hover:text-brand-600 dark:hover:text-sky-400">Beranda</a>
            <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-slate-400"></i>
            <a href="{{ route('opac.search') }}" class="hover:text-brand-600 dark:hover:text-sky-400">Katalog Buku</a>
            <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-slate-400"></i>
            <span class="text-slate-900 dark:text-white truncate max-w-xs sm:max-w-md">{{ $book->title }}</span>
        </nav>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
        <!-- Left: Book Cover & Quick Meta -->
        <div class="lg:col-span-4">
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm sticky top-28">
                <!-- Cover Image -->
                <div class="aspect-[3/4] rounded-2xl overflow-hidden bg-slate-100 dark:bg-slate-800 shadow-xl mb-6 relative">
                    <img src="{{ $book->cover_url }}"
                         alt="{{ $book->title }}"
                         class="w-full h-full object-cover"
                         onerror="this.src='{{ asset('images/default_cover.svg') }}'">

                    <div class="absolute top-3 right-3">
                        @if($book->available_copies > 0)
                            <span class="px-3 py-1.5 rounded-full text-xs font-bold bg-emerald-500 text-white shadow-md flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
                                Tersedia ({{ $book->available_copies }} Eks)
                            </span>
                        @else
                            <span class="px-3 py-1.5 rounded-full text-xs font-bold bg-rose-500 text-white shadow-md">
                                Sedang Dipinjam
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Call Number Badge -->
                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/60 text-center mb-6">
                    <div class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1">Nomor Panggil (Call Number)</div>
                    <div class="text-xl font-black font-mono text-brand-600 dark:text-sky-400">
                        {{ $book->call_number ?: '-' }}
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-2.5">
                    @if(!empty($book->file_att))
                        @php
                            $isExternalUrl = \Illuminate\Support\Str::startsWith($book->file_att, ['http://', 'https://']);
                            $digitalUrl = $isExternalUrl ? $book->file_att : asset($book->file_att);
                        @endphp
                        <a href="{{ $digitalUrl }}" target="_blank" rel="noopener noreferrer" class="w-full py-3 px-4 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-sm text-center shadow-md shadow-purple-600/25 flex items-center justify-center gap-2 transition-all">
                            <i data-lucide="external-link" class="w-4 h-4"></i>
                            <span>Buka / Akses Jurnal Daring (Online)</span>
                        </a>
                    @endif

                    <a href="{{ route('member.login') }}" class="w-full py-3 px-4 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-sm text-center shadow-md flex items-center justify-center gap-2 transition-all">
                        <i data-lucide="bookmark" class="w-4 h-4"></i>
                        <span>Pinjam / Reservasi Buku</span>
                    </a>

                    <button onclick="window.print()" class="w-full py-3 px-4 rounded-xl border border-slate-300 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold text-sm flex items-center justify-center gap-2 transition-colors">
                        <i data-lucide="printer" class="w-4 h-4"></i>
                        <span>Cetak Detail Katalog</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Right: Bibliographic Data & Copies -->
        <div class="lg:col-span-8 space-y-8">
            <!-- Book Header -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-8 shadow-sm">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-lg bg-sky-50 dark:bg-sky-950/80 text-sky-700 dark:text-sky-300 text-xs font-bold mb-3">
                    <i data-lucide="layers" class="w-3.5 h-3.5"></i>
                    <span>{{ $book->gmd?->gmd_name ?: 'Monograf / Buku Teks' }}</span>
                </div>

                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black text-slate-900 dark:text-white leading-tight mb-4">
                    {{ $book->title }}
                </h1>

                @if($book->sor)
                    <div class="text-sm font-medium text-slate-600 dark:text-slate-400 mb-6">
                        Pernyataan Tanggung Jawab: <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $book->sor }}</span>
                    </div>
                @endif

                <!-- Metadata List -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 py-6 border-y border-slate-100 dark:border-slate-800 text-sm">
                    <div>
                        <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Pengarang / Penulis</span>
                        <div class="font-semibold text-slate-800 dark:text-slate-200">
                            @forelse($book->authors as $author)
                                <div class="inline-block mr-2">{{ $author->author_name }}</div>
                            @empty
                                <div>{{ $book->sor ?: '-' }}</div>
                            @endforelse
                        </div>
                    </div>

                    <div>
                        <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Penerbit & Tahun</span>
                        <div class="font-semibold text-slate-800 dark:text-slate-200">
                            {{ $book->publisher?->publisher_name ?: '-' }}
                            @if($book->publish_year) ({{ $book->publish_year }}) @endif
                        </div>
                    </div>

                    <div>
                        <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Tempat Terbit</span>
                        <div class="font-semibold text-slate-800 dark:text-slate-200">
                            {{ $book->place?->place_name ?: '-' }}
                        </div>
                    </div>

                    <div>
                        <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">ISBN / ISSN</span>
                        <div class="font-semibold font-mono text-slate-800 dark:text-slate-200">
                            {{ $book->isbn_issn ?: '-' }}
                        </div>
                    </div>

                    <div>
                        <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Edisi / Kolasi</span>
                        <div class="font-semibold text-slate-800 dark:text-slate-200">
                            {{ $book->edition ?: '-' }} {{ $book->collation ? '• ' . $book->collation : '' }}
                        </div>
                    </div>

                    <div>
                        <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Klasifikasi (DDC)</span>
                        <div class="font-semibold text-slate-800 dark:text-slate-200">
                            {{ $book->classification ?: '-' }}
                        </div>
                    </div>
                </div>

                <!-- Topics / Subject Tags -->
                @if($book->topics->isNotEmpty())
                    <div class="mt-6">
                        <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Subjek & Topik Terkait</span>
                        <div class="flex flex-wrap gap-2">
                            @foreach($book->topics as $topic)
                                <a href="{{ route('opac.search', ['topic' => $topic->topic_id]) }}" class="px-3 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-brand-50 dark:hover:bg-slate-700 text-xs font-medium text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 transition-colors">
                                    # {{ $topic->topic }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Abstract / Notes -->
                @if($book->notes)
                    <div class="mt-8 pt-6 border-t border-slate-100 dark:border-slate-800">
                        <h3 class="text-base font-bold text-slate-900 dark:text-white mb-3">Sinopsis & Catatan Bibliografis</h3>
                        <div class="text-sm leading-relaxed text-slate-600 dark:text-slate-300 whitespace-pre-line">
                            {{ $book->notes }}
                        </div>
                    </div>
                @endif
            </div>

            <!-- Physical Copies (Eksemplar Fisik) -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-8 shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-xl font-bold text-slate-900 dark:text-white">Ketersediaan Eksemplar Fisik</h2>
                        <p class="text-xs text-slate-500">Daftar salinan buku yang tersedia di rak perpustakaan</p>
                    </div>
                    <span class="px-3 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-xs font-bold text-slate-700 dark:text-slate-300">
                        Total {{ $book->items->count() }} Eksemplar
                    </span>
                </div>

                @if($book->items->isEmpty())
                    <div class="p-6 text-center text-sm text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/40 rounded-2xl border border-slate-200 dark:border-slate-800">
                        Belum ada data eksemplar fisik untuk judul ini.
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-600 dark:text-slate-300 text-xs font-bold uppercase tracking-wider">
                                <tr>
                                    <th class="py-3 px-4 rounded-l-xl">Kode Barcode</th>
                                    <th class="py-3 px-4">No. Panggil</th>
                                    <th class="py-3 px-4">Lokasi Rak</th>
                                    <th class="py-3 px-4">Tipe Koleksi</th>
                                    <th class="py-3 px-4 rounded-r-xl">Status Ketersediaan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                                @foreach($book->items as $item)
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                                        <td class="py-3.5 px-4 font-mono font-bold text-slate-900 dark:text-white">
                                            {{ $item->item_code }}
                                        </td>
                                        <td class="py-3.5 px-4 text-slate-600 dark:text-slate-400">
                                            {{ $item->call_number ?: $book->call_number ?: '-' }}
                                        </td>
                                        <td class="py-3.5 px-4 text-slate-600 dark:text-slate-400">
                                            {{ $item->location?->location_name ?: 'Rak Umum' }}
                                        </td>
                                        <td class="py-3.5 px-4 text-slate-600 dark:text-slate-400">
                                            {{ $item->collType?->coll_type_name ?: 'Sirkulasi' }}
                                        </td>
                                        <td class="py-3.5 px-4">
                                            @if($item->activeLoan)
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-700 dark:bg-rose-950/80 dark:text-rose-300">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                                    Dipinjam (Kembali: {{ \Carbon\Carbon::parse($item->activeLoan->due_date)->format('d/m/Y') }})
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-950/80 dark:text-emerald-300">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                    Tersedia di Rak
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- Related Books -->
            @if($relatedBooks->isNotEmpty())
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-8 shadow-sm">
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-6">Buku Terkait Lainnya</h2>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-5">
                        @foreach($relatedBooks as $rel)
                            <a href="{{ route('opac.show', $rel->biblio_id) }}" class="group block">
                                <div class="aspect-[3/4] rounded-xl overflow-hidden bg-slate-100 dark:bg-slate-800 mb-2">
                                    <img src="{{ $rel->cover_url }}" alt="{{ $rel->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform" onerror="this.src='{{ asset('images/default_cover.svg') }}'">
                                </div>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white line-clamp-2 group-hover:text-brand-600 transition-colors">
                                    {{ $rel->title }}
                                </h4>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
