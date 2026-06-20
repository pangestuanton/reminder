<select {{ $attributes->merge(['class' => 'w-full rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 text-sm text-slate-900 dark:text-white shadow-sm outline-none transition focus:border-blue-500 dark:focus:border-blue-400 focus:ring-4 focus:ring-blue-100 dark:focus:ring-blue-900/30']) }}>
    {{ $slot }}
</select>
