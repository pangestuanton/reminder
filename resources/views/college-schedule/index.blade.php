<x-layouts.app title="Jadwal Kuliah - Aviona Sync">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-slate-900">Jadwal Kuliah</h1>
            <p class="mt-1 text-sm text-slate-500">Jadwal kuliah mingguan yang berulang secara otomatis.</p>
        </div>
        <div class="flex items-center gap-3">
            @if ($schedules->isNotEmpty())
                <a href="{{ route('college-schedule.pdf') }}" target="_blank">
                    <x-button variant="secondary" class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m.75 12 3 3m0 0 3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                        Cetak PDF
                    </x-button>
                </a>
            @endif
            <a href="{{ route('college-schedule.create') }}">
                <x-button>Tambah Jadkul</x-button>
            </a>
        </div>
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
