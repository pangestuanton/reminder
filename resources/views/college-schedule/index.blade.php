<x-layouts.app title="Jadwal Kuliah - Aviona Sync">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-slate-900">Jadwal Kuliah</h1>
            <p class="mt-1 text-sm text-slate-500">Jadwal kuliah mingguan yang berulang secara otomatis.</p>
        </div>
        <a href="{{ route('college-schedule.create') }}">
            <x-button>Tambah Jadkul</x-button>
        </a>
    </div>

    @if ($schedules->isEmpty())
        <x-empty-state title="Belum ada jadwal kuliah" description="Tambahkan jadwal kuliah agar otomatis muncul di agenda harianmu.">
            <a href="{{ route('college-schedule.create') }}"><x-button>Tambah Jadkul</x-button></a>
        </x-empty-state>
    @else
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($days as $day)
                @if ($grouped->has($day))
                    <x-card>
                        <h3 class="text-base font-semibold text-slate-900">{{ $day }}</h3>
                        <div class="mt-3 space-y-3">
                            @foreach ($grouped[$day] as $schedule)
                                <a href="{{ route('college-schedule.show', $schedule) }}" class="block rounded-2xl p-3 transition hover:bg-blue-50/60" style="border-left: 4px solid {{ $schedule->warna }}">
                                    <div class="flex items-center justify-between">
                                        <h4 class="font-medium text-slate-900">{{ $schedule->mata_kuliah }}</h4>
                                        @if (! $schedule->is_active)
                                            <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-500">Nonaktif</span>
                                        @endif
                                    </div>
                                    <p class="mt-1 text-sm text-slate-500">{{ $schedule->jam_mulai }}-{{ $schedule->jam_selesai }}</p>
                                    @if ($schedule->dosen)
                                        <p class="mt-0.5 text-xs text-slate-400">{{ $schedule->dosen }}</p>
                                    @endif
                                    @if ($schedule->lokasi)
                                        <p class="mt-0.5 text-xs text-slate-400">{{ $schedule->lokasi }}</p>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </x-card>
                @endif
            @endforeach
        </div>
    @endif
</x-layouts.app>
