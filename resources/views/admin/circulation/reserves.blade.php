@extends('layouts.admin')

@section('title', 'Daftar Reservasi / Booking Buku')
@section('header_title', 'Manajemen Reservasi & Booking Buku Mandiri')

@section('content')
<div class="space-y-6">
    <!-- Header with Stats & Search -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-800 text-amber-600 dark:text-amber-400 flex items-center justify-center shadow-inner">
                <i data-lucide="bookmark-check" class="w-6 h-6"></i>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-base font-black text-slate-900 dark:text-white">Reservasi Buku Aktif</h2>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-black bg-amber-100 dark:bg-amber-950 text-amber-800 dark:text-amber-300">
                        {{ $reserves->total() }} Menunggu Diambil
                    </span>
                </div>
                <p class="text-xs text-slate-400 mt-0.5">Daftar mahasiswa/anggota yang memesan buku melalui katalog OPAC</p>
            </div>
        </div>

        <div class="flex items-center gap-3 w-full md:w-auto">
            <form action="{{ route('admin.circulation.reserves') }}" method="GET" class="flex items-center gap-2 w-full md:w-72">
                <div class="relative flex-grow">
                    <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text"
                           name="search"
                           value="{{ $search ?? '' }}"
                           placeholder="Cari NIM, Nama, Barcode..."
                           class="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-brand-500">
                </div>
                @if(!empty($search))
                    <a href="{{ route('admin.circulation.reserves') }}" class="p-2 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100" title="Reset filter">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </a>
                @endif
            </form>

            <a href="{{ route('admin.circulation.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs transition-colors flex-shrink-0">
                <i data-lucide="repeat" class="w-4 h-4"></i>
                <span class="hidden sm:inline">Meja Sirkulasi</span>
            </a>
        </div>
    </div>

    <!-- Reserves Table -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-600 dark:text-slate-300 font-bold uppercase tracking-wider border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="py-4 px-6">Barcode & Judul Buku</th>
                        <th class="py-4 px-6">Peminjam / Mahasiswa</th>
                        <th class="py-4 px-6">Kontak Anggota</th>
                        <th class="py-4 px-6">Waktu Booking</th>
                        <th class="py-4 px-6">Status</th>
                        <th class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                    @forelse($reserves as $reserve)
                        @php
                            $biblio = $reserve->biblio;
                            $member = $reserve->member;
                            $item = $reserve->item;
                            $timeAgo = $reserve->reserve_date ? \Carbon\Carbon::parse($reserve->reserve_date)->diffForHumans() : '-';
                            $cleanPhone = preg_replace('/[^0-9]/', '', $member?->member_phone ?? '');
                            if (str_starts_with($cleanPhone, '0')) {
                                $cleanPhone = '62' . substr($cleanPhone, 1);
                            }
                        @endphp
                        <tr class="hover:bg-amber-50/20 dark:hover:bg-amber-950/10 transition-colors">
                            <td class="py-3.5 px-6 max-w-xs">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-14 rounded-lg overflow-hidden bg-slate-200 dark:bg-slate-700 flex-shrink-0 shadow">
                                        <img src="{{ $biblio?->cover_url ?: asset('images/default_cover.svg') }}" class="w-full h-full object-cover">
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-1.5 mb-0.5">
                                            <span class="font-mono text-xs font-black text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/60 px-2 py-0.5 rounded">
                                                {{ $reserve->item_code }}
                                            </span>
                                            <span class="text-[10px] text-slate-400 truncate">
                                                {{ $item?->location?->location_name ?: 'Rak Umum' }}
                                            </span>
                                        </div>
                                        <h4 class="font-bold text-slate-900 dark:text-white line-clamp-1 text-sm">
                                            <a href="{{ route('opac.show', $reserve->biblio_id) }}" target="_blank" class="hover:text-brand-600 transition-colors">
                                                {{ $biblio?->title ?: 'Judul Buku' }}
                                            </a>
                                        </h4>
                                        <div class="text-[10px] text-slate-400 truncate">
                                            {{ $biblio?->author_names }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-6">
                                <div class="font-bold text-slate-900 dark:text-white text-sm">{{ $member?->member_name ?: $reserve->member_id }}</div>
                                <div class="font-mono text-[11px] font-bold text-brand-600 dark:text-sky-400">{{ $reserve->member_id }}</div>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                                    {{ $member?->memberType?->member_type_name ?: 'Mahasiswa' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-6">
                                @if(!empty($member?->member_phone))
                                    <a href="https://wa.me/{{ $cleanPhone }}" target="_blank" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 font-bold text-[11px] transition-colors" title="Hubungi via WhatsApp">
                                        <i data-lucide="phone" class="w-3.5 h-3.5"></i>
                                        <span>{{ $member->member_phone }}</span>
                                    </a>
                                @else
                                    <span class="text-slate-400 text-xs italic">Tanpa No. HP</span>
                                @endif
                                <div class="text-[10px] text-slate-400 mt-1 truncate max-w-[150px]">
                                    {{ $member?->member_email ?: '-' }}
                                </div>
                            </td>
                            <td class="py-3.5 px-6">
                                <div class="font-bold text-slate-800 dark:text-slate-200">
                                    {{ $reserve->reserve_date ? \Carbon\Carbon::parse($reserve->reserve_date)->translatedFormat('d M Y, H:i') : '-' }} WIB
                                </div>
                                <div class="text-[11px] text-amber-600 dark:text-amber-400 font-semibold mt-0.5">
                                    {{ $timeAgo }}
                                </div>
                            </td>
                            <td class="py-3.5 px-6">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 dark:bg-amber-950/80 dark:text-amber-300">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                    Menunggu Diambil
                                </span>
                            </td>
                            <td class="py-3.5 px-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <!-- 1-Click Loan Button -->
                                    <form action="{{ route('admin.circulation.loan') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="member_id" value="{{ $reserve->member_id }}">
                                        <input type="hidden" name="item_code" value="{{ $reserve->item_code }}">
                                        <button type="submit" class="px-3.5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs shadow-sm transition-all flex items-center gap-1.5 cursor-pointer" title="Langsung proses pinjam buku ini ke anggota">
                                            <i data-lucide="zap" class="w-3.5 h-3.5"></i>
                                            <span>Pinjamkan</span>
                                        </button>
                                    </form>

                                    <!-- Open in Circulation Desk -->
                                    <a href="{{ route('admin.circulation.index', ['member_id' => $reserve->member_id]) }}" class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 transition-colors" title="Buka profil anggota di Meja Sirkulasi">
                                        <i data-lucide="external-link" class="w-4 h-4"></i>
                                    </a>

                                    <!-- Cancel Reservation -->
                                    <form action="{{ route('admin.circulation.reserve.cancel', $reserve->reserve_id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan reservasi ini?');">
                                        @csrf
                                        <button type="submit" class="p-2 rounded-xl text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/60 transition-colors cursor-pointer" title="Batalkan reservasi">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mx-auto mb-3 text-slate-400">
                                    <i data-lucide="bookmark-x" class="w-8 h-8"></i>
                                </div>
                                <h4 class="font-bold text-sm text-slate-800 dark:text-slate-200">Tidak Ada Antrean Reservasi Buku</h4>
                                <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                                    Saat ini belum ada member atau mahasiswa yang memesan/booking buku melalui katalog online.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($reserves->hasPages())
            <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                {{ $reserves->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
