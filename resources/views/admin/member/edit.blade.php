@extends('layouts.admin')

@section('title', 'Ubah Anggota - ' . $member->member_name)
@section('header_title', 'Perbarui Data Anggota')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.member.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-slate-800 dark:hover:text-white">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Kembali ke Daftar Anggota</span>
        </a>
    </div>

    <form action="{{ route('admin.member.update', $member->member_id) }}" method="POST" enctype="multipart/form-data" class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-8 shadow-sm space-y-6">
        @csrf
        @method('PUT')

        <div class="border-b border-slate-100 dark:border-slate-800 pb-4 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-black text-slate-900 dark:text-white">Ubah Biodata Anggota</h2>
                <p class="text-xs text-slate-400">ID Anggota: {{ $member->member_id }}</p>
            </div>
            <a href="{{ route('admin.member.card', $member->member_id) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-indigo-50 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300 text-xs font-bold">
                <i data-lucide="id-card" class="w-3.5 h-3.5"></i>
                <span>Cetak Kartu</span>
            </a>
        </div>

        <div class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">ID Anggota / NIM</label>
                    <input type="text" value="{{ $member->member_id }}" disabled class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 text-xs font-mono font-bold text-slate-500 cursor-not-allowed">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Tipe Keanggotaan *</label>
                    <select name="member_type_id" required class="w-full px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none">
                        @foreach($memberTypes as $mt)
                            <option value="{{ $mt->member_type_id }}" {{ old('member_type_id', $member->member_type_id) == $mt->member_type_id ? 'selected' : '' }}>{{ $mt->member_type_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Nama Lengkap *</label>
                <input type="text" name="member_name" value="{{ old('member_name', $member->member_name) }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm font-medium text-slate-900 dark:text-white focus:outline-none">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Jenis Kelamin *</label>
                    <select name="gender" required class="w-full px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none">
                        <option value="1" {{ old('gender', $member->gender) == 1 ? 'selected' : '' }}>Laki-laki</option>
                        <option value="2" {{ old('gender', $member->gender) == 2 ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Tanggal Lahir</label>
                    <input type="date" name="birth_date" value="{{ old('birth_date', $member->birth_date) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Masa Berlaku Kartu</label>
                    <input type="date" name="expire_date" value="{{ old('expire_date', $member->expire_date) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Alamat Email</label>
                    <input type="email" name="member_email" value="{{ old('member_email', $member->member_email) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Nomor Telepon / WhatsApp</label>
                    <input type="text" name="member_phone" value="{{ old('member_phone', $member->member_phone) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Institusi / Fakultas / Prodi</label>
                <input type="text" name="inst_name" value="{{ old('inst_name', $member->inst_name) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Alamat Tinggal</label>
                <textarea name="member_address" rows="2" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none">{{ old('member_address', $member->member_address) }}</textarea>
            </div>

            <div class="bg-slate-50 dark:bg-slate-800/60 p-4 rounded-2xl border border-slate-200 dark:border-slate-700/60 space-y-3">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Ubah / Reset Kata Sandi</label>
                        <p class="text-[11px] text-slate-400 mt-0.5">Ketik kata sandi manual baru di bawah, atau klik tombol reset ke tanggal lahir.</p>
                    </div>
                    @if((int)$member->member_type_id === 1)
                        <button type="button" id="btn-edit-reset-pwd" class="px-3 py-1.5 rounded-xl bg-purple-50 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800 text-xs font-bold hover:bg-purple-100 transition-colors flex items-center gap-1.5 shadow-sm self-start sm:self-auto cursor-pointer">
                            <i data-lucide="key-round" class="w-3.5 h-3.5 pointer-events-none"></i>
                            <span>Reset ke Tanggal Lahir ({{ $member->birth_date ?: 'NIM' }})</span>
                        </button>
                    @endif
                </div>
                <input type="password" name="password" placeholder="Kosongkan jika tidak ingin mengubah sandi..." class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Foto Profil</label>
                <div class="flex items-center gap-4">
                    <img src="{{ $member->avatar_url }}" alt="Avatar" class="w-14 h-14 rounded-2xl object-cover border border-slate-200 dark:border-slate-700 shadow">
                    <div class="flex-grow">
                        <input type="file" name="member_image" accept="image/*" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100">
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100 dark:border-slate-800">
            <a href="{{ route('admin.member.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs hover:bg-slate-50 transition-colors">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs shadow-md shadow-brand-500/25 transition-all">
                Simpan Perubahan
            </button>
        </div>
    </form>
    @if((int)$member->member_type_id === 1)
        <form id="form-direct-reset-pwd" action="{{ route('admin.member.reset-password', $member->member_id) }}" method="POST" class="hidden">
            @csrf
        </form>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btnReset = document.getElementById('btn-edit-reset-pwd');
            const formReset = document.getElementById('form-direct-reset-pwd');
            if (btnReset && formReset) {
                btnReset.addEventListener('click', function(e) {
                    e.preventDefault();
                    const target = "{{ !empty($member->birth_date) ? \Carbon\Carbon::parse($member->birth_date)->format('Y-m-d') : 'NIM (' . $member->member_id . ')' }}";
                    if (confirm(`Reset kata sandi mahasiswa ini kembali ke ${target}?`)) {
                        btnReset.disabled = true;
                        formReset.submit();
                    }
                });
            }
        });
        </script>
    @endif
</div>
@endsection
