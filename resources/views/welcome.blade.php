@php
    $userTheme = auth()->check() ? auth()->user()->theme_preference : null;
@endphp

<!DOCTYPE html>
<html lang="id" x-data="themeApp()" x-init="initTheme()" :class="{ 'dark': darkMode }" x-cloak>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-preference" content="{{ $userTheme ?? 'system' }}">
    <title>Aviona Sync - Sinkronisasi & Manajemen Akademik Otomatis</title>
    
    <!-- Meta SEO -->
    <meta name="description" content="Aviona Sync adalah platform terintegrasi untuk mahasiswa untuk menyelaraskan jadwal kuliah, tugas Google Classroom, dan agenda Google Calendar otomatis, lengkap dengan pengingat Telegram.">
    <meta name="keywords" content="aviona sync, sinkronisasi tugas, pengingat telegram, google classroom sync, jadwal kuliah, tracker akademik, manajemen tugas mahasiswa">
    <meta name="author" content="Pangestun Anton">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <script>
        (function() {
            var mode = localStorage.getItem('theme') || '{{ addslashes($userTheme ?? 'system') }}';
            if (mode === 'system') {
                mode = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            }
            if (mode === 'dark') {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>
    <style>
        [x-cloak] { display: none !important; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 antialiased transition-colors duration-200" x-data="{ mobileMenuOpen: false }">

    <!-- Background glow decoration -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none z-0">
        <div class="absolute -top-40 -left-40 w-96 h-96 rounded-full bg-pink-500/10 dark:bg-pink-500/15 blur-3xl"></div>
        <div class="absolute top-1/3 right-10 w-80 h-80 rounded-full bg-rose-500/5 dark:bg-rose-500/10 blur-3xl"></div>
        <div class="absolute bottom-10 left-1/4 w-96 h-96 rounded-full bg-blue-500/5 dark:bg-blue-500/10 blur-3xl"></div>
    </div>

    <!-- Header / Navbar -->
    <header :class="mobileMenuOpen ? 'bg-white dark:bg-slate-950 z-50 shadow-md' : 'bg-white/70 dark:bg-slate-950/70 backdrop-blur-md z-40'" class="border-b border-slate-200/60 dark:border-slate-800/80 sticky top-0 transition-all duration-200">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">
                <!-- Logo -->
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-white dark:bg-slate-800 p-1 shadow-md border border-slate-100 dark:border-slate-700/50">
                        <img src="{{ asset('images/logo.png') }}" alt="Aviona Sync Logo" class="h-full w-full object-contain rounded-xl">
                    </div>
                    <span class="text-xl font-extrabold tracking-tight bg-gradient-to-r from-pink-600 to-rose-500 bg-clip-text text-transparent dark:from-pink-400 dark:to-rose-400">
                        Aviona Sync
                    </span>
                </div>

                <!-- Navigation Links (Desktop) -->
                <nav class="hidden lg:flex items-center gap-8">
                    <a href="#tujuan" class="text-sm font-semibold text-slate-600 dark:text-slate-300 hover:text-pink-600 dark:hover:text-pink-400 transition-colors">Tujuan</a>
                    <a href="#manfaat" class="text-sm font-semibold text-slate-600 dark:text-slate-300 hover:text-pink-600 dark:hover:text-pink-400 transition-colors">Manfaat</a>
                    <a href="#fungsi" class="text-sm font-semibold text-slate-600 dark:text-slate-300 hover:text-pink-600 dark:hover:text-pink-400 transition-colors">Fitur</a>
                </nav>

                <!-- Actions and Theme Switcher (Desktop) -->
                <div class="hidden lg:flex items-center gap-4">
                    <!-- Theme Toggle -->
                    <div class="flex items-center gap-1 rounded-2xl bg-slate-100 dark:bg-slate-800/80 p-1 border border-slate-200/50 dark:border-slate-700/40">
                        <button @click="setTheme('light')" 
                                :class="themeMode === 'light' ? 'bg-white dark:bg-slate-700 text-pink-600 dark:text-pink-400 shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-300'"
                                class="p-1.5 rounded-xl transition-all duration-200" title="Light Mode">
                            <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m0 13.5V21M9.75 12h4.5m-4.5-.375H12m-3.75 3.75h4.5M12 9.75V12m0 0l-3-3m3 3l3-3m9.75 3c0 5.523-4.477 10-10 10S2 17.523 2 12 6.477 2 12 2s10 4.477 10 10zM12 5.25a6.75 6.75 0 100 13.5 6.75 6.75 0 000-13.5z" />
                            </svg>
                        </button>
                        <button @click="setTheme('dark')" 
                                :class="themeMode === 'dark' ? 'bg-white dark:bg-slate-700 text-pink-600 dark:text-pink-400 shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-300'"
                                class="p-1.5 rounded-xl transition-all duration-200" title="Dark Mode">
                            <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                            </svg>
                        </button>
                        <button @click="setTheme('system')" 
                                :class="themeMode === 'system' ? 'bg-white dark:bg-slate-700 text-pink-600 dark:text-pink-400 shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-300'"
                                class="p-1.5 rounded-xl transition-all duration-200" title="System Mode">
                            <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25" />
                            </svg>
                        </button>
                    </div>

                    @auth
                        <a href="{{ route('dashboard') }}" class="rounded-2xl bg-gradient-to-r from-pink-600 to-rose-500 hover:from-pink-700 hover:to-rose-600 text-white px-5 py-2.5 text-sm font-bold shadow-md transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-pink-500/50">
                            Ke Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-700 dark:text-slate-300 hover:text-pink-600 dark:hover:text-pink-400 transition-colors">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" class="rounded-2xl bg-gradient-to-r from-pink-600 to-rose-500 hover:from-pink-700 hover:to-rose-600 text-white px-5 py-2.5 text-sm font-bold shadow-md transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-pink-500/50">
                            Daftar Sekarang
                        </a>
                    @endauth
                </div>

                <!-- Mobile Menu Button -->
                <div class="flex items-center lg:hidden gap-3">
                    <!-- Theme Toggle for Mobile -->
                    <button @click="setTheme(themeMode === 'light' ? 'dark' : (themeMode === 'dark' ? 'system' : 'light'))" 
                            class="p-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition"
                            :title="'Tema: ' + themeMode">
                        <template x-if="themeMode === 'light'">
                            <svg class="h-5 w-5 text-pink-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m0 13.5V21M9.75 12h4.5m-4.5-.375H12m-3.75 3.75h4.5M12 9.75V12m0 0l-3-3m3 3l3-3m9.75 3c0 5.523-4.477 10-10 10S2 17.523 2 12 6.477 2 12 2s10 4.477 10 10zM12 5.25a6.75 6.75 0 100 13.5 6.75 6.75 0 000-13.5z" />
                            </svg>
                        </template>
                        <template x-if="themeMode === 'dark'">
                            <svg class="h-5 w-5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                            </svg>
                        </template>
                        <template x-if="themeMode === 'system'">
                            <svg class="h-5 w-5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25" />
                            </svg>
                        </template>
                    </button>
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="p-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition" id="mobile-menu-btn">
                        <svg x-show="!mobileMenuOpen" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg x-show="mobileMenuOpen" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div x-show="mobileMenuOpen" @click.away="mobileMenuOpen = false" 
             x-transition:enter="transition ease-out duration-150" 
             x-transition:enter-start="opacity-0 scale-95" 
             x-transition:enter-end="opacity-100 scale-100" 
             x-transition:leave="transition ease-in duration-100" 
             x-transition:leave-start="opacity-100 scale-100" 
             x-transition:leave-end="opacity-0 scale-95" 
             class="lg:hidden absolute top-16 inset-x-0 bg-white dark:bg-slate-950 border-b border-slate-200 dark:border-slate-800 p-4 space-y-3 shadow-xl z-50 transition-colors duration-200">
            <a href="#tujuan" @click="mobileMenuOpen = false" class="block rounded-xl px-3 py-2 text-base font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-pink-600 dark:hover:text-pink-400">Tujuan</a>
            <a href="#manfaat" @click="mobileMenuOpen = false" class="block rounded-xl px-3 py-2 text-base font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-pink-600 dark:hover:text-pink-400">Manfaat</a>
            <a href="#fungsi" @click="mobileMenuOpen = false" class="block rounded-xl px-3 py-2 text-base font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-pink-600 dark:hover:text-pink-400">Fitur</a>
            
            <div class="border-t border-slate-100 dark:border-slate-800 my-2 pt-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="flex w-full items-center justify-center rounded-xl bg-gradient-to-r from-pink-600 to-rose-500 text-white px-4 py-2.5 text-sm font-bold shadow-md">
                        Ke Dashboard
                    </a>
                @else
                    <div class="flex flex-col gap-2">
                        <a href="{{ route('login') }}" class="flex w-full items-center justify-center rounded-xl border border-slate-200 dark:border-slate-700 px-4 py-2.5 text-sm font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" class="flex w-full items-center justify-center rounded-xl bg-gradient-to-r from-pink-600 to-rose-500 text-white px-4 py-2.5 text-sm font-bold shadow-md">
                            Daftar Sekarang
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="relative z-10">

        <!-- Hero Section (Deskripsi Singkat) -->
        <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16 sm:py-24 md:py-32 text-center">
            <!-- Decorative badge -->
            <div class="mx-auto inline-flex items-center gap-1.5 rounded-full bg-pink-500/10 dark:bg-pink-500/20 px-3.5 py-1.5 text-xs font-bold text-pink-600 dark:text-pink-400 border border-pink-500/20 mb-8 tracking-wide uppercase">
                <svg class="h-3.5 w-3.5 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd" />
                </svg>
                Asisten Akademik Otomatis Anda
            </div>

            <!-- Heading -->
            <h1 class="text-4xl sm:text-5xl md:text-6xl font-extrabold tracking-tight text-slate-900 dark:text-white leading-none">
                Sinkronisasikan Kehidupan Akademik Anda dengan 
                <span class="bg-gradient-to-r from-pink-600 to-rose-500 bg-clip-text text-transparent dark:from-pink-400 dark:to-rose-400 block sm:inline mt-2">
                    Aviona Sync
                </span>
            </h1>

            <!-- Deskripsi Singkat -->
            <p class="mx-auto mt-6 max-w-2xl text-lg sm:text-xl text-slate-600 dark:text-slate-400 leading-relaxed">
                Aviona Sync adalah platform inovatif yang menyelaraskan jadwal kuliah, tugas Google Classroom, dan agenda Google Calendar Anda secara otomatis, lengkap dengan pengingat instan lewat Telegram. Didesain khusus untuk mahasiswa modern yang menginginkan produktivitas maksimal tanpa stres.
            </p>

            <!-- CTA Buttons -->
            <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
                @auth
                    <a href="{{ route('dashboard') }}" class="w-full sm:w-auto rounded-2xl bg-gradient-to-r from-pink-600 to-rose-500 hover:from-pink-700 hover:to-rose-600 text-white px-8 py-4 text-base font-bold shadow-lg shadow-pink-500/10 hover:shadow-pink-500/20 hover:-translate-y-0.5 transition-all duration-200">
                        Ke Dashboard Saya
                    </a>
                @else
                    <a href="{{ route('register') }}" class="w-full sm:w-auto rounded-2xl bg-gradient-to-r from-pink-600 to-rose-500 hover:from-pink-700 hover:to-rose-600 text-white px-8 py-4 text-base font-bold shadow-lg shadow-pink-500/10 hover:shadow-pink-500/20 hover:-translate-y-0.5 transition-all duration-200">
                        Mulai Sinkronisasi (Gratis)
                    </a>
                    <a href="#fungsi" class="w-full sm:w-auto rounded-2xl border border-slate-200 dark:border-slate-800 bg-white/50 dark:bg-slate-900/50 backdrop-blur-sm px-8 py-4 text-base font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 hover:-translate-y-0.5 transition-all duration-200">
                        Pelajari Fitur
                    </a>
                @endauth
            </div>

            <!-- App mockup element (styled CSS only) -->
            <div class="mt-16 sm:mt-20 relative max-w-5xl mx-auto rounded-3xl border border-slate-200 dark:border-slate-800 p-2 bg-slate-200/50 dark:bg-slate-900/40 shadow-2xl backdrop-blur-sm animate-fade-in-up">
                <div class="overflow-hidden rounded-2xl bg-white dark:bg-slate-900 aspect-auto min-h-[480px] md:aspect-[16/9] md:min-h-0 flex flex-col text-left">
                    <!-- Browser header simulation -->
                    <div class="h-10 bg-slate-100 dark:bg-slate-950 border-b border-slate-200 dark:border-slate-800 px-4 flex items-center gap-2">
                        <div class="flex gap-1.5">
                            <span class="w-3 h-3 rounded-full bg-rose-400"></span>
                            <span class="w-3 h-3 rounded-full bg-amber-400"></span>
                            <span class="w-3 h-3 rounded-full bg-emerald-400"></span>
                        </div>
                        <div class="mx-auto bg-white dark:bg-slate-900 text-[10px] text-slate-400 dark:text-slate-500 px-10 py-1.5 rounded-lg border border-slate-200/40 dark:border-slate-800/40 truncate w-64 text-center">
                            aviona-sync.test/dashboard
                        </div>
                    </div>
                    <!-- Dashboard Mock Content -->
                    <div class="p-4 sm:p-6 flex-1 grid grid-cols-1 md:grid-cols-3 gap-4 overflow-y-auto bg-gradient-to-br from-pink-50/10 to-slate-50/30 dark:from-slate-950/20 dark:to-slate-950/40">
                        <div class="md:col-span-2 space-y-4">
                            <!-- Cards row -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="p-4 rounded-2xl bg-white dark:bg-slate-800 shadow-sm border border-slate-100 dark:border-slate-800">
                                    <div class="text-[10px] uppercase font-bold text-slate-400 dark:text-slate-500">Tugas Google Classroom</div>
                                    <div class="text-xl font-bold mt-1 text-slate-800 dark:text-white">8 Aktif</div>
                                    <div class="text-[10px] text-emerald-500 font-semibold mt-1">✓ Sinkron otomatis terhubung</div>
                                </div>
                                <div class="p-4 rounded-2xl bg-white dark:bg-slate-800 shadow-sm border border-slate-100 dark:border-slate-800">
                                    <div class="text-[10px] uppercase font-bold text-slate-400 dark:text-slate-500">Indeks Prestasi Kumulatif</div>
                                    <div class="text-xl font-bold mt-1 text-slate-800 dark:text-white">3.85 / 4.00</div>
                                    <div class="text-[10px] text-pink-500 font-semibold mt-1">★ 0.15 naik semester ini</div>
                                </div>
                            </div>
                            <!-- Schedule list mockup -->
                            <div class="p-4 rounded-2xl bg-white dark:bg-slate-800 shadow-sm border border-slate-100 dark:border-slate-800">
                                <div class="flex justify-between items-center mb-3">
                                    <span class="text-xs font-bold text-slate-700 dark:text-slate-200">Jadwal Kuliah Hari Ini</span>
                                    <span class="text-[10px] text-pink-500 font-bold">Rabu</span>
                                </div>
                                <div class="space-y-2">
                                    <div class="p-2.5 rounded-xl bg-pink-50/50 dark:bg-pink-950/20 border-l-4 border-pink-500 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 text-xs">
                                        <div>
                                            <div class="font-bold text-slate-800 dark:text-white">Pemrograman Web Lanjut</div>
                                            <div class="text-[10px] text-slate-400 dark:text-slate-500">Ruang Lab 3 • 08:00 - 10:30</div>
                                        </div>
                                        <span class="px-2 py-0.5 rounded-full bg-pink-100 dark:bg-pink-900/50 text-[10px] text-pink-600 dark:text-pink-400 font-semibold inline-block shrink-0">Sedang Berlangsung</span>
                                    </div>
                                    <div class="p-2.5 rounded-xl bg-slate-50/50 dark:bg-slate-800/40 border-l-4 border-slate-300 dark:border-slate-700 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 text-xs">
                                        <div>
                                            <div class="font-bold text-slate-700 dark:text-slate-300">Kecerdasan Buatan (AI)</div>
                                            <div class="text-[10px] text-slate-400 dark:text-slate-500">Gedung D.201 • 13:00 - 15:30</div>
                                        </div>
                                        <span class="px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-700 text-[10px] text-slate-500 dark:text-slate-400 font-semibold inline-block shrink-0">Nanti Siang</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Right column mockup -->
                        <div class="space-y-4">
                            <!-- Telegram sync -->
                            <div class="p-4 rounded-2xl bg-white dark:bg-slate-800 shadow-sm border border-slate-100 dark:border-slate-800">
                                <div class="flex items-center gap-2 mb-3">
                                    <div class="h-6 w-6 rounded-lg bg-blue-100 dark:bg-blue-900/50 flex items-center justify-center text-blue-500">
                                        <svg class="h-4.5 w-4.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15.75-.85 4.31-1.21 6.24-.15.82-.46 1.1-.75 1.13-.64.06-1.13-.43-1.75-.83-.97-.63-1.52-1.02-2.46-1.64-1.09-.72-.38-1.12.24-1.76.16-.17 3-.2.56.09-.32-.38.16-.86.29-1.03.35-.45.05-1.52.28-2.22c.24.08-.2.27-.47.24-.76-.08-1.3-.23-1.84-.28-.54-.06-1.08.2-1.4.37-.32.18-.73.49-1.22.61.16-.76 1.48-4.43 1.83-5.59.35-1.16 1.05-1.58 1.9-1.58h.04c.82.01 1.48.43 1.97 1.08l1.37 2.14c.48.75 1.25.96 1.92.74.67-.22 1.2-.74 1.44-1.39l.26-.71c.07-.19.26-.33.47-.31.21.02.38.18.42.39.06.31.02.66-.09.98z"/></svg>
                                    </div>
                                    <span class="text-xs font-bold text-slate-700 dark:text-slate-200">Notifikasi Telegram</span>
                                </div>
                                <div class="text-[10px] text-slate-500 dark:text-slate-400 space-y-1.5">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        <span>Status: Terkoneksi</span>
                                    </div>
                                    <div class="p-2 rounded-lg bg-blue-50/50 dark:bg-blue-950/20 text-[9px] border border-blue-100/50 dark:border-blue-900/30 text-blue-600 dark:text-blue-400">
                                        📌 [Pengingat] Kuis Kecerdasan Buatan dikumpulkan dalam 4 jam!
                                    </div>
                                </div>
                            </div>
                            <!-- Next task countdown -->
                            <div class="p-4 rounded-2xl bg-gradient-to-r from-pink-500 to-rose-500 text-white shadow-md">
                                <div class="text-[9px] uppercase font-bold tracking-wider opacity-80">Tenggat Terdekat</div>
                                <div class="text-sm font-extrabold mt-1 truncate">Proyek Akhir Web Lanjut</div>
                                <div class="text-lg font-black mt-2">1 Hari : 04 Jam</div>
                                <div class="text-[9px] opacity-90 mt-1">Batas waktu: 25 Juni 2026, 23:59</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Tujuan Section -->
        <section id="tujuan" class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-20 sm:py-28 border-t border-slate-200/50 dark:border-slate-800/40 relative z-10 transition-colors duration-200">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20 items-center">
                <div>
                    <!-- Section Title -->
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white leading-tight">
                        Tujuan Utama Kami:
                        <span class="block bg-gradient-to-r from-pink-600 to-rose-500 bg-clip-text text-transparent dark:from-pink-400 dark:to-rose-400">
                            Menghilangkan Kerumitan Manajemen Akademis Anda
                        </span>
                    </h2>
                    
                    <!-- Content -->
                    <p class="mt-6 text-base sm:text-lg text-slate-600 dark:text-slate-400 leading-relaxed">
                        Kami menyadari bahwa menjadi mahasiswa saat ini sangat menuntut. Anda harus memantau jadwal kelas yang dinamis, tenggat tugas dari berbagai mata kuliah di Google Classroom, agenda pribadi di Google Calendar, hingga melacak performa nilai akademik.
                    </p>
                    <p class="mt-4 text-base sm:text-lg text-slate-600 dark:text-slate-400 leading-relaxed">
                        Tujuan dikembangkannya Aviona Sync adalah untuk menciptakan asisten akademis yang bekerja secara <strong>senyap namun andal</strong> di latar belakang. Dengan otomatisasi ini, Anda tidak perlu lagi menyalin jadwal secara manual atau terbiasa membuka Classroom berulang-ulang kali hanya untuk melihat apa saja tugas yang tersisa.
                    </p>

                    <!-- Quote or highlighted stat -->
                    <div class="mt-8 p-5 border-l-4 border-pink-500 rounded-r-2xl bg-pink-500/5 dark:bg-pink-500/10">
                        <p class="text-sm italic font-medium text-slate-700 dark:text-slate-300">
                            "Misi kami adalah menyederhanakan data akademik agar Anda memiliki waktu lebih banyak untuk belajar dan berkembang, bukan membuang waktu mengurus urusan administrasi."
                        </p>
                    </div>
                </div>

                <!-- Graphic/Visual container -->
                <div class="relative flex justify-center">
                    <div class="absolute inset-0 bg-gradient-to-tr from-pink-500/10 to-transparent dark:from-pink-500/20 rounded-3xl blur-2xl"></div>
                    <div class="relative w-full max-w-md p-6 rounded-3xl bg-white dark:bg-slate-900 shadow-xl border border-slate-100 dark:border-slate-800 space-y-6">
                        <div class="flex items-center gap-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-pink-100 dark:bg-pink-900/50 text-pink-600 dark:text-pink-400 font-bold">
                                01
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-900 dark:text-white">Sentralisasi Tugas</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Semua tugas tersinkronisasi dalam satu dashboard rapi.</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 font-bold">
                                02
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-900 dark:text-white">Otomatisasi Penuh</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Sinkronisasi terjadwal langsung ke Telegram & Google Calendar.</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-teal-100 dark:bg-teal-900/50 text-teal-600 dark:text-teal-400 font-bold">
                                03
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-900 dark:text-white">Peningkatan Fokus</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Fokus pada penyelesaian tugas, bukan mengingat tenggat waktu.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Manfaat Section -->
        <section id="manfaat" class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-20 sm:py-28 border-t border-slate-200/50 dark:border-slate-800/40 relative z-10 transition-colors duration-200">
            <div class="text-center">
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white">
                    Manfaat Luar Biasa Penggunaan Aviona Sync
                </h2>
                <p class="mx-auto mt-4 max-w-2xl text-base sm:text-lg text-slate-600 dark:text-slate-400">
                    Nikmati kemudahan mengorganisasi perkuliahan Anda dan raih kesuksesan akademik dengan manfaat utama berikut.
                </p>
            </div>

            <div class="mt-16 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Manfaat 1 -->
                <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 shadow-md border border-slate-100 dark:border-slate-800 hover:-translate-y-1 transition-all duration-200 flex flex-col justify-between">
                    <div>
                        <div class="h-12 w-12 rounded-2xl bg-rose-100 dark:bg-rose-900/50 text-rose-600 dark:text-rose-400 flex items-center justify-center mb-6">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Selalu Tepat Waktu</h3>
                        <p class="mt-3 text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                            Mencegah pengumpulan tugas yang terlambat berkat pengingat otomatis ke Telegram H-1 sebelum tugas dikumpulkan.
                        </p>
                    </div>
                </div>

                <!-- Manfaat 2 -->
                <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 shadow-md border border-slate-100 dark:border-slate-800 hover:-translate-y-1 transition-all duration-200 flex flex-col justify-between">
                    <div>
                        <div class="h-12 w-12 rounded-2xl bg-pink-100 dark:bg-pink-900/50 text-pink-600 dark:text-pink-400 flex items-center justify-center mb-6">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Terpusat di Satu Tempat</h3>
                        <p class="mt-3 text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                            Satukan semua tugas Google Classroom, jadwal kuliah, agenda luar kelas, dan nilai akademik Anda dalam satu dashboard terpadu.
                        </p>
                    </div>
                </div>

                <!-- Manfaat 3 -->
                <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 shadow-md border border-slate-100 dark:border-slate-800 hover:-translate-y-1 transition-all duration-200 flex flex-col justify-between">
                    <div>
                        <div class="h-12 w-12 rounded-2xl bg-teal-100 dark:bg-teal-900/50 text-teal-600 dark:text-teal-400 flex items-center justify-center mb-6">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Hemat Waktu & Energi</h3>
                        <p class="mt-3 text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                            Proses sinkronisasi berjalan otomatis di latar belakang. Anda tidak perlu membuang waktu untuk menyalin data akademik.
                        </p>
                    </div>
                </div>

                <!-- Manfaat 4 -->
                <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 shadow-md border border-slate-100 dark:border-slate-800 hover:-translate-y-1 transition-all duration-200 flex flex-col justify-between">
                    <div>
                        <div class="h-12 w-12 rounded-2xl bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center mb-6">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Analisis Perkembangan IP</h3>
                        <p class="mt-3 text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                            Evaluasi performa semester demi semester dengan grafik analitik yang memantau perkembangan nilai dan GPA Anda.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Fungsi Section -->
        <section id="fungsi" class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-20 sm:py-28 border-t border-slate-200/50 dark:border-slate-800/40 relative z-10 transition-colors duration-200">
            <div class="text-center">
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white">
                    Fungsi & Fitur Utama Aplikasi
                </h2>
                <p class="mx-auto mt-4 max-w-2xl text-base sm:text-lg text-slate-600 dark:text-slate-400">
                    Platform kami dirancang dengan fitur-fitur berkinerja tinggi untuk memberikan pengalaman pengorganisasian kuliah yang terbaik.
                </p>
            </div>

            <div class="mt-16 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Fitur 1 -->
                <div class="p-8 rounded-3xl bg-white dark:bg-slate-900 shadow-md border border-slate-100 dark:border-slate-800 hover:shadow-lg transition">
                    <div class="h-12 w-12 rounded-2xl bg-pink-100 dark:bg-pink-900/50 text-pink-600 dark:text-pink-400 flex items-center justify-center mb-6">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white">Sinkronisasi Google Classroom</h3>
                    <p class="mt-4 text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                        Menghubungkan akun Google Classroom Anda, lalu menarik semua data mata kuliah, kuis, tugas, materi, dan batas waktu pengumpulannya secara berkala.
                    </p>
                </div>

                <!-- Fitur 2 -->
                <div class="p-8 rounded-3xl bg-white dark:bg-slate-900 shadow-md border border-slate-100 dark:border-slate-800 hover:shadow-lg transition">
                    <div class="h-12 w-12 rounded-2xl bg-rose-100 dark:bg-rose-900/50 text-rose-600 dark:text-rose-400 flex items-center justify-center mb-6">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008H16.5v-.008zm0 2.25h.008v.008H16.5V15z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white">Integrasi Google Calendar</h3>
                    <p class="mt-4 text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                        Ekspor seluruh agenda tugas dan jadwal kegiatan Anda ke Google Calendar utama Anda dalam satu kali klik untuk visualisasi timeline belajar yang lebih baik.
                    </p>
                </div>

                <!-- Fitur 3 -->
                <div class="p-8 rounded-3xl bg-white dark:bg-slate-900 shadow-md border border-slate-100 dark:border-slate-800 hover:shadow-lg transition">
                    <div class="h-12 w-12 rounded-2xl bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400 flex items-center justify-center mb-6">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white">Notifikasi Pengingat Telegram</h3>
                    <p class="mt-4 text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                        Hubungkan akun Telegram dengan bot interaktif Aviona Sync untuk menerima siaran harian jadwal kuliah pagi hari dan notifikasi pengingat sebelum batas waktu tugas.
                    </p>
                </div>

                <!-- Fitur 4 -->
                <div class="p-8 rounded-3xl bg-white dark:bg-slate-900 shadow-md border border-slate-100 dark:border-slate-800 hover:shadow-lg transition">
                    <div class="h-12 w-12 rounded-2xl bg-teal-100 dark:bg-teal-900/50 text-teal-600 dark:text-teal-400 flex items-center justify-center mb-6">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white">Manajemen Jadwal Kuliah (Jadkul)</h3>
                    <p class="mt-4 text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                        Kelola jadwal kelas Anda setiap semesternya dengan praktis. Dilengkapi dengan pengatur ruangan, nama dosen, jam mulai, dan status kelas hari ini.
                    </p>
                </div>

                <!-- Fitur 5 -->
                <div class="p-8 rounded-3xl bg-white dark:bg-slate-900 shadow-md border border-slate-100 dark:border-slate-800 hover:shadow-lg transition">
                    <div class="h-12 w-12 rounded-2xl bg-purple-100 dark:bg-purple-900/50 text-purple-600 dark:text-purple-400 flex items-center justify-center mb-6">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 107.5 7.5h-7.5V6z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0013.5 3v7.5z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white">Tracker Akademik & Analisis</h3>
                    <p class="mt-4 text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                        Input nilai semester Anda untuk melacak pergerakan Indeks Prestasi (IP). Pantau performa Anda dengan visualisasi grafik performa dan ringkasan SKS.
                    </p>
                </div>

                <!-- Fitur 6 -->
                <div class="p-8 rounded-3xl bg-white dark:bg-slate-900 shadow-md border border-slate-100 dark:border-slate-800 hover:shadow-lg transition">
                    <div class="h-12 w-12 rounded-2xl bg-amber-100 dark:bg-amber-900/50 text-amber-600 dark:text-amber-400 flex items-center justify-center mb-6">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white">Jadwal Kegiatan Mandiri</h3>
                    <p class="mt-4 text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                        Tidak hanya tugas kuliah, Anda juga dapat menambahkan agenda mandiri seperti kepanitiaan, rapat organisasi, atau kegiatan sosial di luar tugas Classroom.
                    </p>
                </div>
            </div>
        </section>

        <!-- CTA Section Bottom -->
        <section class="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-20 text-center">
            <div class="rounded-3xl bg-gradient-to-br from-pink-600 to-rose-500 text-white p-8 sm:p-12 shadow-xl relative overflow-hidden">
                <!-- Background decoration in CTA -->
                <div class="absolute -right-10 -bottom-10 w-48 h-48 rounded-full bg-white/10 blur-xl pointer-events-none"></div>
                <div class="absolute -left-10 -top-10 w-48 h-48 rounded-full bg-white/10 blur-xl pointer-events-none"></div>
                
                <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight">Siap Untuk Belajar Lebih Teratur?</h2>
                <p class="mx-auto mt-4 max-w-xl text-sm sm:text-base opacity-90">
                    Hubungkan akun Google Classroom dan Telegram Anda hari ini. Ciptakan hidup mahasiswa yang lebih santai, terorganisasi, dan produktif.
                </p>
                <div class="mt-8 flex justify-center">
                    @auth
                        <a href="{{ route('dashboard') }}" class="rounded-2xl bg-white text-pink-600 hover:bg-slate-50 px-8 py-4 text-base font-bold shadow-md transition duration-200">
                            Masuk ke Dashboard
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="rounded-2xl bg-white text-pink-600 hover:bg-slate-50 px-8 py-4 text-base font-bold shadow-md transition duration-200">
                            Daftar Sekarang Secara Gratis
                        </a>
                    @endauth
                </div>
            </div>
        </section>

    </main>

    <!-- Footer -->
    <footer class="relative z-10 border-t border-slate-200/60 dark:border-slate-800/80 bg-white dark:bg-slate-950 py-12 transition-colors duration-200">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-3">
                <div class="flex h-8 w-8 items-center justify-center rounded-xl bg-white dark:bg-slate-800 p-1 shadow border border-slate-100 dark:border-slate-700/50">
                    <img src="{{ asset('images/logo.png') }}" alt="Aviona Sync Logo" class="h-full w-full object-contain rounded-lg">
                </div>
                <span class="text-base font-bold tracking-tight bg-gradient-to-r from-pink-600 to-rose-500 bg-clip-text text-transparent dark:from-pink-400 dark:to-rose-400">
                    Aviona Sync
                </span>
            </div>
            
            <p class="text-sm text-slate-500 dark:text-slate-400 text-center md:text-right">
                &copy; 2026 Pangestu Anton. Hak Cipta Dilindungi Undang-Undang.
            </p>
        </div>
    </footer>

</body>
</html>
