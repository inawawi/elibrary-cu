@extends('layouts.opac')

@section('title', 'Katalog Digital Terpadu')

@section('content')
<!-- Hero Section with Dynamic Background Slideshow -->
<section x-data="{
            active: 0,
            slides: {{ Js::from($heroSlides) }},
            paused: false,
            init() {
                if (this.slides.length > 1) {
                    setInterval(() => {
                        if (!this.paused) {
                            this.active = (this.active + 1) % this.slides.length;
                        }
                    }, 6000);
                }
            }
        }"
        @mouseenter="paused = true"
        @mouseleave="paused = false"
        class="relative overflow-hidden py-16 lg:py-24 border-b border-slate-800 bg-slate-950 text-white min-h-[480px] flex items-center">

    <!-- Background Image Slideshow Layer -->
    <div class="absolute inset-0 z-0">
        @foreach($heroSlides as $index => $slide)
            <div x-show="active === {{ $index }}"
                 @if($index !== 0) x-cloak @endif
                 x-transition:enter="transition-opacity ease-in-out duration-1000"
                 x-transition:enter-start="opacity-0 scale-105"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition-opacity ease-in-out duration-1000"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-100"
                 class="absolute inset-0 w-full h-full">
                <img src="{{ $slide['img'] }}" alt="{{ $slide['title'] }}" class="w-full h-full object-cover">
            </div>
        @endforeach

        <!-- Soft Balanced Overlay: Makes photos bright & vibrant while text stays readable -->
        <div class="absolute inset-0 bg-slate-950/35"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/20 to-slate-950/45"></div>
    </div>

    <!-- Foreground Content: Sejajar Horizontal Gambar 2 dengan Gambar 3 -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
            
            <!-- Sisi Kiri: Gambar 2 (Pencarian & Topik Populer) -->
            <div class="lg:col-span-6 xl:col-span-6 space-y-4">
                <form action="{{ route('opac.search') }}" method="GET" class="relative group">
                    <div class="relative flex items-center bg-white/95 dark:bg-slate-900/90 backdrop-blur-md rounded-2xl shadow-2xl shadow-black/50 border-2 border-white/30 dark:border-slate-700/80 group-focus-within:border-sky-400 transition-all p-2">
                        <div class="pl-4 text-slate-400 dark:text-slate-500">
                            <i data-lucide="search" class="w-6 h-6"></i>
                        </div>
                        <input type="text"
                               name="q"
                               placeholder="Cari judul buku, nama pengarang, subjek, atau nomor ISBN..."
                               class="w-full px-4 py-3 bg-transparent text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none text-base font-medium"
                               autofocus>
                        <button type="submit" class="inline-flex items-center gap-2 px-6 py-3.5 bg-gradient-to-r from-brand-600 to-sky-600 hover:from-brand-500 hover:to-sky-500 text-white font-bold rounded-xl shadow-lg shadow-sky-500/30 transition-all flex-shrink-0">
                            <span>Cari</span>
                            <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </button>
                    </div>
                </form>

                <!-- Popular Quick Tags -->
                <div class="flex items-center flex-wrap gap-2 pt-1">
                    <span class="text-xs font-semibold text-slate-300 uppercase tracking-wider">Topik Populer:</span>
                    @foreach($popularTopics->take(5) as $top)
                        <a href="{{ route('opac.search', ['topic' => $top->topic_id]) }}" class="px-3.5 py-1 rounded-lg bg-white/10 hover:bg-white/20 text-xs font-medium text-white backdrop-blur border border-white/15 hover:border-white/40 transition-all">
                            {{ $top->topic }} ({{ $top->biblios_count }})
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Sisi Kanan: Gambar 3 (Slide Info Badge & 4 Kartu Statistik) -->
            <div class="lg:col-span-6 xl:col-span-6 space-y-4">
                <!-- Slide Info & Indicator Badge -->
                <div class="p-3 px-5 rounded-2xl bg-slate-900/80 backdrop-blur-md border border-white/15 flex items-center justify-between gap-4 shadow-xl">
                    <div class="flex items-center gap-3 min-w-0">
                        <span class="w-2.5 h-2.5 rounded-full bg-sky-400 animate-ping flex-shrink-0"></span>
                        <div class="truncate text-left">
                            <span class="text-[10px] uppercase tracking-wider font-bold text-sky-400 block" x-text="slides[active] ? slides[active].tag : ''"></span>
                            <span class="text-xs font-semibold text-white truncate block" x-text="slides[active] ? slides[active].title : ''"></span>
                        </div>
                    </div>

                    <!-- Slide Indicator Dots & Prev/Next -->
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <button @click="active = (active - 1 + slides.length) % slides.length" title="Sebelumnya" class="w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors">
                            <i data-lucide="chevron-left" class="w-4 h-4"></i>
                        </button>
                        <div class="flex items-center gap-1.5 px-1">
                            <template x-for="(slide, index) in slides" :key="index">
                                <button @click="active = index"
                                        :class="active === index ? 'w-5 bg-sky-400' : 'w-2 bg-white/30 hover:bg-white/60'"
                                        class="h-2 rounded-full transition-all duration-300"></button>
                            </template>
                        </div>
                        <button @click="active = (active + 1) % slides.length" title="Berikutnya" class="w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors">
                            <i data-lucide="chevron-right" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>

                <!-- Key Metrics Cards (2x2 Grid) -->
                <div class="grid grid-cols-2 gap-3.5">
                    <div class="p-4 rounded-2xl bg-slate-900/80 backdrop-blur-md border border-white/15 shadow-xl flex items-center gap-3.5 hover:border-sky-400/40 transition-colors">
                        <div class="w-11 h-11 rounded-xl bg-sky-500/20 text-sky-400 flex items-center justify-center flex-shrink-0 border border-sky-400/30">
                            <i data-lucide="book" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <div class="text-xl font-black text-white">{{ number_format($stats['total_books']) }}</div>
                            <div class="text-xs text-slate-300 font-medium">Judul Buku</div>
                        </div>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-900/80 backdrop-blur-md border border-white/15 shadow-xl flex items-center gap-3.5 hover:border-indigo-400/40 transition-colors">
                        <div class="w-11 h-11 rounded-xl bg-indigo-500/20 text-indigo-400 flex items-center justify-center flex-shrink-0 border border-indigo-400/30">
                            <i data-lucide="copy" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <div class="text-xl font-black text-white">{{ number_format($stats['total_items']) }}</div>
                            <div class="text-xs text-slate-300 font-medium">Eksemplar Fisik</div>
                        </div>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-900/80 backdrop-blur-md border border-white/15 shadow-xl flex items-center gap-3.5 hover:border-emerald-400/40 transition-colors">
                        <div class="w-11 h-11 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center flex-shrink-0 border border-emerald-400/30">
                            <i data-lucide="users" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <div class="text-xl font-black text-white">{{ number_format($stats['total_members']) }}</div>
                            <div class="text-xs text-slate-300 font-medium">Anggota Terdaftar</div>
                        </div>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-900/80 backdrop-blur-md border border-white/15 shadow-xl flex items-center gap-3.5 hover:border-amber-400/40 transition-colors">
                        <div class="w-11 h-11 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center flex-shrink-0 border border-amber-400/30">
                            <i data-lucide="feather" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <div class="text-xl font-black text-white">{{ number_format($stats['total_authors']) }}</div>
                            <div class="text-xs text-slate-300 font-medium">Pengarang / Penulis</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Featured Books (Koleksi Pilihan) -->
