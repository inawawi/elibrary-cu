@extends('layouts.admin')

@section('title', 'Manajemen Keanggotaan')
@section('header_title', 'Daftar Anggota Perpustakaan')

@section('content')
<div class="space-y-6">
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
        <form action="{{ route('admin.member.index') }}" method="GET" class="flex flex-wrap items-center gap-3 w-full sm:w-auto flex-grow max-w-2xl">
            <div class="relative flex-grow">
                <i data-lucide="search" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text"
                       name="search"
                       value="{{ $search }}"
                       placeholder="Cari ID Anggota, nama, email, atau no telepon..."
                       class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white font-medium focus:outline-none">
            </div>

            <select name="type_id" class="px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none">
                <option value="">Semua Tipe</option>
                @foreach($memberTypes as $mt)
                    <option value="{{ $mt->member_type_id }}" {{ $typeId == $mt->member_type_id ? 'selected' : '' }}>{{ $mt->member_type_name }}</option>
                @endforeach
            </select>

            <select name="status" class="px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none">
                <option value="">Semua Status</option>
                <option value="active" {{ ($status ?? '') == 'active' ? 'selected' : '' }}>Aktif</option>
                <option value="expired" {{ ($status ?? '') == 'expired' ? 'selected' : '' }}>Kedaluwarsa</option>
                <option value="inactive" {{ ($status ?? '') == 'inactive' ? 'selected' : '' }}>Non-Aktif</option>
            </select>

            <button type="submit" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-xl transition-colors">
                Filter
            </button>
            @if($search || $typeId || ($status ?? ''))
                <a href="{{ route('admin.member.index') }}" class="text-xs text-rose-500 hover:underline">Reset</a>
            @endif
        </form>

        <a href="{{ route('admin.member.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs shadow-md shadow-brand-500/25 transition-all flex-shrink-0">
            <i data-lucide="user-plus" class="w-4 h-4"></i>
            <span>Daftar Anggota Baru</span>
        </a>
    </div>

    <!-- Members Table -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-600 dark:text-slate-300 font-bold uppercase tracking-wider border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="py-4 px-6 w-14">Foto</th>
                        <th class="py-4 px-6">Identitas Anggota</th>
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
                                <div class="font-mono text-[11px] text-brand-600 dark:text-sky-400 font-bold">{{ $m->member_id }}</div>
                                <div class="text-[10px] text-slate-400">{{ $m->member_email ?: '-' }}</div>
                            </td>
                            <td class="py-3 px-6">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                    {{ $m->memberType?->member_type_name ?: 'Mahasiswa' }}
                                </span>
                            </td>
                            <td class="py-3 px-6">
                                <div class="text-xs">{{ $m->expire_date ? \Carbon\Carbon::parse($m->expire_date)->format('d/m/Y') : '-' }}</div>
                                <div class="mt-1 flex items-center gap-1.5 flex-wrap">
                                    @if($m->is_pending == 1)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700 dark:bg-rose-950/80 dark:text-rose-300">
                                            Non-Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-950/80 dark:text-emerald-300">
                                            Aktif
                                        </span>
                                    @endif
                                    @if($m->isExpired())
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700 dark:bg-amber-950/80 dark:text-amber-300" title="Masa berlaku kartu telah habis">
                                            Kedaluwarsa
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
                                    <a href="{{ route('admin.member.edit', $m->member_id) }}" class="p-2 rounded-lg bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 hover:bg-amber-100 transition-colors" title="Ubah">
                                        <i data-lucide="pencil" class="w-4 h-4"></i>
                                    </a>
                                    <form action="{{ route('admin.member.destroy', $m->member_id) }}" method="POST" onsubmit="return confirm('Hapus anggota ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 rounded-lg bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 hover:bg-rose-100 transition-colors" title="Hapus">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400">Tidak ada data anggota.</td>
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
@endsection
