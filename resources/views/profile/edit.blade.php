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

                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">Nomor WhatsApp</label>
                    <x-input name="whatsapp_number" value="{{ old('whatsapp_number', $user->whatsapp_number) }}" placeholder="Contoh: 081234567890" />
                    <p class="mt-2 text-xs text-slate-500">Nomor ini akan dipakai untuk pengingat WhatsApp via Fonnte.</p>
                    <x-validation-error name="whatsapp_number" />
                </div>

                <div class="flex justify-end">
                    <x-button type="submit">Simpan Perubahan</x-button>
                </div>
            </form>
        </x-card>
    </div>
</x-layouts.app>
