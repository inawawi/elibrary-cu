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

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Filter Tabs: Semua / Portal Nasional / Internal Kampus -->
    <div class="flex flex-wrap items-center justify-center gap-2 mb-10">
        <a href="{{ route('opac.news', ['tab' => 'all']) }}"
           class="px-4 py-2.5 rounded-2xl text-xs font-bold transition-all flex items-center gap-2 {{ $tab === 'all' ? 'bg-brand-600 text-white shadow-lg shadow-brand-500/25' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-50' }}">
            <i data-lucide="newspaper" class="w-4 h-4"></i>
            <span>Semua Berita</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] {{ $tab === 'all' ? 'bg-white/20 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300' }}">
                {{ $counts['all'] }}
            </span>
        </a>

        <a href="{{ route('opac.news', ['tab' => 'national']) }}"
           class="px-4 py-2.5 rounded-2xl text-xs font-bold transition-all flex items-center gap-2 {{ $tab === 'national' ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-500/25' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-50' }}">
            <span class="relative flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
            </span>
            <span>🌐 Portal Nasional Terkini (Live)</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] {{ $tab === 'national' ? 'bg-white/20 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300' }}">
                {{ $counts['national'] }}
            </span>
        </a>

        <a href="{{ route('opac.news', ['tab' => 'internal']) }}"
           class="px-4 py-2.5 rounded-2xl text-xs font-bold transition-all flex items-center gap-2 {{ $tab === 'internal' ? 'bg-brand-600 text-white shadow-lg shadow-brand-500/25' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-50' }}">
            <i data-lucide="building-2" class="w-4 h-4"></i>
            <span>🏛️ Berita Kampus & Perpustakaan</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] {{ $tab === 'internal' ? 'bg-white/20 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300' }}">
                {{ $counts['internal'] }}
            </span>
        </a>
    </div>

    <!-- News Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($filteredArticles as $news)
            <article class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-200 flex flex-col group">
                <div class="relative h-48 overflow-hidden bg-slate-100 dark:bg-slate-800">
                    <img src="{{ str_starts_with($news['image'], 'http') ? $news['image'] : asset($news['image']) }}"
                         alt="{{ $news['title'] }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                         onerror="this.src='{{ asset('images/slides/slide1_campus.jpg') }}'">
                    <div class="absolute top-4 left-4 flex items-center gap-2">
                        <span class="px-3 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-white/95 dark:bg-slate-900/95 text-brand-600 dark:text-sky-300 backdrop-blur shadow-sm">
                            {{ $news['category'] }}
                        </span>
                        @if(!empty($news['is_national']))
                            <span class="px-2.5 py-1 rounded-full text-[9px] font-bold bg-emerald-600 text-white shadow-sm flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                                <span>Portal Nasional</span>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="p-6 flex flex-col flex-grow justify-between space-y-4">
                    <div class="space-y-2.5">
                        <div class="flex items-center justify-between text-xs text-slate-400">
                            <span class="flex items-center gap-1 font-bold {{ !empty($news['is_national']) ? 'text-emerald-600 dark:text-emerald-400' : 'text-brand-600 dark:text-sky-400' }}">
                                <i data-lucide="{{ !empty($news['is_national']) ? 'globe' : 'building-2' }}" class="w-3.5 h-3.5"></i>
                                {{ $news['source'] }}
                            </span>
                            <span class="text-[11px]">{{ $news['date'] }}</span>
                        </div>

                        <h3 class="font-black text-base text-slate-900 dark:text-white group-hover:text-brand-600 dark:group-hover:text-sky-400 transition-colors leading-snug">
                            {{ $news['title'] }}
                        </h3>

                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed line-clamp-3">
                            {{ $news['excerpt'] }}
                        </p>
                    </div>

                    <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                        <span class="text-[11px] text-slate-400">
                            {{ !empty($news['is_national']) ? 'Sumber: ' . $news['source'] : 'Rilis Resmi Kampus' }}
                        </span>
                        <a href="{{ $news['url'] }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 text-xs font-bold text-brand-600 dark:text-sky-400 hover:text-brand-700 group-hover:underline">
                            <span>Baca Berita Asli</span>
                            <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                        </a>
                    </div>
                </div>
            </article>
        @empty
            <div class="col-span-3 text-center py-12 text-slate-400">
                <i data-lucide="newspaper" class="w-12 h-12 mx-auto mb-3 opacity-30"></i>
                <p class="text-sm font-semibold">Belum ada berita pada kategori ini.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
