<x-layouts.app title="Profil - Aviona Sync">
    <div class="max-w-2xl space-y-6">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-slate-900">Profil</h1>
            <p class="mt-1 text-sm text-slate-500">Perbarui data akunmu di sini.</p>
        </div>

        <x-card>
            <form method="POST" action="{{ route('profile.update') }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">Nama Lengkap</label>
                    <x-input name="name" value="{{ old('name', $user->name) }}" required />
                    <x-validation-error name="name" />
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">Alamat Email</label>
                    <x-input name="email" type="email" value="{{ old('email', $user->email) }}" required />
                    <x-validation-error name="email" />
                </div>

                <div class="flex justify-end">
                    <x-button type="submit">Simpan Perubahan</x-button>
                </div>
            </form>
        </x-card>

        <x-card>
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Notifikasi Telegram</h2>

                    @if ($user->telegram_chat_id)
                        <p class="mt-1 text-sm text-emerald-600">
                            Terhubung{{ $user->telegram_linked_at ? ' sejak '.$user->telegram_linked_at->translatedFormat('d F Y H.i') : '' }}.
                        </p>
                    @else
                        <p class="mt-1 text-sm text-slate-500">
                            Hubungkan akun Telegram agar semua pengingat jadwal dikirim melalui bot.
                        </p>
                    @endif
                </div>

                @if ($user->telegram_chat_id)
                    <form method="POST" action="{{ route('profile.telegram.destroy') }}">
                        @csrf
                        @method('DELETE')
                        <x-button type="submit" variant="danger">Lepas Telegram</x-button>
                    </form>
                @else
                    <form method="POST" action="{{ route('profile.telegram.store') }}">
                        @csrf
                        <x-button type="submit">Hubungkan Telegram</x-button>
                    </form>
                @endif
            </div>
        </x-card>
    </div>
</x-layouts.app>
