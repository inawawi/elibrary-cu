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
            <!-- Tombol Sinkronisasi Mahasiswa via Database (db_student_usi) -->
            <form action="{{ route('admin.member.sync-student') }}" method="POST" id="form-sync-student" onsubmit="return confirmSyncStudent(event)">
                @csrf
                <button type="submit" id="btn-sync-student" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-500/20 transition-all flex-shrink-0 cursor-pointer" title="Sinkron data mahasiswa langsung dari database mhs">
                    <i data-lucide="refresh-cw" class="w-4 h-4" id="icon-sync-student"></i>
                    <span id="text-sync-student">Sinkron Mahasiswa (DB)</span>
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

    @if($activeTab === 'mahasiswa')
    <!-- Bulk Action Bar Mahasiswa (Checklist Reset Sandi ke Tanggal Lahir) -->
    <div id="bulk-action-bar" style="display: none;" class="mb-4 p-4 rounded-2xl bg-gradient-to-r from-purple-900 via-indigo-900 to-purple-800 text-white shadow-xl flex flex-col sm:flex-row items-center justify-between gap-3 border border-purple-400/30 transition-all">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center flex-shrink-0 text-purple-300">
                <i data-lucide="check-square" class="w-5 h-5"></i>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <span id="selected-count" class="font-black text-sm text-purple-200 bg-white/20 px-2 py-0.5 rounded-md">0</span>
                    <span class="text-xs font-bold text-white">Mahasiswa Dichecklist</span>
                </div>
                <p class="text-[11px] text-purple-200/80 mt-0.5">Reset kata sandi mahasiswa yang dipilih kembali ke tanggal lahir masing-masing (format: YYYY-MM-DD).</p>
            </div>
        </div>
        <div class="flex items-center gap-2.5 w-full sm:w-auto justify-end">
            <button type="button" id="btn-uncheck-all" class="px-3.5 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-xs font-semibold text-white/90 transition-colors cursor-pointer">
                Batal Pilih
            </button>
            <button type="button" id="btn-bulk-reset" class="px-4 py-2 rounded-xl bg-purple-500 hover:bg-purple-600 text-white font-bold text-xs shadow-md shadow-purple-900/50 flex items-center gap-1.5 transition-all cursor-pointer">
                <i data-lucide="key-round" class="w-4 h-4 pointer-events-none"></i>
                <span>Reset Sandi yang Dichecklist</span>
            </button>
        </div>
    </div>

    <form id="bulk-reset-form" action="{{ route('admin.member.bulk-reset-password') }}" method="POST" class="hidden">
        @csrf
        <input type="hidden" name="selected_members" id="bulk-selected-members-input">
    </form>
    @endif

    <!-- Single Reset Password Form (Shared for reliability) -->
    <form id="form-single-reset" method="POST" class="hidden">
        @csrf
    </form>

    <!-- Members Table -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-600 dark:text-slate-300 font-bold uppercase tracking-wider border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        @if($activeTab === 'mahasiswa')
                        <th class="py-4 px-3 w-10 text-center">
                            <input type="checkbox" id="check-all" class="w-4 h-4 rounded border-slate-300 text-purple-600 focus:ring-purple-500 cursor-pointer" title="Pilih Semua di Halaman Ini">
                        </th>
                        @endif
                        <th class="py-4 px-6 w-14">Foto</th>
                        <th class="py-4 px-6">{{ $activeTab === 'mahasiswa' ? 'Identitas Mahasiswa' : 'Identitas Dosen / Staf' }}</th>
                        <th class="py-4 px-6">Tipe Keanggotaan</th>
                        <th class="py-4 px-6">Tanggal Lahir</th>
                        <th class="py-4 px-6">Masa Berlaku</th>
                        <th class="py-4 px-6 text-center">Pinjaman Aktif</th>
                        <th class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                    @forelse($members as $m)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                        @if($activeTab === 'mahasiswa')
                        <td class="py-3 px-3 text-center">
                            <input type="checkbox" value="{{ $m->member_id }}" class="member-checkbox w-4 h-4 rounded border-slate-300 text-purple-600 focus:ring-purple-500 cursor-pointer">
                        </td>
                        @endif
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
                            <div class="text-xs font-semibold text-slate-800 dark:text-slate-200">
                                @if(!empty($m->birth_date) && $m->birth_date !== '0000-00-00')
                                <div>{{ \Carbon\Carbon::parse($m->birth_date)->translatedFormat('d F Y') }}</div>
                                <div class="font-mono text-[10px] text-purple-700 dark:text-purple-300 font-bold bg-purple-50 dark:bg-purple-950/60 px-1.5 py-0.5 rounded border border-purple-200/50 dark:border-purple-800/40 inline-block mt-0.5" title="Format sandi reset: {{ \Carbon\Carbon::parse($m->birth_date)->format('Y-m-d') }}">
                                    {{ \Carbon\Carbon::parse($m->birth_date)->format('Y-m-d') }}
                                </div>
                                @else
                                <span class="text-slate-400 text-xs italic">- Belum Diisi -</span>
                                @endif
                            </div>
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
                                <button type="button"
                                    class="p-2 rounded-lg bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 hover:bg-purple-100 dark:hover:bg-purple-900/60 transition-colors btn-reset-single cursor-pointer"
                                    data-id="{{ $m->member_id }}"
                                    data-name="{{ htmlspecialchars($m->member_name, ENT_QUOTES) }}"
                                    data-target="{{ (!empty($m->birth_date) && $m->birth_date !== '0000-00-00') ? \Carbon\Carbon::parse($m->birth_date)->format('Y-m-d') : 'NIM (' . $m->member_id . ')' }}"
                                    title="Reset Sandi ke Tanggal Lahir ({{ (!empty($m->birth_date) && $m->birth_date !== '0000-00-00') ? \Carbon\Carbon::parse($m->birth_date)->format('Y-m-d') : 'NIM' }})">
                                    <i data-lucide="key-round" class="w-4 h-4 pointer-events-none"></i>
                                </button>
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
                        <td colspan="{{ $activeTab === 'mahasiswa' ? 7 : 6 }}" class="p-12 text-center text-slate-400">
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

