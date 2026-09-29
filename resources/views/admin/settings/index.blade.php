@extends('layouts.admin')

@section('title', 'Pengaturan Perpustakaan & Aturan Keanggotaan')

@section('content')
<div class="space-y-8" x-data="{ activeTab: 'rules', newTypeModal: false }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white flex items-center gap-2.5">
                <i data-lucide="sliders" class="w-7 h-7 text-brand-500"></i>
                Pengaturan Perpustakaan
            </h1>
            <p class="text-xs text-slate-500 mt-1">Konfigurasi aturan peminjaman, denda, kuota keanggotaan, dan informasi berkala anggota</p>
        </div>

        <!-- Tab Switcher -->
        <div class="flex items-center gap-1.5 p-1 bg-slate-200/80 dark:bg-slate-800 rounded-2xl">
            <button @click="activeTab = 'rules'" :class="activeTab === 'rules' ? 'bg-white dark:bg-slate-700 text-brand-600 dark:text-sky-300 shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2">
                <i data-lucide="scale" class="w-4 h-4"></i>
                <span>Aturan & Denda</span>
            </button>
            <button @click="activeTab = 'announcement'" :class="activeTab === 'announcement' ? 'bg-white dark:bg-slate-700 text-brand-600 dark:text-sky-300 shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2">
                <i data-lucide="megaphone" class="w-4 h-4"></i>
                <span>Pengumuman Anggota</span>
            </button>
            <button @click="activeTab = 'general'" :class="activeTab === 'general' ? 'bg-white dark:bg-slate-700 text-brand-600 dark:text-sky-300 shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2">
                <i data-lucide="info" class="w-4 h-4"></i>
                <span>Tata Tertib & Identitas</span>
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
</div>
@endsection
