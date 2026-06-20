<x-layouts.app title="Kalender - Aviona Sync">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-slate-900">Kalender</h1>
            <p class="mt-1 text-sm text-slate-500">Acara dari Google Calendar dan tugas dengan tenggat waktu.</p>
        </div>
        <form method="POST" action="{{ route('calendar.sync') }}">
            @csrf
            <x-button type="submit">Sinkronkan Calendar</x-button>
        </form>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div class="space-y-4">
            <h2 class="text-lg font-semibold text-slate-900">Acara Kalender</h2>
            @if ($events->isEmpty())
                <x-empty-state title="Belum ada acara" description="Acara dari Google Calendar akan muncul di sini." />
            @else
                @foreach ($events->take(20) as $event)
                    <x-card>
                        <div class="flex items-start gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                                📅
                            </div>
                            <div class="flex-1">
                                <h3 class="font-semibold text-slate-900">{{ $event->title }}</h3>
                                <p class="mt-1 text-sm text-slate-500">
                                    @if ($event->is_all_day) Sepanjang hari {{ $event->start_date->format('d/m/Y') }} @else {{ $event->start_datetime->format('d/m/Y H:i') }} - {{ $event->end_datetime->format('H:i') }} @endif
                                </p>
                                @if ($event->location)
                                    <p class="mt-0.5 text-xs text-slate-400">{{ $event->location }}</p>
                                @endif
                            </div>
                        </div>
                    </x-card>
                @endforeach
            @endif
        </div>

        <div class="space-y-4">
            <h2 class="text-lg font-semibold text-slate-900">Tugas & Deadline</h2>
            @if ($jadwalItems->isEmpty())
                <x-empty-state title="Belum ada deadline" description="Deadline tugas akan muncul di sini." />
            @else
                @foreach ($jadwalItems->take(20) as $jadwal)
                    <x-card>
                        <div class="flex items-start gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl {{ $jadwal->isOverdue() ? 'bg-red-50 text-red-600' : 'bg-blue-50 text-blue-600' }}">
                                @if ($jadwal->isOverdue()) ⚠️ @else 📝 @endif
                            </div>
                            <div class="flex-1">
                                <div class="flex flex-wrap gap-2">
                                    <x-badge>{{ ucfirst($jadwal->kategori) }}</x-badge>
                                    @if ($jadwal->source !== 'local')
                                        <x-badge type="blue">{{ $jadwal->source_label }}</x-badge>
                                    @endif
                                </div>
                                <h3 class="mt-1 font-semibold text-slate-900">{{ $jadwal->judul }}</h3>
                                <p class="mt-1 text-sm text-slate-500">{{ $jadwal->waktu_pelaksanaan->format('l, d F Y \a\t H.i') }}</p>
                            </div>
                            <a href="{{ route('jadwal-kegiatan.show', $jadwal) }}" class="text-xs font-semibold text-blue-600">Detail</a>
                        </div>
                    </x-card>
                @endforeach
            @endif
        </div>
    </div>
</x-layouts.app>
