@props(['message' => 'Apakah kamu yakin ingin melanjutkan?'])

<div x-data="{ open: false }" class="inline-block">
    <div @click="open = true">
        {{ $trigger }}
    </div>

    <div x-show="open" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 px-4" style="display:none;">
        <div class="w-full max-w-md rounded-3xl bg-white dark:bg-slate-800 p-6 shadow-[0_8px_30px_rgb(0,0,0,0.12)] border border-transparent dark:border-slate-700/60">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Konfirmasi</h3>
            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">{{ $message }}</p>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" @click="open = false" class="inline-flex items-center justify-center rounded-2xl border border-slate-100 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm font-semibold text-slate-700 dark:text-slate-300 shadow-sm transition hover:bg-slate-50 dark:hover:bg-slate-700">Batal</button>
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
