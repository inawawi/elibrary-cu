@extends('layouts.opac')

@section('title', 'Warta & Berita Perpustakaan')

@section('content')
<div class="bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-brand-50 dark:bg-sky-950/80 border border-brand-200 dark:border-sky-800 text-brand-700 dark:text-sky-300 text-xs font-bold mb-3 shadow-sm">
            <i data-lucide="newspaper" class="w-4 h-4 text-brand-500"></i>
            Portal Warta & Publikasi Online
        </div>
        <h1 class="text-3xl sm:text-4xl font-black text-slate-900 dark:text-white mb-3 tracking-tight">
            Berita & Kegiatan Perpustakaan
        </h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 max-w-2xl mx-auto">
            Kumpulan berita, rilis pers, liputan media, dan agenda kegiatan Perpustakaan Universitas Siber Indonesia yang diterbitkan di berbagai portal berita online.
        </p>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($newsArticles as $news)
            <article class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-200 flex flex-col group">
                <div class="relative h-48 overflow-hidden bg-slate-100 dark:bg-slate-800">
                    <img src="{{ asset($news['image']) }}" alt="{{ $news['title'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    <div class="absolute top-4 left-4">
                        <span class="px-3 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-white/90 dark:bg-slate-900/90 text-brand-600 dark:text-sky-300 backdrop-blur shadow-sm">
                            {{ $news['category'] }}
                        </span>
                    </div>
                </div>

                <div class="p-6 flex flex-col flex-grow justify-between space-y-4">
                    <div class="space-y-2.5">
                        <div class="flex items-center justify-between text-xs text-slate-400">
                            <span class="flex items-center gap-1 font-medium text-brand-600 dark:text-sky-400">
                                <i data-lucide="globe" class="w-3.5 h-3.5"></i>
                                {{ $news['source'] }}
                            </span>
                            <span>{{ $news['date'] }}</span>
                        </div>

                        <h3 class="font-black text-base text-slate-900 dark:text-white group-hover:text-brand-600 dark:group-hover:text-sky-400 transition-colors leading-snug">
                            {{ $news['title'] }}
                        </h3>

                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed line-clamp-3">
                            {{ $news['excerpt'] }}
                        </p>
                    </div>

                    <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                        <span class="text-[11px] text-slate-400">Liputan Media Online</span>
                        <a href="{{ $news['url'] }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 text-xs font-bold text-brand-600 dark:text-sky-400 hover:text-brand-700 group-hover:underline">
                            <span>Baca Berita Asli</span>
                            <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                        </a>
                    </div>
                </div>
            </article>
        @empty
            <div class="col-span-3 text-center py-12 text-slate-400">
                Belum ada berita yang dipublikasikan.
            </div>
        @endforelse
    </div>
</div>
@endsection
