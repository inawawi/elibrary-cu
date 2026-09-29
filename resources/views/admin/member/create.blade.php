@extends('layouts.admin')

@section('title', 'Daftar Anggota Baru')
@section('header_title', 'Registrasi Anggota Perpustakaan')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.member.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-slate-800 dark:hover:text-white">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Kembali ke Daftar Anggota</span>
        </a>
    </div>

    <form action="{{ route('admin.member.store') }}" method="POST" enctype="multipart/form-data" class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-8 shadow-sm space-y-6">
        @csrf

        <div class="border-b border-slate-100 dark:border-slate-800 pb-4">
            <h2 class="text-lg font-black text-slate-900 dark:text-white">Biodata Anggota Baru</h2>
            <p class="text-xs text-slate-400">Lengkapi formulir untuk membuat kartu anggota baru</p>
        </div>

        <div class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">ID Anggota / NIM / NIP / NIDN *</label>
                    <input type="text" name="member_id" value="{{ old('member_id') }}" required placeholder="Contoh NIM: 12220099 atau NIP: 19850101..." class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-mono font-bold text-slate-900 dark:text-white focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Tipe Keanggotaan *</label>
                    <select name="member_type_id" required class="w-full px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none">
                        @foreach($memberTypes as $mt)
                            <option value="{{ $mt->member_type_id }}" {{ old('member_type_id') == $mt->member_type_id ? 'selected' : '' }}>
                                {{ $mt->member_type_name }} (Limit: {{ $mt->loan_limit }} buku, {{ $mt->loan_periode }} hari, {{ $mt->member_periode > 0 ? floor($mt->member_periode/365) . ' Thn' : 'Tanpa Masa Berlaku' }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Nama Lengkap & Gelar *</label>
                <input type="text" name="member_name" value="{{ old('member_name') }}" required placeholder="Masukkan nama lengkap (sertakan gelar untuk Dosen/Peneliti, misal: Dr. Budi, M.Kom)..." class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm font-medium text-slate-900 dark:text-white focus:outline-none">
                <span class="text-[10px] text-slate-400 mt-1 block">Untuk Dosen: Nama lengkap dan gelar akan otomatis terhubung ke sistem pembimbing skripsi mahasiswa.</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Jenis Kelamin *</label>
                    <select name="gender" required class="w-full px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none">
                        <option value="1">Laki-laki</option>
                        <option value="2">Perempuan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Tanggal Lahir</label>
                    <input type="date" name="birth_date" value="{{ old('birth_date') }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Alamat Email</label>
                    <input type="email" name="member_email" value="{{ old('member_email') }}" placeholder="mahasiswa@gmail.com" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Nomor Telepon / WhatsApp</label>
                    <input type="text" name="member_phone" value="{{ old('member_phone') }}" placeholder="08123456789" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Institusi / Fakultas / Prodi</label>
                <input type="text" name="inst_name" value="{{ old('inst_name', 'Universitas Siber Indonesia') }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Alamat Tinggal</label>
                <textarea name="member_address" rows="2" placeholder="Alamat lengkap..." class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none">{{ old('member_address') }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Kata Sandi Akun Anggota *</label>
                <input type="password" name="password" required placeholder="Minimal 6 karakter" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Foto Profil Anggota</label>
                <input type="file" name="member_image" accept="image/*" class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-brand-50 file:text-brand-700 dark:file:bg-sky-950 dark:file:text-sky-300 hover:file:bg-brand-100">
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100 dark:border-slate-800">
            <a href="{{ route('admin.member.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs hover:bg-slate-50 transition-colors">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs shadow-md shadow-brand-500/25 transition-all">
                Simpan Anggota
            </button>
        </div>
    </form>
</div>
@endsection
