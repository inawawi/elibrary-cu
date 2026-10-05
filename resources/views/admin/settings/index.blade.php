@extends('layouts.admin')

@section('title', 'Pengaturan Perpustakaan & Aturan Keanggotaan')

@section('content')
<div class="space-y-8" x-data="{ activeTab: 'rules', newTypeModal: false, newSlideModal: false, newNewsModal: false }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white flex items-center gap-2.5">
                <i data-lucide="sliders" class="w-7 h-7 text-brand-500"></i>
                Pengaturan Perpustakaan
            </h1>
            <p class="text-xs text-slate-500 mt-1">Konfigurasi aturan peminjaman, denda, kuota keanggotaan, gambar slide hero, berita publikasi, dan informasi berkala</p>
        </div>

        <!-- Tab Switcher -->
        <div class="flex items-center flex-wrap gap-1.5 p-1 bg-slate-200/80 dark:bg-slate-800 rounded-2xl">
            <button @click="activeTab = 'rules'" :class="activeTab === 'rules' ? 'bg-white dark:bg-slate-700 text-brand-600 dark:text-sky-300 shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900'" class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5">
                <i data-lucide="scale" class="w-4 h-4"></i>
                <span>Aturan & Denda</span>
            </button>
            <button @click="activeTab = 'slides'" :class="activeTab === 'slides' ? 'bg-white dark:bg-slate-700 text-brand-600 dark:text-sky-300 shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900'" class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5">
                <i data-lucide="image" class="w-4 h-4"></i>
                <span>Slide Hero</span>
            </button>
            <button @click="activeTab = 'news'" :class="activeTab === 'news' ? 'bg-white dark:bg-slate-700 text-brand-600 dark:text-sky-300 shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900'" class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5">
                <i data-lucide="newspaper" class="w-4 h-4"></i>
                <span>Kelola Berita</span>
            </button>
            <button @click="activeTab = 'announcement'" :class="activeTab === 'announcement' ? 'bg-white dark:bg-slate-700 text-brand-600 dark:text-sky-300 shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900'" class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5">
                <i data-lucide="megaphone" class="w-4 h-4"></i>
                <span>Pengumuman</span>
            </button>
            <button @click="activeTab = 'general'" :class="activeTab === 'general' ? 'bg-white dark:bg-slate-700 text-brand-600 dark:text-sky-300 shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900'" class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5">
                <i data-lucide="info" class="w-4 h-4"></i>
                <span>Tata Tertib</span>
            </button>
        </div>
    </div>

    <!-- TAB 1: ATURAN & DENDA PER TIPE KEANGGOTAAN -->
    <div x-show="activeTab === 'rules'" class="space-y-6" x-cloak>
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-slate-900 dark:text-white">Aturan Peminjaman & Tarif Denda</h2>
                <p class="text-xs text-slate-500">Kebijakan batas pinjam, durasi peminjaman, dan denda keterlambatan per jenis keanggotaan</p>
            </div>
            <button @click="newTypeModal = true" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold shadow-md shadow-brand-500/20 transition-all">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                <span>Tambah Tipe Anggota</span>
            </button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @foreach($memberTypes as $type)
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm hover:border-brand-300 dark:hover:border-brand-800 transition-all">
                    <form action="{{ route('admin.settings.member-type.update', $type->member_type_id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Card Header -->
                        <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800 mb-5">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-brand-50 dark:bg-sky-950 text-brand-600 dark:text-sky-400 flex items-center justify-center font-bold">
                                    <i data-lucide="badge-percent" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <h3 class="font-bold text-base text-slate-900 dark:text-white">{{ $type->member_type_name }}</h3>
                                    <span class="text-[11px] text-slate-400 font-medium">{{ $type->members_count }} Anggota Terdaftar</span>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                                ID: #{{ $type->member_type_id }}
                            </span>
                        </div>

                        <!-- Form Fields Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                            <div class="sm:col-span-2">
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Tipe Keanggotaan</label>
                                <input type="text" name="member_type_name" value="{{ $type->member_type_name }}" required
                                    class="w-full px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 font-semibold focus:ring-2 focus:ring-brand-500">
                            </div>

                            <!-- Batas Pinjam Buku -->
                            <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800">
                                <label class="block font-bold text-slate-800 dark:text-slate-200 mb-1 flex items-center gap-1.5">
                                    <i data-lucide="book-copy" class="w-3.5 h-3.5 text-brand-500"></i>
                                    Batas Pinjam Buku
                                </label>
                                <div class="flex items-center gap-2">
                                    <input type="number" min="1" max="50" name="loan_limit" value="{{ $type->loan_limit }}" required
                                        class="w-full px-3 py-1.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 font-bold text-slate-900 dark:text-white">
                                    <span class="text-slate-400 font-medium text-[11px]">Buku</span>
                                </div>
                                <span class="text-[10px] text-slate-400 mt-1 block">Maksimal pinjaman aktif</span>
                            </div>

                            <!-- Lama Pinjam -->
                            <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800">
                                <label class="block font-bold text-slate-800 dark:text-slate-200 mb-1 flex items-center gap-1.5">
                                    <i data-lucide="calendar" class="w-3.5 h-3.5 text-brand-500"></i>
                                    Durasi Pinjam
                                </label>
                                <div class="flex items-center gap-2">
                                    <input type="number" min="1" max="365" name="loan_periode" value="{{ $type->loan_periode }}" required
                                        class="w-full px-3 py-1.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 font-bold text-slate-900 dark:text-white">
                                    <span class="text-slate-400 font-medium text-[11px]">Hari</span>
                                </div>
                                <span class="text-[10px] text-slate-400 mt-1 block">Waktu sebelum jatuh tempo</span>
                            </div>

                            <!-- Denda Keterlambatan -->
                            <div class="p-3.5 rounded-2xl bg-rose-50/60 dark:bg-rose-950/20 border border-rose-100 dark:border-rose-900/40">
                                <label class="block font-bold text-rose-800 dark:text-rose-300 mb-1 flex items-center gap-1.5">
                                    <i data-lucide="coins" class="w-3.5 h-3.5 text-rose-500"></i>
                                    Tarif Denda / Hari
                                </label>
                                <div class="flex items-center gap-2">
                                    <span class="text-slate-500 font-bold text-[11px]">Rp</span>
                                    <input type="number" min="0" step="500" name="fine_each_day" value="{{ $type->fine_each_day }}" required
                                        class="w-full px-3 py-1.5 rounded-xl bg-white dark:bg-slate-900 border border-rose-200 dark:border-rose-800/80 font-bold text-rose-600 dark:text-rose-400">
                                </div>
                                <span class="text-[10px] text-rose-600/70 dark:text-rose-400/60 mt-1 block">Per hari per buku terlambat</span>
                            </div>

                            <!-- Masa Toleransi (Grace Period) -->
                            <div class="p-3.5 rounded-2xl bg-amber-50/60 dark:bg-amber-950/20 border border-amber-100 dark:border-amber-900/40">
                                <label class="block font-bold text-amber-800 dark:text-amber-300 mb-1 flex items-center gap-1.5">
                                    <i data-lucide="clock" class="w-3.5 h-3.5 text-amber-500"></i>
                                    Masa Toleransi Denda
                                </label>
                                <div class="flex items-center gap-2">
                                    <input type="number" min="0" max="30" name="grace_periode" value="{{ $type->grace_periode }}" required
                                        class="w-full px-3 py-1.5 rounded-xl bg-white dark:bg-slate-900 border border-amber-200 dark:border-amber-800/80 font-bold text-amber-600 dark:text-amber-400">
                                    <span class="text-slate-400 font-medium text-[11px]">Hari</span>
                                </div>
                                <span class="text-[10px] text-amber-700/70 dark:text-amber-400/60 mt-1 block">Toleransi sebelum denda berlaku</span>
                            </div>

                            <!-- Batas Perpanjangan Pinjaman -->
                            <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800">
                                <label class="block font-bold text-slate-800 dark:text-slate-200 mb-1 flex items-center gap-1.5">
                                    <i data-lucide="refresh-cw" class="w-3.5 h-3.5 text-brand-500"></i>
                                    Maks. Perpanjangan
                                </label>
                                <div class="flex items-center gap-2">
                                    <input type="number" min="0" max="10" name="reborrow_limit" value="{{ $type->reborrow_limit }}" required
                                        class="w-full px-3 py-1.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 font-bold text-slate-900 dark:text-white">
                                    <span class="text-slate-400 font-medium text-[11px]">Kali</span>
                                </div>
                                <span class="text-[10px] text-slate-400 mt-1 block">Batas re-borrow per judul</span>
                            </div>

                            <!-- Masa Berlaku Keanggotaan -->
                            <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800">
                                <label class="block font-bold text-slate-800 dark:text-slate-200 mb-1 flex items-center gap-1.5">
                                    <i data-lucide="id-card" class="w-3.5 h-3.5 text-brand-500"></i>
                                    Masa Berlaku Kartu
                                </label>
                                <div class="flex items-center gap-2">
                                    <input type="number" min="1" max="3650" name="member_periode" value="{{ $type->member_periode }}" required
                                        class="w-full px-3 py-1.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 font-bold text-slate-900 dark:text-white">
                                    <span class="text-slate-400 font-medium text-[11px]">Hari</span>
                                </div>
                                <span class="text-[10px] text-slate-400 mt-1 block">Masa aktif kartu (contoh 1460 hari = 4 th)</span>
                            </div>
                        </div>

                        <!-- Card Action -->
                        <div class="mt-5 pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                            <span class="text-[10px] text-slate-400">Pembaruan terakhir: {{ $type->last_update ?: '-' }}</span>
                            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs shadow-sm transition-colors">
                                <i data-lucide="save" class="w-3.5 h-3.5"></i>
                                <span>Simpan Aturan {{ $type->member_type_name }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            @endforeach
        </div>
    </div>

    <!-- TAB 2: INFORMASI / PENGUMUMAN BERKALA ANGGOTA -->
    <div x-show="activeTab === 'announcement'" class="space-y-6" x-cloak>
        <div>
            <h2 class="text-base font-bold text-slate-900 dark:text-white">Pengumuman & Informasi Berkala Anggota</h2>
            <p class="text-xs text-slate-500">Informasi ini akan muncul di dasbor setiap anggota saat mereka login ke portal perpustakaan</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Edit Form -->
            <div class="lg:col-span-7 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 sm:p-8 shadow-sm">
                <form action="{{ route('admin.settings.announcement.update') }}" method="POST" class="space-y-5">
                    @csrf

                    <!-- Status Banner -->
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800 flex items-center justify-between">
                        <div>
                            <div class="font-bold text-sm text-slate-900 dark:text-white">Tampilkan Pengumuman di Dasbor Anggota</div>
                            <div class="text-xs text-slate-500">Jika dinonaktifkan, pengumuman tidak akan terlihat oleh anggota.</div>
                        </div>
                        <select name="is_active" class="px-4 py-2 rounded-xl text-xs font-bold border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900">
                            <option value="1" {{ ($announcement['is_active'] ?? 1) == 1 ? 'selected' : '' }}>Aktif (Tampilkan)</option>
                            <option value="0" {{ ($announcement['is_active'] ?? 1) == 0 ? 'selected' : '' }}>Non-Aktif (Sembunyikan)</option>
                        </select>
                    </div>

                    <!-- Tipe Pengumuman -->
                    <div>
                        <label class="block font-bold text-xs text-slate-700 dark:text-slate-300 mb-2">Jenis Pesan / Level Urgensi</label>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                            <label class="flex items-center gap-2.5 p-3 rounded-2xl border border-sky-200 dark:border-sky-900 bg-sky-50/60 dark:bg-sky-950/40 cursor-pointer hover:border-sky-400">
                                <input type="radio" name="type" value="info" {{ ($announcement['type'] ?? 'info') === 'info' ? 'checked' : '' }} class="text-sky-600">
                                <span class="font-bold text-sky-800 dark:text-sky-300">Informasi</span>
                            </label>
                            <label class="flex items-center gap-2.5 p-3 rounded-2xl border border-amber-200 dark:border-amber-900 bg-amber-50/60 dark:bg-amber-950/40 cursor-pointer hover:border-amber-400">
                                <input type="radio" name="type" value="warning" {{ ($announcement['type'] ?? '') === 'warning' ? 'checked' : '' }} class="text-amber-600">
                                <span class="font-bold text-amber-800 dark:text-amber-300">Peringatan</span>
                            </label>
                            <label class="flex items-center gap-2.5 p-3 rounded-2xl border border-rose-200 dark:border-rose-900 bg-rose-50/60 dark:bg-rose-950/40 cursor-pointer hover:border-rose-400">
                                <input type="radio" name="type" value="danger" {{ ($announcement['type'] ?? '') === 'danger' ? 'checked' : '' }} class="text-rose-600">
                                <span class="font-bold text-rose-800 dark:text-rose-300">Penting / Kritis</span>
                            </label>
                            <label class="flex items-center gap-2.5 p-3 rounded-2xl border border-emerald-200 dark:border-emerald-900 bg-emerald-50/60 dark:bg-emerald-950/40 cursor-pointer hover:border-emerald-400">
                                <input type="radio" name="type" value="success" {{ ($announcement['type'] ?? '') === 'success' ? 'checked' : '' }} class="text-emerald-600">
                                <span class="font-bold text-emerald-800 dark:text-emerald-300">Kabar Baik</span>
                            </label>
                        </div>
                    </div>

                    <!-- Judul -->
                    <div>
                        <label class="block font-bold text-xs text-slate-700 dark:text-slate-300 mb-1">Judul Pengumuman</label>
                        <input type="text" name="title" value="{{ $announcement['title'] ?? '' }}" required
                            placeholder="Contoh: Jadwal Pengembalian Buku Menjelang Ujian Akhir Semester"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm font-bold focus:ring-2 focus:ring-brand-500">
                    </div>

                    <!-- Isi Pesan -->
                    <div>
                        <label class="block font-bold text-xs text-slate-700 dark:text-slate-300 mb-1">Isi Pesan / Instruksi untuk Anggota</label>
                        <textarea name="content" rows="4" required
                            placeholder="Tuliskan informasi penting, pengingat pengembalian, perpanjangan, atau jam operasional perpustakaan..."
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-2 focus:ring-brand-500 leading-relaxed">{{ $announcement['content'] ?? '' }}</textarea>
                    </div>

                    <!-- Jadwal Tampil (Opsional) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-500 mb-1">Mulai Ditampilkan (Opsional)</label>
                            <input type="date" name="start_date" value="{{ $announcement['start_date'] ?? '' }}"
                                class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-500 mb-1">Berakhir Pada (Opsional)</label>
                            <input type="date" name="end_date" value="{{ $announcement['end_date'] ?? '' }}"
                                class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs">
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex justify-end">
                        <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs shadow-md transition-colors">
                            <i data-lucide="send" class="w-4 h-4"></i>
                            <span>Simpan & Publikasikan Pengumuman</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Live Preview -->
            <div class="lg:col-span-5 space-y-4">
                <div class="font-bold text-xs uppercase tracking-wider text-slate-400 flex items-center gap-2">
                    <i data-lucide="eye" class="w-4 h-4 text-brand-500"></i>
                    Pratinjau Tampilan di Dasbor Anggota
                </div>

                @php
                    $pType = $announcement['type'] ?? 'info';
                    $colorClasses = match($pType) {
                        'warning' => 'bg-amber-500/10 border-amber-400/40 text-amber-900 dark:text-amber-200',
                        'danger' => 'bg-rose-500/10 border-rose-400/40 text-rose-900 dark:text-rose-200',
                        'success' => 'bg-emerald-500/10 border-emerald-400/40 text-emerald-900 dark:text-emerald-200',
                        default => 'bg-sky-500/10 border-sky-400/40 text-sky-900 dark:text-sky-200',
                    };
                    $iconName = match($pType) {
                        'warning' => 'alert-triangle',
                        'danger' => 'alert-circle',
                        'success' => 'check-circle-2',
                        default => 'megaphone',
                    };
                @endphp

                <div class="rounded-3xl border p-5 shadow-sm relative overflow-hidden {{ $colorClasses }}">
                    <div class="flex items-start gap-4">
                        <div class="p-2.5 rounded-2xl bg-white/60 dark:bg-slate-900/60 shadow-sm flex-shrink-0">
                            <i data-lucide="{{ $iconName }}" class="w-5 h-5"></i>
                        </div>
                        <div class="flex-grow min-w-0">
                            <div class="flex items-center justify-between gap-2 mb-1">
                                <h4 class="font-black text-sm tracking-tight leading-snug">{{ $announcement['title'] ?? 'Belum ada judul' }}</h4>
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-white/70 dark:bg-slate-800/80 uppercase">
                                    {{ $announcement['type'] ?? 'info' }}
                                </span>
                            </div>
                            <p class="text-xs leading-relaxed opacity-90">{{ $announcement['content'] ?? 'Belum ada konten pengumuman yang disimpan.' }}</p>
                            <div class="mt-3 flex items-center justify-between text-[10px] opacity-75 pt-2 border-t border-current/10">
                                <span>Pemberitahuan Resmi Perpustakaan</span>
                                <span>{{ $announcement['updated_at'] ?? 'Hari ini' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-4 rounded-2xl bg-slate-100/80 dark:bg-slate-800/50 border border-slate-200/60 dark:border-slate-800 text-xs text-slate-500 space-y-1">
                    <div class="font-semibold text-slate-700 dark:text-slate-300">Catatan Penggunaan:</div>
                    <ul class="list-disc list-inside space-y-0.5 text-[11px]">
                        <li>Pengumuman ini tampil di bagian paling atas saat mahasiswa atau dosen membuka akun mandiri mereka.</li>
                        <li>Gunakan level <strong>Penting/Kritis</strong> untuk denda massal atau tutup layanan.</li>
                        <li>Gunakan level <strong>Informasi</strong> untuk jam buka perpustakaan atau penambahan koleksi baru.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 3: TATA TERTIB & IDENTITAS PERPUSTAKAAN -->
    <div x-show="activeTab === 'general'" class="space-y-6" x-cloak>
        <div>
            <h2 class="text-base font-bold text-slate-900 dark:text-white">Tata Tertib & Identitas Perpustakaan</h2>
            <p class="text-xs text-slate-500">Aturan umum perpustakaan yang menjadi pedoman seluruh anggota perpustakaan</p>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 sm:p-8 shadow-sm max-w-4xl">
            <form action="{{ route('admin.settings.general.update') }}" method="POST" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-xs text-slate-700 dark:text-slate-300 mb-1">Nama Resmi Perpustakaan</label>
                        <input type="text" name="library_name" value="{{ $generalSettings['library_name'] }}" required
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold focus:ring-2 focus:ring-brand-500">
                    </div>
                    <div>
                        <label class="block font-bold text-xs text-slate-700 dark:text-slate-300 mb-1">Nama Institusi / Perguruan Tinggi</label>
                        <input type="text" name="library_subname" value="{{ $generalSettings['library_subname'] }}"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold focus:ring-2 focus:ring-brand-500">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-xs text-slate-700 dark:text-slate-300 mb-1">
                        Tata Tertib Perpustakaan (Aturan Umum & Ketentuan Keanggotaan)
                    </label>
                    <p class="text-[11px] text-slate-400 mb-2">Tuliskan aturan umum perpustakaan, hak & kewajiban anggota, serta sanksi kerusakan/kehilangan koleksi.</p>
                    <textarea name="library_rules" rows="8"
                        class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs focus:ring-2 focus:ring-brand-500 leading-relaxed font-mono">{{ $libraryRules }}</textarea>
                </div>

                <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex justify-end">
                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs shadow-md transition-colors">
                        <i data-lucide="check" class="w-4 h-4"></i>
                        <span>Simpan Perubahan Tata Tertib</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- TAB 4: PENGATURAN SLIDE GAMBAR HERO -->
    <div x-show="activeTab === 'slides'" class="space-y-6" x-cloak>
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-base font-bold text-slate-900 dark:text-white">Slide Gambar Background Dashboard</h2>
                <p class="text-xs text-slate-500">Kelola foto pemandangan kampus, fasilitas, judul, dan status aktif slide hero utama</p>
            </div>
            <button @click="newSlideModal = true" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold shadow-md shadow-brand-500/20 transition-all flex-shrink-0">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                <span>+ Tambah Slide Baru</span>
            </button>
        </div>

        <!-- Panduan Informasi Penting Dimensi & Aspek Rasio Slider -->
        <div class="p-4 sm:p-5 rounded-2xl bg-sky-50 dark:bg-sky-950/40 border border-sky-200 dark:border-sky-800 text-sky-950 dark:text-sky-200 text-xs shadow-xs">
            <div class="flex items-start gap-3.5">
                <div class="p-2.5 rounded-xl bg-sky-600 text-white flex-shrink-0 mt-0.5 shadow-sm">
                    <i data-lucide="info" class="w-5 h-5"></i>
                </div>
                <div class="space-y-3 w-full">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-sky-200/60 dark:border-sky-800/60 pb-2">
                        <div>
                            <h4 class="font-bold text-sky-900 dark:text-sky-100 text-sm">
                                Panduan Rekomendasi Format & Aspek Rasio Gambar Slider Beranda
                            </h4>
                            <p class="text-[11px] text-sky-700 dark:text-sky-300 mt-0.5">Gunakan panduan rasio berikut agar foto slider tampil tajam, presisi, dan tidak terpotong bagian pentingnya.</p>
                        </div>
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <span class="text-[10px] font-bold px-2.5 py-1 rounded-full bg-sky-200 dark:bg-sky-900 text-sky-800 dark:text-sky-200 font-mono">
                                Utama: 16:9 Landscape
                            </span>
                            <span class="text-[10px] font-bold px-2.5 py-1 rounded-full bg-indigo-100 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300 font-mono border border-indigo-200 dark:border-indigo-800">
                                Banner: 21:9 Ultra-Wide
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 pt-0.5 text-[11px] text-sky-800 dark:text-sky-300">
                        <div class="bg-white/90 dark:bg-slate-900/70 p-3 rounded-xl border border-sky-100 dark:border-sky-900 shadow-xs">
                            <span class="font-bold block text-slate-800 dark:text-slate-100 mb-1 text-xs flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-sky-500"></span>
                                📐 Rekomendasi Aspek Rasio
                            </span>
                            <ul class="space-y-1 text-slate-600 dark:text-slate-300">
                                <li>• <b>16:9 (Landscape Standar):</b> <b>1920 × 1080 px</b> (Full HD) atau <b>2560 × 1440 px</b> (2K). Sangat ideal untuk foto dokumentasi & kampus.</li>
                                <li>• <b>21:9 (Ultra-Wide Banner):</b> <b>1920 × 820 px</b> atau <b>2560 × 1080 px</b> jika ingin format banner memanjang sinematik.</li>
                            </ul>
                        </div>
                        <div class="bg-white/90 dark:bg-slate-900/70 p-3 rounded-xl border border-sky-100 dark:border-sky-900 shadow-xs">
                            <span class="font-bold block text-slate-800 dark:text-slate-100 mb-1 text-xs flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                                🎯 Fokus Objek / Safe Zone
                            </span>
                            <span class="text-slate-600 dark:text-slate-300 leading-relaxed block">
                                Posisikan objek utama (wajah, kepala orang, teks logo) di <b>area atas hingga tengah (top 60%)</b>. Sistem otomatis memprioritaskan pemotongan dari sisi bawah agar kepala tidak terpotong.
                            </span>
                        </div>
                        <div class="bg-white/90 dark:bg-slate-900/70 p-3 rounded-xl border border-sky-100 dark:border-sky-900 shadow-xs">
                            <span class="font-bold block text-slate-800 dark:text-slate-100 mb-1 text-xs flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                📁 Format File & Ukuran
                            </span>
                            <span class="text-slate-600 dark:text-slate-300 leading-relaxed block">
                                Ekstensi: <b>JPG, JPEG, PNG, WEBP</b>.<br>
                                Ukuran maksimal: <b>5 MB</b> (disarankan di kisaran <b>500 KB – 1.5 MB</b> agar proses loading halaman cepat dan ringan bagi pengunjung).
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @foreach($heroSlides as $slide)
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm hover:border-brand-300 dark:hover:border-sky-700 transition-all flex flex-col justify-between">
                    <div>
                        <!-- Slide Image Preview -->
                        <div class="relative aspect-[16/9] rounded-2xl overflow-hidden bg-slate-950 mb-4 border border-slate-200 dark:border-slate-800">
                            <img src="{{ asset($slide['image']) }}" alt="{{ $slide['title'] }}" class="w-full h-full object-cover">
                            <div class="absolute top-3 left-3 flex items-center gap-2">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-brand-600/90 text-white backdrop-blur shadow-sm">
                                    {{ $slide['tag'] }}
                                </span>
                            </div>
                            <div class="absolute top-3 right-3">
                                @if($slide['is_active'])
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500 text-white backdrop-blur shadow-sm flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                                        Aktif
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-500/90 text-white backdrop-blur shadow-sm">
                                        Nonaktif
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Edit Form -->
                        <form action="{{ route('admin.settings.slides.update', $slide['id']) }}" method="POST" enctype="multipart/form-data" class="space-y-3 text-xs">
                            @csrf
                            <div>
                                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Tag / Label Kategori *</label>
                                <input type="text" name="tag" value="{{ $slide['tag'] }}" required
                                    class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 font-semibold focus:ring-2 focus:ring-brand-500 text-slate-900 dark:text-white">
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Judul Utama Slide *</label>
                                <input type="text" name="title" value="{{ $slide['title'] }}" required
                                    class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 font-semibold focus:ring-2 focus:ring-brand-500 text-slate-900 dark:text-white">
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Keterangan / Deskripsi *</label>
                                <textarea name="desc" rows="2" required
                                    class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 font-normal focus:ring-2 focus:ring-brand-500 text-slate-900 dark:text-white">{{ $slide['desc'] }}</textarea>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 items-center">
                                <div>
                                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Status Tampil</label>
                                    <select name="is_active" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 font-semibold text-slate-900 dark:text-white">
                                        <option value="1" {{ $slide['is_active'] ? 'selected' : '' }}>Aktif (Ditampilkan)</option>
                                        <option value="0" {{ !$slide['is_active'] ? 'selected' : '' }}>Nonaktif (Disembunyikan)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1 flex items-center justify-between">
                                        <span>Ganti Foto (Opsional)</span>
                                        <span class="text-[10px] font-bold text-brand-600 dark:text-sky-400">16:9 / 21:9</span>
                                    </label>
                                    <input type="file" name="image" accept="image/*" class="w-full text-[11px] text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-xl file:border-0 file:text-[11px] file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100">
                                    <p class="text-[10px] text-slate-400 mt-1">
                                        Rekomendasi rasio: <b>16:9</b> (1920×1080 px) atau <b>21:9</b> (1920×820 px).
                                    </p>
                                </div>
                            </div>

                            <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs shadow-sm transition-colors">
                                    <i data-lucide="check" class="w-3.5 h-3.5"></i>
                                    <span>Simpan Perubahan</span>
                                </button>
                        </form>

                        @if(count($heroSlides) > 1)
                            <form action="{{ route('admin.settings.slides.delete', $slide['id']) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus slide ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center gap-1 px-3 py-2 rounded-xl text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 text-xs font-semibold transition-colors">
                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                    <span>Hapus</span>
                                </button>
                            </form>
                        @endif
                            </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- TAB 5: KELOLA BERITA & PUBLIKASI -->
    <div x-show="activeTab === 'news'" class="space-y-6" x-cloak>
        <!-- Integrasi Otomatis Berita Portal Nasional -->
        <div class="rounded-3xl border border-emerald-200 dark:border-emerald-800 bg-gradient-to-br from-emerald-50/70 via-white to-teal-50/40 dark:from-emerald-950/30 dark:via-slate-900 dark:to-teal-950/20 p-6 sm:p-7 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-emerald-100 dark:border-emerald-900/60">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-600 text-white flex items-center justify-center shadow-lg shadow-emerald-500/25 flex-shrink-0">
                        <i data-lucide="globe" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-base font-black text-slate-900 dark:text-white">Integrasi Berita Portal Nasional (Live RSS)</h3>
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider {{ $nationalNewsEnabled ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300' : 'bg-slate-200 text-slate-600' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $nationalNewsEnabled ? 'bg-emerald-500 animate-ping' : 'bg-slate-400' }}"></span>
                                {{ $nationalNewsEnabled ? 'Live Terhubung' : 'Nonaktif' }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Otomatis menyinkronkan berita ilmiah, teknologi, pendidikan, dan warta nasional terkini dari media kredibel Indonesia.</p>
                    </div>
                </div>

                <!-- Tombol Sinkronisasi Manual -->
                <form action="{{ route('admin.settings.news.sync') }}" method="POST">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-500/20 transition-all flex-shrink-0">
                        <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                        <span>Sinkronkan Berita Sekarang</span>
                    </button>
                </form>
            </div>

            <!-- Form Konfigurasi Sumber Portal -->
            <form action="{{ route('admin.settings.news.national-config') }}" method="POST" class="pt-5 space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-1">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Status Integrasi</label>
                        <label class="flex items-center gap-2.5 p-3 rounded-2xl bg-white dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 cursor-pointer">
                            <input type="checkbox" name="national_news_enabled" value="1" {{ $nationalNewsEnabled ? 'checked' : '' }} class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                            <span class="text-xs font-semibold text-slate-800 dark:text-slate-200">Aktifkan Berita Portal Nasional</span>
                        </label>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Pilihan Sumber Portal Berita</label>
                        <div class="flex items-center gap-2">
                            <select name="national_news_source" class="w-full px-3.5 py-2.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                                @foreach($portalSources as $key => $src)
                                    <option value="{{ $key }}" {{ $nationalNewsSource === $key ? 'selected' : '' }}>
                                        {{ $src['name'] }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold transition-colors whitespace-nowrap">
                                Simpan Pilihan
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Cuplikan Berita Nasional Terkini yang Terhubung -->
            @if(!empty($latestNationalNews))
                <div class="mt-6 pt-5 border-t border-emerald-100 dark:border-emerald-900/60">
                    <div class="flex items-center justify-between mb-3 text-xs">
                        <span class="font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                            <i data-lucide="radio" class="w-3.5 h-3.5 text-emerald-600 animate-pulse"></i>
                            Cuplikan Berita Nasional Terkini (Hasil Sinkronisasi Terkini):
                        </span>
                        <a href="{{ route('opac.news', ['tab' => 'national']) }}" target="_blank" class="text-emerald-700 dark:text-emerald-400 font-bold hover:underline flex items-center gap-1 text-[11px]">
                            <span>Lihat di Halaman OPAC</span>
                            <i data-lucide="arrow-up-right" class="w-3.5 h-3.5"></i>
                        </a>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach(array_slice($latestNationalNews, 0, 3) as $natItem)
                            <div class="p-3 rounded-2xl bg-white/90 dark:bg-slate-800/90 border border-slate-200 dark:border-slate-700/60 flex flex-col justify-between text-xs">
                                <div>
                                    <div class="flex items-center justify-between text-[10px] text-slate-400 mb-1">
                                        <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ $natItem['source'] }}</span>
                                        <span>{{ $natItem['date'] }}</span>
                                    </div>
                                    <h4 class="font-bold text-slate-900 dark:text-white line-clamp-2 leading-snug">{{ $natItem['title'] }}</h4>
                                </div>
                                <div class="mt-2 pt-2 border-t border-slate-100 dark:border-slate-700/40 flex items-center justify-between">
                                    <span class="text-[10px] text-slate-400">{{ $natItem['category'] }}</span>
                                    <a href="{{ $natItem['url'] }}" target="_blank" class="text-brand-600 dark:text-sky-400 font-bold text-[11px] hover:underline flex items-center gap-1">
                                        <span>Tautan Asli</span>
                                        <i data-lucide="external-link" class="w-3 h-3"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Section: Berita Internal Kampus / Manual -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-4 border-t border-slate-200 dark:border-slate-800">
            <div>
                <h2 class="text-base font-bold text-slate-900 dark:text-white">Berita Internal & Rilis Perpustakaan Cyber University</h2>
                <p class="text-xs text-slate-500">Kelola artikel kegiatan, warta literasi, dan pengumuman resmi internal perpustakaan</p>
            </div>
            <button @click="newNewsModal = true" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold shadow-md shadow-brand-500/20 transition-all flex-shrink-0">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                <span>+ Tambah Berita Internal</span>
            </button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @foreach($newsArticles as $article)
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm hover:border-brand-300 dark:hover:border-sky-700 transition-all flex flex-col justify-between">
                    <div>
                        <!-- Article Thumbnail & Meta -->
                        <div class="relative aspect-[16/9] rounded-2xl overflow-hidden bg-slate-950 mb-4 border border-slate-200 dark:border-slate-800">
                            <img src="{{ asset($article['image']) }}" alt="{{ $article['title'] }}" class="w-full h-full object-cover">
                            <div class="absolute top-3 left-3">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-brand-600/90 text-white backdrop-blur shadow-sm">
                                    {{ $article['category'] }}
                                </span>
                            </div>
                            <div class="absolute top-3 right-3">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-900/80 text-white backdrop-blur shadow-sm">
                                    {{ $article['date'] }}
                                </span>
                            </div>
                        </div>

                        <!-- Edit Form -->
                        <form action="{{ route('admin.settings.news.update', $article['id']) }}" method="POST" enctype="multipart/form-data" class="space-y-3 text-xs">
                            @csrf
                            <div>
                                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Judul Artikel Berita *</label>
                                <input type="text" name="title" value="{{ $article['title'] }}" required
                                    class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 font-semibold focus:ring-2 focus:ring-brand-500 text-slate-900 dark:text-white">
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Kategori *</label>
                                    <input type="text" name="category" value="{{ $article['category'] }}" required
                                    class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 font-semibold focus:ring-2 focus:ring-brand-500 text-slate-900 dark:text-white">
                                </div>
                                <div>
                                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Sumber / Media *</label>
                                    <input type="text" name="source" value="{{ $article['source'] }}" required
                                    class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 font-semibold focus:ring-2 focus:ring-brand-500 text-slate-900 dark:text-white">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Tanggal Publikasi *</label>
                                    <input type="text" name="date" value="{{ $article['date'] }}" required
                                    class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 font-semibold focus:ring-2 focus:ring-brand-500 text-slate-900 dark:text-white">
                                </div>
                                <div>
                                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Tautan / Link Berita *</label>
                                    <input type="url" name="url" value="{{ $article['url'] }}" required
                                    class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 font-semibold focus:ring-2 focus:ring-brand-500 text-slate-900 dark:text-white">
                                </div>
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Ringkasan / Cuplikan *</label>
                                <textarea name="excerpt" rows="2" required
                                    class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 font-normal focus:ring-2 focus:ring-brand-500 text-slate-900 dark:text-white">{{ $article['excerpt'] }}</textarea>
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Ganti Foto Sampul (Opsional)</label>
                                <input type="file" name="image" accept="image/*" class="w-full text-[11px] text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-xl file:border-0 file:text-[11px] file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100">
                            </div>

                            <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs shadow-sm transition-colors">
                                        <i data-lucide="check" class="w-3.5 h-3.5"></i>
                                        <span>Simpan Perubahan</span>
                                    </button>
                                    <a href="{{ $article['url'] }}" target="_blank" class="inline-flex items-center gap-1 px-3 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:text-brand-600 text-xs font-semibold">
                                        <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                                        <span>Buka</span>
                                    </a>
                                </div>
                        </form>

                        <form action="{{ route('admin.settings.news.delete', $article['id']) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus berita ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center gap-1 px-3 py-2 rounded-xl text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 text-xs font-semibold transition-colors">
                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                <span>Hapus</span>
                            </button>
                        </form>
                            </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- MODAL: TAMBAH TIPE KEANGGOTAAN BARU -->
    <div x-show="newTypeModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" x-cloak>
        <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-800 relative">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800 mb-4">
                <h3 class="font-bold text-base text-slate-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="user-plus" class="w-5 h-5 text-brand-500"></i>
                    Tambah Tipe Keanggotaan Baru
                </h3>
                <button @click="newTypeModal = false" class="text-slate-400 hover:text-slate-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form action="{{ route('admin.settings.member-type.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Tipe (Contoh: Dosen Luar Biasa, Alumni, Staf)</label>
                    <input type="text" name="member_type_name" required placeholder="Nama tipe keanggotaan"
                        class="w-full px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 font-semibold">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Batas Pinjam (Buku)</label>
                        <input type="number" min="1" max="50" name="loan_limit" value="3" required
                            class="w-full px-3 py-1.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 font-bold">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Lama Pinjam (Hari)</label>
                        <input type="number" min="1" max="365" name="loan_periode" value="14" required
                            class="w-full px-3 py-1.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 font-bold">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Tarif Denda / Hari (Rp)</label>
                        <input type="number" min="0" step="500" name="fine_each_day" value="1000" required
                            class="w-full px-3 py-1.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 font-bold text-rose-600">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Toleransi Denda (Hari)</label>
                        <input type="number" min="0" max="30" name="grace_periode" value="0" required
                            class="w-full px-3 py-1.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 font-bold">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Maks. Perpanjangan</label>
                        <input type="number" min="0" max="10" name="reborrow_limit" value="1" required
                            class="w-full px-3 py-1.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 font-bold">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Masa Aktif Kartu (Hari)</label>
                        <input type="number" min="1" max="3650" name="member_periode" value="365" required
                            class="w-full px-3 py-1.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 font-bold">
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-2">
                    <button type="button" @click="newTypeModal = false" class="px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 font-semibold hover:bg-slate-50">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold">
                        Simpan Tipe Baru
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: TAMBAH SLIDE BARU -->
    <div x-show="newSlideModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" x-cloak>
        <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-800 relative">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800 mb-4">
                <h3 class="font-bold text-base text-slate-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="image-plus" class="w-5 h-5 text-brand-500"></i>
                    Tambah Slide Gambar Hero Baru
                </h3>
                <button @click="newSlideModal = false" class="text-slate-400 hover:text-slate-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form action="{{ route('admin.settings.slides.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
                @csrf

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Tag / Label Kategori *</label>
                    <input type="text" name="tag" required placeholder="Contoh: Gedung Kampus, Student Corner, Perpustakaan..."
                        class="w-full px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 font-semibold text-slate-900 dark:text-white">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Judul Utama Slide *</label>
                    <input type="text" name="title" required placeholder="Contoh: The First Fintech University in Indonesia..."
                        class="w-full px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 font-semibold text-slate-900 dark:text-white">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Deskripsi / Keterangan *</label>
                    <textarea name="desc" rows="2" required placeholder="Tuliskan keterangan singkat slide..."
                        class="w-full px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 font-normal text-slate-900 dark:text-white"></textarea>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1 flex items-center justify-between">
                        <span>Upload File Foto Slide *</span>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-brand-50 dark:bg-brand-950 text-brand-700 dark:text-sky-300 font-mono border border-brand-200 dark:border-brand-800">
                            16:9 / 21:9
                        </span>
                    </label>

                    <!-- Informasi Panduan Dimensi & Posisi Objek -->
                    <div class="mb-2 p-3 rounded-xl bg-sky-50 dark:bg-sky-950/40 border border-sky-200 dark:border-sky-800 text-[11px] text-sky-900 dark:text-sky-200 space-y-1.5">
                        <div class="flex items-center gap-1.5 font-bold text-sky-800 dark:text-sky-300">
                            <i data-lucide="info" class="w-3.5 h-3.5 text-sky-600"></i>
                            <span>Ketentuan & Rekomendasi Format Foto:</span>
                        </div>
                        <ul class="list-disc list-inside space-y-0.5 text-[10.5px] text-slate-600 dark:text-slate-300">
                            <li><b>Rasio Aspek:</b> <b>16:9</b> (Landscape Standar) atau <b>21:9</b> (Ultra-Wide Banner).</li>
                            <li><b>Dimensi Ideal:</b> <b>1920 × 1080 px</b> (16:9) atau <b>1920 × 820 px</b> (21:9). Minimal lebar 1280 px.</li>
                            <li><b>Fokus Objek:</b> Posisikan objek penting (wajah/kepala orang) di area <b>atas hingga tengah</b> foto agar tidak terpotong saat layar melebar.</li>
                            <li><b>Format File:</b> JPG, JPEG, PNG, WEBP (Maksimal 5 MB, optimal 500 KB - 1.5 MB).</li>
                        </ul>
                    </div>

                    <input type="file" name="image" required accept="image/*"
                        class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100">
                </div>

                <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-2">
                    <button type="button" @click="newSlideModal = false" class="px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 font-semibold hover:bg-slate-50">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold">
                        Simpan Slide Baru
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: TAMBAH BERITA BARU -->
    <div x-show="newNewsModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" x-cloak>
        <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-xl w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-800 relative max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800 mb-4 sticky top-0 bg-white dark:bg-slate-900 z-10">
                <h3 class="font-bold text-base text-slate-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="newspaper" class="w-5 h-5 text-brand-500"></i>
                    Tambah Berita & Publikasi Baru
                </h3>
                <button @click="newNewsModal = false" class="text-slate-400 hover:text-slate-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form action="{{ route('admin.settings.news.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
                @csrf

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Judul Berita *</label>
                    <input type="text" name="title" required placeholder="Contoh: Sosialisasi Layanan Akses Jurnal Internasional 2026"
                        class="w-full px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 font-semibold text-slate-900 dark:text-white">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Kategori *</label>
                        <input type="text" name="category" required placeholder="Contoh: Pengumuman, Layanan, Workshop"
                            class="w-full px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 font-semibold text-slate-900 dark:text-white">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Sumber / Penulis *</label>
                        <input type="text" name="source" required placeholder="Contoh: Humas Perpustakaan, Cyber Univ"
                            class="w-full px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 font-semibold text-slate-900 dark:text-white">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Tanggal Terbit</label>
                        <input type="text" name="date" placeholder="Contoh: 29 Sep 2026 (kosongkan untuk hari ini)"
                            class="w-full px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 font-semibold text-slate-900 dark:text-white">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Tautan Web Asli (URL)</label>
                        <input type="url" name="url" placeholder="https://cyber-univ.ac.id/..."
                            class="w-full px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 font-semibold text-slate-900 dark:text-white">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Ringkasan Berita (Excerpt) *</label>
                    <textarea name="excerpt" rows="3" required placeholder="Tuliskan ringkasan singkat konten berita..."
                        class="w-full px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 font-normal text-slate-900 dark:text-white"></textarea>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Foto Sampul Berita</label>
                    <input type="file" name="image" accept="image/*"
                        class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100">
                    <span class="text-[10px] text-slate-400 mt-1 block">Format didukung: JPG, PNG, WEBP (Jika dikosongkan, gambar default akan digunakan)</span>
                </div>

                <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-2">
                    <button type="button" @click="newNewsModal = false" class="px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 font-semibold hover:bg-slate-50">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold">
                        Simpan Berita Baru
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

