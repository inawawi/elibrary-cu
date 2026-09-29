@extends('layouts.admin')

@section('title', 'Manajemen User Admin')
@section('header_title', 'Manajemen User Admin (Hak Akses Pengembang)')

@section('content')
<div class="space-y-6" x-data="{
    newModal: false,
    editModal: false,
    editUser: {
        id: '',
        username: '',
        realname: '',
        email: '',
        role: 'administrator'
    },
    openEdit(user) {
        this.editUser = {
            id: user.user_id,
            username: user.username,
            realname: user.realname,
            email: user.email,
            role: user.role || 'administrator'
        };
        this.editModal = true;
    }
}">

    <!-- Top Alert info for Developer -->
    <div class="p-4 rounded-3xl bg-indigo-50/80 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-800/80 flex items-start sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-indigo-600 text-white flex items-center justify-center flex-shrink-0 shadow-md shadow-indigo-500/25">
                <i data-lucide="shield-check" class="w-5 h-5"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-indigo-950 dark:text-indigo-200">Akses Khusus: Pengembang Sistem</h3>
                <p class="text-xs text-indigo-700 dark:text-indigo-300">
                    Anda memiliki hak akses penuh untuk membuat, memperbarui, mengubah peran (role), dan mengatur kata sandi seluruh administrator perpustakaan.
                </p>
            </div>
        </div>
        <span class="hidden sm:inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-indigo-100 dark:bg-indigo-900/60 text-indigo-800 dark:text-indigo-200 flex-shrink-0">
            Super Privilege
        </span>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <span class="text-slate-400 text-xs font-semibold block">Total User Admin</span>
            <div class="text-2xl font-black text-slate-900 dark:text-white mt-1">{{ $totalUsers }}</div>
            <span class="text-[11px] text-slate-500 mt-0.5 block">Akun terdaftar</span>
        </div>

        <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-indigo-200 dark:border-indigo-900/60 shadow-sm bg-gradient-to-br from-indigo-50/30 to-transparent">
            <span class="text-indigo-600 dark:text-indigo-400 text-xs font-bold block">Pengembang Sistem</span>
            <div class="text-2xl font-black text-indigo-600 dark:text-indigo-400 mt-1">{{ $developerCount }}</div>
            <span class="text-[11px] text-slate-500 mt-0.5 block">Akses kelola user</span>
        </div>

        <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-sky-200 dark:border-sky-900/60 shadow-sm bg-gradient-to-br from-sky-50/30 to-transparent">
            <span class="text-sky-600 dark:text-sky-400 text-xs font-bold block">Administrator</span>
            <div class="text-2xl font-black text-sky-600 dark:text-sky-400 mt-1">{{ $adminCount }}</div>
            <span class="text-[11px] text-slate-500 mt-0.5 block">Pustakawan utama</span>
        </div>

        <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-emerald-200 dark:border-emerald-900/60 shadow-sm bg-gradient-to-br from-emerald-50/30 to-transparent">
            <span class="text-emerald-600 dark:text-emerald-400 text-xs font-bold block">Staf Perpustakaan</span>
            <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ $staffCount }}</div>
            <span class="text-[11px] text-slate-500 mt-0.5 block">Layanan & sirkulasi</span>
        </div>
    </div>

    <!-- Filter & Action Card -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm flex flex-col lg:flex-row items-center justify-between gap-4">
        <form action="{{ route('admin.users.index') }}" method="GET" class="flex flex-wrap items-center gap-3 w-full lg:w-auto flex-grow max-w-2xl">
            <div class="relative flex-grow">
                <i data-lucide="search" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text"
                       name="search"
                       value="{{ $search }}"
                       placeholder="Cari nama, username, atau email admin..."
                       class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-brand-500">
            </div>

            <div class="w-48 flex-shrink-0">
                <select name="role" onchange="this.form.submit()" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-brand-500">
                    <option value="">Semua Role</option>
                    <option value="pengembang sistem" {{ $roleFilter === 'pengembang sistem' ? 'selected' : '' }}>Pengembang Sistem</option>
                    <option value="administrator" {{ $roleFilter === 'administrator' ? 'selected' : '' }}>Administrator</option>
                    <option value="staf" {{ $roleFilter === 'staf' ? 'selected' : '' }}>Staf Perpustakaan</option>
                </select>
            </div>

            <button type="submit" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-900 dark:bg-brand-600 dark:hover:bg-brand-700 text-white font-bold text-xs rounded-xl transition-colors">
                Cari
            </button>

            @if($search || $roleFilter)
                <a href="{{ route('admin.users.index') }}" class="text-xs text-rose-500 hover:underline">Reset</a>
            @endif
        </form>

        <button type="button" @click="newModal = true" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-500/25 transition-all flex-shrink-0 w-full lg:w-auto justify-center">
            <i data-lucide="user-plus" class="w-4 h-4"></i>
            <span>Tambah User Admin Baru</span>
        </button>
    </div>

    <!-- Users Table -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-600 dark:text-slate-300 font-bold uppercase tracking-wider border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="py-4 px-6">User Administrator</th>
                        <th class="py-4 px-6">Alamat Email</th>
                        <th class="py-4 px-6">Role & Hak Akses</th>
                        <th class="py-4 px-6">Tgl Terdaftar</th>
                        <th class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                    @forelse($users as $u)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors {{ (int)Auth::id() === (int)$u->user_id ? 'bg-indigo-50/30 dark:bg-indigo-950/20' : '' }}">
                            <td class="py-3 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-2xl {{ $u->isDeveloper() ? 'bg-indigo-600' : 'bg-brand-600' }} text-white flex items-center justify-center font-bold text-sm shadow-sm flex-shrink-0">
                                        {{ strtoupper(substr($u->realname ?: $u->username, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                            <span>{{ $u->realname ?: $u->username }}</span>
                                            @if((int)Auth::id() === (int)$u->user_id)
                                                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-indigo-100 dark:bg-indigo-900/60 text-indigo-700 dark:text-indigo-300">
                                                    Akun Anda
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-[11px] font-mono text-slate-400">@<span>{{ $u->username }}</span></div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-6 text-slate-600 dark:text-slate-300">
                                {{ $u->email ?: '-' }}
                            </td>
                            <td class="py-3 px-6">
                                @if($u->isDeveloper())
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-indigo-100 text-indigo-800 dark:bg-indigo-950/90 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                                        <i data-lucide="shield-check" class="w-3.5 h-3.5 text-indigo-600"></i>
                                        <span>Pengembang Sistem</span>
                                    </span>
                                @elseif(strtolower($u->role ?? '') === 'staf')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/90 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                        <i data-lucide="user" class="w-3.5 h-3.5 text-emerald-600"></i>
                                        <span>Staf Perpustakaan</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-sky-100 text-sky-800 dark:bg-sky-950/90 dark:text-sky-300 border border-sky-200 dark:border-sky-800">
                                        <i data-lucide="shield" class="w-3.5 h-3.5 text-sky-600"></i>
                                        <span>Administrator</span>
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-6 text-slate-500 text-[11px]">
                                <div>{{ $u->input_date ? \Carbon\Carbon::parse($u->input_date)->format('d/m/Y') : '-' }}</div>
                                @if($u->last_login)
                                    <div class="text-[10px] text-slate-400">Login: {{ \Carbon\Carbon::parse($u->last_login)->diffForHumans() }}</div>
                                @endif
                            </td>
                            <td class="py-3 px-6 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" @click="openEdit({{ json_encode($u) }})" class="p-2 rounded-lg bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 hover:bg-amber-100 transition-colors" title="Ubah Data / Password">
                                        <i data-lucide="pencil" class="w-4 h-4"></i>
                                    </button>

                                    @if((int)Auth::id() !== (int)$u->user_id)
                                        <form action="{{ route('admin.users.destroy', $u->user_id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user admin &quot;{{ $u->realname ?: $u->username }}&quot;?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 rounded-lg bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 hover:bg-rose-100 transition-colors" title="Hapus User">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    @else
                                        <button type="button" disabled class="p-2 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-300 dark:text-slate-600 cursor-not-allowed" title="Tidak dapat menghapus akun sendiri">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-slate-400">Tidak ada data user admin yang sesuai.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    <!-- MODAL: TAMBAH USER ADMIN BARU -->
    <div x-show="newModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" x-cloak>
        <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-800 relative max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800 mb-4 sticky top-0 bg-white dark:bg-slate-900 z-10">
                <h3 class="font-bold text-base text-slate-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="user-plus" class="w-5 h-5 text-indigo-600"></i>
                    Tambah User Admin Baru
                </h3>
                <button @click="newModal = false" class="text-slate-400 hover:text-slate-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Lengkap *</label>
                    <input type="text" name="realname" required placeholder="Contoh: Budi Santoso, M.Kom"
                        class="w-full px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 font-semibold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Username (Login) *</label>
                        <input type="text" name="username" required placeholder="contoh: budi_lib"
                            class="w-full px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 font-semibold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <span class="text-[10px] text-slate-400 mt-0.5 block">Hanya huruf, angka, dan garis bawah</span>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Role / Hak Akses *</label>
                        <select name="role" required class="w-full px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 font-semibold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="administrator">Administrator</option>
                            <option value="pengembang sistem">Pengembang Sistem</option>
                            <option value="staf">Staf Perpustakaan</option>
                        </select>
                        <span class="text-[10px] text-slate-400 mt-0.5 block">Khusus Pengembang dapat kelola data user admin</span>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Alamat Email *</label>
                    <input type="email" name="email" required placeholder="budi@cyber-univ.ac.id"
                        class="w-full px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 font-semibold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Kata Sandi Akun *</label>
                    <input type="password" name="password" required minlength="6" placeholder="Minimal 6 karakter"
                        class="w-full px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 font-semibold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-2">
                    <button type="button" @click="newModal = false" class="px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 font-semibold hover:bg-slate-50">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold shadow-md shadow-indigo-500/25">
                        Simpan User Admin
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: EDIT USER ADMIN -->
    <div x-show="editModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" x-cloak>
        <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-800 relative max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800 mb-4 sticky top-0 bg-white dark:bg-slate-900 z-10">
                <h3 class="font-bold text-base text-slate-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="user-check" class="w-5 h-5 text-indigo-600"></i>
                    Ubah Data & Hak Akses Admin
                </h3>
                <button @click="editModal = false" class="text-slate-400 hover:text-slate-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form :action="'{{ url('admin/users') }}/' + editUser.id" method="POST" class="space-y-4 text-xs">
                @csrf
                @method('PUT')

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Lengkap *</label>
                    <input type="text" name="realname" x-model="editUser.realname" required
                        class="w-full px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 font-semibold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Username *</label>
                        <input type="text" name="username" x-model="editUser.username" required
                            class="w-full px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 font-semibold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Role / Hak Akses *</label>
                        <select name="role" x-model="editUser.role" required class="w-full px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 font-semibold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="administrator">Administrator</option>
                            <option value="pengembang sistem">Pengembang Sistem</option>
                            <option value="staf">Staf Perpustakaan</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Alamat Email *</label>
                    <input type="email" name="email" x-model="editUser.email" required
                        class="w-full px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 font-semibold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div class="p-3.5 rounded-2xl bg-amber-50/70 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-900/60">
                    <label class="block font-bold text-amber-900 dark:text-amber-300 mb-1">Ganti Kata Sandi (Opsional)</label>
                    <input type="password" name="password" minlength="6" placeholder="Kosongkan jika tidak ingin mengubah password"
                        class="w-full px-3.5 py-2 rounded-xl bg-white dark:bg-slate-800 border border-amber-200 dark:border-slate-700 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <span class="text-[10px] text-amber-700 dark:text-amber-400 mt-1 block">Hanya isi field ini bila ingin mereset password akun user admin ini.</span>
                </div>

                <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-2">
                    <button type="button" @click="editModal = false" class="px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 font-semibold hover:bg-slate-50">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold shadow-md shadow-indigo-500/25">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
