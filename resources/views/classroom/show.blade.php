<x-layouts.app title="{{ $course->name }} - Aviona Sync">
    <div class="space-y-6">
        <div>
            <a href="{{ route('classroom.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700">&larr; Kembali</a>
            <h1 class="mt-2 text-2xl md:text-3xl font-bold tracking-tight text-slate-900">{{ $course->name }}</h1>
            @if ($course->section)
                <p class="mt-1 text-sm text-slate-500">{{ $course->section }}</p>
            @endif
        </div>

        @if ($course->courseWorks->isEmpty())
            <x-empty-state title="Belum ada coursework" description="Coursework akan muncul setelah sinkronisasi." />
        @else
            <div class="space-y-3">
                @foreach ($course->courseWorks->sortByDesc('due_date') as $work)
                    <x-card>
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <div class="flex flex-wrap gap-2">
                                    <x-badge>{{ $work->work_type }}</x-badge>
                                    @if ($work->isSubmitted())
                                        <x-badge type="selesai">Selesai</x-badge>
                                    @else
                                        <x-badge type="pending">Menunggu</x-badge>
                                    @endif
                                </div>
                                <h3 class="mt-2 font-semibold text-slate-900">{{ $work->title }}</h3>
                                @if ($work->due_date)
                                    <p class="mt-1 text-sm text-slate-500">Deadline: {{ $work->due_date->format('l, d F Y') }}{{ $work->due_time_only ? ' ' . $work->due_time_only : '' }}</p>
                                @endif
                                @if ($work->description)
                                    <p class="mt-2 text-sm text-slate-600 line-clamp-2">{{ $work->description }}</p>
                                @endif
                                @if ($work->materials)
                                    <div class="mt-2 flex flex-wrap gap-1">
                                        @foreach ($work->materials as $material)
                                            <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-500">{{ $material['type'] }}: {{ $material['title'] ?? $material['id'] ?? 'file' }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            <div class="flex gap-2">
                                @if ($work->alternate_link)
                                    <a href="{{ $work->alternate_link }}" target="_blank"><x-button variant="secondary">Buka</x-button></a>
                                @endif
                                @if ($work->jadwalKegiatan)
                                    <a href="{{ route('jadwal-kegiatan.show', $work->jadwalKegiatan) }}"><x-button>Tugas</x-button></a>
                                @endif
                            </div>
                        </div>
                    </x-card>
                @endforeach
            </div>
        @endif
    </div>
</x-layouts.app>
