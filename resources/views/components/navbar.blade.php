<nav x-data="{ open: false }" class="border-b border-blue-100/50 bg-white/90 backdrop-blur">
    <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-white p-1 shadow-sm border border-slate-100">
                <img src="{{ asset('images/logo.png') }}" alt="AS" class="h-full w-full object-contain rounded-xl">
            </div>
            <div>
                <div class="text-sm font-semibold text-slate-900">Aviona Sync</div>
                <div class="text-xs text-slate-500">Pusat jadwal akademikmu</div>
            </div>
        </a>

        @auth
            <div class="hidden items-center gap-1 md:flex">
                <a href="{{ route('dashboard') }}" class="rounded-2xl px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-blue-50 hover:text-blue-700">Dashboard</a>
                <a href="{{ route('jadwal-kegiatan.index') }}" class="rounded-2xl px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-blue-50 hover:text-blue-700">Tugas</a>
                <a href="{{ route('college-schedule.index') }}" class="rounded-2xl px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-blue-50 hover:text-blue-700">Jadkul</a>
                <a href="{{ route('calendar.index') }}" class="rounded-2xl px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-blue-50 hover:text-blue-700">Kalender</a>
                <a href="{{ route('analytics.index') }}" class="rounded-2xl px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-blue-50 hover:text-blue-700">Analitik</a>
                <a href="{{ route('academic-tracker.index') }}" class="rounded-2xl px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-blue-50 hover:text-blue-700">Akademik</a>
                <a href="{{ route('integrations.index') }}" class="rounded-2xl px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-blue-50 hover:text-blue-700">Integrasi</a>
                <div class="mx-1 h-5 w-px bg-slate-200"></div>
                <a href="{{ route('profile.edit') }}" class="rounded-2xl px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-blue-50 hover:text-blue-700">Profil</a>
                <a href="{{ route('settings.index') }}" class="rounded-2xl px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-blue-50 hover:text-blue-700">Pengaturan</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-button variant="secondary" type="submit">Keluar</x-button>
                </form>
            </div>
            <button @click="open = !open" class="rounded-2xl bg-blue-50 p-2 text-blue-600 transition hover:bg-blue-100 md:hidden">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16" /></svg>
            </button>
        @endauth
    </div>

    @auth
        <div x-show="open" x-transition class="border-t border-blue-100/50 px-4 pb-4 md:hidden">
            <div class="space-y-2 pt-4">
                <a href="{{ route('dashboard') }}" class="block rounded-2xl px-3 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-blue-50 hover:text-blue-700">Dashboard</a>
                <a href="{{ route('jadwal-kegiatan.index') }}" class="block rounded-2xl px-3 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-blue-50 hover:text-blue-700">Tugas</a>
                <a href="{{ route('college-schedule.index') }}" class="block rounded-2xl px-3 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-blue-50 hover:text-blue-700">Jadkul</a>
                <a href="{{ route('calendar.index') }}" class="block rounded-2xl px-3 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-blue-50 hover:text-blue-700">Kalender</a>
                <a href="{{ route('analytics.index') }}" class="block rounded-2xl px-3 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-blue-50 hover:text-blue-700">Analitik</a>
                <a href="{{ route('academic-tracker.index') }}" class="block rounded-2xl px-3 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-blue-50 hover:text-blue-700">Akademik</a>
                <a href="{{ route('integrations.index') }}" class="block rounded-2xl px-3 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-blue-50 hover:text-blue-700">Integrasi</a>
                <a href="{{ route('profile.edit') }}" class="block rounded-2xl px-3 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-blue-50 hover:text-blue-700">Profil</a>
                <a href="{{ route('settings.index') }}" class="block rounded-2xl px-3 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-blue-50 hover:text-blue-700">Pengaturan</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full rounded-2xl bg-slate-100 px-3 py-2.5 text-left text-sm font-medium text-slate-700 transition hover:bg-slate-200">Keluar</button>
                </form>
            </div>
        </div>
    @endauth
</nav>
