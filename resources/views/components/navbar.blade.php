<nav x-data="{ open: false }" class="border-b border-pink-100/50 bg-white/90 backdrop-blur">
    <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-gradient-to-br from-pink-500 to-rose-500 text-sm font-bold text-white shadow-[0_4px_14px_rgb(236,72,153,0.35)]">AS</div>
            <div>
                <div class="text-sm font-semibold text-slate-900">Aviona Sync</div>
                <div class="text-xs text-slate-500">Pusat jadwal akademikmu</div>
            </div>
        </a>

        @auth
            <div class="hidden items-center gap-2 md:flex">
                <a href="{{ route('dashboard') }}" class="rounded-2xl px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-pink-50 hover:text-pink-700">Dashboard</a>
                <a href="{{ route('jadwal-kegiatan.index') }}" class="rounded-2xl px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-pink-50 hover:text-pink-700">Jadwal</a>
                <a href="{{ route('profile.edit') }}" class="rounded-2xl px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-pink-50 hover:text-pink-700">Profil</a>
                <div class="mx-1 h-5 w-px bg-slate-200"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-button variant="secondary" type="submit">Keluar</x-button>
                </form>
            </div>
            <button @click="open = !open" class="rounded-2xl bg-pink-50 p-2 text-pink-600 transition hover:bg-pink-100 md:hidden">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16" /></svg>
            </button>
        @endauth
    </div>

    @auth
        <div x-show="open" x-transition class="border-t border-pink-100/50 px-4 pb-4 md:hidden">
            <div class="space-y-2 pt-4">
                <a href="{{ route('dashboard') }}" class="block rounded-2xl px-3 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-pink-50 hover:text-pink-700">Dashboard</a>
                <a href="{{ route('jadwal-kegiatan.index') }}" class="block rounded-2xl px-3 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-pink-50 hover:text-pink-700">Jadwal</a>
                <a href="{{ route('profile.edit') }}" class="block rounded-2xl px-3 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-pink-50 hover:text-pink-700">Profil</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full rounded-2xl bg-slate-100 px-3 py-2.5 text-left text-sm font-medium text-slate-700 transition hover:bg-slate-200">Keluar</button>
                </form>
            </div>
        </div>
    @endauth
</nav>
