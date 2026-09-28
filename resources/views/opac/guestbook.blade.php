@extends('layouts.opac')

@section('title', 'Buku Tamu Kunjungan')

@section('content')
<div class="bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 py-10">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="w-12 h-12 mx-auto mb-3 rounded-2xl bg-brand-50 dark:bg-sky-950 flex items-center justify-center text-brand-600 dark:text-sky-400">
            <i data-lucide="clipboard-pen" class="w-6 h-6"></i>
        </div>
        <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white mb-2">Buku Tamu Pengunjung Perpustakaan</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400">Catat kunjungan Anda setiap kali memasuki perpustakaan digital Universitas Siber Indonesia</p>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="grid grid-cols-1 md:grid-cols-12 gap-8">
        <!-- Form -->
        <div class="md:col-span-6">
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 sm:p-8 shadow-sm">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-5 flex items-center gap-2">
                    <i data-lucide="user-check" class="w-5 h-5 text-brand-500"></i>
                    Formulir Kunjungan
                </h2>

                <form action="{{ route('opac.guestbook') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">NIM / Nomor Anggota (Opsional)</label>
                        <input type="text"
                               name="id_anggota"
                               value="{{ old('id_anggota') }}"
                               placeholder="Contoh: 12220001"
                               class="w-full px-4 py-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 font-medium">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Nama Lengkap *</label>
                        <input type="text"
                               name="nama"
                               value="{{ old('nama') }}"
                               required
                               placeholder="Masukkan nama lengkap Anda..."
                               class="w-full px-4 py-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 font-medium">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Status Pengunjung *</label>
                        <select name="status" required class="w-full px-4 py-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 font-medium">
                            <option value="Anggota" {{ old('status') === 'Anggota' ? 'selected' : '' }}>Mahasiswa / Sivitas Anggota</option>
                            <option value="Non Anggota" {{ old('status') === 'Non Anggota' ? 'selected' : '' }}>Pengunjung Umum / Tamu Luar</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Keperluan Kunjungan *</label>
                        <input type="text"
                               name="keperluan"
                               value="{{ old('keperluan') }}"
                               required
                               placeholder="Contoh: Membaca, Referensi Skripsi, Pinjam Buku..."
                               class="w-full px-4 py-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 font-medium">
                    </div>

                    <button type="submit" class="w-full py-3.5 px-6 rounded-xl bg-gradient-to-r from-brand-600 to-sky-600 hover:from-brand-700 hover:to-sky-700 text-white font-bold text-sm shadow-md shadow-brand-500/25 transition-all">
                        Simpan Kunjungan Saya
                    </button>
                </form>
            </div>
        </div>

        <!-- Recent Visitors -->
        <div class="md:col-span-6">
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 sm:p-8 shadow-sm">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-5 flex items-center justify-between">
                    <span class="flex items-center gap-2">
                        <i data-lucide="clock" class="w-5 h-5 text-indigo-500"></i>
                        Pengunjung Terakhir
                    </span>
                    <span class="text-xs font-normal text-slate-400">Hari ini & terbaru</span>
                </h2>

                <div class="space-y-3">
                    @forelse($recentGuests as $guest)
                        <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-brand-100 dark:bg-sky-950 text-brand-700 dark:text-sky-300 flex items-center justify-center font-bold">
                                    {{ strtoupper(substr($guest->nama, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="font-bold text-slate-900 dark:text-white text-sm">{{ $guest->nama }}</div>
                                    <div class="text-slate-400">{{ $guest->keperluan }} • <span class="font-semibold text-brand-600 dark:text-sky-400">{{ $guest->status }}</span></div>
                                </div>
                            </div>
                            <div class="text-right text-slate-400">
                                <div>{{ \Carbon\Carbon::parse($guest->tgl)->format('d/m/Y') }}</div>
                                <div class="font-mono text-[11px]">{{ substr($guest->jam, 0, 5) }} WIB</div>
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-slate-400 text-sm">
                            Belum ada riwayat pengunjung.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
