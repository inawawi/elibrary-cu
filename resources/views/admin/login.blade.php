@extends('layouts.opac')

@section('title', 'Login Staff Perpustakaan')

@section('content')
<div class="min-h-[75vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full">
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-8 shadow-xl">
            <div class="text-center mb-8">
                <img src="{{ asset('images/logo.png') }}" alt="Logo Universitas Siber Indonesia" class="w-16 h-16 mx-auto mb-4 object-contain">
                <h1 class="text-2xl font-black text-slate-900 dark:text-white">Portal Staf Perpustakaan</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Masuk untuk mengelola katalog, sirkulasi & data pustaka</p>
            </div>

            <form action="{{ route('admin.login.post') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Username / Email</label>
                    <div class="relative">
                        <i data-lucide="user" class="w-5 h-5 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text"
                               name="username"
                               value="{{ old('username') }}"
                               required
                               placeholder="AdminLib"
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

                <!-- Verifikasi Captcha Perhitungan (Operan 0 sampai 9) -->
                <div x-data="{
                    num1: {{ $captcha['num1'] ?? rand(0, 9) }},
                    num2: {{ $captcha['num2'] ?? rand(0, 9) }},
                    op: '{{ $captcha['operator'] ?? '+' }}',
                    loading: false,
                    refresh() {
                        this.loading = true;
                        fetch('{{ route('captcha.refresh', ['type' => 'admin']) }}')
                            .then(r => r.json())
                            .then(d => {
                                this.num1 = d.num1;
                                this.num2 = d.num2;
                                this.op = d.operator;
                                this.loading = false;
                            })
                            .catch(() => this.loading = false);
                    }
                }" class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/80">
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                            <i data-lucide="shield-check" class="w-4 h-4 text-brand-600 dark:text-sky-400"></i>
                            Hitung Nilai Captcha *
                        </label>
                        <button type="button" @click="refresh()" title="Ganti soal captcha" class="text-[11px] font-semibold text-brand-600 hover:text-brand-700 dark:text-sky-400 dark:hover:text-sky-300 flex items-center gap-1 cursor-pointer">
                            <i data-lucide="refresh-cw" class="w-3 h-3" :class="{ 'animate-spin': loading }"></i>
                            <span>Ganti Soal</span>
                        </button>
                    </div>

                    <div class="flex items-center gap-2.5">
                        <!-- Kotak Soal Operan 0 - 9 -->
                        <div class="flex items-center justify-center gap-2 px-3.5 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 font-mono font-black text-base select-none shadow-xs flex-shrink-0">
                            <span class="w-7 h-7 rounded-lg bg-sky-50 dark:bg-sky-950 text-sky-700 dark:text-sky-300 flex items-center justify-center border border-sky-200 dark:border-sky-800" x-text="num1"></span>
                            <span class="text-slate-500 font-bold" x-text="op"></span>
                            <span class="w-7 h-7 rounded-lg bg-sky-50 dark:bg-sky-950 text-sky-700 dark:text-sky-300 flex items-center justify-center border border-sky-200 dark:border-sky-800" x-text="num2"></span>
                            <span class="text-slate-400">=</span>
                        </div>
                        <!-- Input Jawaban -->
                        <div class="relative flex-1">
                            <input type="number"
                                   name="captcha"
                                   required
                                   min="0"
                                   max="18"
                                   placeholder="Hasil?"
                                   class="w-full px-3 py-2 rounded-xl border @error('captcha') border-rose-500 ring-1 ring-rose-500 @else border-slate-300 dark:border-slate-700 @enderror bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-base focus:outline-none focus:ring-2 focus:ring-brand-500 font-black text-center shadow-xs">
                        </div>
                    </div>
                    @error('captcha')
                        <p class="text-xs text-rose-500 font-semibold mt-1.5 flex items-center gap-1">
                            <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <button type="submit" class="w-full py-3.5 px-4 rounded-xl bg-gradient-to-r from-brand-600 to-indigo-600 hover:from-brand-700 hover:to-indigo-700 text-white font-bold text-sm shadow-lg shadow-brand-500/25 transition-all">
                    Masuk ke Sistem Administrasi
                </button>
            </form>

            <div class="mt-8 pt-6 border-t border-slate-100 dark:border-slate-800 text-center text-xs text-slate-400">
                Dilindungi dengan proteksi CSRF & Hash Bcrypt Modern
            </div>
        </div>
    </div>
</div>
@endsection
