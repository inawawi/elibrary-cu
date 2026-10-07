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

                <div x-data="{ showPass: false }">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Kata Sandi</label>
                    <div class="relative">
                        <i data-lucide="lock" class="w-5 h-5 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input :type="showPass ? 'text' : 'password'"
                               name="password"
                               required
                               placeholder="••••••••"
                               class="w-full pl-11 pr-11 py-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 font-medium">
                        <button type="button"
                                @mousedown="showPass = true"
                                @mouseup="showPass = false"
                                @mouseleave="showPass = false"
                                @touchstart.prevent="showPass = true"
                                @touchend="showPass = false"
                                @touchcancel="showPass = false"
                                tabindex="-1"
                                class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer focus:outline-none p-1 select-none transition-colors"
                                title="Klik dan tahan untuk melihat sandi">
                            <svg x-show="!showPass" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                            <svg x-show="showPass" style="display: none;" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-brand-600 dark:text-sky-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m15 18-.722-3.25"/>
                                <path d="M2 8a10.645 10.645 0 0 0 20 0"/>
                                <path d="m20 15-1.726-2.05"/>
                                <path d="m4 15 1.726-2.05"/>
                                <path d="m9 18 .722-3.25"/>
                            </svg>
                        </button>
                    </div>
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
