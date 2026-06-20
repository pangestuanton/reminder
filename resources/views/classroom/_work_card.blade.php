@php
    $daysLeft = $work->due_date ? (int) now()->startOfDay()->diffInDays($work->due_date->startOfDay(), false) : null;
@endphp

<x-card>
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div class="flex-1 min-w-0">

            {{-- Badges --}}
            <div class="flex flex-wrap items-center gap-2 mb-1">
                <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-500">
                    {{ $work->work_type === 'ASSIGNMENT' ? 'Tugas' : ($work->work_type === 'QUIZ' ? 'Kuis' : $work->work_type) }}
                </span>

                @if ($work->isSubmitted())
                    <span class="rounded-full bg-emerald-50 dark:bg-emerald-900/30 px-2 py-0.5 text-xs font-semibold text-emerald-700 dark:text-emerald-400">Selesai</span>
                @elseif ($daysLeft !== null)
                    @if ($daysLeft < 0)
                        <span class="rounded-full bg-red-50 px-2 py-0.5 text-xs font-semibold text-red-600">Terlambat {{ abs($daysLeft) }} hari</span>
                    @elseif ($daysLeft === 0)
                        <span class="rounded-full bg-orange-50 px-2 py-0.5 text-xs font-semibold text-orange-600">Hari ini!</span>
                    @elseif ($daysLeft === 1)
                        <span class="rounded-full bg-amber-50 px-2 py-0.5 text-xs font-semibold text-amber-600">Besok</span>
                    @elseif ($daysLeft <= 7)
                        <span class="rounded-full bg-yellow-50 px-2 py-0.5 text-xs font-semibold text-yellow-600">{{ $daysLeft }} hari lagi</span>
                    @else
                        <span class="rounded-full bg-slate-50 px-2 py-0.5 text-xs font-medium text-slate-500">{{ $daysLeft }} hari lagi</span>
                    @endif
                @else
                    <span class="rounded-full bg-slate-50 px-2 py-0.5 text-xs font-medium text-slate-400">Tanpa deadline</span>
                @endif
            </div>

            {{-- Title --}}
            <h3 class="font-semibold text-slate-900 dark:text-white leading-tight">{{ Str::limit($work->title, 80) }}</h3>

            {{-- Course name --}}
            @if ($work->course)
                <p class="mt-0.5 text-xs text-slate-400 dark:text-slate-500">{{ $work->course->name }}</p>
            @endif

            {{-- Due date --}}
            @if ($work->due_date)
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Deadline: {{ $work->due_date->translatedFormat('l, d F Y') }}{{ $work->due_time_only ? ' pukul ' . $work->due_time_only : '' }}
                </p>
            @endif

            {{-- Description snippet --}}
            @if ($work->description)
                <p class="mt-1 text-xs text-slate-400 dark:text-slate-500 line-clamp-2">{{ $work->description }}</p>
            @endif

        </div>

        {{-- Action buttons --}}
        <div class="flex shrink-0 gap-2">
            @if ($work->alternate_link)
                <a href="{{ $work->alternate_link }}" target="_blank" rel="noopener noreferrer">
                    <x-button variant="secondary">Buka ↗</x-button>
                </a>
            @endif
            @if ($work->jadwalKegiatan)
                <a href="{{ route('jadwal-kegiatan.show', $work->jadwalKegiatan) }}">
                    <x-button>Lihat Tugas</x-button>
                </a>
            @endif
        </div>
    </div>
</x-card>