<section class="py-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between mb-8">
        <div>
            <div class="flex items-center gap-2 text-brand-600 dark:text-sky-400 font-bold text-xs uppercase tracking-wider mb-1">
                <i data-lucide="sparkles" class="w-4 h-4"></i>
                <span>Rekomendasi Pustaka</span>
            </div>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">Buku Pilihan Terpopuler</h2>
        </div>
        <a href="{{ route('opac.search') }}" class="inline-flex items-center gap-1.5 text-sm font-bold text-brand-600 dark:text-sky-400 hover:underline">
            <span>Lihat Semua</span>
            <i data-lucide="chevron-right" class="w-4 h-4"></i>
        </a>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 gap-6">
        @foreach($featuredBooks as $book)
            <div class="group flex flex-col bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 overflow-hidden hover:border-brand-300 dark:hover:border-sky-700 transition-all duration-300 hover:-translate-y-1.5 shadow-sm hover:shadow-xl">
                <!-- Cover Image Container -->
                <div class="relative aspect-[3/4] bg-slate-100 dark:bg-slate-800 overflow-hidden">
                    <img src="{{ $book->cover_url }}"
                         alt="{{ $book->title }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                         loading="lazy"
                         onerror="this.src='{{ asset('images/default_cover.svg') }}'">

                    <!-- Availability Badge -->
                    <div class="absolute top-3 right-3">
                        @if($book->available_copies > 0)
                            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-500/90 text-white backdrop-blur shadow-sm flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                                Tersedia ({{ $book->available_copies }})
                            </span>
                        @else
                            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-500/90 text-white backdrop-blur shadow-sm">
                                Dipinjam
                            </span>
                        @endif
                    </div>

                    @if($book->publish_year)
                        <div class="absolute bottom-3 left-3">
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-900/80 text-white backdrop-blur">
                                {{ $book->publish_year }}
                            </span>
                        </div>
                    @endif
                </div>

                <!-- Info -->
                <div class="p-4 flex flex-col flex-grow">
                    <div class="text-[11px] font-semibold text-brand-600 dark:text-sky-400 mb-1 truncate">
                        {{ $book->call_number ?: 'Koleksi Umum' }}
                    </div>
                    <h3 class="font-bold text-slate-900 dark:text-white line-clamp-2 text-sm leading-snug group-hover:text-brand-600 dark:group-hover:text-sky-400 transition-colors mb-2" title="{{ $book->title }}">
                        <a href="{{ route('opac.show', $book->biblio_id) }}">
                            {{ $book->title }}
                        </a>
                    </h3>
                    <div class="mt-auto pt-2 border-t border-slate-100 dark:border-slate-800 text-xs text-slate-500 dark:text-slate-400 flex items-center justify-between">
                        <span class="truncate max-w-[140px]" title="{{ $book->author_names }}">{{ $book->author_names }}</span>
                        <a href="{{ route('opac.show', $book->biblio_id) }}" class="text-brand-600 dark:text-sky-400 hover:scale-110 transition-transform">
                            <i data-lucide="arrow-up-right" class="w-4 h-4"></i>
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</section>

