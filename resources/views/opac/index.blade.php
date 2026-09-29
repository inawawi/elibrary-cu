@extends('layouts.opac')

@section('title', 'Katalog Digital Terpadu')

@section('content')
<!-- Hero Section -->
<section class="relative overflow-hidden pt-12 pb-20 bg-gradient-to-b from-brand-50/50 via-white to-slate-50 dark:from-slate-900/60 dark:via-slate-950 dark:to-slate-950 border-b border-slate-200/60 dark:border-slate-800/60">
    <!-- Background Glows -->
    <div class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[350px] bg-gradient-to-tr from-brand-400/20 via-sky-400/20 to-indigo-400/20 blur-[100px] pointer-events-none rounded-full"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="text-center max-w-3xl mx-auto mb-10">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-brand-50 dark:bg-sky-950/80 border border-brand-200 dark:border-sky-800 text-brand-700 dark:text-sky-300 text-xs font-bold mb-4 shadow-sm">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                Koleksi Pustaka Digital Terbaru & Terlengkap
            </div>
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-slate-900 dark:text-white leading-[1.15] mb-5">
                Jelajahi Ribuan <span class="bg-gradient-to-r from-brand-600 via-sky-500 to-indigo-600 dark:from-sky-400 dark:via-cyan-300 dark:to-indigo-400 bg-clip-text text-transparent">Buku & Literatur</span> Ilmiah
            </h1>
            <p class="text-base sm:text-lg text-slate-600 dark:text-slate-300 leading-relaxed font-normal">
                Pusat referensi dan katalog perpustakaan digital Universitas Siber Indonesia. Temukan buku teks, karya ilmiah, jurnal, dan modul akademik dengan mudah dan cepat.
            </p>
        </div>

        <!-- Big Search Box -->
        <div class="max-w-3xl mx-auto mb-8">
            <form action="{{ route('opac.search') }}" method="GET" class="relative group">
                <div class="relative flex items-center bg-white dark:bg-slate-900 rounded-2xl shadow-xl shadow-slate-200/50 dark:shadow-black/40 border-2 border-slate-200 dark:border-slate-700/80 group-focus-within:border-brand-500 dark:group-focus-within:border-sky-500 transition-all p-2">
                    <div class="pl-4 text-slate-400 dark:text-slate-500">
                        <i data-lucide="search" class="w-6 h-6"></i>
                    </div>
                    <input type="text"
                           name="q"
                           placeholder="Cari judul buku, nama pengarang, subjek, atau nomor ISBN..."
                           class="w-full px-4 py-3 bg-transparent text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none text-base font-medium"
                           autofocus>
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-3.5 bg-gradient-to-r from-brand-600 to-sky-600 hover:from-brand-700 hover:to-sky-700 text-white font-bold rounded-xl shadow-md shadow-brand-500/25 transition-all flex-shrink-0">
                        <span>Cari</span>
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </button>
                </div>
            </form>

            <!-- Popular Quick Tags -->
            <div class="flex items-center flex-wrap gap-2 mt-4 justify-center">
                <span class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Topik Populer:</span>
                @foreach($popularTopics->take(5) as $top)
                    <a href="{{ route('opac.search', ['topic' => $top->topic_id]) }}" class="px-3 py-1 rounded-lg bg-white dark:bg-slate-900 hover:bg-brand-50 dark:hover:bg-slate-800 text-xs font-medium text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-800 hover:border-brand-300 dark:hover:border-sky-700 transition-all">
                        {{ $top->topic }} ({{ $top->biblios_count }})
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Hero Visual Carousel Banner -->
        <div x-data="{
                active: 0,
                slides: [
                    {
                        img: '{{ asset('images/slides/slide1_campus.jpg') }}',
                        tag: 'Kampus Cyber University',
                        title: 'The First Fintech University in Indonesia',
                        desc: 'Kampus modern berorientasi digital dan keunggulan teknologi siber untuk mencetak generasi inovator masa depan.'
                    },
                    {
                        img: '{{ asset('images/slides/slide2_library.jpg') }}',
                        tag: 'Ruang Koleksi & Literasi',
                        title: 'Koleksi Pustaka & Literatur Ilmiah Lengkap',
                        desc: 'Akses ribuan judul buku teks, e-book, jurnal akademik, dan repositori skripsi untuk sivitas akademika.'
                    },
                    {
                        img: '{{ asset('images/slides/slide3_student_corner.jpg') }}',
                        tag: 'Student Corner & Diskusi',
                        title: 'Ruang Belajar Kolaboratif & Kreatif Mahasiswa',
                        desc: 'Fasilitas student lounge nyaman untuk bedah referensi riset, belajar bersama, dan penyusunan tugas akhir.'
                    },
                    {
                        img: '{{ asset('images/slides/slide4_podcast.jpg') }}',
                        tag: 'Podcast Studio & Media Hub',
                        title: 'Pusat Literasi Digital & Podcast Edukasi',
                        desc: 'Studio podcast modern untuk menyiarkan diskursus ilmu pengetahuan, review literatur, dan kreativitas mahasiswa.'
                    }
                ],
                paused: false,
                init() {
                    setInterval(() => {
                        if (!this.paused) {
                            this.active = (this.active + 1) % this.slides.length;
                        }
                    }, 5000);
                }
            }"
            @mouseenter="paused = true"
            @mouseleave="paused = false"
            class="max-w-5xl mx-auto my-8 relative group rounded-3xl overflow-hidden shadow-2xl border border-slate-200/80 dark:border-slate-800 bg-slate-900 aspect-[16/8] sm:aspect-[16/7] md:aspect-[21/9]">
            
            <!-- Slides -->
            <template x-for="(slide, index) in slides" :key="index">
                <div x-show="active === index"
                     x-transition:enter="transition ease-out duration-700"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-500"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-105"
                     class="absolute inset-0 w-full h-full">
                    <img :src="slide.img" :alt="slide.title" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/40 to-transparent"></div>
                    
                    <!-- Text Overlay -->
                    <div class="absolute bottom-0 inset-x-0 p-6 sm:p-8 md:p-10 text-white">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-brand-600/90 backdrop-blur text-white text-[11px] font-bold uppercase tracking-wider mb-2" x-text="slide.tag"></span>
                        <h3 class="text-xl sm:text-2xl md:text-3xl font-black text-white leading-tight drop-shadow-md" x-text="slide.title"></h3>
                        <p class="text-xs sm:text-sm text-slate-200 max-w-2xl mt-1.5 line-clamp-2 drop-shadow-sm font-medium" x-text="slide.desc"></p>
                    </div>
                </div>
            </template>

            <!-- Navigation Chevrons -->
            <button @click="active = (active - 1 + slides.length) % slides.length"
                    class="absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-black/40 hover:bg-black/70 backdrop-blur text-white flex items-center justify-center transition-all opacity-0 group-hover:opacity-100 z-10 focus:outline-none">
                <i data-lucide="chevron-left" class="w-5 h-5"></i>
            </button>
            <button @click="active = (active + 1) % slides.length"
                    class="absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-black/40 hover:bg-black/70 backdrop-blur text-white flex items-center justify-center transition-all opacity-0 group-hover:opacity-100 z-10 focus:outline-none">
                <i data-lucide="chevron-right" class="w-5 h-5"></i>
            </button>

            <!-- Slide Indicators -->
            <div class="absolute top-4 right-4 flex items-center gap-1.5 z-10 bg-black/40 backdrop-blur px-3 py-1.5 rounded-full">
                <template x-for="(slide, index) in slides" :key="index">
                    <button @click="active = index"
                            :class="active === index ? 'w-6 bg-brand-400' : 'w-2 bg-white/40 hover:bg-white/70'"
                            class="h-2 rounded-full transition-all duration-300 focus:outline-none"></button>
                </template>
            </div>
        </div>

        <!-- Key Metrics Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 max-w-4xl mx-auto pt-4">
            <div class="p-5 rounded-2xl bg-white/70 dark:bg-slate-900/60 backdrop-blur border border-slate-200/80 dark:border-slate-800/80 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-sky-100 dark:bg-sky-950 flex items-center justify-center text-sky-600 dark:text-sky-400 flex-shrink-0">
                    <i data-lucide="book" class="w-6 h-6"></i>
                </div>
                <div>
                    <div class="text-2xl font-black text-slate-900 dark:text-white">{{ number_format($stats['total_books']) }}</div>
                    <div class="text-xs text-slate-500 dark:text-slate-400 font-medium">Judul Buku</div>
                </div>
            </div>

            <div class="p-5 rounded-2xl bg-white/70 dark:bg-slate-900/60 backdrop-blur border border-slate-200/80 dark:border-slate-800/80 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-indigo-100 dark:bg-indigo-950 flex items-center justify-center text-indigo-600 dark:text-indigo-400 flex-shrink-0">
                    <i data-lucide="copy" class="w-6 h-6"></i>
                </div>
                <div>
                    <div class="text-2xl font-black text-slate-900 dark:text-white">{{ number_format($stats['total_items']) }}</div>
                    <div class="text-xs text-slate-500 dark:text-slate-400 font-medium">Eksemplar Fisik</div>
                </div>
            </div>

            <div class="p-5 rounded-2xl bg-white/70 dark:bg-slate-900/60 backdrop-blur border border-slate-200/80 dark:border-slate-800/80 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-100 dark:bg-emerald-950 flex items-center justify-center text-emerald-600 dark:text-emerald-400 flex-shrink-0">
                    <i data-lucide="users" class="w-6 h-6"></i>
                </div>
                <div>
                    <div class="text-2xl font-black text-slate-900 dark:text-white">{{ number_format($stats['total_members']) }}</div>
                    <div class="text-xs text-slate-500 dark:text-slate-400 font-medium">Anggota Terdaftar</div>
                </div>
            </div>

            <div class="p-5 rounded-2xl bg-white/70 dark:bg-slate-900/60 backdrop-blur border border-slate-200/80 dark:border-slate-800/80 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-100 dark:bg-amber-950 flex items-center justify-center text-amber-600 dark:text-amber-400 flex-shrink-0">
                    <i data-lucide="feather" class="w-6 h-6"></i>
                </div>
                <div>
                    <div class="text-2xl font-black text-slate-900 dark:text-white">{{ number_format($stats['total_authors']) }}</div>
                    <div class="text-xs text-slate-500 dark:text-slate-400 font-medium">Pengarang / Penulis</div>
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
