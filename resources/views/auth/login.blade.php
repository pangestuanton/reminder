<x-layouts.guest title="Masuk - Aviona Sync">
    <div class="w-full rounded-[2rem] bg-white p-6 shadow-card sm:p-8">
        <div class="mb-8 text-center">
            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-3xl bg-white p-1.5 shadow-[0_8px_30px_rgb(236,72,153,0.15)]">
                <img src="{{ asset('images/logo.png') }}" alt="Aviona Sync Logo" class="h-full w-full object-contain rounded-2xl">
            </div>
            <h1 class="mt-5 text-3xl font-bold tracking-tight text-slate-900">Aviona Sync</h1>
            <h2 class="mt-2 text-lg font-semibold text-slate-800">Masuk ke akunmu</h2>
            <p class="mt-2 text-sm leading-relaxed text-slate-500">Kelola jadwal kuliah, tenggat tugas, dan pengingat penting dalam satu dashboard yang rapi.</p>
        </div>

        <div class="space-y-4">
            <a href="{{ route('google.redirect') }}" class="flex w-full items-center justify-center gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-slate-100">
                <svg class="h-5 w-5" width="20" height="20" viewBox="0 0 24 24" style="width: 20px; height: 20px; flex-shrink: 0;">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                Masuk dengan Google
            </a>

            <div class="relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-slate-200"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="bg-white px-3 text-slate-400">atau masuk dengan email</span>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('login.store') }}" class="mt-6 space-y-5">
            @csrf

            <div>
                <label for="email" class="mb-2 block text-sm font-medium text-slate-700">Alamat Email</label>
                <x-input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus placeholder="nama@kampus.ac.id" />
                <x-validation-error name="email" />
            </div>

            <div>
                <label for="password" class="mb-2 block text-sm font-medium text-slate-700">Kata Sandi</label>
                <x-input id="password" name="password" type="password" required placeholder="Masukkan kata sandi" />
                <x-validation-error name="password" />
            </div>

            <label class="flex items-center gap-3 text-sm text-slate-600">
                <input type="checkbox" name="remember" class="h-4 w-4 rounded border-slate-300 text-pink-500 focus:ring-2 focus:ring-pink-200">
                Tetap masuk di perangkat ini
            </label>

            <x-button type="submit" class="w-full">Masuk</x-button>
        </form>

        <div class="mt-6 rounded-2xl bg-pink-50/50 px-4 py-3 text-center text-sm text-slate-500">
            Belum punya akun?
            <a href="{{ route('register') }}" class="font-semibold text-pink-600 transition hover:text-pink-700">Daftar sekarang</a>
        </div>
    </div>
</x-layouts.guest>
