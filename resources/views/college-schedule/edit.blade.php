<x-layouts.app title="Edit Jadwal Kuliah - Aviona Sync">
    <div class="max-w-2xl space-y-6">
        <div>
            <a href="{{ route('college-schedule.show', $schedule) }}" class="text-sm font-medium text-pink-600 hover:text-pink-700 dark:text-pink-400 dark:hover:text-pink-300">&larr; Kembali</a>
            <h1 class="mt-2 text-2xl md:text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Edit Jadwal Kuliah</h1>
        </div>

        <x-card>
            <form method="POST" action="{{ route('college-schedule.update', $schedule) }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300">Mata Kuliah *</label>
                    <x-input name="mata_kuliah" value="{{ old('mata_kuliah', $schedule->mata_kuliah) }}" required />
                    <x-validation-error name="mata_kuliah" />
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300">Dosen</label>
                    <x-input name="dosen" value="{{ old('dosen', $schedule->dosen) }}" />
                    <x-validation-error name="dosen" />
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300">Hari *</label>
                        <x-select name="hari" required>
                            @foreach (['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'] as $day)
                                <option value="{{ $day }}" {{ old('hari', $schedule->hari) === $day ? 'selected' : '' }}>{{ $day }}</option>
                            @endforeach
                        </x-select>
                        <x-validation-error name="hari" />
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300">Warna</label>
                        <x-input name="warna" type="color" value="{{ old('warna', $schedule->warna) }}" />
                        <x-validation-error name="warna" />
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300">Jam Mulai *</label>
                        <x-input name="jam_mulai" type="time" value="{{ old('jam_mulai', $schedule->jam_mulai) }}" required />
                        <x-validation-error name="jam_mulai" />
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300">Jam Selesai *</label>
                        <x-input name="jam_selesai" type="time" value="{{ old('jam_selesai', $schedule->jam_selesai) }}" required />
                        <x-validation-error name="jam_selesai" />
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300">Lokasi / Link Meeting</label>
                    <x-input name="lokasi" value="{{ old('lokasi', $schedule->lokasi) }}" />
                    <x-validation-error name="lokasi" />
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300">Catatan</label>
                    <x-textarea name="catatan" rows="3">{{ old('catatan', $schedule->catatan) }}</x-textarea>
                    <x-validation-error name="catatan" />
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300">Pengingat (menit)</label>
                        <x-input name="reminder_minutes" type="number" value="{{ old('reminder_minutes', $schedule->reminder_minutes) }}" min="0" max="1440" />
                        <x-validation-error name="reminder_minutes" />
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300">Semester Mulai</label>
                        <x-input name="semester_mulai" type="date" value="{{ old('semester_mulai', $schedule->semester_mulai?->format('Y-m-d')) }}" />
                        <x-validation-error name="semester_mulai" />
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300">Semester Akhir</label>
                        <x-input name="semester_akhir" type="date" value="{{ old('semester_akhir', $schedule->semester_akhir?->format('Y-m-d')) }}" />
                        <x-validation-error name="semester_akhir" />
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('college-schedule.show', $schedule) }}"><x-button variant="secondary">Batal</x-button></a>
                    <x-button type="submit">Simpan Perubahan</x-button>
                </div>
            </form>
        </x-card>
    </div>
</x-layouts.app>
