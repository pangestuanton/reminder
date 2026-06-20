<x-layouts.app title="{{ $jadwalKegiatan->judul }} - Aviona Sync">
    <div class="max-w-4xl space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <a href="{{ route('jadwal-kegiatan.index') }}" class="text-sm font-semibold text-blue-600">&larr; Kembali</a>
                <h1 class="mt-3 text-2xl md:text-3xl font-bold tracking-tight text-slate-900">{{ $jadwalKegiatan->judul }}</h1>
                <p class="mt-1 text-sm text-slate-500">Detail lengkap tugas dan pengingatnya.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                @if ($jadwalKegiatan->status === 'pending')
                    <form method="POST" action="{{ route('jadwal-kegiatan.complete', $jadwalKegiatan) }}">
                        @csrf
                        @method('PATCH')
                        <x-button type="submit">Selesai</x-button>
                    </form>
                @endif
                @if ($jadwalKegiatan->source === 'local')
                    <a href="{{ route('jadwal-kegiatan.edit', $jadwalKegiatan) }}"><x-button variant="secondary">Edit</x-button></a>
                @endif
                @if (auth()->user()->hasCalendarAccess() && ! $jadwalKegiatan->synced_to_calendar)
                    <form method="POST" action="{{ route('calendar.export-task', $jadwalKegiatan) }}">
                        @csrf
                        <x-button type="submit" variant="secondary">Ekspor ke Calendar</x-button>
                    </form>
                @endif
                <x-modal message="Tugas yang dihapus tidak bisa dikembalikan.">
                    <x-slot:trigger>
                        <x-button type="button" variant="danger">Hapus</x-button>
                    </x-slot:trigger>
                    <form method="POST" action="{{ route('jadwal-kegiatan.destroy', $jadwalKegiatan) }}">
                        @csrf
                        @method('DELETE')
                        <x-button variant="danger" type="submit">Ya, Hapus</x-button>
                    </form>
                </x-modal>
            </div>
        </div>

        <x-card>
            <div class="flex flex-wrap gap-2">
                <x-badge>{{ ucfirst($jadwalKegiatan->kategori) }}</x-badge>
                <x-badge type="{{ $jadwalKegiatan->isOverdue() ? 'terlambat' : ($jadwalKegiatan->status === 'pending' ? 'pending' : 'selesai') }}">{{ $jadwalKegiatan->isOverdue() ? 'Terlambat' : ($jadwalKegiatan->status === 'pending' ? 'Menunggu' : ($jadwalKegiatan->status === 'selesai' ? 'Selesai' : 'Dibatalkan')) }}</x-badge>
                <x-badge>{{ ucfirst($jadwalKegiatan->prioritas) }}</x-badge>
                @if ($jadwalKegiatan->source !== 'local')
                    <x-badge type="blue">{{ $jadwalKegiatan->source_label }}</x-badge>
                @endif
            </div>

            <dl class="mt-6 grid grid-cols-1 gap-5 md:grid-cols-2">
                <div>
                    <dt class="text-sm font-medium text-slate-500">Deadline</dt>
                    <dd class="mt-1 text-base text-slate-900">{{ $jadwalKegiatan->waktu_pelaksanaan ? $jadwalKegiatan->waktu_pelaksanaan->translatedFormat('l, d F Y \a\t H.i') : 'Tanpa deadline' }}</dd>
                </div>
                @if ($jadwalKegiatan->course_name)
                    <div>
                        <dt class="text-sm font-medium text-slate-500">Mata Kuliah</dt>
                        <dd class="mt-1 text-base text-slate-900">{{ $jadwalKegiatan->course_name }}</dd>
                    </div>
                @endif
                <div>
                    <dt class="text-sm font-medium text-slate-500">Lokasi / Link</dt>
                    <dd class="mt-1 text-base text-slate-900 break-all">{{ $jadwalKegiatan->lokasi_atau_link ?: 'Belum diisi' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-slate-500">Sumber</dt>
                    <dd class="mt-1 text-base text-slate-900">{{ $jadwalKegiatan->source_label }}</dd>
                </div>
                @if ($jadwalKegiatan->deskripsi)
                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-slate-500">Deskripsi</dt>
                        <dd class="mt-1 text-base leading-relaxed text-slate-900 break-words whitespace-pre-line">{{ $jadwalKegiatan->deskripsi }}</dd>
                    </div>
                @endif
                <div>
                    <dt class="text-sm font-medium text-slate-500">Countdown</dt>
                    <dd class="mt-1 text-base font-medium {{ $jadwalKegiatan->isOverdue() ? 'text-red-600' : 'text-blue-600' }}">{{ $jadwalKegiatan->countdown_text ?: '-' }}</dd>
                </div>
                @if ($jadwalKegiatan->lokasi_atau_link && filter_var($jadwalKegiatan->lokasi_atau_link, FILTER_VALIDATE_URL))
                    <div>
                        <dt class="text-sm font-medium text-slate-500">Tautan</dt>
                        <dd class="mt-1">
                            <a href="{{ $jadwalKegiatan->lokasi_atau_link }}" target="_blank" class="text-sm font-medium text-blue-600 hover:text-blue-700">Buka Tautan &rarr;</a>
                        </dd>
                    </div>
                @endif
                @if ($jadwalKegiatan->synced_to_calendar)
                    <div>
                        <dt class="text-sm font-medium text-slate-500">Google Calendar</dt>
                        <dd class="mt-1 text-sm text-emerald-600">Tersinkronisasi</dd>
                    </div>
                @endif
            </dl>
        </x-card>
    </div>
</x-layouts.app>
