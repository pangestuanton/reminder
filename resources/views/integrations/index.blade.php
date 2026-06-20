<x-layouts.app title="Integrasi - Aviona Sync">
    <div>
        <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Integrasi</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Hubungkan layanan Google untuk sinkronisasi otomatis.</p>
    </div>

    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
        <x-card>
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Google Classroom</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Impor kursus, tugas, dan status pengumpulan</p>
                    </div>
                </div>
                @if ($hasClassroom)
                    <span class="rounded-full bg-emerald-50 dark:bg-emerald-950/40 px-3 py-1 text-xs font-semibold text-emerald-700 dark:text-emerald-400">Terhubung</span>
                @else
                    <span class="rounded-full bg-slate-100 dark:bg-slate-800 px-3 py-1 text-xs font-semibold text-slate-500 dark:text-slate-400">Tidak terhubung</span>
                @endif
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
                        <x-button>Hubungkan Classroom</x-button>
                    </a>
                @endif
            </div>
        </x-card>

        <x-card>
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10m-11 9h12a2 2 0 002-2V7a2 2 0 00-2-2H6a2 2 0 00-2 2v11a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Google Calendar</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Impor acara dan ekspor tugas & jadwal kuliah</p>
                    </div>
                </div>
                @if ($hasCalendar)
                    <span class="rounded-full bg-emerald-50 dark:bg-emerald-950/40 px-3 py-1 text-xs font-semibold text-emerald-700 dark:text-emerald-400">Terhubung</span>
                @else
                    <span class="rounded-full bg-slate-100 dark:bg-slate-800 px-3 py-1 text-xs font-semibold text-slate-500 dark:text-slate-400">Tidak terhubung</span>
                @endif
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
                        <x-button>Hubungkan Calendar</x-button>
                    </a>
                @endif
            </div>
        </x-card>

        <x-card>
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Telegram Bot</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Pengingat dan agenda harian via Telegram</p>
                    </div>
                </div>
                @if (auth()->user()->telegram_chat_id)
                    <span class="rounded-full bg-emerald-50 dark:bg-emerald-950/40 px-3 py-1 text-xs font-semibold text-emerald-700 dark:text-emerald-400">Terhubung</span>
                @else
                    <span class="rounded-full bg-slate-100 dark:bg-slate-800 px-3 py-1 text-xs font-semibold text-slate-500 dark:text-slate-400">Tidak terhubung</span>
                @endif
            </div>
            <div class="mt-4">
                <a href="{{ route('profile.edit') }}"><x-button variant="secondary">Kelola Telegram</x-button></a>
            </div>
        </x-card>
    </div>
</x-layouts.app>

