<x-layouts.app title="Google Classroom - Aviona Sync">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-slate-900">Google Classroom</h1>
            <p class="mt-1 text-sm text-slate-500">Tugas dan coursework dari Google Classroom-mu.</p>
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
        <div class="space-y-4">
            @foreach ($courses as $course)
                <x-card>
                    <a href="{{ route('classroom.show', $course) }}" class="block">
                        <h3 class="font-semibold text-slate-900 hover:text-blue-600">{{ $course->name }}</h3>
                        @if ($course->section)
                            <p class="mt-1 text-sm text-slate-500">{{ $course->section }}</p>
                        @endif
                        <p class="mt-1 text-xs text-slate-400">{{ $course->courseWorks->count() }} tugas</p>
                    </a>
                </x-card>
            @endforeach
        </div>
    @endif
</x-layouts.app>
