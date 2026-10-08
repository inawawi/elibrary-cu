@extends('layouts.admin')

@section('title', 'Manajemen Keanggotaan')
@section('header_title', 'Daftar Anggota Perpustakaan')

@section('content')
<div class="space-y-6">

    <!-- Tab Navigasi: Mahasiswa vs Non-Mahasiswa (Dosen & Staf) -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-2">
        <div class="flex items-center gap-2">
            <!-- Tab Mahasiswa -->
            <a href="{{ route('admin.member.index', ['tab' => 'mahasiswa']) }}" 
               class="inline-flex items-center gap-2.5 px-5 py-3 rounded-2xl font-bold text-xs transition-all {{ $activeTab === 'mahasiswa' ? 'bg-brand-600 text-white shadow-md shadow-brand-500/25 ring-2 ring-brand-500/30' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800' }}">
                <i data-lucide="graduation-cap" class="w-4 h-4"></i>
                <span>Mahasiswa</span>
                <span class="px-2 py-0.5 rounded-full text-[11px] font-extrabold {{ $activeTab === 'mahasiswa' ? 'bg-white/20 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300' }}">
                    {{ number_format($studentCount) }}
                </span>
            </a>

            <!-- Tab Dosen & Staf (Non-Mahasiswa) -->
            <a href="{{ route('admin.member.index', ['tab' => 'non-mahasiswa']) }}" 
               class="inline-flex items-center gap-2.5 px-5 py-3 rounded-2xl font-bold text-xs transition-all {{ $activeTab === 'non-mahasiswa' ? 'bg-brand-600 text-white shadow-md shadow-brand-500/25 ring-2 ring-brand-500/30' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800' }}">
                <i data-lucide="briefcase" class="w-4 h-4"></i>
                <span>Dosen & Staf (Non-Mahasiswa)</span>
                <span class="px-2 py-0.5 rounded-full text-[11px] font-extrabold {{ $activeTab === 'non-mahasiswa' ? 'bg-white/20 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300' }}">
                    {{ number_format($staffCount) }}
                </span>
            </a>
        </div>

        <!-- Tombol Aksi Tambah & Sinkronisasi -->
        <div class="flex items-center gap-2.5 flex-wrap sm:flex-nowrap">
            @if($activeTab === 'mahasiswa')
                <!-- Tombol Sinkronisasi Mahasiswa via API Web Student -->
                <form action="{{ route('admin.member.sync-student') }}" method="POST" id="form-sync-student" onsubmit="return confirmSyncStudent(event)">
                    @csrf
                    <button type="submit" id="btn-sync-student" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-500/20 transition-all flex-shrink-0 cursor-pointer" title="Sinkron data mahasiswa baru dari API Web Student">
                        <i data-lucide="refresh-cw" class="w-4 h-4" id="icon-sync-student"></i>
                        <span id="text-sync-student">Sinkron Mahasiswa (API)</span>
                    </button>
                </form>
            @else
                <!-- Tombol Sinkronisasi Data Kepegawaian (Khusus Dosen & Staf) -->
                <form action="{{ route('admin.member.sync') }}" method="POST" id="form-sync-member" onsubmit="return confirmSync(event)">
                    @csrf
                    <button type="submit" id="btn-sync-member" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-bold text-xs shadow-md shadow-sky-500/20 transition-all flex-shrink-0 cursor-pointer" title="Sinkron data dosen dan staf dari tabel karyawanbs1">
                        <i data-lucide="refresh-cw" class="w-4 h-4" id="icon-sync-member"></i>
                        <span id="text-sync-member">Sinkron Dosen & Staf</span>
                    </button>
                </form>
            @endif

            <!-- Export Multi-Format Button -->
            <div class="relative" x-data="{ exportOpen: false }">
                <button type="button" @click="exportOpen = !exportOpen"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-900 dark:bg-slate-700 dark:hover:bg-slate-600 text-white font-bold text-xs shadow-md transition-all flex-shrink-0 cursor-pointer">
                    <i data-lucide="download" class="w-4 h-4 text-emerald-400"></i>
                    <span>Ekspor Data</span>
                    <i data-lucide="chevron-down" class="w-3.5 h-3.5"></i>
                </button>

                <div x-show="exportOpen" @click.away="exportOpen = false" x-cloak
                     class="absolute right-0 mt-2 w-56 bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-100 dark:border-slate-700 p-2 z-50 space-y-1 text-left">
                    <div class="px-3 py-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100 dark:border-slate-700 mb-1">
                        Pilih Format Ekspor
                    </div>
                    <a href="{{ route('admin.member.export', array_merge(request()->query(), ['format' => 'excel', 'tab' => $activeTab])) }}"
                       class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-emerald-50 hover:text-emerald-700 dark:hover:bg-emerald-950/50 transition-colors">
                        <span class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-700 dark:bg-emerald-900/60 dark:text-emerald-400 flex items-center justify-center font-bold text-[10px]">XLS</span>
                        <div>
                            <div class="font-bold">Microsoft Excel</div>
                            <div class="text-[10px] text-slate-400">Spreadsheet (.xls)</div>
                        </div>
                    </a>
                    <a href="{{ route('admin.member.export', array_merge(request()->query(), ['format' => 'word', 'tab' => $activeTab])) }}"
                       class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-blue-50 hover:text-blue-700 dark:hover:bg-blue-950/50 transition-colors">
                        <span class="w-6 h-6 rounded-lg bg-blue-100 text-blue-700 dark:bg-blue-900/60 dark:text-blue-400 flex items-center justify-center font-bold text-[10px]">DOC</span>
                        <div>
                            <div class="font-bold">Microsoft Word</div>
                            <div class="text-[10px] text-slate-400">Dokumen Resmi (.doc)</div>
                        </div>
                    </a>
                    <a href="{{ route('admin.member.export', array_merge(request()->query(), ['format' => 'pdf', 'tab' => $activeTab])) }}" target="_blank"
                       class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-rose-50 hover:text-rose-700 dark:hover:bg-rose-950/50 transition-colors">
                        <span class="w-6 h-6 rounded-lg bg-rose-100 text-rose-700 dark:bg-rose-900/60 dark:text-rose-400 flex items-center justify-center font-bold text-[10px]">PDF</span>
                        <div>
                            <div class="font-bold">Cetak / Simpan PDF</div>
                            <div class="text-[10px] text-slate-400">Layout Kop Surat Resmi</div>
                        </div>
                    </a>
                    <a href="{{ route('admin.member.export', array_merge(request()->query(), ['format' => 'csv', 'tab' => $activeTab])) }}"
                       class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-100 hover:text-slate-900 dark:hover:bg-slate-700 transition-colors">
                        <span class="w-6 h-6 rounded-lg bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-300 flex items-center justify-center font-bold text-[10px]">CSV</span>
                        <div>
                            <div class="font-bold">File CSV</div>
                            <div class="text-[10px] text-slate-400">Universal Raw Data (.csv)</div>
                        </div>
                    </a>
                </div>
            </div>

            <a href="{{ route('admin.member.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs shadow-md shadow-brand-500/25 transition-all flex-shrink-0">
                <i data-lucide="user-plus" class="w-4 h-4"></i>
                <span>Daftar Anggota Baru</span>
            </a>
        </div>
    </div>

    @if($activeTab === 'non-mahasiswa' && $staffCount === 0)
        <!-- Banner Informasi Sinkronisasi jika data masih kosong -->
        <div class="bg-sky-50 dark:bg-sky-950/40 border border-sky-200 dark:border-sky-800 rounded-2xl p-5 flex items-start gap-4">
            <div class="p-2 bg-sky-100 dark:bg-sky-900/60 rounded-xl text-sky-600 dark:text-sky-300 flex-shrink-0">
                <i data-lucide="info" class="w-5 h-5"></i>
            </div>
            <div class="space-y-1 text-xs">
                <h4 class="font-bold text-sky-900 dark:text-sky-200 text-sm">Data Dosen & Staf Belum Tersedia di e-Library</h4>
                <p class="text-sky-700 dark:text-sky-300/80 leading-relaxed">
                    Klik tombol <strong>"Sinkron Dosen & Staf"</strong> di atas untuk mengimpor dan memperbarui data seluruh Dosen dan Staf dari basis data kepegawaian (tabel <code class="bg-sky-100 dark:bg-sky-900 px-1 py-0.5 rounded text-sky-800 dark:text-sky-200 font-mono">karyawanbs1</code>).
                    Sumber data eksternal dibaca secara <em>read-only</em> dan aman tanpa perubahan apapun pada server kepegawaian.
                </p>
            </div>
        </div>
    @endif

    <!-- Form Pencarian & Filter -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm">
        <form action="{{ route('admin.member.index') }}" method="GET" class="flex flex-wrap items-center gap-3 w-full">
            <input type="hidden" name="tab" value="{{ $activeTab }}">

            <div class="relative flex-grow min-w-[240px]">
                <i data-lucide="search" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text"
                       name="search"
                       value="{{ $search }}"
                       placeholder="{{ $activeTab === 'mahasiswa' ? 'Cari NIM, nama mahasiswa, email...' : 'Cari NIP, nama dosen/staf, jabatan...' }}"
                       class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white font-medium focus:outline-none">
            </div>

            @if($memberTypes->count() > 1)
                <select name="type_id" class="px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none">
                    <option value="">Semua Tipe {{ $activeTab === 'non-mahasiswa' ? 'Non-Mahasiswa' : '' }}</option>
                    @foreach($memberTypes as $mt)
                        <option value="{{ $mt->member_type_id }}" {{ $typeId == $mt->member_type_id ? 'selected' : '' }}>{{ $mt->member_type_name }}</option>
                    @endforeach
                </select>
            @endif

            <select name="status" class="px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none">
                <option value="">Semua Status</option>
                <option value="active" {{ ($status ?? '') == 'active' ? 'selected' : '' }}>Aktif</option>
                @if($activeTab === 'mahasiswa')
                    <option value="expired" {{ ($status ?? '') == 'expired' ? 'selected' : '' }}>Kedaluwarsa</option>
                @endif
                <option value="inactive" {{ ($status ?? '') == 'inactive' ? 'selected' : '' }}>Non-Aktif</option>
            </select>

            <button type="submit" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-xl transition-colors">
                Filter
            </button>
            @if($search || $typeId || ($status ?? ''))
                <a href="{{ route('admin.member.index', ['tab' => $activeTab]) }}" class="text-xs text-rose-500 hover:underline">Reset</a>
            @endif
        </form>
    </div>

    <!-- Members Table -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-600 dark:text-slate-300 font-bold uppercase tracking-wider border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="py-4 px-6 w-14">Foto</th>
                        <th class="py-4 px-6">{{ $activeTab === 'mahasiswa' ? 'Identitas Mahasiswa' : 'Identitas Dosen / Staf' }}</th>
                        <th class="py-4 px-6">Tipe Keanggotaan</th>
                        <th class="py-4 px-6">Masa Berlaku</th>
                        <th class="py-4 px-6 text-center">Pinjaman Aktif</th>
                        <th class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                    @forelse($members as $m)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                            <td class="py-3 px-6">
                                <img src="{{ $m->avatar_url }}" alt="{{ $m->member_name }}" class="w-10 h-10 rounded-xl object-cover border border-slate-200 dark:border-slate-700 shadow-sm">
                            </td>
                            <td class="py-3 px-6">
                                <div class="font-bold text-sm text-slate-900 dark:text-white">{{ $m->member_name }}</div>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="font-mono text-[11px] text-brand-600 dark:text-sky-400 font-bold">{{ $m->member_id }}</span>
                                    @if($m->isNonStudent() && !empty($m->member_notes))
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                                            {{ $m->member_notes }}
                                        </span>
                                    @endif
                                </div>
                                <div class="text-[10px] text-slate-400 mt-0.5">
                                    {{ $m->member_email ?: '-' }}
                                    @if(!empty($m->member_phone))
                                        &bull; {{ $m->member_phone }}
                                    @endif
                                </div>
                                @if((int)$m->member_type_id === 1)
                                    <div class="mt-1 flex items-center gap-1.5 text-[11px] flex-wrap">
                                        <span class="text-slate-500 font-semibold text-[10px]">Tgl Lahir (Sandi Default):</span>
                                        @if(!empty($m->birth_date))
                                            <span class="font-mono font-bold text-purple-700 dark:text-purple-300 bg-purple-50 dark:bg-purple-950/60 px-1.5 py-0.5 rounded border border-purple-200/50 dark:border-purple-800/40 text-[10px]">
                                                {{ \Carbon\Carbon::parse($m->birth_date)->format('Y-m-d') }}
                                            </span>
                                        @else
                                            <span class="font-bold text-amber-700 dark:text-amber-300 bg-amber-50 dark:bg-amber-950/60 px-1.5 py-0.5 rounded border border-amber-200/60 dark:border-amber-800/40 text-[10px]" title="Tanggal lahir belum ada di database, login menggunakan NIM">
                                                Belum Ada (Sandi: NIM)
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td class="py-3 px-6">
                                @if($m->isLecturer())
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 border border-indigo-200/50 dark:border-indigo-800/50">
                                        {{ $m->memberType?->member_type_name ?: 'Dosen' }}
                                    </span>
                                @elseif($m->isStaff())
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-teal-50 dark:bg-teal-950/60 text-teal-700 dark:text-teal-300 border border-teal-200/50 dark:border-teal-800/50">
                                        {{ $m->memberType?->member_type_name ?: 'Staf' }}
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                        {{ $m->memberType?->member_type_name ?: 'Mahasiswa' }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-6">
                                @if($m->isNonStudent())
                                    <div class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 flex items-center gap-1.5">
                                        <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                                        <span>Aktif Selama Bertugas</span>
                                    </div>
                                @else
                                    <div class="text-xs font-medium text-slate-700 dark:text-slate-300">
                                        <span class="text-[9px] text-slate-400 block uppercase font-bold tracking-wider">Masa Aktif Kartu s/d</span>
                                        {{ $m->expire_date ? \Carbon\Carbon::parse($m->expire_date)->translatedFormat('d F Y') : '-' }}
                                    </div>
                                @endif

                                <div class="mt-1 flex items-center gap-1.5 flex-wrap">
                                    @if($m->is_pending == 1)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700 dark:bg-rose-950/80 dark:text-rose-300">
                                            Non-Aktif
                                        </span>
                                    @elseif($m->isExpired())
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700 dark:bg-amber-950/80 dark:text-amber-300" title="Masa berlaku 7 tahun telah habis">
                                            Kedaluwarsa
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-950/80 dark:text-emerald-300">
                                            Aktif
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3 px-6 text-center">
                                @if($m->activeLoans->isNotEmpty())
                                    <a href="{{ route('admin.circulation.index', ['member_id' => $m->member_id]) }}" class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300">
                                        {{ $m->activeLoans->count() }} Buku
                                    </a>
                                @else
                                    <span class="text-slate-400 text-[11px]">-</span>
                                @endif
                            </td>
                            <td class="py-3 px-6 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <form action="{{ route('admin.member.toggle-status', $m->member_id) }}" method="POST" onsubmit="return confirm('{{ $m->is_pending == 1 ? 'Aktifkan kembali keanggotaan ini?' : 'Nonaktifkan keanggotaan ini? (Anggota tidak dapat meminjam buku)' }}')">
                                        @csrf
                                        @method('PATCH')
                                        @if($m->is_pending == 1)
                                            <button type="submit" class="p-2 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 transition-colors" title="Aktifkan Anggota">
                                                <i data-lucide="user-check" class="w-4 h-4"></i>
                                            </button>
                                        @else
                                            <button type="submit" class="p-2 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-500 hover:bg-rose-50 hover:text-rose-600 dark:hover:bg-rose-950/50 dark:hover:text-rose-400 transition-colors" title="Nonaktifkan Anggota">
                                                <i data-lucide="user-x" class="w-4 h-4"></i>
                                            </button>
                                        @endif
                                    </form>
                                    <a href="{{ route('admin.circulation.index', ['member_id' => $m->member_id]) }}" class="p-2 rounded-lg bg-sky-50 dark:bg-sky-950/60 text-sky-600 dark:text-sky-400 hover:bg-sky-100 transition-colors" title="Pinjam/Kembali">
                                        <i data-lucide="repeat" class="w-4 h-4"></i>
                                    </a>
                                    <a href="{{ route('admin.member.card', $m->member_id) }}" target="_blank" class="p-2 rounded-lg bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 transition-colors" title="Cetak Kartu">
                                        <i data-lucide="id-card" class="w-4 h-4"></i>
                                    </a>
                                    @if((int)$m->member_type_id === 1)
                                        <form action="{{ route('admin.member.reset-password', $m->member_id) }}" method="POST" onsubmit="return confirm('Reset kata sandi mahasiswa {{ addslashes($m->member_name) }} ({{ $m->member_id }}) kembali ke tanggal lahir ({{ $m->birth_date ?: 'NIM' }})?')">
                                            @csrf
                                            <button type="submit" class="p-2 rounded-lg bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 hover:bg-purple-100 transition-colors" title="Reset Sandi ke Tanggal Lahir ({{ $m->birth_date ?: 'NIM' }})">
                                                <i data-lucide="key-round" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <a href="{{ route('admin.member.edit', $m->member_id) }}" class="p-2 rounded-lg bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 hover:bg-amber-100 transition-colors" title="Ubah">
                                        <i data-lucide="pencil" class="w-4 h-4"></i>
                                    </a>
                                    <form action="{{ route('admin.member.destroy', $m->member_id) }}" method="POST" onsubmit="return confirm('{{ $m->isNonStudent() ? 'Hapus data anggota ini? Pastikan yang bersangkutan sudah tidak aktif bertugas di Universitas Siber Indonesia.' : 'Hapus data anggota ini?' }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 rounded-lg bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 hover:bg-rose-100 transition-colors" title="Hapus Anggota">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-12 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center space-y-3">
                                    <i data-lucide="users" class="w-10 h-10 text-slate-300 dark:text-slate-600"></i>
                                    <p class="font-medium">Tidak ada data {{ $activeTab === 'mahasiswa' ? 'mahasiswa' : 'dosen & staf' }} yang sesuai.</p>
                                    @if($activeTab === 'non-mahasiswa' && $staffCount === 0)
                                        <button type="button" onclick="document.getElementById('form-sync-member').submit();" class="text-xs font-bold text-sky-600 hover:underline">
                                            Klik di sini untuk menyinkronkan data sekarang
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100 dark:border-slate-800">
            {{ $members->links() }}
        </div>
    </div>
</div>

<script>
function confirmSync(e) {
    const confirmed = confirm('Mulai sinkronisasi data Dosen dan Staf dari database kepegawaian (karyawanbs1)?\n\nData di e-library akan diperbarui/ditambahkan secara otomatis tanpa mengubah database sumber.');
    if (!confirmed) {
        e.preventDefault();
        return false;
    }
    
    // Tampilkan indikator loading pada tombol
    const btn = document.getElementById('btn-sync-member');
    const icon = document.getElementById('icon-sync-member');
    const text = document.getElementById('text-sync-member');
    
    if (btn && icon && text) {
        btn.disabled = true;
        btn.classList.add('opacity-75', 'cursor-wait');
        icon.classList.add('animate-spin');
        text.innerText = 'Menyinkronkan...';
    }
    return true;
}

function confirmSyncStudent(e) {
    const confirmed = confirm('Mulai sinkronisasi data Mahasiswa dari API Web Student (students.cyber-univ.ac.id)?\n\nCATATAN:\n• Data mahasiswa yang sudah ada di e-Library TIDAK AKAN DIHAPUS.\n• Sistem hanya menambahkan mahasiswa yang belum ada di database e-Library.\n• Aplikasi sumber dibaca secara Read-Only.');
    if (!confirmed) {
        e.preventDefault();
        return false;
    }
    
    const btn = document.getElementById('btn-sync-student');
    const icon = document.getElementById('icon-sync-student');
    const text = document.getElementById('text-sync-student');
    
    if (btn && icon && text) {
        btn.disabled = true;
        btn.classList.add('opacity-75', 'cursor-wait');
        icon.classList.add('animate-spin');
        text.innerText = 'Menyinkronkan API...';
    }
    return true;
}
</script>
@endsection
