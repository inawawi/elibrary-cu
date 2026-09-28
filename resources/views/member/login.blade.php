@extends('layouts.opac')

@section('title', 'Masuk Area Anggota')

@section('content')
<div class="min-h-[75vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full">
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-8 shadow-xl">
            <div class="text-center mb-8">
                <img src="{{ asset('images/logo.png') }}" alt="Logo Universitas Siber Indonesia" class="w-16 h-16 mx-auto mb-4 object-contain">
                <h1 class="text-2xl font-black text-slate-900 dark:text-white">Area Mandiri Anggota</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Masuk untuk melihat pinjaman buku & kartu anggota digital</p>
            </div>

            <form action="{{ route('member.login.post') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">ID Anggota / NIM</label>
                    <div class="relative">
                        <i data-lucide="badge-info" class="w-5 h-5 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text"
                               name="member_id"
                               value="{{ old('member_id') }}"
                               required
                               placeholder="Contoh: 12220001"
                               class="w-full pl-11 pr-4 py-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 font-medium"
                               autofocus>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Kata Sandi</label>
                    <div class="relative">
                        <i data-lucide="lock" class="w-5 h-5 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="password"
                               name="password"
                               required
                               placeholder="••••••••"
                               class="w-full pl-11 pr-4 py-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 font-medium">
                    </div>
                </div>

                <!-- Info Box -->
                <div class="p-3.5 rounded-2xl bg-sky-50 dark:bg-sky-950/60 border border-sky-200 dark:border-sky-800 text-xs">
                    <div class="font-bold text-sky-800 dark:text-sky-300 flex items-center gap-1.5 mb-1">
                        <i data-lucide="info" class="w-4 h-4 text-sky-500 flex-shrink-0"></i>
                        <span>Petunjuk Sandi Default:</span>
                    </div>
                    <p class="leading-relaxed text-slate-600 dark:text-slate-300 text-[11px]">
                        Kata sandi standar adalah <strong>NIM Anda</strong> (contoh: <code class="font-mono bg-white dark:bg-slate-800 px-1 py-0.5 rounded border border-slate-200 dark:border-slate-700 font-bold">12220001</code>) atau <strong>Tanggal Lahir</strong> (<code class="font-mono bg-white dark:bg-slate-800 px-1 py-0.5 rounded border border-slate-200 dark:border-slate-700">2004-08-13</code> / <code class="font-mono bg-white dark:bg-slate-800 px-1 py-0.5 rounded border border-slate-200 dark:border-slate-700">13082004</code>).
                    </p>
                </div>

                <button type="submit" class="w-full py-3.5 px-4 rounded-xl bg-gradient-to-r from-brand-600 to-sky-600 hover:from-brand-700 hover:to-sky-700 text-white font-bold text-sm shadow-lg shadow-brand-500/25 transition-all">
                    Masuk ke Akun
                </button>
            </form>

            <div class="mt-8 pt-6 border-t border-slate-100 dark:border-slate-800 text-center text-xs text-slate-500 dark:text-slate-400">
                <p>Belum memiliki akun atau lupa kata sandi?</p>
                <p class="mt-1">Silakan hubungi bagian sirkulasi di perpustakaan kampus.</p>
            </div>
        </div>
    </div>
</div>
@endsection
