<!DOCTYPE html>
<html lang="id" x-data="{ darkMode: localStorage.getItem('theme') === 'dark', sidebarOpen: false }" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') - Universitas Siber Indonesia</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0284c7',
                            600: '#0369a1',
                            700: '#075985',
                            800: '#0c4a6e',
                            900: '#082f49',
                        }
                    }
                }
            }
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        [x-cloak] { display: none !important; }

        @keyframes flashBounceIn {
            0% {
                opacity: 0;
                transform: translateY(-20px) scale(0.92);
            }
            50% {
                opacity: 1;
                transform: translateY(6px) scale(1.02);
            }
            75% {
                transform: translateY(-3px) scale(0.99);
            }
            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @keyframes flashBounceOut {
            0% {
                opacity: 1;
                transform: translateY(0) scale(1);
                max-height: 120px;
                margin-bottom: 0.75rem;
            }
            20% {
                opacity: 1;
                transform: translateY(-8px) scale(1.03);
            }
            40% {
                opacity: 0.95;
                transform: translateY(4px) scale(0.98);
            }
            65% {
                opacity: 0.7;
                transform: translateY(-12px) scale(0.94);
            }
            100% {
                opacity: 0;
                transform: translateY(-40px) scale(0.6);
                max-height: 0;
                margin-bottom: 0;
                padding-top: 0;
                padding-bottom: 0;
                overflow: hidden;
            }
        }

        @keyframes flashProgress {
            from { width: 100%; }
            to { width: 0%; }
        }

        .flash-bounce-in {
            animation: flashBounceIn 0.45s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
        }

        .flash-bounce-out {
            animation: flashBounceOut 0.6s cubic-bezier(0.6, -0.28, 0.735, 0.045) forwards !important;
            pointer-events: none;
        }

        .flash-timer-progress {
            animation: flashProgress 5s linear forwards;
        }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-100 text-slate-800 dark:bg-slate-950 dark:text-slate-100 min-h-screen font-sans flex transition-colors duration-200">

    <!-- Mobile Backdrop -->
    <div x-show="sidebarOpen"
         @click="sidebarOpen = false"
         class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-sm lg:hidden"
         x-cloak></div>

    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
           class="fixed lg:sticky top-0 left-0 z-50 h-screen w-72 bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 flex flex-col transition-transform duration-200 ease-in-out shadow-lg lg:shadow-none flex-shrink-0">

        <!-- Logo / Header -->
        <div class="h-20 flex items-center justify-between px-6 border-b border-slate-200 dark:border-slate-800">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                <img src="{{ asset('images/logo.png') }}" alt="Logo Universitas Siber Indonesia" class="w-10 h-10 object-contain">
                <div>
                    <span class="font-extrabold tracking-tight text-slate-900 dark:text-white text-base">ADMIN LIB</span>
                    <span class="block text-[10px] font-bold text-slate-400">Univ. Siber Indonesia</span>
                </div>
            </a>
            <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- Navigation Menu -->
        <div class="flex-grow overflow-y-auto px-4 py-6 space-y-6">
            <!-- Main -->
            <div>
                <span class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-2">Utama</span>
                <nav class="space-y-1">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-brand-50 text-brand-600 dark:bg-sky-950/80 dark:text-sky-300 font-bold' : 'text-slate-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200' }}">
                        <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                        <span>Dashboard</span>
                    </a>
                </nav>
            </div>

            <!-- Bibliography -->
            <div>
                <span class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-2">Katalog & Koleksi</span>
                <nav class="space-y-1">
                    <a href="{{ route('admin.biblio.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('admin.biblio.index') ? 'bg-brand-50 text-brand-600 dark:bg-sky-950/80 dark:text-sky-300 font-bold' : 'text-slate-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200' }}">
                        <i data-lucide="book-copy" class="w-4 h-4"></i>
                        <span>Katalog Buku</span>
                    </a>
                    <a href="{{ route('admin.biblio.create') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('admin.biblio.create') ? 'bg-brand-50 text-brand-600 dark:bg-sky-950/80 dark:text-sky-300 font-bold' : 'text-slate-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200' }}">
                        <i data-lucide="plus-circle" class="w-4 h-4"></i>
                        <span>Tambah Buku Baru</span>
                    </a>
                    <a href="{{ route('admin.skripsi.create') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('admin.skripsi.create') ? 'bg-brand-50 text-brand-600 dark:bg-sky-950/80 dark:text-sky-300 font-bold' : 'text-slate-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200' }}">
                        <i data-lucide="graduation-cap" class="w-4 h-4 text-purple-500"></i>
                        <span>Tambah Data Skripsi</span>
                    </a>
                    <a href="{{ route('admin.skripsi.verify') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('admin.skripsi.verify*') ? 'bg-brand-50 text-brand-600 dark:bg-sky-950/80 dark:text-sky-300 font-bold' : 'text-slate-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200' }}">
                        <div class="flex items-center gap-3">
                            <i data-lucide="file-check" class="w-4 h-4 text-purple-600 dark:text-purple-400"></i>
                            <span>Verifikasi Skripsi</span>
                        </div>
                        @php
                            $adminPendingCount = \App\Models\Biblio::where(function($q) {
                                $q->where('gmd_id', 262)->orWhere('spec_detail_info', 'like', '%"tipe":"Skripsi"%');
                            })->where('spec_detail_info', 'like', '%"status":"pending"%')->count();
                        @endphp
                        @if($adminPendingCount > 0)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-500 text-white">
                                {{ $adminPendingCount }}
                            </span>
                        @endif
                    </a>
                    <a href="{{ route('admin.ebook.create') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('admin.ebook.create') ? 'bg-brand-50 text-brand-600 dark:bg-sky-950/80 dark:text-sky-300 font-bold' : 'text-slate-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200' }}">
                        <i data-lucide="tablet" class="w-4 h-4 text-emerald-500"></i>
                        <span>Tambah Data e-Book</span>
                    </a>
                    <a href="{{ route('admin.jurnal.create') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('admin.jurnal.create') ? 'bg-brand-50 text-brand-600 dark:bg-sky-950/80 dark:text-sky-300 font-bold' : 'text-slate-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200' }}">
                        <i data-lucide="book-open-check" class="w-4 h-4 text-purple-600"></i>
                        <span>Tambah Data Jurnal</span>
                    </a>
                    <a href="{{ route('admin.biblio.print_labels') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('admin.biblio.print_labels') ? 'bg-brand-50 text-brand-600 dark:bg-sky-950/80 dark:text-sky-300 font-bold' : 'text-slate-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200' }}">
                        <i data-lucide="printer" class="w-4 h-4 text-sky-600"></i>
                        <span>Cetak Label & Barcode</span>
                    </a>
                </nav>
            </div>

            <!-- Circulation -->
            <div>
                <span class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-2">Sirkulasi & Layanan</span>
                <nav class="space-y-1">
                    <a href="{{ route('admin.circulation.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('admin.circulation.index') ? 'bg-brand-50 text-brand-600 dark:bg-sky-950/80 dark:text-sky-300 font-bold' : 'text-slate-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200' }}">
                        <i data-lucide="repeat" class="w-4 h-4"></i>
                        <span>Meja Sirkulasi</span>
                    </a>
                    <a href="{{ route('admin.circulation.active') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('admin.circulation.active') ? 'bg-brand-50 text-brand-600 dark:bg-sky-950/80 dark:text-sky-300 font-bold' : 'text-slate-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200' }}">
                        <i data-lucide="clock" class="w-4 h-4"></i>
                        <span>Pinjaman Aktif</span>
                    </a>
                    <a href="{{ route('admin.circulation.history') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('admin.circulation.history') ? 'bg-brand-50 text-brand-600 dark:bg-sky-950/80 dark:text-sky-300 font-bold' : 'text-slate-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200' }}">
                        <i data-lucide="history" class="w-4 h-4"></i>
                        <span>Riwayat Pengembalian</span>
                    </a>
                    <a href="{{ route('admin.guestbook.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('admin.guestbook*') ? 'bg-brand-50 text-brand-600 dark:bg-sky-950/80 dark:text-sky-300 font-bold' : 'text-slate-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200' }}">
                        <i data-lucide="book-open-check" class="w-4 h-4 text-amber-500"></i>
                        <span>Buku Tamu (Kunjungan)</span>
                    </a>
                </nav>
            </div>

            <!-- Members -->
            <div>
                <span class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-2">Keanggotaan</span>
                <nav class="space-y-1">
                    <a href="{{ route('admin.member.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('admin.member.index') ? 'bg-brand-50 text-brand-600 dark:bg-sky-950/80 dark:text-sky-300 font-bold' : 'text-slate-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200' }}">
                        <i data-lucide="users" class="w-4 h-4"></i>
                        <span>Daftar Anggota</span>
                    </a>
                    <a href="{{ route('admin.member.create') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('admin.member.create') ? 'bg-brand-50 text-brand-600 dark:bg-sky-950/80 dark:text-sky-300 font-bold' : 'text-slate-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200' }}">
                        <i data-lucide="user-plus" class="w-4 h-4"></i>
                        <span>Tambah Anggota</span>
                    </a>
                </nav>
            </div>

            <!-- Reports & Export Center -->
            <div>
                <span class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-2">Laporan & Ekspor</span>
                <nav class="space-y-1">
                    <a href="{{ route('admin.export.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('admin.export*') ? 'bg-brand-50 text-brand-600 dark:bg-sky-950/80 dark:text-sky-300 font-bold' : 'text-slate-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200' }}">
                        <i data-lucide="download-cloud" class="w-4 h-4 text-emerald-500"></i>
                        <span>Pusat Ekspor Data</span>
                    </a>
                </nav>
            </div>

            <!-- Master Data -->
            <div>
                <span class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-2">Master Data</span>
                <nav class="space-y-1">
                    <a href="{{ route('admin.master.authors') }}" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ request()->routeIs('admin.master.authors*') ? 'font-bold text-brand-600 dark:text-sky-400' : '' }}">
                        <i data-lucide="feather" class="w-3.5 h-3.5"></i>
                        <span>Pengarang</span>
                    </a>
                    <a href="{{ route('admin.master.publishers') }}" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ request()->routeIs('admin.master.publishers*') ? 'font-bold text-brand-600 dark:text-sky-400' : '' }}">
                        <i data-lucide="building" class="w-3.5 h-3.5"></i>
                        <span>Penerbit</span>
                    </a>
                    <a href="{{ route('admin.master.topics') }}" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ request()->routeIs('admin.master.topics*') ? 'font-bold text-brand-600 dark:text-sky-400' : '' }}">
                        <i data-lucide="tag" class="w-3.5 h-3.5"></i>
                        <span>Topik / Subjek</span>
                    </a>
                </nav>
            </div>

            <!-- Settings -->
            <div>
                <span class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-2">Konfigurasi</span>
                <nav class="space-y-1">
                    <a href="{{ route('admin.settings.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('admin.settings*') ? 'bg-brand-50 text-brand-600 dark:bg-sky-950/80 dark:text-sky-300 font-bold' : 'text-slate-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200' }}">
                        <i data-lucide="settings" class="w-4 h-4"></i>
                        <span>Pengaturan Perpustakaan</span>
                    </a>
                    @if(Auth::user()?->isDeveloper())
                    <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('admin.users*') ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-950/80 dark:text-indigo-300 font-bold' : 'text-slate-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200' }}">
                        <i data-lucide="shield-check" class="w-4 h-4 text-indigo-500"></i>
                        <span>Manajemen User Admin</span>
                    </a>
                    @endif
                </nav>
            </div>

            <!-- Link to OPAC -->
            <div class="pt-4 border-t border-slate-200 dark:border-slate-800">
                <a href="{{ route('opac.index') }}" target="_blank" class="flex items-center gap-2.5 px-3 py-2 text-xs font-bold text-brand-600 dark:text-sky-400 hover:underline">
                    <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                    <span>Buka OPAC Publik</span>
                </a>
            </div>
        </div>

        <!-- Current User Profile & Logout -->
        <div class="p-4 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-9 h-9 rounded-xl {{ Auth::user()?->isDeveloper() ? 'bg-indigo-600' : 'bg-brand-600' }} text-white flex items-center justify-center font-bold text-sm flex-shrink-0 shadow-sm">
                    {{ strtoupper(substr(Auth::user()?->realname ?: Auth::user()?->username ?: 'Admin', 0, 1)) }}
                </div>
                <div class="truncate">
                    <div class="text-xs font-bold text-slate-900 dark:text-white truncate">
                        {{ Auth::user()?->realname ?: Auth::user()?->username ?: 'Administrator' }}
                    </div>
                    <div class="text-[10px] {{ Auth::user()?->isDeveloper() ? 'text-indigo-600 dark:text-indigo-400 font-bold' : 'text-slate-400' }}">
                        {{ Auth::user()?->role_name ?: 'Administrator' }}
                    </div>
                </div>
            </div>

            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button type="submit" class="p-2 text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/50 rounded-lg transition-colors" title="Keluar">
                    <i data-lucide="log-out" class="w-4 h-4"></i>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-grow flex flex-col min-w-0">
        <!-- Top Navbar -->
        <header class="h-20 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 px-6 flex items-center justify-between sticky top-0 z-30">
            <div class="flex items-center gap-4">
                <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800">
                    <i data-lucide="menu" class="w-5 h-5"></i>
                </button>
                <div class="text-sm font-extrabold text-slate-900 dark:text-white">
                    @yield('header_title', 'Sistem Manajemen Perpustakaan')
                </div>
            </div>

            <div class="flex items-center gap-3">
                <!-- Theme Toggle -->
                <button @click="darkMode = !darkMode; localStorage.setItem('theme', darkMode ? 'dark' : 'light')"
                        class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                        title="Ganti Tema">
                    <i x-show="!darkMode" data-lucide="moon" class="w-4 h-4"></i>
                    <i x-show="darkMode" data-lucide="sun" class="w-4 h-4" x-cloak></i>
                </button>

                <a href="{{ route('opac.index') }}" class="px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-xs font-semibold transition-colors flex items-center gap-1.5">
                    <i data-lucide="globe" class="w-3.5 h-3.5"></i>
                    <span class="hidden sm:inline">Lihat Web OPAC</span>
                </a>
            </div>
        </header>

        <!-- Flash Messages -->
        @if(session('success') || session('error') || session('info') || (isset($errors) && $errors->any()))
            <div class="px-6 pt-6 space-y-3">
                @if(session('success'))
                    <div x-data="{
                            show: true,
                            isExiting: false,
                            timer: null,
                            duration: 5000,
                            startTimer() {
                                this.timer = setTimeout(() => this.dismiss(), this.duration);
                            },
                            stopTimer() {
                                if (this.timer) clearTimeout(this.timer);
                            },
                            dismiss() {
                                if (this.isExiting) return;
                                this.isExiting = true;
                                setTimeout(() => { this.show = false; }, 580);
                            }
                        }"
                        x-init="startTimer()"
                        @mouseenter="stopTimer()"
                        @mouseleave="startTimer()"
                        x-show="show"
                        :class="isExiting ? 'flash-bounce-out' : 'flash-bounce-in'"
                        class="relative overflow-hidden flex items-center justify-between gap-3 p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/70 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs font-semibold shadow-sm">
                        <div class="flex items-center gap-3">
                            <i data-lucide="check-circle" class="w-4 h-4 text-emerald-500 flex-shrink-0"></i>
                            <div>{{ session('success') }}</div>
                        </div>
                        <button type="button" @click="dismiss()" class="text-emerald-500 hover:text-emerald-700 dark:hover:text-emerald-200 p-1 rounded-lg hover:bg-emerald-100/50 dark:hover:bg-emerald-900/50 transition-colors flex-shrink-0" title="Tutup">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                        <div class="absolute bottom-0 left-0 right-0 h-1 bg-emerald-200/40 dark:bg-emerald-800/40 overflow-hidden">
                            <div class="h-full bg-emerald-500 flash-timer-progress"></div>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div x-data="{
                            show: true,
                            isExiting: false,
                            timer: null,
                            duration: 6000,
                            startTimer() {
                                this.timer = setTimeout(() => this.dismiss(), this.duration);
                            },
                            stopTimer() {
                                if (this.timer) clearTimeout(this.timer);
                            },
                            dismiss() {
                                if (this.isExiting) return;
                                this.isExiting = true;
                                setTimeout(() => { this.show = false; }, 580);
                            }
                        }"
                        x-init="startTimer()"
                        @mouseenter="stopTimer()"
                        @mouseleave="startTimer()"
                        x-show="show"
                        :class="isExiting ? 'flash-bounce-out' : 'flash-bounce-in'"
                        class="relative overflow-hidden flex items-center justify-between gap-3 p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/70 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 text-xs font-semibold shadow-sm">
                        <div class="flex items-center gap-3">
                            <i data-lucide="alert-circle" class="w-4 h-4 text-rose-500 flex-shrink-0"></i>
                            <div>{{ session('error') }}</div>
                        </div>
                        <button type="button" @click="dismiss()" class="text-rose-500 hover:text-rose-700 dark:hover:text-rose-200 p-1 rounded-lg hover:bg-rose-100/50 dark:hover:bg-rose-900/50 transition-colors flex-shrink-0" title="Tutup">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                        <div class="absolute bottom-0 left-0 right-0 h-1 bg-rose-200/40 dark:bg-rose-800/40 overflow-hidden">
                            <div class="h-full bg-rose-500 flash-timer-progress" style="animation-duration: 6s;"></div>
                        </div>
                    </div>
                @endif

                @if(session('info'))
                    <div x-data="{
                            show: true,
                            isExiting: false,
                            timer: null,
                            duration: 5000,
                            startTimer() {
                                this.timer = setTimeout(() => this.dismiss(), this.duration);
                            },
                            stopTimer() {
                                if (this.timer) clearTimeout(this.timer);
                            },
                            dismiss() {
                                if (this.isExiting) return;
                                this.isExiting = true;
                                setTimeout(() => { this.show = false; }, 580);
                            }
                        }"
                        x-init="startTimer()"
                        @mouseenter="stopTimer()"
                        @mouseleave="startTimer()"
                        x-show="show"
                        :class="isExiting ? 'flash-bounce-out' : 'flash-bounce-in'"
                        class="relative overflow-hidden flex items-center justify-between gap-3 p-4 rounded-2xl bg-sky-50 dark:bg-sky-950/70 border border-sky-200 dark:border-sky-800 text-sky-800 dark:text-sky-300 text-xs font-semibold shadow-sm">
                        <div class="flex items-center gap-3">
                            <i data-lucide="info" class="w-4 h-4 text-sky-500 flex-shrink-0"></i>
                            <div>{{ session('info') }}</div>
                        </div>
                        <button type="button" @click="dismiss()" class="text-sky-500 hover:text-sky-700 dark:hover:text-sky-200 p-1 rounded-lg hover:bg-sky-100/50 dark:hover:bg-sky-900/50 transition-colors flex-shrink-0" title="Tutup">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                        <div class="absolute bottom-0 left-0 right-0 h-1 bg-sky-200/40 dark:bg-sky-800/40 overflow-hidden">
                            <div class="h-full bg-sky-500 flash-timer-progress"></div>
                        </div>
                    </div>
                @endif

                @if(isset($errors) && $errors->any())
                    <div x-data="{
                            show: true,
                            isExiting: false,
                            timer: null,
                            duration: 7000,
                            startTimer() {
                                this.timer = setTimeout(() => this.dismiss(), this.duration);
                            },
                            stopTimer() {
                                if (this.timer) clearTimeout(this.timer);
                            },
                            dismiss() {
                                if (this.isExiting) return;
                                this.isExiting = true;
                                setTimeout(() => { this.show = false; }, 580);
                            }
                        }"
                        x-init="startTimer()"
                        @mouseenter="stopTimer()"
                        @mouseleave="startTimer()"
                        x-show="show"
                        :class="isExiting ? 'flash-bounce-out' : 'flash-bounce-in'"
                        class="relative overflow-hidden p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/70 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 text-xs shadow-sm">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <div class="font-bold mb-1 flex items-center gap-1.5">
                                    <i data-lucide="alert-triangle" class="w-4 h-4 text-rose-500"></i>
                                    Periksa kembali input Anda:
                                </div>
                                <ul class="list-disc list-inside space-y-0.5">
                                    @foreach($errors->all() as $err)
                                        <li>{{ $err }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            <button type="button" @click="dismiss()" class="text-rose-500 hover:text-rose-700 dark:hover:text-rose-200 p-1 rounded-lg hover:bg-rose-100/50 dark:hover:bg-rose-900/50 transition-colors flex-shrink-0" title="Tutup">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                        <div class="absolute bottom-0 left-0 right-0 h-1 bg-rose-200/40 dark:bg-rose-800/40 overflow-hidden">
                            <div class="h-full bg-rose-500 flash-timer-progress" style="animation-duration: 7s;"></div>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <!-- Page Content -->
        <main class="p-6 flex-grow">
            @yield('content')
        </main>
    </div>

    <script>
        lucide.createIcons();
    </script>
    @stack('scripts')
</body>
</html>