<!-- Latest Arrivals (Katalog Terbaru) -->
<section class="py-12 bg-slate-100/60 dark:bg-slate-900/40 border-y border-slate-200 dark:border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8">
            <div>
                <div class="flex items-center gap-2 text-indigo-600 dark:text-indigo-400 font-bold text-xs uppercase tracking-wider mb-1">
                    <i data-lucide="clock" class="w-4 h-4"></i>
                    <span>Koleksi Terbaru</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">Buku Baru Masuk</h2>
            </div>
            <a href="{{ route('opac.search', ['sort' => 'newest']) }}" class="inline-flex items-center gap-1.5 text-sm font-bold text-brand-600 dark:text-sky-400 hover:underline">
                <span>Eksplor Katalog</span>
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-5">
            @foreach($latestBooks->take(12) as $book)
                <div class="group flex flex-col bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden hover:border-brand-300 dark:hover:border-sky-700 transition-all duration-200 shadow-sm hover:shadow-lg">
                    <div class="relative aspect-[3/4] bg-slate-100 dark:bg-slate-800 overflow-hidden">
                        <img src="{{ $book->cover_url }}"
                             alt="{{ $book->title }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                             loading="lazy"
                             onerror="this.src='{{ asset('images/default_cover.svg') }}'">
                    </div>
                    <div class="p-3 flex flex-col flex-grow">
                        <h4 class="font-bold text-slate-900 dark:text-white line-clamp-2 text-xs leading-snug group-hover:text-brand-600 dark:group-hover:text-sky-400 transition-colors mb-1.5" title="{{ $book->title }}">
                            <a href="{{ route('opac.show', $book->biblio_id) }}">
                                {{ $book->title }}
                            </a>
                        </h4>
                        <div class="mt-auto text-[11px] text-slate-400 truncate">
                            {{ $book->author_names }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Warta & Berita Terkini Perpustakaan -->
<section class="py-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between mb-8">
        <div>
            <div class="flex items-center gap-2 text-brand-600 dark:text-sky-400 font-bold text-xs uppercase tracking-wider mb-1">
                <i data-lucide="newspaper" class="w-4 h-4"></i>
                <span>Warta & Berita Terkini</span>
            </div>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">Publikasi & Kabar Perpustakaan</h2>
        </div>
        <a href="{{ route('opac.news') }}" class="inline-flex items-center gap-1.5 text-sm font-bold text-brand-600 dark:text-sky-400 hover:underline">
            <span>Lihat Semua Berita</span>
            <i data-lucide="chevron-right" class="w-4 h-4"></i>
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach(collect($newsArticles)->take(4) as $article)
            <article class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 overflow-hidden hover:border-brand-300 dark:hover:border-sky-700 transition-all duration-300 hover:-translate-y-1.5 shadow-sm hover:shadow-xl flex flex-col group">
                <div class="relative aspect-[16/10] overflow-hidden bg-slate-100 dark:bg-slate-800">
                    <img src="{{ asset($article['image']) }}" alt="{{ $article['title'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    <div class="absolute top-3 left-3">
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-brand-600/90 text-white backdrop-blur shadow-sm">
                            {{ $article['category'] }}
                        </span>
                    </div>
                </div>

                <div class="p-5 flex flex-col flex-grow">
                    <div class="flex items-center justify-between text-[11px] text-slate-400 mb-2">
                        <span class="font-semibold text-brand-600 dark:text-sky-400">{{ $article['source'] }}</span>
                        <span>{{ $article['date'] }}</span>
                    </div>

                    <h3 class="font-bold text-slate-900 dark:text-white text-sm line-clamp-2 leading-snug group-hover:text-brand-600 dark:group-hover:text-sky-400 transition-colors mb-2">
                        {{ $article['title'] }}
                    </h3>

                    <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-3 mb-4 leading-relaxed font-normal">
                        {{ $article['excerpt'] }}
                    </p>

                    <div class="mt-auto pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                        <a href="{{ $article['url'] }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 text-xs font-bold text-brand-600 dark:text-sky-400 hover:text-brand-700 dark:hover:text-sky-300">
                            <span>Baca Artikel</span>
                            <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                        </a>
                        <span class="text-[10px] text-slate-400 uppercase tracking-wider font-semibold">Tautan Resmi</span>
                    </div>
                </div>
            </article>
        @endforeach
    </div>
</section>

<!-- Call to Action / Guestbook Banner -->
<section class="py-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="rounded-3xl bg-gradient-to-r from-brand-600 via-sky-600 to-indigo-700 p-8 sm:p-12 text-white shadow-xl relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-72 h-72 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>

        <div class="max-w-2xl relative">
            <span class="inline-block px-3 py-1 rounded-full bg-white/20 text-xs font-bold uppercase tracking-wider mb-4">Kunjungan Perpustakaan</span>
            <h2 class="text-3xl sm:text-4xl font-black mb-4 tracking-tight leading-tight">
                Sedang Berkunjung ke Perpustakaan Hari Ini?
            </h2>
            <p class="text-white/80 text-sm sm:text-base leading-relaxed mb-6">
                Mohon luangkan waktu beberapa detik untuk mengisi Buku Tamu Digital kunjungan Anda untuk membantu kami meningkatkan mutu layanan pustaka.
            </p>
            <div class="flex flex-wrap items-center gap-4">
                <a href="{{ route('opac.guestbook') }}" class="px-6 py-3.5 rounded-xl bg-white text-brand-700 hover:bg-slate-100 font-bold text-sm shadow-lg transition-transform hover:scale-105 inline-flex items-center gap-2">
                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                    <span>Isi Buku Tamu Sekarang</span>
                </a>
                <a href="{{ route('member.login') }}" class="px-6 py-3.5 rounded-xl bg-brand-700/60 hover:bg-brand-700/80 border border-white/20 text-white font-bold text-sm transition-all inline-flex items-center gap-2">
                    <i data-lucide="log-in" class="w-4 h-4"></i>
                    <span>Login Area Anggota</span>
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
