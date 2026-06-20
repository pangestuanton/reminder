<x-layouts.app title="Analitik - Aviona Sync">
    <div>
        <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-slate-900">Analitik Progres</h1>
        <p class="mt-1 text-sm text-slate-500">Pantau progres tugas dan produktivitasmu.</p>
    </div>

    <form method="GET" action="{{ route('analytics.index') }}" class="flex flex-wrap gap-3 rounded-3xl border border-slate-100 bg-white p-4">
        <div>
            <label class="mb-1 block text-xs font-medium text-slate-500">Dari</label>
            <x-input name="date_from" type="date" value="{{ $filters['date_from'] ?? '' }}" />
        </div>
        <div>
            <label class="mb-1 block text-xs font-medium text-slate-500">Sampai</label>
            <x-input name="date_to" type="date" value="{{ $filters['date_to'] ?? '' }}" />
        </div>
        <div>
            <label class="mb-1 block text-xs font-medium text-slate-500">Mata Kuliah</label>
            <x-select name="course">
                <option value="">Semua</option>
                @foreach ($courses as $course)
                    <option value="{{ $course }}" {{ ($filters['course'] ?? '') === $course ? 'selected' : '' }}>{{ $course }}</option>
                @endforeach
            </x-select>
        </div>
        <div>
            <label class="mb-1 block text-xs font-medium text-slate-500">Kategori</label>
            <x-select name="category">
                <option value="">Semua</option>
                @foreach (['tugas','kuliah','uts','uas','organisasi'] as $cat)
                    <option value="{{ $cat }}" {{ ($filters['category'] ?? '') === $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
                @endforeach
            </x-select>
        </div>
        <div>
            <label class="mb-1 block text-xs font-medium text-slate-500">Sumber</label>
            <x-select name="source">
                <option value="">Semua</option>
                @foreach (['local','classroom','calendar'] as $src)
                    <option value="{{ $src }}" {{ ($filters['source'] ?? '') === $src ? 'selected' : '' }}>{{ ucfirst($src) }}</option>
                @endforeach
            </x-select>
        </div>
        <div class="flex items-end">
            <x-button type="submit" variant="secondary">Filter</x-button>
        </div>
    </form>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-5">
        <x-card>
            <p class="text-sm text-slate-500">Total Tugas</p>
            <p class="mt-1 text-3xl font-bold text-slate-900">{{ $total }}</p>
        </x-card>
        <x-card>
            <p class="text-sm text-slate-500">Selesai</p>
            <p class="mt-1 text-3xl font-bold text-emerald-600">{{ $completed }}</p>
        </x-card>
        <x-card>
            <p class="text-sm text-slate-500">Dikerjakan</p>
            <p class="mt-1 text-3xl font-bold text-blue-600">{{ $in_progress }}</p>
        </x-card>
        <x-card>
            <p class="text-sm text-slate-500">Terlambat</p>
            <p class="mt-1 text-3xl font-bold text-red-600">{{ $overdue }}</p>
        </x-card>
        <x-card>
            <p class="text-sm text-slate-500">Progres</p>
            <p class="mt-1 text-3xl font-bold text-blue-600">{{ $percentage }}%</p>
            <div class="mt-2 h-2 w-full overflow-hidden rounded-full bg-slate-100">
                <div class="h-full rounded-full bg-blue-600" style="width: {{ $percentage }}%"></div>
            </div>
        </x-card>
    </div>

    <x-card>
        <h3 class="text-lg font-semibold text-slate-900">Tren Mingguan</h3>
        <div class="mt-4 grid grid-cols-7 gap-2">
            @foreach ($weekly_trend as $day)
                <div class="text-center">
                    <div class="mx-auto mb-2 flex h-24 items-end justify-center">
                        <div class="w-8 rounded-t-lg bg-blue-500 transition-all" style="height: {{ min(($day['completed'] * 30), 96) }}px"></div>
                    </div>
                    <p class="text-xs font-medium text-slate-900">{{ $day['day'] }}</p>
                    <p class="text-xs text-slate-500">{{ $day['completed'] }}</p>
                </div>
            @endforeach
        </div>
    </x-card>

    @if ($upcoming->isNotEmpty())
        <x-card>
            <h3 class="text-lg font-semibold text-slate-900">Tugas Mendatang</h3>
            <div class="mt-4 space-y-3">
                @foreach ($upcoming as $task)
                    <div class="flex items-center gap-3 rounded-2xl bg-blue-50/30 p-3">
                        <span class="h-2 w-2 rounded-full bg-blue-500"></span>
                        <span class="text-sm font-medium text-slate-900">{{ $task->judul }}</span>
                        <span class="ml-auto text-xs text-slate-500">{{ $task->waktu_pelaksanaan->format('d/m/Y') }}</span>
                    </div>
                @endforeach
            </div>
        </x-card>
    @endif
</x-layouts.app>
