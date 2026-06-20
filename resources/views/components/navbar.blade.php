<nav x-data="{ open: false }" class="border-b border-blue-100/50 dark:border-slate-800 bg-white/90 dark:bg-slate-900/90 backdrop-blur transition-colors duration-200">
    <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-white p-1 shadow-sm border border-slate-100 dark:border-slate-800">
                <img src="{{ asset('images/logo.png') }}" alt="AS" class="h-full w-full object-contain rounded-xl">
            </div>
            <div>
                <div class="text-sm font-semibold text-slate-900 dark:text-white">Aviona Sync</div>
                <div class="text-xs text-slate-500 dark:text-slate-400">Pusat jadwal akademikmu</div>
            </div>
        </a>

        <div class="flex items-center gap-2">
            @auth
                <div class="hidden items-center gap-1 md:flex">
                    <a href="{{ route('dashboard') }}" class="rounded-2xl px-3 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 transition hover:bg-blue-50 dark:hover:bg-slate-800 hover:text-blue-700 dark:hover:text-pink-400">Dashboard</a>
                    <a href="{{ route('jadwal-kegiatan.index') }}" class="rounded-2xl px-3 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 transition hover:bg-blue-50 dark:hover:bg-slate-800 hover:text-blue-700 dark:hover:text-pink-400">Tugas</a>
                    <a href="{{ route('college-schedule.index') }}" class="rounded-2xl px-3 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 transition hover:bg-blue-50 dark:hover:bg-slate-800 hover:text-blue-700 dark:hover:text-pink-400">Jadkul</a>
                    <a href="{{ route('calendar.index') }}" class="rounded-2xl px-3 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 transition hover:bg-blue-50 dark:hover:bg-slate-800 hover:text-blue-700 dark:hover:text-pink-400">Kalender</a>
                    <a href="{{ route('academic-tracker.index') }}" class="rounded-2xl px-3 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 transition hover:bg-blue-50 dark:hover:bg-slate-800 hover:text-blue-700 dark:hover:text-pink-400">Akademik</a>
                    <div class="mx-1 h-5 w-px bg-slate-200 dark:bg-slate-700"></div>
                    <a href="{{ route('profile.edit') }}" class="rounded-2xl px-3 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 transition hover:bg-blue-50 dark:hover:bg-slate-800 hover:text-blue-700 dark:hover:text-pink-400">Profil</a>
                    <a href="{{ route('settings.index') }}" class="rounded-2xl px-3 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 transition hover:bg-blue-50 dark:hover:bg-slate-800 hover:text-blue-700 dark:hover:text-pink-400">Pengaturan</a>
                    
                    <button @click="darkMode = !darkMode" class="rounded-2xl p-2 text-slate-500 dark:text-slate-400 hover:bg-blue-50 dark:hover:bg-slate-800 hover:text-blue-700 dark:hover:text-pink-400 transition" aria-label="Toggle Dark Mode">
                        <svg x-show="darkMode" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-12.728l.707.707m12.728 12.728l.707.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
                        </svg>
                        <svg x-show="!darkMode" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                    </button>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-button variant="secondary" type="submit">Keluar</x-button>
                    </form>
                </div>
                
                <div class="flex items-center gap-2 md:hidden">
                    <button @click="darkMode = !darkMode" class="rounded-2xl p-2 text-slate-500 dark:text-slate-400 hover:bg-blue-50 dark:hover:bg-slate-800 hover:text-blue-700 dark:hover:text-pink-400 transition" aria-label="Toggle Dark Mode">
                        <svg x-show="darkMode" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-12.728l.707.707m12.728 12.728l.707.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
                        </svg>
                        <svg x-show="!darkMode" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                    </button>
                    <button @click="open = !open" class="rounded-2xl bg-blue-50 dark:bg-slate-800 p-2 text-blue-600 dark:text-slate-300 transition hover:bg-blue-100 dark:hover:bg-slate-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16" /></svg>
                    </button>
                </div>
            @else
                <button @click="darkMode = !darkMode" class="rounded-2xl p-2 text-slate-500 dark:text-slate-400 hover:bg-blue-50 dark:hover:bg-slate-800 hover:text-blue-700 dark:hover:text-pink-400 transition" aria-label="Toggle Dark Mode">
                    <svg x-show="darkMode" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-12.728l.707.707m12.728 12.728l.707.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
                    </svg>
                    <svg x-show="!darkMode" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                </button>
            @endauth
        </div>
    </div>

    @auth
        <div x-show="open" x-transition class="border-t border-blue-100/50 dark:border-slate-800 px-4 pb-4 md:hidden">
            <div class="space-y-2 pt-4">
                <a href="{{ route('dashboard') }}" class="block rounded-2xl px-3 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 transition hover:bg-blue-50 dark:hover:bg-slate-800 hover:text-blue-700 dark:hover:text-pink-400">Dashboard</a>
                <a href="{{ route('jadwal-kegiatan.index') }}" class="block rounded-2xl px-3 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 transition hover:bg-blue-50 dark:hover:bg-slate-800 hover:text-blue-700 dark:hover:text-pink-400">Tugas</a>
                <a href="{{ route('college-schedule.index') }}" class="block rounded-2xl px-3 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 transition hover:bg-blue-50 dark:hover:bg-slate-800 hover:text-blue-700 dark:hover:text-pink-400">Jadkul</a>
                <a href="{{ route('calendar.index') }}" class="block rounded-2xl px-3 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 transition hover:bg-blue-50 dark:hover:bg-slate-800 hover:text-blue-700 dark:hover:text-pink-400">Kalender</a>
                <a href="{{ route('academic-tracker.index') }}" class="block rounded-2xl px-3 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 transition hover:bg-blue-50 dark:hover:bg-slate-800 hover:text-blue-700 dark:hover:text-pink-400">Akademik</a>
                <a href="{{ route('profile.edit') }}" class="block rounded-2xl px-3 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 transition hover:bg-blue-50 dark:hover:bg-slate-800 hover:text-blue-700 dark:hover:text-pink-400">Profil</a>
                <a href="{{ route('settings.index') }}" class="block rounded-2xl px-3 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 transition hover:bg-blue-50 dark:hover:bg-slate-800 hover:text-blue-700 dark:hover:text-pink-400">Pengaturan</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full rounded-2xl bg-slate-100 dark:bg-slate-800 px-3 py-2.5 text-left text-sm font-medium text-slate-700 dark:text-slate-300 transition hover:bg-slate-200 dark:hover:bg-slate-700">Keluar</button>
                </form>
            </div>
        </div>
    @endauth
</nav>
