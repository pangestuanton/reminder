<x-layouts.app title="{{ $schedule->mata_kuliah }} - Aviona Sync">
    <div class="max-w-2xl space-y-6">
        <div>
            <a href="{{ route('college-schedule.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700">&larr; Kembali</a>
            <div class="mt-2 flex items-center justify-between">
                <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-slate-900">{{ $schedule->mata_kuliah }}</h1>
                <div class="flex gap-2">
                    <a href="{{ route('college-schedule.edit', $schedule) }}"><x-button variant="secondary">Edit</x-button></a>
                    <form method="POST" action="{{ route('college-schedule.destroy', $schedule) }}" onsubmit="return confirm('Yakin ingin menghapus jadwal ini?')">
                        @csrf
                        @method('DELETE')
                        <x-button type="submit" variant="danger">Hapus</x-button>
                    </form>
                </div>
            </div>
        </div>

        <x-card>
            <div class="space-y-4">
                <div class="flex items-center gap-3">
                    <div class="h-3 w-3 rounded-full" style="background-color: {{ $schedule->warna }}"></div>
                    <span class="text-sm font-medium text-slate-700">{{ $schedule->hari }}</span>
                    <span class="text-sm text-slate-500">{{ $schedule->jam_mulai }} - {{ $schedule->jam_selesai }}</span>
                    @if (! $schedule->is_active)
                        <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-500">Nonaktif</span>
                    @endif
                </div>

                @if ($schedule->dosen)
                    <div class="text-sm text-slate-600"><span class="font-medium">Dosen:</span> {{ $schedule->dosen }}</div>
                @endif

                @if ($schedule->lokasi)
                    <div class="text-sm text-slate-600"><span class="font-medium">Lokasi:</span> {{ $schedule->lokasi }}</div>
                @endif

                @if ($schedule->catatan)
                    <div class="text-sm text-slate-600"><span class="font-medium">Catatan:</span> {{ $schedule->catatan }}</div>
                @endif

                <div class="text-sm text-slate-600">
                    <span class="font-medium">Pengingat:</span> {{ $schedule->reminder_minutes }} menit sebelum
                </div>

                @if ($schedule->semester_mulai || $schedule->semester_akhir)
                    <div class="text-sm text-slate-600">
                        <span class="font-medium">Semester:</span>
                        {{ $schedule->semester_mulai?->format('d/m/Y') ?? '-' }} &mdash; {{ $schedule->semester_akhir?->format('d/m/Y') ?? '-' }}
                    </div>
                @endif

                <div class="flex gap-2 pt-4">
                    <form method="POST" action="{{ route('college-schedule.toggle', $schedule) }}">
                        @csrf
                        @method('PATCH')
                        <x-button type="submit" variant="secondary">{{ $schedule->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</x-button>
                    </form>
                </div>
            </div>
        </x-card>
    </div>
</x-layouts.app>
