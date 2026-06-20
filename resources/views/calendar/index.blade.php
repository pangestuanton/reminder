<x-layouts.app title="Kalender - Aviona Sync">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Kalender</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Acara dari Google Calendar dan tugas dengan tenggat waktu.</p>
        </div>
        <form method="POST" action="{{ route('calendar.sync') }}">
            @csrf
            <x-button type="submit">Sinkronkan Calendar</x-button>
        </form>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div class="space-y-4">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Acara Kalender</h2>
            @if ($events->isEmpty())
                <x-empty-state title="Belum ada acara" description="Acara dari Google Calendar akan muncul di sini." />
            @else
                @foreach ($events->take(20) as $event)
                    <x-card>
                        <div class="flex items-start gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 dark:bg-amber-950/30 text-amber-600 dark:text-amber-400">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10m-11 9h12a2 2 0 002-2V7a2 2 0 00-2-2H6a2 2 0 00-2 2v11a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-semibold text-slate-900 dark:text-white">{{ $event->title }}</h3>
                                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                                    @if ($event->is_all_day) Sepanjang hari {{ $event->start_date->format('d/m/Y') }} @else {{ $event->start_datetime->format('d/m/Y H:i') }} - {{ $event->end_datetime->format('H:i') }} @endif
                                </p>
                                @if ($event->location)
                                    <p class="mt-0.5 text-xs text-slate-400 dark:text-slate-500">{{ $event->location }}</p>
                                @endif
                            </div>
                        </div>
                    </x-card>
                @endforeach
            @endif
        </div>

        <div class="space-y-4">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Tugas & Deadline</h2>
            @if ($jadwalItems->isEmpty())
                <x-empty-state title="Belum ada deadline" description="Deadline tugas akan muncul di sini." />
            @else
                @foreach ($jadwalItems->take(20) as $jadwal)
                    <x-card>
                        <div class="flex items-start gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl {{ $jadwal->isOverdue() ? 'bg-red-50 dark:bg-red-950/30 text-red-600 dark:text-red-400' : 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400' }}">
                                @if ($jadwal->isOverdue())
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                    </svg>
                                @else
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                @endif
                            </div>
                            <div class="flex-1">
                                <div class="flex flex-wrap gap-2">
                                    <x-badge>{{ ucfirst($jadwal->kategori) }}</x-badge>
                                    @if ($jadwal->source !== 'local')
                                        <x-badge type="blue">{{ $jadwal->source_label }}</x-badge>
                                    @endif
                                </div>
                                <h3 class="mt-1 font-semibold text-slate-900 dark:text-white">{{ $jadwal->judul }}</h3>
                                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $jadwal->waktu_pelaksanaan->format('l, d F Y \a\t H.i') }}</p>
                            </div>
                            <a href="{{ route('jadwal-kegiatan.show', $jadwal) }}" class="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300">Detail</a>
                        </div>
                    </x-card>
                @endforeach
            @endif
        </div>
    </div>
</x-layouts.app>
