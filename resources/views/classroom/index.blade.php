<x-layouts.app title="Google Classroom - Aviona Sync">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-slate-900">Google Classroom</h1>
            <p class="mt-1 text-sm text-slate-500">Tugas dan kursus dari Google Classroom-mu.</p>
        </div>
        <form method="POST" action="{{ route('classroom.sync') }}">
            @csrf
            <x-button type="submit">🔄 Sinkronkan</x-button>
        </form>
    </div>

    @if ($courses->isEmpty())
        <x-empty-state title="Belum ada kursus Classroom" description="Hubungkan Google Classroom di halaman Integrasi untuk mengimpor tugas.">
            <a href="{{ route('integrations.index') }}"><x-button>Hubungkan Google</x-button></a>
        </x-empty-state>
    @else
        {{-- Upcoming Assignments Section --}}
        @php
            $upcoming = $courseWorks->filter(fn($w) => ! $w->isSubmitted())->sortBy('due_date')->take(10);
        @endphp

        @if ($upcoming->isNotEmpty())
            <div>
                <h2 class="mb-3 text-lg font-semibold text-slate-800">📋 Tugas Mendatang</h2>
                <div class="space-y-3">
                    @foreach ($upcoming as $work)
                        <x-card>
                            <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                <div class="flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <x-badge>{{ $work->work_type }}</x-badge>
                                        @if ($work->due_date)
                                            @php $daysLeft = now()->startOfDay()->diffInDays($work->due_date, false); @endphp
                                            @if ($daysLeft < 0)
                                                <x-badge type="tinggi">Terlambat</x-badge>
                                            @elseif ($daysLeft <= 3)
                                                <x-badge type="tinggi">{{ $daysLeft == 0 ? 'Hari ini' : ($daysLeft == 1 ? 'Besok' : $daysLeft . ' hari lagi') }}</x-badge>
                                            @else
                                                <x-badge type="pending">{{ $daysLeft }} hari lagi</x-badge>
                                            @endif
                                        @endif
                                    </div>
                                    <h3 class="mt-1 font-semibold text-slate-900">{{ $work->title }}</h3>
                                    @if ($work->course)
                                        <p class="mt-0.5 text-xs text-slate-400">{{ $work->course->name }}</p>
                                    @endif
                                    @if ($work->due_date)
                                        <p class="mt-1 text-sm text-slate-500">
                                            Deadline: {{ $work->due_date->translatedFormat('l, d F Y') }}{{ $work->due_time_only ? ' ' . $work->due_time_only : '' }}
                                        </p>
                                    @endif
                                </div>
                                @if ($work->alternate_link)
                                    <a href="{{ $work->alternate_link }}" target="_blank">
                                        <x-button variant="secondary">Buka</x-button>
                                    </a>
                                @endif
                            </div>
                        </x-card>
                    @endforeach
                </div>
            </div>
        @else
            <x-empty-state title="Tidak ada tugas mendatang" description="Semua tugas sudah selesai atau belum ada coursework yang tersinkronisasi." />
        @endif

        {{-- Courses Section --}}
        <div>
            <h2 class="mb-3 text-lg font-semibold text-slate-800">📚 Kursus ({{ $courses->count() }})</h2>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($courses as $course)
                    <x-card>
                        <a href="{{ route('classroom.show', $course) }}" class="block">
                            <h3 class="font-semibold text-slate-900 hover:text-pink-600 transition-colors">{{ $course->name }}</h3>
                            @if ($course->section)
                                <p class="mt-1 text-sm text-slate-500">{{ $course->section }}</p>
                            @endif
                            <p class="mt-2 text-xs text-slate-400">{{ $course->courseWorks->count() }} tugas</p>
                        </a>
                    </x-card>
                @endforeach
            </div>
        </div>
    @endif
</x-layouts.app>