@if(session('syncReport'))
@php $report = session('syncReport'); @endphp
<!-- Modal Popup Tampilan Tabel Hasil Sinkronisasi -->
<div x-data="{ 
        modalOpen: true, 
        searchTerm: '', 
        filterStatus: 'all',
        items: {{ json_encode($report['items'] ?? []) }},
        get filteredItems() {
            return this.items.filter(item => {
                const term = this.searchTerm.toLowerCase().trim();
                const matchSearch = !term || 
                    (item.member_id && item.member_id.toLowerCase().includes(term)) ||
                    (item.member_name && item.member_name.toLowerCase().includes(term)) ||
                    (item.prodi && item.prodi.toLowerCase().includes(term)) ||
                    (item.kelas && item.kelas.toLowerCase().includes(term)) ||
                    (item.changes && item.changes.toLowerCase().includes(term));
                
                const matchStatus = this.filterStatus === 'all' || 
                    (this.filterStatus === 'new' && item.badge === 'new') ||
                    (this.filterStatus === 'updated' && item.badge === 'updated');

                return matchSearch && matchStatus;
            });
        }
    }" 
    x-init="if (window.lucide) { $nextTick(() => lucide.createIcons()); }"
    x-show="modalOpen" 
    x-cloak
    class="fixed inset-0 z-50 overflow-y-auto"
    aria-labelledby="modal-title" role="dialog" aria-modal="true">

    <!-- Backdrop -->
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" 
         @click="modalOpen = false"></div>

    <div class="flex min-h-screen items-center justify-center p-3 sm:p-6">
        <div class="relative transform overflow-hidden rounded-3xl bg-white dark:bg-slate-900 text-left shadow-2xl transition-all w-full max-w-4xl border border-slate-200 dark:border-slate-800 flex flex-col max-h-[90vh]">
            
            <!-- Modal Header -->
            <div class="p-5 sm:p-6 border-b border-slate-100 dark:border-slate-800 flex items-start justify-between bg-slate-50/70 dark:bg-slate-800/40">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center flex-shrink-0 shadow-inner">
                        <i data-lucide="check-circle-2" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white" id="modal-title">
                            {{ $report['title'] ?? 'Hasil Sinkronisasi Data Anggota' }}
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Sumber: <span class="font-mono font-semibold text-slate-700 dark:text-slate-300">{{ $report['source_db'] }}.{{ $report['source_table'] }}</span> &bull; 
                            Total Terbaca: <span class="font-semibold text-slate-700 dark:text-slate-300">{{ number_format($report['total_source']) }} data</span>
                        </p>
                    </div>
                </div>
                <button type="button" @click="modalOpen = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Stats Bar -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 p-4 sm:p-5 bg-white dark:bg-slate-900 border-b border-slate-100 dark:border-slate-800">
                <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-700/60">
                    <div class="text-[11px] font-medium text-slate-500 dark:text-slate-400">Total di Sumber</div>
                    <div class="text-lg font-black text-slate-800 dark:text-slate-100 mt-0.5">{{ number_format($report['total_source']) }}</div>
                </div>
                <div class="p-3.5 rounded-2xl bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-100 dark:border-emerald-900/40">
                    <div class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400">Baru Ditambahkan</div>
                    <div class="text-lg font-black text-emerald-700 dark:text-emerald-300 mt-0.5">+{{ number_format($report['inserted']) }}</div>
                </div>
                <div class="p-3.5 rounded-2xl bg-blue-50 dark:bg-blue-950/30 border border-blue-100 dark:border-blue-900/40">
                    <div class="text-[11px] font-bold text-blue-600 dark:text-blue-400">Data Diperbarui</div>
                    <div class="text-lg font-black text-blue-700 dark:text-blue-300 mt-0.5">{{ number_format($report['updated']) }}</div>
                </div>
                <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-700/60">
                    <div class="text-[11px] font-medium text-slate-500 dark:text-slate-400">Tidak Berubah / Sudah Sesuai</div>
                    <div class="text-lg font-black text-slate-600 dark:text-slate-400 mt-0.5">{{ number_format($report['unchanged']) }}</div>
                </div>
            </div>

            <!-- Filter & Search Toolbar (jika ada items) -->
            <template x-if="items.length > 0">
                <div class="px-5 sm:px-6 py-3 border-b border-slate-100 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-800/30 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <button type="button" @click="filterStatus = 'all'" 
                            :class="filterStatus === 'all' ? 'bg-slate-900 text-white dark:bg-white dark:text-slate-900' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700'"
                            class="px-3 py-1.5 rounded-xl text-xs font-bold transition-colors">
                            Semua Perubahan (<span x-text="items.length"></span>)
                        </button>
                        <button type="button" @click="filterStatus = 'new'" 
                            :class="filterStatus === 'new' ? 'bg-emerald-600 text-white' : 'bg-white dark:bg-slate-800 text-emerald-600 dark:text-emerald-400 border border-slate-200 dark:border-slate-700'"
                            class="px-3 py-1.5 rounded-xl text-xs font-bold transition-colors">
                            Baru ({{ $report['inserted'] }})
                        </button>
                        <button type="button" @click="filterStatus = 'updated'" 
                            :class="filterStatus === 'updated' ? 'bg-blue-600 text-white' : 'bg-white dark:bg-slate-800 text-blue-600 dark:text-blue-400 border border-slate-200 dark:border-slate-700'"
                            class="px-3 py-1.5 rounded-xl text-xs font-bold transition-colors">
                            Diperbarui ({{ $report['updated'] }})
                        </button>
                    </div>

                    <div class="relative w-full sm:w-64">
                        <i data-lucide="search" class="w-3.5 h-3.5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" x-model="searchTerm" placeholder="Cari NIM, nama, prodi..." 
                            class="w-full pl-8 pr-3 py-1.5 text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none">
                    </div>
                </div>
            </template>

            <!-- Table Body (Scrollable) -->
            <div class="overflow-y-auto flex-grow p-4 sm:p-6">
                <template x-if="items.length > 0">
                    <div>
                        <div class="overflow-x-auto rounded-2xl border border-slate-200 dark:border-slate-800">
                            <table class="w-full text-left text-xs border-collapse">
                                <thead>
                                    <tr class="bg-slate-100/75 dark:bg-slate-800/80 text-slate-600 dark:text-slate-400 font-bold border-b border-slate-200 dark:border-slate-700">
                                        <th class="py-3 px-3.5 text-center w-12">No</th>
                                        <th class="py-3 px-3.5 w-28">Status</th>
                                        <th class="py-3 px-3.5 w-32">NIM / ID</th>
                                        <th class="py-3 px-3.5 min-w-[180px]">Nama Anggota</th>
                                        <th class="py-3 px-3.5 min-w-[160px]">Prodi / Info</th>
                                        <th class="py-3 px-3.5 min-w-[150px]">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                                    <template x-for="(item, idx) in filteredItems" :key="item.member_id + '-' + idx">
                                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                                            <td class="py-3 px-3.5 text-center font-semibold text-slate-400" x-text="idx + 1"></td>
                                            <td class="py-3 px-3.5">
                                                <span x-show="item.badge === 'new'" 
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-black bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                                    <i data-lucide="plus" class="w-3 h-3"></i> Baru
                                                </span>
                                                <span x-show="item.badge === 'updated'" 
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-black bg-blue-100 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                                    <i data-lucide="edit-3" class="w-3 h-3"></i> Diperbarui
                                                </span>
                                            </td>
                                            <td class="py-3 px-3.5 font-mono font-bold text-slate-800 dark:text-slate-200" x-text="item.member_id"></td>
                                            <td class="py-3 px-3.5">
                                                <div class="font-bold text-slate-900 dark:text-white" x-text="item.member_name"></div>
                                                <div class="text-[11px] text-slate-400 flex items-center gap-2 mt-0.5">
                                                    <span x-show="item.email && item.email !== '-'" x-text="item.email"></span>
                                                    <span x-show="item.phone && item.phone !== '-'" x-text="item.phone"></span>
                                                </div>
                                            </td>
                                            <td class="py-3 px-3.5 text-slate-600 dark:text-slate-300">
                                                <div class="font-semibold" x-text="item.prodi || '-'"></div>
                                                <div class="text-[10px] text-slate-400" x-show="item.kelas && item.kelas !== '-'" x-text="'Kelas: ' + item.kelas"></div>
                                            </td>
                                            <td class="py-3 px-3.5">
                                                <span class="text-[11px] text-slate-600 dark:text-slate-300 font-medium" x-text="item.changes"></span>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                        <div x-show="filteredItems.length === 0" class="py-8 text-center text-slate-400 text-xs">
                            Tidak ada data yang sesuai dengan filter / pencarian "<span x-text="searchTerm"></span>".
                        </div>
                    </div>
                </template>

                <template x-if="items.length === 0">
                    <div class="py-12 px-4 text-center">
                        <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 mx-auto flex items-center justify-center mb-3">
                            <i data-lucide="check" class="w-8 h-8 text-emerald-500"></i>
                        </div>
                        <h4 class="font-bold text-slate-800 dark:text-slate-200 text-sm">Semua Data Sudah Terkini</h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400 max-w-md mx-auto mt-1">
                            Tidak ada data anggota baru atau pembaruan profil yang ditemukan dari database <span class="font-mono font-semibold">{{ $report['source_db'] }}.{{ $report['source_table'] }}</span>. Seluruh data di e-Library sudah sinkron.
                        </p>
                    </div>
                </template>
            </div>

            <!-- Modal Footer -->
            <div class="p-4 sm:p-5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/70 dark:bg-slate-800/40">
                <div class="text-xs text-slate-500 dark:text-slate-400">
                    Total: <strong class="text-slate-700 dark:text-slate-200" x-text="filteredItems.length"></strong> data ditampilkan
                </div>
                <button type="button" @click="modalOpen = false"
                    class="px-5 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs shadow-md shadow-brand-500/20 transition-all cursor-pointer">
                    Tutup Tampilan
                </button>
            </div>
        </div>
    </div>
