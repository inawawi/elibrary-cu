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

                <form action="{{ route('opac.guestbook') }}" method="POST" class="space-y-4" x-data="{ keperluan: '{{ old('keperluan', 'Membaca') }}' }">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">NIM / Nomor Anggota (Opsional)</label>
                        <input type="text"
                            name="id_anggota"
                            value="{{ old('id_anggota') }}"
                            placeholder="Contoh: 12220001"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-xs focus:outline-none focus:ring-2 focus:ring-brand-500 font-medium">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Nama Lengkap *</label>
                        <input type="text"
                            name="nama"
                            value="{{ old('nama') }}"
                            required
                            placeholder="Masukkan nama lengkap Anda..."
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-xs focus:outline-none focus:ring-2 focus:ring-brand-500 font-medium">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Status Pengunjung *</label>
                            <select name="status" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-xs focus:outline-none focus:ring-2 focus:ring-brand-500 font-medium">
                                <option value="Anggota" {{ old('status') === 'Anggota' ? 'selected' : '' }}>Mahasiswa / Sivitas Anggota</option>
                                <option value="Non Anggota" {{ old('status') === 'Non Anggota' ? 'selected' : '' }}>Pengunjung Umum / Tamu Luar</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Tujuan Kunjungan *</label>
                            <select name="tujuan" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-xs focus:outline-none focus:ring-2 focus:ring-brand-500 font-semibold text-brand-600 dark:text-sky-400">
                                <option value="Library" {{ old('tujuan') === 'Library' ? 'selected' : '' }}>Library</option>
                                <option value="Student Corner" {{ old('tujuan') === 'Student Corner' ? 'selected' : '' }}>Student Corner</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Program Studi *</label>
                        <select name="prodi" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-xs focus:outline-none focus:ring-2 focus:ring-brand-500 font-medium">
                            <option value="">-- Pilih Program Studi --</option>
                            <option value="Bisnis Digital" {{ old('prodi') === 'Bisnis Digital' ? 'selected' : '' }}>S1 - Bisnis Digital</option>
                            <option value="Kewirausahaan" {{ old('prodi') === 'Kewirausahaan' ? 'selected' : '' }}>S1 - Kewirausahaan</option>
                            <option value="Sistem dan Teknologi Informasi" {{ old('prodi') === 'Sistem dan Teknologi Informasi' ? 'selected' : '' }}>S1 - Sistem dan Teknologi Informasi</option>
                            <option value="Sistem Informasi" {{ old('prodi') === 'Sistem Informasi' ? 'selected' : '' }}>S1 - Sistem Informasi</option>
                            <option value="Teknologi Informasi" {{ old('prodi') === 'Teknologi Informasi' ? 'selected' : '' }}>S1 - Teknologi Informasi</option>
                            <option value="Dosen / Karyawan / Umum" {{ old('prodi') === 'Dosen / Karyawan / Umum' ? 'selected' : '' }}>Dosen / Karyawan / Umum</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Keperluan Kunjungan *</label>
                        <select name="keperluan" x-model="keperluan" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-xs focus:outline-none focus:ring-2 focus:ring-brand-500 font-medium">
                            <option value="Membaca">Membaca</option>
                            <option value="Referensi Skripsi">Referensi Skripsi</option>
                            <option value="Podcast">Podcast</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>

                    <div x-show="keperluan === 'Lainnya'" x-cloak>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Keterangan Keperluan Lainnya *</label>
                        <input type="text"
                            name="keperluan_lainnya"
                            value="{{ old('keperluan_lainnya') }}"
                            placeholder="Tuliskan keperluan Anda..."
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-xs focus:outline-none focus:ring-2 focus:ring-brand-500 font-medium">
                    </div>

                    <button type="submit" class="w-full py-3.5 px-6 rounded-xl bg-gradient-to-r from-brand-600 to-sky-600 hover:from-brand-700 hover:to-sky-700 text-white font-bold text-xs shadow-md shadow-brand-500/25 transition-all">
                        Simpan Kunjungan Saya
                    </button>
                </form>
            </div>
        </div>

        <!-- Recent Visitors (Balanced Height & Scrollable) -->
        <div class="md:col-span-6 flex flex-col">
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 sm:p-8 shadow-sm flex flex-col h-full">
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100 dark:border-slate-800">
                    <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i data-lucide="clock" class="w-5 h-5 text-indigo-500"></i>
                        <span>Pengunjung Terakhir</span>
                    </h2>
                    <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-500">
                        {{ $recentGuests->count() }} Kunjungan Terbaru
                    </span>
                </div>

                <!-- Scrollable Visitor Stream (Matches height of left form) -->
                <div class="space-y-3 overflow-y-auto max-h-[480px] pr-2 scrollbar-thin">
                    @forelse($recentGuests as $guest)
                    <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs hover:border-slate-300 dark:hover:border-slate-700 transition-colors">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-9 h-9 rounded-xl bg-brand-100 dark:bg-sky-950 text-brand-700 dark:text-sky-300 flex items-center justify-center font-bold flex-shrink-0">
                                {{ strtoupper(substr($guest->nama, 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <div class="font-bold text-slate-900 dark:text-white text-sm truncate">{{ $guest->nama }}</div>
                                <div class="text-slate-400 flex items-center gap-1.5 flex-wrap mt-0.5">
                                    <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $guest->keperluan }}</span>
                                    @if($guest->tujuan)
                                    <span class="px-2 py-0.5 rounded-md bg-brand-50 dark:bg-sky-950/80 text-brand-600 dark:text-sky-300 text-[10px] font-bold">{{ $guest->tujuan }}</span>
                                    @endif
                                    @if($guest->prodi)
                                    <span class="px-2 py-0.5 rounded-md bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 text-[10px]">{{ $guest->prodi }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="text-right text-slate-400 flex-shrink-0 ml-3">
                            <div>{{ \Carbon\Carbon::parse($guest->tgl)->format('d/m/Y') }}</div>
                            <div class="font-mono text-[11px]">{{ substr($guest->jam, 0, 5) }} WIB</div>
                        </div>
                    </div>
                    @empty
                    <div class="p-8 text-center text-slate-400 text-xs">
                        Belum ada riwayat pengunjung hari ini.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection