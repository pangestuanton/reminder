<x-layouts.app title="Tugas - Aviona Sync">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-slate-900">Tugas</h1>
            <p class="mt-1 text-sm text-slate-500">Kelola tugas, deadline, dan kegiatanmu.</p>
        </div>
        <a href="{{ route('jadwal-kegiatan.create') }}"><x-button>Tambah Tugas</x-button></a>
    </div>

    <x-card>
        <form method="GET" action="{{ route('jadwal-kegiatan.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="xl:col-span-2">
                <label class="mb-2 block text-sm font-medium text-slate-700">Cari</label>
                <x-input name="q" value="{{ request('q') }}" placeholder="Cari judul, deskripsi, atau lokasi" />
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">Kategori</label>
                <x-select name="kategori">
                    <option value="">Semua kategori</option>
                    @foreach (['kuliah' => 'Kuliah', 'tugas' => 'Tugas', 'uts' => 'UTS', 'uas' => 'UAS', 'organisasi' => 'Organisasi'] as $value => $label)
                        <option value="{{ $value }}" @selected(request('kategori') === $value)>{{ $label }}</option>
                    @endforeach
                </x-select>
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">Status</label>
                <x-select name="status">
                    <option value="">Semua status</option>
                    @foreach (['pending' => 'Menunggu', 'selesai' => 'Selesai', 'dibatalkan' => 'Dibatalkan'] as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                    @endforeach
                </x-select>
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">Prioritas</label>
                <x-select name="prioritas">
                    <option value="">Semua prioritas</option>
                    @foreach (['rendah' => 'Rendah', 'sedang' => 'Sedang', 'tinggi' => 'Tinggi'] as $value => $label)
                        <option value="{{ $value }}" @selected(request('prioritas') === $value)>{{ $label }}</option>
                    @endforeach
                </x-select>
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">Sumber</label>
                <x-select name="source">
                    <option value="">Semua sumber</option>
                    <option value="local" @selected(request('source') === 'local')>Lokal</option>
                    <option value="classroom" @selected(request('source') === 'classroom')>Classroom</option>
                    <option value="calendar" @selected(request('source') === 'calendar')>Calendar</option>
                </x-select>
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">Mulai Tanggal</label>
                <x-input type="date" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}" />
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">Sampai Tanggal</label>
                <x-input type="date" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}" />
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">Urutkan</label>
                <x-select name="sort">
                    <option value="nearest" @selected(request('sort', 'nearest') === 'nearest')>Deadline terdekat</option>
                    <option value="newest" @selected(request('sort') === 'newest')>Terbaru</option>
                    <option value="oldest" @selected(request('sort') === 'oldest')>Terlama</option>
                    <option value="priority" @selected(request('sort') === 'priority')>Prioritas</option>
                </x-select>
            </div>
            <div class="flex items-end gap-3">
                <x-button type="submit">Filter</x-button>
                <a href="{{ route('jadwal-kegiatan.index') }}"><x-button type="button" variant="secondary">Reset</x-button></a>
            </div>
        </form>
    </x-card>

    <div class="space-y-4">
        @forelse ($jadwalKegiatans as $jadwal)
            <x-card>
                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div class="space-y-3">
                        <div class="flex flex-wrap gap-2">
                            <x-badge>{{ ucfirst($jadwal->kategori) }}</x-badge>
                            <x-badge type="{{ $jadwal->isOverdue() ? 'terlambat' : ($jadwal->status === 'pending' ? 'pending' : 'selesai') }}">{{ $jadwal->isOverdue() ? 'Terlambat' : ($jadwal->status === 'pending' ? 'Menunggu' : ($jadwal->status === 'selesai' ? 'Selesai' : 'Dibatalkan')) }}</x-badge>
                            @if ($jadwal->source !== 'local')
                                <x-badge type="blue">{{ $jadwal->source_label }}</x-badge>
                            @endif
                            @if ($jadwal->daysUntilDue() <= 3 && $jadwal->status === 'pending' && ! $jadwal->isOverdue())
                                <x-badge type="mendesak">Mendesak</x-badge>
                            @endif
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-slate-900">{{ $jadwal->judul }}</h2>
                            <p class="mt-1 text-sm text-slate-500">{{ $jadwal->waktu_pelaksanaan ? $jadwal->waktu_pelaksanaan->translatedFormat('l, d F Y \a\t H.i') : 'Tanpa deadline' }}</p>
                            @if ($jadwal->course_name)
                                <p class="mt-0.5 text-xs text-slate-400">{{ $jadwal->course_name }}</p>
                            @endif
                            @if ($jadwal->lokasi_atau_link)
                                <p class="mt-1 text-sm text-slate-500">{{ $jadwal->lokasi_atau_link }}</p>
                            @endif
                            @if ($jadwal->countdown_text)
                                <p class="mt-2 text-sm font-medium {{ $jadwal->isOverdue() ? 'text-red-600' : 'text-blue-600' }}">
                                    {{ $jadwal->countdown_text }}
                                </p>
                            @endif
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-3 lg:justify-end">
                        <a href="{{ route('jadwal-kegiatan.show', $jadwal) }}"><x-button variant="secondary">Detail</x-button></a>
                        @if ($jadwal->source === 'local')
                            <a href="{{ route('jadwal-kegiatan.edit', $jadwal) }}"><x-button variant="secondary">Edit</x-button></a>
                        @endif
                        @if ($jadwal->status === 'pending')
                            <form method="POST" action="{{ route('jadwal-kegiatan.complete', $jadwal) }}">
                                @csrf
                                @method('PATCH')
                                <x-button type="submit">Selesai</x-button>
                            </form>
                        @endif
                        @if ($jadwal->source === 'local')
                            <x-modal message="Tugas yang dihapus tidak bisa dikembalikan.">
                                <x-slot:trigger>
                                    <button type="button" class="inline-flex items-center justify-center rounded-2xl bg-red-50 px-4 py-2.5 text-sm font-semibold text-red-700 transition hover:bg-red-100">Hapus</button>
                                </x-slot:trigger>
                                <form method="POST" action="{{ route('jadwal-kegiatan.destroy', $jadwal) }}">
                                    @csrf
                                    @method('DELETE')
                                    <x-button variant="danger" type="submit">Ya, Hapus</x-button>
                                </form>
                            </x-modal>
                        @endif
                    </div>
                </div>
            </x-card>
        @empty
            <x-empty-state title="Belum ada tugas yang cocok" description="Coba ubah filter atau tambahkan tugas baru.">
                <a href="{{ route('jadwal-kegiatan.create') }}"><x-button>Tambah Tugas</x-button></a>
            </x-empty-state>
        @endforelse
    </div>

    <div>
        {{ $jadwalKegiatans->links() }}
    </div>
</x-layouts.app>
