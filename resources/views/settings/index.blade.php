<x-layouts.app title="Pengaturan - Aviona Sync">
    <div class="max-w-2xl space-y-6">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Pengaturan</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Atur notifikasi, agenda harian, dan preferensi lainnya.</p>
        </div>

        <x-card>
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Notifikasi Telegram</h2>
            <form method="POST" action="{{ route('settings.notifications.update') }}" class="mt-4 space-y-4">
                @csrf
                @method('PUT')

                <div class="flex items-center gap-3">
                    <input type="checkbox" name="telegram_enabled" value="1" {{ $preferences->telegram_enabled ? 'checked' : '' }} class="h-4 w-4 rounded border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-pink-600 focus:ring-pink-500 dark:focus:ring-offset-slate-800" />
                    <label class="text-sm text-slate-700 dark:text-slate-300">Aktifkan notifikasi Telegram</label>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-xs font-medium text-slate-500 dark:text-slate-400">Jam Mulai Senyap</label>
                        <x-input name="quiet_hours_start" type="time" value="{{ $preferences->quiet_hours_start }}" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-slate-500 dark:text-slate-400">Jam Akhir Senyap</label>
                        <x-input name="quiet_hours_end" type="time" value="{{ $preferences->quiet_hours_end }}" />
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-xs font-medium text-slate-500 dark:text-slate-400">Nada Pesan</label>
                        <x-select name="tone">
                            @foreach (['friendly' => 'Ramah', 'formal' => 'Formal', 'casual' => 'Santai'] as $value => $label)
                                <option value="{{ $value }}" {{ $preferences->tone === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </x-select>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-slate-500 dark:text-slate-400">Detail Pesan</label>
                        <x-select name="detail_level">
                            @foreach (['compact' => 'Ringkas', 'normal' => 'Normal', 'detailed' => 'Detail'] as $value => $label)
                                <option value="{{ $value }}" {{ $preferences->detail_level === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </x-select>
                    </div>
                </div>

                <div class="space-y-2">
                    <div class="flex items-center gap-3">
                        <input type="checkbox" name="reminder_h3_enabled" value="1" {{ $preferences->reminder_h3_enabled ? 'checked' : '' }} class="h-4 w-4 rounded border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-pink-600 focus:ring-pink-500 dark:focus:ring-offset-slate-800" />
                        <label class="text-sm text-slate-700 dark:text-slate-300">Pengingat H-3</label>
                    </div>
                    <div class="flex items-center gap-3">
                        <input type="checkbox" name="reminder_h1_enabled" value="1" {{ $preferences->reminder_h1_enabled ? 'checked' : '' }} class="h-4 w-4 rounded border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-pink-600 focus:ring-pink-500 dark:focus:ring-offset-slate-800" />
                        <label class="text-sm text-slate-700 dark:text-slate-300">Pengingat H-1</label>
                    </div>
                    <div class="flex items-center gap-3">
                        <input type="checkbox" name="reminder_3h_enabled" value="1" {{ $preferences->reminder_3h_enabled ? 'checked' : '' }} class="h-4 w-4 rounded border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-pink-600 focus:ring-pink-500 dark:focus:ring-offset-slate-800" />
                        <label class="text-sm text-slate-700 dark:text-slate-300">Pengingat 3 jam sebelum</label>
                    </div>
                    <div class="flex items-center gap-3">
                        <input type="checkbox" name="reminder_overdue_enabled" value="1" {{ $preferences->reminder_overdue_enabled ? 'checked' : '' }} class="h-4 w-4 rounded border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-pink-600 focus:ring-pink-500 dark:focus:ring-offset-slate-800" />
                        <label class="text-sm text-slate-700 dark:text-slate-300">Pengingat terlambat</label>
                    </div>
                </div>

                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-500 dark:text-slate-400">Maksimal Notifikasi per Hari</label>
                    <x-input name="reminder_max_per_day" type="number" value="{{ $preferences->reminder_max_per_day }}" min="1" max="50" />
                </div>

                <div class="flex justify-end">
                    <x-button type="submit">Simpan Pengaturan</x-button>
                </div>
            </form>
        </x-card>

        <x-card>
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Agenda Harian</h2>
            <form method="POST" action="{{ route('settings.agenda.update') }}" class="mt-4 space-y-4">
                @csrf
                @method('PUT')

                <div class="flex items-center gap-3">
                    <input type="checkbox" name="daily_agenda_enabled" value="1" {{ $user->daily_agenda_enabled ? 'checked' : '' }} class="h-4 w-4 rounded border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-pink-600 focus:ring-pink-500 dark:focus:ring-offset-slate-800" />
                    <label class="text-sm text-slate-700 dark:text-slate-300">Aktifkan agenda harian (jam 05:00)</label>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-xs font-medium text-slate-500 dark:text-slate-400">Waktu Kirim</label>
                        <x-input name="daily_agenda_time" type="time" value="{{ $user->daily_agenda_time }}" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-slate-500 dark:text-slate-400">Format</label>
                        <x-select name="daily_agenda_format">
                            <option value="compact" {{ $user->daily_agenda_format === 'compact' ? 'selected' : '' }}>Ringkas</option>
                            <option value="detailed" {{ $user->daily_agenda_format === 'detailed' ? 'selected' : '' }}>Detail</option>
                        </x-select>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <input type="checkbox" name="daily_agenda_include_overdue" value="1" {{ $user->daily_agenda_include_overdue ? 'checked' : '' }} class="h-4 w-4 rounded border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-pink-600 focus:ring-pink-500 dark:focus:ring-offset-slate-800" />
                    <label class="text-sm text-slate-700 dark:text-slate-300">Sertakan tugas terlambat</label>
                </div>

                <div class="flex justify-end">
                    <x-button type="submit">Simpan Agenda</x-button>
                </div>
            </form>
        </x-card>
    </div>
</x-layouts.app>

