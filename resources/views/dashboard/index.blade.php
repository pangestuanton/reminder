<x-layouts.app title="Dashboard - Aviona Sync">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-slate-900">Halo, {{ auth()->user()->name }}! 👋</h1>
            <p class="mt-1 text-sm text-slate-500">Pantau jadwal, tenggat terdekat, dan aktivitas pentingmu di satu tempat.</p>
        </div>
        <a href="{{ route('jadwal-kegiatan.create') }}">
            <x-button>Tambah Jadwal</x-button>
        </a>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
        <x-card>
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-pink-50 text-pink-500">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10m-11 9h12a2 2 0 002-2V7a2 2 0 00-2-2H6a2 2 0 00-2 2v11a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Total Jadwal</p>
                    <p class="text-2xl font-bold text-slate-900">{{ $total }}</p>
                </div>
            </div>
        </x-card>
        <x-card>
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-500">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Menunggu</p>
                    <p class="text-2xl font-bold text-amber-600">{{ $pending }}</p>
                </div>
            </div>
        </x-card>
        <x-card>
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-500">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Selesai</p>
                    <p class="text-2xl font-bold text-emerald-600">{{ $completed }}</p>
                </div>
            </div>
        </x-card>
        <x-card>
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-red-50 text-red-500">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Mendesak</p>
                    <p class="text-2xl font-bold text-red-600">{{ $urgent }}</p>
                </div>
            </div>
        </x-card>
    </div>

    @if (! $total)
        <x-empty-state title="Belum ada jadwal" description="Tambahkan jadwal pertama agar aktivitas kuliah dan organisasimu lebih teratur.">
            <a href="{{ route('jadwal-kegiatan.create') }}"><x-button>Tambah Jadwal</x-button></a>
        </x-empty-state>
    @else
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-[1.2fr_0.8fr]">
            <div class="space-y-6">
                <x-card>
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm text-slate-500">Jadwal Terdekat</p>
                            @if ($nearest)
                                <h2 class="mt-2 text-xl font-semibold text-slate-900">{{ $nearest->judul }}</h2>
                                <p class="mt-2 text-sm text-slate-600">{{ $nearest->waktu_pelaksanaan->translatedFormat('l, d F Y • H.i') }}</p>
                                <p class="mt-3 text-sm font-medium text-pink-600">{{ app(\App\Services\DashboardStatsService::class)->countdownText($nearest) }}</p>
                                <div class="mt-4 flex flex-wrap gap-2">
                                    <x-badge>{{ ucfirst($nearest->kategori) }}</x-badge>
                                    <x-badge type="pending">Menunggu</x-badge>
                                    @if ($nearest->daysUntilDue() <= 3)
                                        <x-badge type="mendesak">Pengingat aktif</x-badge>
                                    @endif
                                </div>
                            @else
                                <p class="mt-2 text-sm text-slate-500">Belum ada jadwal mendatang.</p>
                            @endif
                        </div>
                        @if ($nearest)
                            <a href="{{ route('jadwal-kegiatan.show', $nearest) }}"><x-button variant="secondary">Lihat Detail</x-button></a>
                        @endif
                    </div>
                </x-card>

                <x-card>
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-slate-900">Jadwal Berikutnya</h3>
                        <a href="{{ route('jadwal-kegiatan.index') }}" class="text-sm font-semibold text-pink-600 hover:text-pink-700">Lihat semua</a>
                    </div>
                    <div class="space-y-4">
                        @forelse ($upcoming as $jadwal)
                            <div class="rounded-2xl bg-pink-50/30 p-4 transition hover:bg-pink-50/60">
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                    <div>
                                        <div class="flex flex-wrap gap-2">
                                            <x-badge>{{ ucfirst($jadwal->kategori) }}</x-badge>
                                            @if ($jadwal->isOverdue())
                                                <x-badge type="terlambat">Terlambat</x-badge>
                                            @elseif ($jadwal->daysUntilDue() <= 3)
                                                <x-badge type="mendesak">Mendesak</x-badge>
                                            @endif
                                        </div>
                                        <h4 class="mt-3 font-semibold text-slate-900">{{ $jadwal->judul }}</h4>
                                        <p class="mt-1 text-sm text-slate-500">{{ $jadwal->waktu_pelaksanaan->translatedFormat('l, d F Y • H.i') }}</p>
                                    </div>
                                    <a href="{{ route('jadwal-kegiatan.show', $jadwal) }}" class="text-sm font-semibold text-pink-600">Detail</a>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-slate-500">Belum ada jadwal terdekat.</p>
                        @endforelse
                    </div>
                </x-card>
            </div>

            <x-card>
                <h3 class="text-lg font-semibold text-slate-900">Tenggat Mendesak</h3>
                <p class="mt-1 text-sm text-slate-500">Jadwal yang membutuhkan perhatianmu dalam 7 hari ke depan.</p>
                <div class="mt-5 space-y-4">
                    @forelse ($urgentList as $jadwal)
                        <div class="rounded-2xl bg-amber-50 p-4 transition hover:bg-amber-100/60">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="flex flex-wrap gap-2">
                                        <x-badge type="mendesak">Mendesak</x-badge>
                                        <x-badge>{{ ucfirst($jadwal->kategori) }}</x-badge>
                                    </div>
                                    <h4 class="mt-3 font-semibold text-slate-900">{{ $jadwal->judul }}</h4>
                                    <p class="mt-1 text-sm text-slate-600">{{ $jadwal->waktu_pelaksanaan->translatedFormat('l, d F Y • H.i') }}</p>
                                    <p class="mt-2 text-sm font-medium text-amber-700">{{ app(\App\Services\DashboardStatsService::class)->countdownText($jadwal) }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">Belum ada tugas mendesak hari ini.</p>
                    @endforelse
                </div>
            </x-card>
        </div>
    @endif
</x-layouts.app>
