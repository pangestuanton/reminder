<x-layouts.app title="Tambah Jadwal Kuliah - Aviona Sync">
    <div class="max-w-2xl space-y-6">
        <div>
            <a href="{{ route('college-schedule.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700">&larr; Kembali</a>
            <h1 class="mt-2 text-2xl md:text-3xl font-bold tracking-tight text-slate-900">Tambah Jadwal Kuliah</h1>
        </div>

        <x-card>
            <form method="POST" action="{{ route('college-schedule.store') }}" class="space-y-5">
                @csrf

                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">Mata Kuliah *</label>
                    <x-input name="mata_kuliah" value="{{ old('mata_kuliah') }}" placeholder="Contoh: Pemrograman Web" required />
                    <x-validation-error name="mata_kuliah" />
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">Dosen</label>
                    <x-input name="dosen" value="{{ old('dosen') }}" placeholder="Nama dosen pengajar" />
                    <x-validation-error name="dosen" />
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">Hari *</label>
                        <x-select name="hari" required>
                            <option value="">Pilih hari</option>
                            @foreach (['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'] as $day)
                                <option value="{{ $day }}" {{ old('hari') === $day ? 'selected' : '' }}>{{ $day }}</option>
                            @endforeach
                        </x-select>
                        <x-validation-error name="hari" />
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">Warna</label>
                        <x-input name="warna" type="color" value="{{ old('warna', '#3B82F6') }}" />
                        <x-validation-error name="warna" />
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">Jam Mulai *</label>
                        <x-input name="jam_mulai" type="time" value="{{ old('jam_mulai') }}" required />
                        <x-validation-error name="jam_mulai" />
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">Jam Selesai *</label>
                        <x-input name="jam_selesai" type="time" value="{{ old('jam_selesai') }}" required />
                        <x-validation-error name="jam_selesai" />
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">Lokasi / Link Meeting</label>
                    <x-input name="lokasi" value="{{ old('lokasi') }}" placeholder="Ruang A.1.01 atau link Google Meet" />
                    <x-validation-error name="lokasi" />
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">Catatan</label>
                    <x-textarea name="catatan" rows="3" placeholder="Catatan tambahan untuk jadwal ini...">{{ old('catatan') }}</x-textarea>
                    <x-validation-error name="catatan" />
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">Pengingat (menit)</label>
                        <x-input name="reminder_minutes" type="number" value="{{ old('reminder_minutes', 30) }}" min="0" max="1440" />
                        <x-validation-error name="reminder_minutes" />
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">Semester Mulai</label>
                        <x-input name="semester_mulai" type="date" value="{{ old('semester_mulai') }}" />
                        <x-validation-error name="semester_mulai" />
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">Semester Akhir</label>
                        <x-input name="semester_akhir" type="date" value="{{ old('semester_akhir') }}" />
                        <x-validation-error name="semester_akhir" />
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('college-schedule.index') }}"><x-button variant="secondary">Batal</x-button></a>
                    <x-button type="submit">Simpan</x-button>
                </div>
            </form>
        </x-card>
    </div>
</x-layouts.app>
