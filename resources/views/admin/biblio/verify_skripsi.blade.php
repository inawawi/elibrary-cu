@extends('layouts.admin')

@section('title', 'Verifikasi Skripsi Mahasiswa & Bebas Pustaka')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1 text-xs font-semibold text-slate-500">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-brand-600">Dashboard</a>
                <span>/</span>
                <span>Katalog & Koleksi</span>
                <span>/</span>
                <span class="text-slate-700 dark:text-slate-300">Verifikasi Skripsi</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white flex items-center gap-3">
                <span class="p-2.5 rounded-2xl bg-purple-100 dark:bg-purple-950/80 text-purple-600 dark:text-purple-300 shadow-sm">
                    <i data-lucide="graduation-cap" class="w-6 h-6"></i>
                </span>
                <span>Verifikasi Skripsi & Bebas Pustaka</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">Review naskah skripsi mahasiswa calon wisudawan, kelengkapan lembar pengesahan, watermark, dan penerbitan Surat Bebas Pustaka.</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.skripsi.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors shadow-sm">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Input Skripsi Manual (Petugas)</span>
            </a>
        </div>
    </div>

    <!-- Alert Success / Info -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-900 dark:text-emerald-200 flex items-start gap-3 shadow-sm">
            <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600 dark:text-emerald-400 flex-shrink-0 mt-0.5"></i>
            <div class="text-sm font-semibold">{{ session('success') }}</div>
        </div>
    @endif

    @if(session('info'))
        <div class="p-4 rounded-2xl bg-sky-50 dark:bg-sky-950/40 border border-sky-200 dark:border-sky-800 text-sky-900 dark:text-sky-200 flex items-start gap-3 shadow-sm">
            <i data-lucide="info" class="w-5 h-5 text-sky-600 dark:text-sky-400 flex-shrink-0 mt-0.5"></i>
            <div class="text-sm font-semibold">{{ session('info') }}</div>
        </div>
    @endif

    <!-- Stat Badges & Filter Tabs -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-slate-900 p-4 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('admin.skripsi.verify', ['status' => 'all', 'search' => $search]) }}"
               class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $status === 'all' ? 'bg-brand-600 text-white shadow-md shadow-brand-500/20' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200' }}">
                Semua Pengajuan
            </a>
            <a href="{{ route('admin.skripsi.verify', ['status' => 'pending', 'search' => $search]) }}"
               class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 {{ $status === 'pending' ? 'bg-amber-500 text-white shadow-md shadow-amber-500/20' : 'bg-amber-50 dark:bg-amber-950/30 text-amber-700 dark:text-amber-300 hover:bg-amber-100' }}">
                <span>Menunggu Verifikasi</span>
                @if($pendingCount > 0)
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $status === 'pending' ? 'bg-white text-amber-600' : 'bg-amber-200 dark:bg-amber-900 text-amber-800 dark:text-amber-100' }}">
                        {{ $pendingCount }}
                    </span>
                @endif
            </a>
            <a href="{{ route('admin.skripsi.verify', ['status' => 'approved', 'search' => $search]) }}"
               class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 {{ $status === 'approved' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-500/20' : 'bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-300 hover:bg-emerald-100' }}">
                <span>Disetujui / Terbit Bebas Pustaka</span>
                @if($approvedCount > 0)
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $status === 'approved' ? 'bg-white text-emerald-600' : 'bg-emerald-200 dark:bg-emerald-900 text-emerald-800 dark:text-emerald-100' }}">
                        {{ $approvedCount }}
                    </span>
                @endif
            </a>
            <a href="{{ route('admin.skripsi.verify', ['status' => 'revision', 'search' => $search]) }}"
               class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $status === 'revision' ? 'bg-rose-600 text-white shadow-md shadow-rose-500/20' : 'bg-rose-50 dark:bg-rose-950/30 text-rose-700 dark:text-rose-300 hover:bg-rose-100' }}">
                Perlu Revisi
            </a>
        </div>

        <!-- Search Form -->
        <form action="{{ route('admin.skripsi.verify') }}" method="GET" class="flex items-center gap-2">
            <input type="hidden" name="status" value="{{ $status }}">
            <div class="relative w-full sm:w-64">
                <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari NIM / Nama / Judul..."
                       class="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-brand-500">
            </div>
            @if($search)
                <a href="{{ route('admin.skripsi.verify', ['status' => $status]) }}" class="p-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-500 hover:text-slate-700">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </a>
            @endif
        </form>
    </div>

    <!-- Theses List Table -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-400 uppercase font-bold text-[10px] tracking-wider border-b border-slate-100 dark:border-slate-800">
                    <tr>
                        <th class="py-4 px-6">Mahasiswa</th>
                        <th class="py-4 px-6">Judul Skripsi & Pembimbing</th>
                        <th class="py-4 px-6">Berkas PDF & Dokumen</th>
                        <th class="py-4 px-6 text-center">Status Sirkulasi</th>
                        <th class="py-4 px-6 text-center">Status Verifikasi</th>
                        <th class="py-4 px-6 text-right">Aksi Pustakawan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($theses as $item)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition-colors">
                            <!-- Mahasiswa -->
                            <td class="py-4 px-6">
                                <div class="font-bold text-slate-900 dark:text-white text-sm">
                                    {{ $item->student_name }}
                                </div>
                                <div class="font-mono text-xs font-semibold text-brand-600 dark:text-sky-400 mt-0.5">
                                    {{ $item->nim ?: '-' }}
                                </div>
                                <div class="text-[11px] text-slate-500 mt-0.5">
                                    {{ $item->prodi }} &bull; Semester {{ $item->semester }}
                                </div>
                            </td>

                            <!-- Judul & Pembimbing -->
                            <td class="py-4 px-6 max-w-sm">
                                <div class="font-bold text-slate-800 dark:text-slate-200 line-clamp-2 leading-relaxed">
                                    {{ $item->biblio->title }}
                                </div>
                                <div class="text-[11px] text-slate-500 mt-1 flex items-center gap-1.5">
                                    <i data-lucide="user-check" class="w-3.5 h-3.5 text-purple-500"></i>
                                    <span>Pembimbing: {{ $item->pembimbing_1 }}</span>
                                    @if($item->pembimbing_2)
                                        <span> / {{ $item->pembimbing_2 }}</span>
                                    @endif
                                </div>

                                <!-- Rekomendasi / Pilihan Subjek -->
                                @if(!empty($item->subjects))
                                    <div class="mt-2 flex flex-wrap items-center gap-1">
                                        <span class="text-[10px] text-slate-400 font-semibold flex items-center gap-0.5 mr-0.5">
                                            <i data-lucide="tags" class="w-3 h-3 text-purple-500"></i> Subjek:
                                        </span>
                                        @foreach($item->subjects as $sub)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-purple-50 dark:bg-purple-950/70 text-purple-700 dark:text-purple-300 text-[10px] font-semibold border border-purple-200 dark:border-purple-800">
                                                {{ $sub }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif

                                @if($item->notes_admin)
                                    <div class="mt-1.5 p-2 rounded-lg bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-900/60 text-[10px] text-amber-900 dark:text-amber-200 leading-tight">
                                        <strong>Catatan Admin:</strong> {{ $item->notes_admin }}
                                    </div>
                                @endif
                            </td>

                            <!-- Berkas PDF -->
                            <td class="py-4 px-6">
                                @if($item->file_url && $item->file_exists)
                                    <a href="{{ $item->file_url }}" target="_blank"
                                       class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-purple-50 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800 hover:bg-purple-100 font-bold text-xs transition-colors">
                                        <i data-lucide="file-text" class="w-4 h-4 text-purple-600"></i>
                                        <span>Lihat PDF Skripsi</span>
                                        <i data-lucide="external-link" class="w-3 h-3 opacity-60"></i>
                                    </a>
                                    <div class="text-[10px] text-slate-400 mt-1">
                                        {{ \Carbon\Carbon::parse($item->submitted_at)->format('d/m/Y H:i') }} WIB
                                    </div>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-500 text-[11px] font-semibold">
                                        <i data-lucide="file-x" class="w-3.5 h-3.5"></i>
                                        Tidak Ada File
                                    </span>
                                @endif
                            </td>

                            <!-- Status Sirkulasi -->
                            <td class="py-4 px-6 text-center">
                                @if($item->active_loans_count > 0)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-rose-100 dark:bg-rose-950 text-rose-700 dark:text-rose-300 font-bold text-[10px]">
                                        <i data-lucide="alert-circle" class="w-3 h-3"></i>
                                        {{ $item->active_loans_count }} Pinjaman Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 font-bold text-[10px]">
                                        <i data-lucide="check" class="w-3 h-3"></i>
                                        Bebas Pinjaman
                                    </span>
                                @endif
                            </td>

                            <!-- Status Verifikasi -->
                            <td class="py-4 px-6 text-center">
                                @if($item->status === 'approved')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 font-bold text-xs">
                                        <i data-lucide="badge-check" class="w-4 h-4"></i>
                                        Disetujui
                                    </span>
                                @elseif($item->status === 'revision')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-100 dark:bg-rose-950 text-rose-700 dark:text-rose-300 font-bold text-xs">
                                        <i data-lucide="alert-octagon" class="w-4 h-4"></i>
                                        Perlu Revisi
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-100 dark:bg-amber-950 text-amber-700 dark:text-amber-300 font-bold text-xs animate-pulse">
                                        <i data-lucide="clock" class="w-4 h-4"></i>
                                        Menunggu Review
                                    </span>
                                @endif
                            </td>

                            <!-- Action Buttons -->
                            <td class="py-4 px-6 text-right space-x-1 whitespace-nowrap">
                                <div class="inline-flex items-center gap-2">
                                    @if($item->status !== 'approved')
                                        <!-- Trigger Modal Setujui & Konfirmasi Subjek -->
                                        <button type="button"
                                                onclick="openApproveModal({{ $item->biblio->biblio_id }}, '{{ addslashes($item->student_name) }}', '{{ addslashes($item->nim) }}', '{{ addslashes($item->biblio->title) }}', {{ json_encode($item->subjects) }})"
                                                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm transition-all" title="Verifikasi & Setujui Skripsi">
                                            <i data-lucide="check" class="w-3.5 h-3.5"></i>
                                            <span>Setujui</span>
                                        </button>

                                        <!-- Button Trigger Modal Reject / Revisi -->
                                        <button type="button" onclick="openRevisionModal({{ $item->biblio->biblio_id }}, '{{ addslashes($item->student_name) }}')"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-rose-50 dark:bg-rose-950/60 hover:bg-rose-100 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-900 font-bold text-xs transition-all" title="Minta Revisi Dokumen">
                                            <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                                            <span>Revisi</span>
                                        </button>
                                    @else
                                        <div class="flex items-center gap-1.5 text-xs text-emerald-600 dark:text-emerald-400 font-bold">
                                            <i data-lucide="shield-check" class="w-4 h-4"></i>
                                            <span>Terverifikasi</span>
                                        </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                <i data-lucide="graduation-cap" class="w-12 h-12 mx-auto mb-3 opacity-30"></i>
                                <p class="text-sm font-semibold">Belum ada data pengajuan skripsi pada filter ini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($allTheses->hasPages())
            <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                {{ $allTheses->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Dialog Setujui & Konfirmasi Subjek Skripsi -->
<div id="approveModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-xl w-full p-6 sm:p-8 shadow-2xl border border-slate-200 dark:border-slate-800 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800 mb-4">
            <h3 class="font-black text-base text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="badge-check" class="w-5 h-5 text-emerald-500"></i>
                <span>Verifikasi & Setujui Skripsi</span>
            </h3>
            <button type="button" onclick="closeApproveModal()" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form id="approveForm" method="POST" class="space-y-4">
            @csrf
            <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/60 text-xs space-y-1">
                <div class="text-slate-400 text-[11px]">Mahasiswa:</div>
                <div class="font-bold text-slate-900 dark:text-white flex items-center justify-between">
                    <span id="approveStudentName">-</span>
                    <span id="approveStudentNim" class="font-mono text-brand-600 dark:text-sky-400">-</span>
                </div>
                <div class="text-slate-400 text-[11px] pt-1">Judul Skripsi:</div>
                <div id="approveThesisTitle" class="font-medium text-slate-800 dark:text-slate-200 leading-snug">
                    -
                </div>
            </div>

            <!-- Reviewer Subjects Selection -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5 flex items-center justify-between">
                    <span>Subjek / Bidang Ilmu (Bisa > 1)</span>
                    <span class="text-[10px] text-purple-600 dark:text-purple-400 font-normal">Rekomendasi otomatis dari judul</span>
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 p-3 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/40">
                    @foreach($reviewerTopics as $idx => $topicOption)
                        <label class="flex items-center gap-2 text-xs font-medium text-slate-800 dark:text-slate-200 cursor-pointer p-1.5 rounded-xl hover:bg-white dark:hover:bg-slate-700/50 transition-colors">
                            <input type="checkbox" name="subjects[]" value="{{ $topicOption }}" class="approve-subject-cb rounded border-slate-300 text-purple-600 focus:ring-purple-500">
                            <span class="leading-tight">{{ $topicOption }}</span>
                        </label>
                    @endforeach
                </div>
                <span class="text-[10px] text-slate-400 mt-1 block">Subjek telah dicentang otomatis berdasarkan deteksi kata kunci judul & prodi mahasiswa. Anda dapat menyesuaikannya bila perlu.</span>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                    Catatan Verifikasi Admin (Opsional)
                </label>
                <textarea name="notes_admin" rows="2"
                          placeholder="Dokumen dan lembar pengesahan terverifikasi lengkap & valid."
                          class="w-full px-3.5 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-medium text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 leading-relaxed">Dokumen dan lembar pengesahan terverifikasi lengkap & valid.</textarea>
            </div>

            <div class="p-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 text-[11px] text-emerald-900 dark:text-emerald-200 flex items-start gap-2">
                <i data-lucide="info" class="w-4 h-4 text-emerald-600 flex-shrink-0 mt-0.5"></i>
                <div class="leading-relaxed">
                    Setelah disetujui, nomor barcode dengan <strong>awalan 'S'</strong> (misal: S00336) otomatis dibuatkan untuk eksemplar perpustakaan dan <strong>Surat Keterangan Bebas Pustaka</strong> mahasiswa akan otomatis terbit.
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" onclick="closeApproveModal()"
                        class="px-4 py-2 rounded-xl border border-slate-300 dark:border-slate-700 text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-500/20 transition-all flex items-center gap-1.5">
                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                    <span>Setujui & Terbitkan Bebas Pustaka</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Dialog Revisi Skripsi -->
<div id="revisionModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-200 dark:border-slate-800">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800 mb-4">
            <h3 class="font-black text-base text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="alert-octagon" class="w-5 h-5 text-rose-500"></i>
                <span>Permintaan Revisi Dokumen Skripsi</span>
            </h3>
            <button type="button" onclick="closeRevisionModal()" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form id="revisionForm" method="POST" class="space-y-4">
            @csrf
            <div>
                <p class="text-xs text-slate-500 mb-3">
                    Berikan catatan perbaikan untuk mahasiswa <strong id="modalStudentName" class="text-slate-900 dark:text-white"></strong> agar dapat memperbaiki dokumennya:
                </p>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                    Catatan Perbaikan / Alasan Penolakan *
                </label>
                <textarea name="notes_admin" id="revisionNotes" rows="4" required
                          placeholder="Contoh: Lembar pengesahan belum terdapat tanda tangan penguji, atau halaman dokumen belum memuat watermark resmi kampus..."
                          class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-medium text-slate-900 dark:text-white focus:ring-2 focus:ring-brand-500 leading-relaxed"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" onclick="closeRevisionModal()"
                        class="px-4 py-2 rounded-xl border border-slate-300 dark:border-slate-700 text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-md shadow-rose-500/20 transition-all">
                    Kirim Permintaan Revisi
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openApproveModal(biblioId, studentName, nim, title, subjects) {
        const modal = document.getElementById('approveModal');
        const form = document.getElementById('approveForm');
        form.action = '/admin/skripsi/' + biblioId + '/approve';

        document.getElementById('approveStudentName').textContent = studentName;
        document.getElementById('approveStudentNim').textContent = nim || '-';
        document.getElementById('approveThesisTitle').textContent = title || '-';

        // Set checkboxes
        const subjectList = Array.isArray(subjects) ? subjects : [];
        document.querySelectorAll('.approve-subject-cb').forEach(cb => {
            cb.checked = subjectList.includes(cb.value);
        });

        modal.classList.remove('hidden');
    }

    function closeApproveModal() {
        document.getElementById('approveModal').classList.add('hidden');
    }

    function openRevisionModal(biblioId, studentName) {
        const modal = document.getElementById('revisionModal');
        const form = document.getElementById('revisionForm');
        const studentSpan = document.getElementById('modalStudentName');
        const notes = document.getElementById('revisionNotes');

        form.action = '/admin/skripsi/' + biblioId + '/reject';
        studentSpan.textContent = studentName;
        notes.value = '';
        modal.classList.remove('hidden');
    }

    function closeRevisionModal() {
        const modal = document.getElementById('revisionModal');
        modal.classList.add('hidden');
    }
</script>
@endsection
