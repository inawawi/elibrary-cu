<!DOCTYPE html>
<html lang="id" x-data="{ darkMode: localStorage.getItem('theme') === 'dark' }" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Katalog Digital') - Perpustakaan Universitas Siber Indonesia</title>
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
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        [x-cloak] { display: none !important; }
        .glass {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }
        .dark .glass {
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }
        .book-shadow {
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        }
        .book-shadow:hover {
            box-shadow: 0 20px 30px -10px rgba(2, 132, 199, 0.3), 0 10px 15px -5px rgba(0, 0, 0, 0.1);
        }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-800 dark:bg-slate-950 dark:text-slate-100 min-h-screen flex flex-col font-sans transition-colors duration-200">

    <!-- Navbar -->
    <header class="sticky top-0 z-50 glass border-b border-slate-200/80 dark:border-slate-800/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Brand / Logo -->
                <a href="{{ route('opac.index') }}" class="flex items-center gap-3 group">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo Universitas Siber Indonesia" class="w-12 h-12 object-contain group-hover:scale-105 transition-transform duration-200">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xl font-extrabold tracking-tight bg-gradient-to-r from-brand-600 via-sky-600 to-indigo-600 dark:from-sky-400 dark:to-indigo-400 bg-clip-text text-transparent">PERPUSTAKAAN</span>
                            <span class="px-2 py-0.5 text-xs font-bold rounded-full bg-brand-100 text-brand-700 dark:bg-sky-950/80 dark:text-sky-300 border border-brand-200 dark:border-sky-800">DIGITAL</span>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Universitas Siber Indonesia</p>
                    </div>
                </a>

                <!-- Desktop Navigation Links -->
                <nav class="hidden md:flex items-center gap-1">
                    <a href="{{ route('opac.index') }}" class="px-4 py-2 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('opac.index') ? 'bg-brand-50 text-brand-600 dark:bg-sky-950/60 dark:text-sky-400' : 'text-slate-600 hover:text-brand-600 dark:text-slate-300 dark:hover:text-white' }}">
                        Beranda
                    </a>
                    <a href="{{ route('opac.search') }}" class="px-4 py-2 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('opac.search') ? 'bg-brand-50 text-brand-600 dark:bg-sky-950/60 dark:text-sky-400' : 'text-slate-600 hover:text-brand-600 dark:text-slate-300 dark:hover:text-white' }}">
                        Katalog Buku
                    </a>
                    <a href="{{ route('opac.guestbook') }}" class="px-4 py-2 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('opac.guestbook') ? 'bg-brand-50 text-brand-600 dark:bg-sky-950/60 dark:text-sky-400' : 'text-slate-600 hover:text-brand-600 dark:text-slate-300 dark:hover:text-white' }}">
                        Buku Tamu
                    </a>
                </nav>

                <!-- Right Action Buttons -->
                <div class="flex items-center gap-3">
                    <!-- Theme Toggle -->
                    <button @click="darkMode = !darkMode; localStorage.setItem('theme', darkMode ? 'dark' : 'light')"
                            class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                            title="Ganti Tema">
                        <i x-show="!darkMode" data-lucide="moon" class="w-5 h-5"></i>
                        <i x-show="darkMode" data-lucide="sun" class="w-5 h-5" x-cloak></i>
                    </button>

                    <!-- Member Area Button -->
                    @if(Auth::guard('member')->check())
                        <a href="{{ route('member.dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-sm font-semibold transition-all border border-slate-200 dark:border-slate-700">
                            <img src="{{ Auth::guard('member')->user()->avatar_url }}" class="w-6 h-6 rounded-full object-cover">
                            <span class="hidden sm:inline max-w-[120px] truncate">{{ Auth::guard('member')->user()->member_name }}</span>
                        </a>
                    @else
                        <a href="{{ route('member.login') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-semibold transition-all shadow-sm">
                            <i data-lucide="user" class="w-4 h-4 text-brand-500"></i>
                            <span>Area Anggota</span>
                        </a>
                    @endif

                    <!-- Staff/Admin Link -->
                    @if(Auth::guard('web')->check())
                        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-brand-600 to-indigo-600 hover:from-brand-700 hover:to-indigo-700 text-white text-sm font-semibold shadow-md shadow-brand-500/20 transition-all">
                            <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                            <span class="hidden sm:inline">Admin Panel</span>
                        </a>
                    @else
                        <a href="{{ route('admin.login') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold shadow-md shadow-brand-500/20 transition-all">
                            <i data-lucide="shield" class="w-4 h-4"></i>
                            <span class="hidden sm:inline">Staff Perpustakaan</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </header>

    <!-- Flash Alerts -->
    @if(session('success') || session('error') || session('info') || $errors->any())
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">
            @if(session('success'))
                <div class="flex items-center gap-3 p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-sm font-medium">
                    <i data-lucide="check-circle" class="w-5 h-5 text-emerald-500 flex-shrink-0"></i>
                    <div>{{ session('success') }}</div>
                </div>
            @endif
            @if(session('error'))
                <div class="flex items-center gap-3 p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 text-sm font-medium">
                    <i data-lucide="alert-circle" class="w-5 h-5 text-rose-500 flex-shrink-0"></i>
                    <div>{{ session('error') }}</div>
                </div>
            @endif
            @if(session('info'))
                <div class="flex items-center gap-3 p-4 rounded-2xl bg-sky-50 dark:bg-sky-950/50 border border-sky-200 dark:border-sky-800 text-sky-800 dark:text-sky-300 text-sm font-medium">
                    <i data-lucide="info" class="w-5 h-5 text-sky-500 flex-shrink-0"></i>
                    <div>{{ session('info') }}</div>
                </div>
            @endif
            @if($errors->any())
                <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 text-sm">
                    <div class="font-semibold mb-1">Periksa kembali data Anda:</div>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    @endif

    <!-- Main Content -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="mt-20 border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/50 text-slate-600 dark:text-slate-400">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Col 1: About -->
                <div class="md:col-span-2">
                    <div class="flex items-center gap-3 mb-4">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo Universitas Siber Indonesia" class="w-10 h-10 object-contain">
                        <span class="text-lg font-bold text-slate-900 dark:text-white">PERPUSTAKAAN UNIVERSITAS SIBER INDONESIA</span>
                    </div>
                    <p class="text-sm leading-relaxed text-slate-500 dark:text-slate-400 mb-6 max-w-md">
                        Sistem informasi perpustakaan digital modern dengan katalog buku lengkap, sirkulasi terpadu, dan akses pustaka ilmiah untuk sivitas akademika Universitas Siber Indonesia.
                    </p>
                    <div class="flex items-center gap-3 text-xs text-slate-400 dark:text-slate-500">
                        <span class="inline-flex items-center gap-1.5"><i data-lucide="shield-check" class="w-4 h-4 text-emerald-500"></i> Laravel 13 Powered</span>
                        <span>•</span>
                        <span class="inline-flex items-center gap-1.5"><i data-lucide="lock" class="w-4 h-4 text-brand-500"></i> Secure & Encrypted</span>
                    </div>
                </div>

                <!-- Col 2: Navigation -->
                <div>
                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900 dark:text-white mb-4">Layanan Pustaka</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('opac.search') }}" class="hover:text-brand-600 dark:hover:text-sky-400 transition-colors">Pencarian Koleksi</a></li>
                        <li><a href="{{ route('opac.guestbook') }}" class="hover:text-brand-600 dark:hover:text-sky-400 transition-colors">Buku Tamu Pengunjung</a></li>
                        <li><a href="{{ route('member.login') }}" class="hover:text-brand-600 dark:hover:text-sky-400 transition-colors">Area Mandiri Anggota</a></li>
                        <li><a href="{{ route('admin.login') }}" class="hover:text-brand-600 dark:hover:text-sky-400 transition-colors">Portal Pustakawan</a></li>
                    </ul>
                </div>

                <!-- Col 3: Hours & Contact -->
                <div>
                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900 dark:text-white mb-4">Jam Operasional</h3>
                    <ul class="space-y-2 text-sm">
                        <li class="flex items-center justify-between"><span class="text-slate-500">Senin - Kamis:</span> <span class="font-medium text-slate-700 dark:text-slate-300">08.00 - 16.00</span></li>
                        <li class="flex items-center justify-between"><span class="text-slate-500">Jumat:</span> <span class="font-medium text-slate-700 dark:text-slate-300">08.00 - 16.30</span></li>
                        <li class="flex items-center justify-between"><span class="text-slate-500">Sabtu - Minggu:</span> <span class="text-rose-500 font-medium">Tutup</span></li>
                    </ul>
                </div>
            </div>

            <div class="mt-12 pt-8 border-t border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
                <p>&copy; {{ date('Y') }} Perpustakaan Universitas Siber Indonesia. Seluruh hak cipta dilindungi undang-undang.</p>
                <p>Dikembangkan dengan arsitektur aman dan antarmuka modern.</p>
            </div>
        </div>
    </footer>

    <script>
        lucide.createIcons();
    </script>
    @stack('scripts')
</body>
</html>
