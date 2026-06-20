<x-layouts.app title="Profil - Aviona Sync">
    <div class="max-w-2xl space-y-6">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Profil</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Kelola informasi pribadi dan integrasi akun Anda.</p>
        </div>

        {{-- Profile Avatar Card (Coming Soon) --}}
        <x-card>
            <div class="flex flex-col items-center gap-4 sm:flex-row">
                <div class="relative group">
                    <div class="flex h-20 w-20 items-center justify-center rounded-full bg-slate-100 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-slate-400 dark:text-slate-300 shadow-sm relative overflow-hidden">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-10 w-10">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                    </div>
                    <div class="absolute inset-0 flex items-center justify-center rounded-full bg-black/40 opacity-0 transition group-hover:opacity-100 cursor-not-allowed">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6 text-white">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z" />
                        </svg>
                    </div>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Foto Profil</h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Sesuaikan tampilan avatar akun Anda.</p>
                    <span class="mt-1.5 inline-block rounded-full bg-pink-50 dark:bg-pink-900/30 px-2.5 py-0.5 text-xs font-semibold text-pink-700 dark:text-pink-400">Tambah Foto Profil (Coming Soon)</span>
                </div>
            </div>
        </x-card>

        {{-- Name and Email Form Card --}}
        <x-card>
            <form method="POST" action="{{ route('profile.update') }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300">Nama Lengkap</label>
                    <x-input name="name" value="{{ old('name', $user->name) }}" required />
                    <x-validation-error name="name" />
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300">Alamat Email</label>
                    <x-input name="email" type="email" value="{{ old('email', $user->email) }}" required />
                    <x-validation-error name="email" />
                </div>

                <div class="flex justify-end">
                    <x-button type="submit">Simpan Perubahan</x-button>
                </div>
            </form>
        </x-card>

        {{-- Telegram Integration Card --}}
        <x-card>
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Notifikasi Telegram</h2>
                    </div>

                    @if ($user->telegram_chat_id)
                        <p class="mt-1 text-sm text-emerald-600 dark:text-emerald-400">
                            Terhubung{{ $user->telegram_linked_at ? ' sejak '.$user->telegram_linked_at->translatedFormat('d F Y H.i') : '' }}.
                        </p>
                    @else
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                            Hubungkan akun Telegram agar semua pengingat jadwal dikirim melalui bot.
                        </p>
                    @endif
                </div>

                @if ($user->telegram_chat_id)
                    <form method="POST" action="{{ route('profile.telegram.destroy') }}">
                        @csrf
                        @method('DELETE')
                        <x-button type="submit" variant="danger">Lepas Telegram</x-button>
                    </form>
                @else
                    <form method="POST" action="{{ route('profile.telegram.store') }}">
                        @csrf
                        <x-button type="submit">Hubungkan Telegram</x-button>
                    </form>
                @endif
            </div>
        </x-card>

        {{-- Google Integrations Card --}}
        <x-card>
            <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Integrasi Layanan Google</h2>
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                {{-- Classroom --}}
                <div class="rounded-2xl border border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50 p-4 flex flex-col justify-between">
                    <div>
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            </div>
                            @if ($hasClassroom)
                                <span class="rounded-full bg-emerald-50 dark:bg-emerald-900/30 px-2.5 py-0.5 text-xs font-semibold text-emerald-700 dark:text-emerald-400">Terhubung</span>
                            @else
                                <span class="rounded-full bg-slate-100 dark:bg-slate-700 px-2.5 py-0.5 text-xs font-semibold text-slate-500 dark:text-slate-400">Terputus</span>
                            @endif
                        </div>
                        <h3 class="mt-3 font-semibold text-slate-900 dark:text-white">Google Classroom</h3>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Impor kursus, tugas, dan status pengumpulan secara otomatis.</p>
                    </div>
                    <div class="mt-4">
                        @if ($hasClassroom)
                            <div class="flex gap-2">
                                <form method="POST" action="{{ route('classroom.sync') }}">
                                    @csrf
                                    <x-button type="submit" variant="secondary">Sinkronkan</x-button>
                                </form>
                                <form method="POST" action="{{ route('integrations.google.disconnect') }}">
                                    @csrf
                                    <x-button type="submit" variant="danger">Putuskan</x-button>
                                </form>
                            </div>
                        @else
                            <a href="{{ route('integrations.google.connect', 'classroom') }}">
                                <x-button>Hubungkan</x-button>
                            </a>
                        @endif
                    </div>
                </div>

                {{-- Calendar --}}
                <div class="rounded-2xl border border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50 p-4 flex flex-col justify-between">
                    <div>
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 dark:bg-amber-950/30 text-amber-600 dark:text-amber-400">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10m-11 9h12a2 2 0 002-2V7a2 2 0 00-2-2H6a2 2 0 00-2 2v11a2 2 0 002 2z"/></svg>
                            </div>
                            @if ($hasCalendar)
                                <span class="rounded-full bg-emerald-50 dark:bg-emerald-900/30 px-2.5 py-0.5 text-xs font-semibold text-emerald-700 dark:text-emerald-400">Terhubung</span>
                            @else
                                <span class="rounded-full bg-slate-100 dark:bg-slate-700 px-2.5 py-0.5 text-xs font-semibold text-slate-500 dark:text-slate-400">Terputus</span>
                            @endif
                        </div>
                        <h3 class="mt-3 font-semibold text-slate-900 dark:text-white">Google Calendar</h3>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Impor acara dan ekspor tugas & jadwal kuliah secara otomatis.</p>
                    </div>
                    <div class="mt-4">
                        @if ($hasCalendar)
                            <div class="flex gap-2">
                                <form method="POST" action="{{ route('calendar.sync') }}">
                                    @csrf
                                    <x-button type="submit" variant="secondary">Sinkronkan</x-button>
                                </form>
                                <form method="POST" action="{{ route('integrations.google.disconnect') }}">
                                    @csrf
                                    <x-button type="submit" variant="danger">Putuskan</x-button>
                                </form>
                            </div>
                        @else
                            <a href="{{ route('integrations.google.connect', 'calendar') }}">
                                <x-button>Hubungkan</x-button>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </x-card>
    </div>
</x-layouts.app>
