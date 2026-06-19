@props(['title' => 'Belum ada data', 'description' => 'Data akan muncul di sini setelah kamu menambahkannya.'])

<div class="rounded-3xl bg-white p-8 text-center shadow-[0_8px_30px_rgb(0,0,0,0.04)]">
    <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-pink-50 text-pink-500">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10m-11 9h12a2 2 0 002-2V7a2 2 0 00-2-2H6a2 2 0 00-2 2v11a2 2 0 002 2z" /></svg>
    </div>
    <h3 class="text-base font-semibold text-slate-900">{{ $title }}</h3>
    <p class="mt-2 text-sm text-slate-500">{{ $description }}</p>
    @if (trim($slot))
        <div class="mt-5">{{ $slot }}</div>
    @endif
</div>
