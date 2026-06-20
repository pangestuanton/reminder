<x-layouts.app title="Google Classroom - Aviona Sync">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Google Classroom</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Tugas dan kursus dari Google Classroom-mu.</p>
        </div>
        <form method="POST" action="{{ route('classroom.sync') }}">
            @csrf
            <x-button type="submit">Sinkronkan</x-button>
        </form>
    </div>

    @if ($courses->isEmpty())
        <x-empty-state title="Belum ada kursus Classroom" description="Hubungkan Google Classroom di halaman Integrasi untuk mengimpor tugas.">
            <a href="{{ route('integrations.index') }}"><x-button>Hubungkan Google</x-button></a>
        </x-empty-state>
    @else

        {{-- ── Upcoming / Pending tasks ──────────────────────── --}}
        @php
            $pending = $courseWorks
                ->filter(fn($w) => ! $w->isSubmitted())
                ->sortBy(fn($w) => $w->due_date?->timestamp ?? PHP_INT_MAX);

            $overdue = $pending->filter(fn($w) => $w->due_date && $w->due_date->isPast());
            $upcoming = $pending->filter(fn($w) => ! $w->due_date || ! $w->due_date->isPast());
        @endphp

        {{-- Overdue --}}
        @if ($overdue->isNotEmpty())
            <div>
                <h2 class="mb-3 text-lg font-semibold text-red-600 dark:text-red-400">Terlambat ({{ $overdue->count() }})</h2>
                <div class="space-y-3">
                    @foreach ($overdue as $work)
                        @include('classroom._work_card', ['work' => $work])
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Upcoming --}}
        <div>
            <h2 class="mb-3 text-lg font-semibold text-slate-800 dark:text-slate-200">
                Tugas Mendatang
                <span class="ml-1 text-base font-normal text-slate-400 dark:text-slate-500">({{ $upcoming->count() }})</span>
            </h2>

            @if ($upcoming->isEmpty() && $overdue->isEmpty())
                <x-empty-state
                    title="Tidak ada tugas pending"
                    description="Semua tugas sudah selesai, atau kursus belum memiliki tugas. Coba tekan Sinkronkan."
                />
            @elseif ($upcoming->isEmpty())
                <p class="text-sm text-slate-400 italic">Tidak ada tugas mendatang.</p>
            @else
                <div class="space-y-3">
                    @foreach ($upcoming->take(20) as $work)
                        @include('classroom._work_card', ['work' => $work])
                    @endforeach
                </div>
            @endif
        </div>

        {{-- All course works (toggle) --}}
        @if ($courseWorks->isNotEmpty())
            <details class="group">
                <summary class="cursor-pointer text-sm font-medium text-pink-600 hover:text-pink-700 select-none list-none flex items-center gap-1">
                    <svg class="h-4 w-4 transition-transform group-open:rotate-90" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    Tampilkan semua tugas ({{ $courseWorks->count() }})
                </summary>
                <div class="mt-3 space-y-3">
                    @foreach ($courseWorks->sortBy(fn($w) => $w->due_date?->timestamp ?? PHP_INT_MAX) as $work)
                        @include('classroom._work_card', ['work' => $work])
                    @endforeach
                </div>
            </details>
        @endif

        {{-- ── Courses grid ────────────────────────────────── --}}
        <div>
            <h2 class="mb-3 text-lg font-semibold text-slate-800 dark:text-slate-200">Kursus ({{ $courses->count() }})</h2>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($courses as $course)
                    <x-card>
                        <a href="{{ route('classroom.show', $course) }}" class="block group">
                            <h3 class="font-semibold text-slate-900 dark:text-white group-hover:text-pink-600 dark:group-hover:text-pink-400 transition-colors">
                                {{ $course->name }}
                            </h3>
                            @if ($course->section)
                                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $course->section }}</p>
                            @endif
                            <p class="mt-2 text-xs text-slate-400 dark:text-slate-500">{{ $course->courseWorks->count() }} tugas</p>
                        </a>
                    </x-card>
                @endforeach
            </div>
        </div>

    @endif
</x-layouts.app>
