<x-layouts.app title="Dashboard - Aviona Sync">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Halo, {{ auth()->user()->name }}!</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Pantau jadwal, tenggat terdekat, dan aktivitas pentingmu di satu tempat.</p>
        </div>
        <a href="{{ route('jadwal-kegiatan.create') }}">
            <x-button>Tambah Tugas</x-button>
        </a>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
        <x-card>
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 dark:bg-blue-900/30 text-blue-500 dark:text-blue-400">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <div>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Total Tugas</p>
                    <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $total }}</p>
                </div>
            </div>
        </x-card>
        <x-card>
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-yellow-50 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Menunggu</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $pending }}</p>
                </div>
            </div>
        </x-card>
        <x-card>
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Selesai</p>
                    <p class="text-2xl font-bold text-emerald-600">{{ $completed }}</p>
                </div>
            </div>
        </x-card>
        <x-card>
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                </div>
                <div>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Mendesak</p>
                    <p class="text-2xl font-bold text-red-600">{{ $urgent }}</p>
                </div>
            </div>
        </x-card>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <x-card>
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Progres Keseluruhan</h3>
                <span class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $analytics['percentage'] }}%</span>
            </div>
            <div class="mt-3 h-2 w-full overflow-hidden rounded-full bg-slate-100 dark:bg-slate-700">
                <div class="h-full rounded-full bg-blue-600 dark:bg-blue-500 transition-all" style="width: {{ $analytics['percentage'] }}%"></div>
            </div>
            <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">{{ $analytics['completed'] }} dari {{ $analytics['total'] }} tugas selesai</p>
        </x-card>

        <x-card>
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Agenda Hari Ini</h3>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ $todayClasses->count() }} kelas, {{ $todayEvents->count() }} acara</p>
                </div>
                <a href="{{ route('calendar.index') }}" class="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300">Lihat</a>
            </div>
        </x-card>

        <x-card>
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Integrasi</h3>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ auth()->user()->hasClassroomAccess() ? 'Classroom aktif' : 'Classroom tidak terhubung' }}</p>
                </div>
                <a href="{{ route('integrations.index') }}" class="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300">Kelola</a>
            </div>
        </x-card>
    </div>

    @if ($todayClasses->isNotEmpty())
        <x-card>
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Kuliah Hari Ini</h3>
            <div class="mt-4 space-y-3">
                @foreach ($todayClasses as $class)
                    <div class="flex items-center gap-4 rounded-2xl bg-blue-50/50 dark:bg-slate-800/40 p-4 transition hover:bg-blue-50 dark:hover:bg-slate-800/80">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-100 dark:bg-blue-900/30 text-sm font-bold text-blue-700 dark:text-blue-400">
                            {{ substr($class->jam_mulai, 0, 5) }}
                        </div>
                        <div class="flex-1">
                            <h4 class="font-semibold text-slate-900 dark:text-white">{{ $class->mata_kuliah }}</h4>
                            <p class="text-sm text-slate-500 dark:text-slate-400">
                                @if ($class->dosen) {{ $class->dosen }} &middot; @endif
                                {{ $class->lokasi ?? 'Online' }}
                            </p>
                        </div>
                        <span class="text-xs text-slate-400">{{ $class->jam_mulai }}-{{ $class->jam_selesai }}</span>
                    </div>
                @endforeach
            </div>
        </x-card>
    @endif

    @if ($todayEvents->isNotEmpty())
        <x-card>
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Acara Kalender</h3>
            <div class="mt-4 space-y-3">
                @foreach ($todayEvents as $event)
                    <div class="flex items-center gap-4 rounded-2xl bg-amber-50/50 dark:bg-slate-800/40 p-4 transition hover:bg-amber-50 dark:hover:bg-slate-800/80">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-100 dark:bg-amber-900/30 text-sm font-bold text-amber-700 dark:text-amber-400">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10m-11 9h12a2 2 0 002-2V7a2 2 0 00-2-2H6a2 2 0 00-2 2v11a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-semibold text-slate-900 dark:text-white">{{ $event->title }}</h4>
                            <p class="text-sm text-slate-500 dark:text-slate-400">
                                @if ($event->is_all_day) Sepanjang hari @else {{ $event->start_datetime->format('H.i') }} - {{ $event->end_datetime->format('H.i') }} @endif
                                @if ($event->location) &middot; {{ $event->location }} @endif
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-card>
    @endif

    @if (! $total)
        <x-empty-state title="Belum ada tugas" description="Tambahkan tugas pertama agar aktivitas kuliah dan organisasimu lebih teratur.">
            <a href="{{ route('jadwal-kegiatan.create') }}"><x-button>Tambah Tugas</x-button></a>
        </x-empty-state>
    @else
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-[1.2fr_0.8fr]">
            <div class="space-y-6">
                <x-card>
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm text-slate-500 dark:text-slate-400">Tugas Terdekat</p>
                            @if ($nearest)
                                <h2 class="mt-2 text-xl font-semibold text-slate-900 dark:text-white">{{ $nearest->judul }}</h2>
                                <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">{{ $nearest->waktu_pelaksanaan ? $nearest->waktu_pelaksanaan->translatedFormat('l, d F Y \a\t H.i') : 'Tanpa deadline' }}</p>
                                @if ($nearest->countdown_text)
                                    <p class="mt-3 text-sm font-medium text-blue-600 dark:text-blue-400">{{ $nearest->countdown_text }}</p>
                                @endif
                                <div class="mt-4 flex flex-wrap gap-2">
                                    <x-badge>{{ ucfirst($nearest->kategori) }}</x-badge>
                                    <x-badge type="pending">Menunggu</x-badge>
                                    @if ($nearest->source !== 'local')
                                        <x-badge type="blue">{{ $nearest->source_label }}</x-badge>
                                    @endif
                                    @if ($nearest->daysUntilDue() <= 3)
                                        <x-badge type="mendesak">Mendesak</x-badge>
                                    @endif
                                </div>
                            @else
                                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Belum ada tugas mendatang.</p>
                            @endif
                        </div>
                        @if ($nearest)
                            <a href="{{ route('jadwal-kegiatan.show', $nearest) }}"><x-button variant="secondary">Lihat Detail</x-button></a>
                        @endif
                    </div>
                </x-card>

                <x-card>
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Tugas Berikutnya</h3>
                        <a href="{{ route('jadwal-kegiatan.index') }}" class="text-sm font-semibold text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300">Lihat semua</a>
                    </div>
                    <div class="space-y-4">
                        @forelse ($upcoming as $jadwal)
                            <div class="rounded-2xl bg-blue-50/30 dark:bg-slate-800/40 p-4 transition hover:bg-blue-50/60 dark:hover:bg-slate-800/80">
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                    <div>
                                        <div class="flex flex-wrap gap-2">
                                            <x-badge>{{ ucfirst($jadwal->kategori) }}</x-badge>
                                            @if ($jadwal->source !== 'local')
                                                <x-badge type="blue">{{ $jadwal->source_label }}</x-badge>
                                            @endif
                                            @if ($jadwal->isOverdue())
                                                <x-badge type="terlambat">Terlambat</x-badge>
                                            @elseif ($jadwal->daysUntilDue() <= 3)
                                                <x-badge type="mendesak">Mendesak</x-badge>
                                            @endif
                                        </div>
                                        <h4 class="mt-3 font-semibold text-slate-900 dark:text-white">{{ $jadwal->judul }}</h4>
                                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $jadwal->waktu_pelaksanaan ? $jadwal->waktu_pelaksanaan->translatedFormat('l, d F Y \a\t H.i') : 'Tanpa deadline' }}</p>
                                    </div>
                                    <a href="{{ route('jadwal-kegiatan.show', $jadwal) }}" class="text-sm font-semibold text-blue-600 dark:text-blue-400">Detail</a>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-slate-500 dark:text-slate-400">Belum ada tugas terdekat.</p>
                        @endforelse
                    </div>
                </x-card>
            </div>

            <div class="space-y-6">
                <x-card>
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Tenggat Mendesak</h3>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Tugas yang membutuhkan perhatianmu dalam 7 hari ke depan.</p>
                    <div class="mt-5 space-y-4">
                        @forelse ($urgentList as $jadwal)
                            <div class="rounded-2xl bg-yellow-50 dark:bg-yellow-950/20 p-4 transition hover:bg-yellow-100/60 dark:hover:bg-yellow-900/30">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <div class="flex flex-wrap gap-2">
                                            <x-badge type="mendesak">Mendesak</x-badge>
                                            <x-badge>{{ ucfirst($jadwal->kategori) }}</x-badge>
                                        </div>
                                        <h4 class="mt-3 font-semibold text-slate-900 dark:text-white">{{ $jadwal->judul }}</h4>
                                        <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">{{ $jadwal->waktu_pelaksanaan ? $jadwal->waktu_pelaksanaan->translatedFormat('l, d F Y \a\t H.i') : 'Tanpa deadline' }}</p>
                                        @if ($jadwal->countdown_text)
                                            <p class="mt-2 text-sm font-medium text-yellow-700 dark:text-yellow-400">{{ $jadwal->countdown_text }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-slate-500 dark:text-slate-400">Belum ada tugas mendesak hari ini.</p>
                        @endforelse
                    </div>
                </x-card>

                @if ($recentNotifications->isNotEmpty())
                    <x-card>
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Notifikasi Terakhir</h3>
                        <div class="mt-4 space-y-3">
                            @foreach ($recentNotifications as $log)
                                <div class="flex items-center gap-3 text-sm">
                                    <span class="h-2 w-2 rounded-full {{ $log->status === 'sent' ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
                                    <span class="text-slate-600 dark:text-slate-300">{{ ucfirst(str_replace('_', ' ', $log->notification_type)) }}</span>
                                    <span class="ml-auto text-xs text-slate-400 dark:text-slate-500">{{ $log->sent_at?->diffForHumans() }}</span>
                                </div>
                            @endforeach
                        </div>
                    </x-card>
                @endif
            </div>
        </div>
    @endif
</x-layouts.app>
