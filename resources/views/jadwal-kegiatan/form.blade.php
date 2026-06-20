@php
    $isEdit = isset($jadwalKegiatan) && $jadwalKegiatan->exists;
    $route = $isEdit ? route('jadwal-kegiatan.update', $jadwalKegiatan) : route('jadwal-kegiatan.store');
@endphp

<x-layouts.app title="{{ $isEdit ? 'Edit Jadwal' : 'Tambah Jadwal' }} - Aviona Sync">
    <div class="max-w-3xl space-y-6">
        <div>
            <a href="{{ route('jadwal-kegiatan.index') }}" class="text-sm font-semibold text-pink-600 hover:text-pink-700 dark:text-pink-400 dark:hover:text-pink-300">← Kembali</a>
            <h1 class="mt-3 text-2xl md:text-3xl font-bold tracking-tight text-slate-900 dark:text-white">{{ $isEdit ? 'Edit Jadwal' : 'Tambah Jadwal' }}</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Periksa kembali data jadwalmu sebelum menyimpan.</p>
        </div>

        <x-card>
            <form method="POST" action="{{ $route }}" class="space-y-5">
                @csrf
                @if ($isEdit)
                    @method('PUT')
                @endif

                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300">Judul</label>
                    <x-input name="judul" value="{{ old('judul', $jadwalKegiatan->judul) }}" placeholder="Contoh: Pengumpulan Tugas Akhir" required />
                    <x-validation-error name="judul" />
                </div>

                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300">Kategori</label>
                        <x-select name="kategori" required>
                            @foreach (['kuliah' => 'Kuliah', 'tugas' => 'Tugas', 'uts' => 'UTS', 'uas' => 'UAS', 'organisasi' => 'Organisasi'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('kategori', $jadwalKegiatan->kategori) === $value)>{{ $label }}</option>
                            @endforeach
                        </x-select>
                        <x-validation-error name="kategori" />
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300">Waktu Pelaksanaan</label>
                        <x-input name="waktu_pelaksanaan" type="datetime-local" value="{{ old('waktu_pelaksanaan', optional($jadwalKegiatan->waktu_pelaksanaan)->format('Y-m-d\TH:i')) }}" required />
                        <x-validation-error name="waktu_pelaksanaan" />
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300">Status</label>
                        <x-select name="status" required>
                            @foreach (['pending' => 'Menunggu', 'selesai' => 'Selesai', 'dibatalkan' => 'Dibatalkan'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('status', $jadwalKegiatan->status) === $value)>{{ $label }}</option>
                            @endforeach
                        </x-select>
                        <x-validation-error name="status" />
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300">Prioritas</label>
                        <x-select name="prioritas" required>
                            @foreach (['rendah' => 'Rendah', 'sedang' => 'Sedang', 'tinggi' => 'Tinggi'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('prioritas', $jadwalKegiatan->prioritas) === $value)>{{ $label }}</option>
                            @endforeach
                        </x-select>
                        <x-validation-error name="prioritas" />
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300">Lokasi atau Link</label>
                    <x-input name="lokasi_atau_link" value="{{ old('lokasi_atau_link', $jadwalKegiatan->lokasi_atau_link) }}" placeholder="Ruang kelas, alamat, atau tautan meeting" />
                    <x-validation-error name="lokasi_atau_link" />
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300">Deskripsi</label>
                    <x-textarea name="deskripsi" rows="5" placeholder="Tambahkan catatan penting jika diperlukan">{{ old('deskripsi', $jadwalKegiatan->deskripsi) }}</x-textarea>
                    <x-validation-error name="deskripsi" />
                </div>

                <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                    <a href="{{ route('jadwal-kegiatan.index') }}"><x-button variant="secondary" type="button">Batal</x-button></a>
                    <x-button type="submit">{{ $isEdit ? 'Simpan Perubahan' : 'Simpan' }}</x-button>
                </div>
            </form>
        </x-card>
    </div>
</x-layouts.app>

