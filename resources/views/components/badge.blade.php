@props(['type' => 'default'])

@php
$classes = match ($type) {
    'pending' => 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
    'selesai' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
    'dibatalkan' => 'bg-slate-100 text-slate-600 dark:bg-slate-700/50 dark:text-slate-400',
    'terlambat' => 'bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-400',
    'mendesak' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
    'blue' => 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
    default => 'bg-slate-100 text-slate-700 dark:bg-slate-700/50 dark:text-slate-300',
};
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold '.$classes]) }}>
    {{ $slot }}
</span>
