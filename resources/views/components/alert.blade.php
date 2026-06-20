@if (session('success'))
    <div class="rounded-2xl bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-100 dark:border-emerald-900/30 px-4 py-3 text-sm font-medium text-emerald-700 dark:text-emerald-400">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="rounded-2xl bg-red-50 dark:bg-red-950/20 border border-red-100 dark:border-red-900/30 px-4 py-3 text-sm font-medium text-red-700 dark:text-red-400">
        {{ session('error') }}
    </div>
@endif
