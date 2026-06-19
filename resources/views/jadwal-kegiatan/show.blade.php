<x-layouts.app title="{{ $jadwalKegiatan->judul }} - Aviona Sync">
    <div class="max-w-4xl space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <a href="{{ route('jadwal-kegiatan.index') }}" class="text-sm font-semibold text-blue-600">← Kembali</a>
                <h1 class="mt-3 text-2xl md:text-3xl font-bold tracking-tight text-slate-900">{{ $jadwalKegiatan->judul }}</h1>
                <p class="mt-1 text-sm text-slate-500">Detail lengkap jadwal dan pengingatnya.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('jadwal-kegiatan.edit', $jadwalKegiatan) }}"><x-button variant="secondary">Edit Jadwal</x-button></a>
                @if ($jadwalKegiatan->status === 'pending')
                    <form method="POST" action="{{ route('jadwal-kegiatan.complete', $jadwalKegiatan) }}">
                        @csrf
                        @method('PATCH')
                        <x-button type="submit">Tandai Selesai</x-button>
                    </form>
                @endif
            </div>
        </div>

        <x-card>
            <div class="flex flex-wrap gap-2">
                <x-badge>{{ ucfirst($jadwalKegiatan->kategori) }}</x-badge>
                <x-badge type="{{ $jadwalKegiatan->isOverdue() ? 'terlambat' : $jadwalKegiatan->status }}">{{ $jadwalKegiatan->isOverdue() ? 'Terlambat' : ($jadwalKegiatan->status === 'pending' ? 'Menunggu' : ($jadwalKegiatan->status === 'selesai' ? 'Selesai' : 'Dibatalkan')) }}</x-badge>
                <x-badge>{{ ucfirst($jadwalKegiatan->prioritas) }}</x-badge>
            </div>

            <dl class="mt-6 grid grid-cols-1 gap-5 md:grid-cols-2">
                <div>
                    <dt class="text-sm font-medium text-slate-500">Waktu Pelaksanaan</dt>
                    <dd class="mt-1 text-base text-slate-900">{{ $jadwalKegiatan->waktu_pelaksanaan->translatedFormat('l, d F Y • H.i') }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-slate-500">Lokasi atau Link</dt>
                    <dd class="mt-1 text-base text-slate-900">{{ $jadwalKegiatan->lokasi_atau_link ?: 'Belum diisi' }}</dd>
                </div>
                <div class="md:col-span-2">
                    <dt class="text-sm font-medium text-slate-500">Deskripsi</dt>
                    <dd class="mt-1 text-base leading-relaxed text-slate-900">{{ $jadwalKegiatan->deskripsi ?: 'Belum ada deskripsi tambahan.' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-slate-500">Countdown</dt>
                    <dd class="mt-1 text-base font-medium {{ $jadwalKegiatan->isOverdue() ? 'text-red-600' : 'text-blue-600' }}">{{ app(\App\Services\DashboardStatsService::class)->countdownText($jadwalKegiatan) }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-slate-500">Indikator Pengingat</dt>
                    <dd class="mt-1 text-base text-slate-900">{{ $jadwalKegiatan->status === 'pending' ? 'Pengingat H-3 dan H-1 akan diproses otomatis.' : 'Pengingat tidak aktif untuk status ini.' }}</dd>
                </div>
            </dl>
        </x-card>
    </div>
</x-layouts.app>