</div>
@endif

<script>
(function() {
    function updateBulkBar() {
        const checkAll = document.getElementById('check-all');
        const memberCheckboxes = document.querySelectorAll('.member-checkbox');
        const bulkBar = document.getElementById('bulk-action-bar');
        const selectedCountEl = document.getElementById('selected-count');
        const checked = document.querySelectorAll('.member-checkbox:checked');
        const count = checked.length;

        if (selectedCountEl) selectedCountEl.innerText = count;
        if (bulkBar) {
            bulkBar.style.display = count > 0 ? 'flex' : 'none';
        }
        if (checkAll && memberCheckboxes.length > 0) {
            checkAll.checked = (count === memberCheckboxes.length);
        }
    }

    // 1. Delegasi Event Perubahan Centang / Checklist
    document.addEventListener('change', function(e) {
        if (e.target && e.target.id === 'check-all') {
            const isChecked = e.target.checked;
            document.querySelectorAll('.member-checkbox').forEach(function(cb) {
                cb.checked = isChecked;
            });
            updateBulkBar();
        } else if (e.target && e.target.classList.contains('member-checkbox')) {
            updateBulkBar();
        }
    });

    // 2. Delegasi Klik Tombol
    document.addEventListener('click', function(e) {
        // Tombol Batal Pilih
        if (e.target && e.target.closest('#btn-uncheck-all')) {
            const checkAll = document.getElementById('check-all');
            if (checkAll) checkAll.checked = false;
            document.querySelectorAll('.member-checkbox').forEach(function(cb) {
                cb.checked = false;
            });
            updateBulkBar();
            return;
        }

        // Tombol Reset Sandi Checklist (Bulk)
        const btnBulk = e.target.closest('#btn-bulk-reset');
        if (btnBulk) {
            e.preventDefault();
            const checked = document.querySelectorAll('.member-checkbox:checked');
            if (checked.length === 0) {
                alert('Silakan centang / checklist minimal satu mahasiswa terlebih dahulu.');
                return;
            }

            const ids = Array.from(checked).map(function(cb) { return cb.value; });
            const count = ids.length;

            if (confirm('Reset kata sandi ' + count + ' mahasiswa yang dichecklist kembali ke tanggal lahir masing-masing (format: YYYY-MM-DD)?')) {
                const bulkForm = document.getElementById('bulk-reset-form');
                const bulkInput = document.getElementById('bulk-selected-members-input');
                if (bulkForm && bulkInput) {
                    bulkInput.value = ids.join(',');
                    btnBulk.disabled = true;
                    btnBulk.innerHTML = '<span class="inline-block animate-spin mr-1">⏳</span> Memproses...';
                    bulkForm.submit();
                }
            }
            return;
        }

        // Tombol Reset Sandi Perorangan (Single)
        const btnSingle = e.target.closest('.btn-reset-single');
        if (btnSingle) {
            e.preventDefault();
            const id = btnSingle.getAttribute('data-id');
            const name = btnSingle.getAttribute('data-name') || id;
            const target = btnSingle.getAttribute('data-target') || 'tanggal lahir';

            if (confirm('Reset kata sandi mahasiswa "' + name + '" (' + id + ') kembali ke ' + target + '?')) {
                const singleForm = document.getElementById('form-single-reset');
                if (singleForm) {
                    singleForm.action = '/admin/member/' + id + '/reset-password';
                    btnSingle.disabled = true;
                    btnSingle.classList.add('opacity-50');
                    singleForm.submit();
                }
            }
            return;
        }
    });

    // Jalankan kalkulasi awal saat dokumen siap
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', updateBulkBar);
    } else {
        updateBulkBar();
    }
})();

function confirmSync(e) {
    const confirmed = confirm('Mulai sinkronisasi data Dosen dan Staf dari database kepegawaian (karyawanbs1)?\n\nData di e-library akan diperbarui/ditambahkan secara otomatis tanpa mengubah database sumber.');
    if (!confirmed) {
        e.preventDefault();
        return false;
    }

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
    const confirmed = confirm('Mulai sinkronisasi data Mahasiswa langsung dari Database (db_student_usi tabel mhs)?\n\nCATATAN:\n• Data mahasiswa yang sudah ada di e-Library TIDAK AKAN DIHAPUS.\n• Sistem hanya menambahkan mahasiswa baru dan memperbarui profil jika ada perubahan.\n• Database sumber diakses secara READ-ONLY (aman tanpa perubahan pada server sumber).\n• Hasil pembaruan akan ditampilkan dalam popup tabel.');
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
        text.innerText = 'Menyinkronkan DB...';
    }
    return true;
}
</script>
@endsection