@props(['variant' => 'primary', 'type' => 'button'])

@php
$classes = match ($variant) {
    'secondary' => 'inline-flex items-center justify-center rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm font-semibold text-slate-700 dark:text-slate-300 shadow-sm transition hover:bg-slate-50 dark:hover:bg-slate-700 focus:outline-none focus:ring-4 focus:ring-slate-100 dark:focus:ring-slate-900',
    'danger' => 'inline-flex items-center justify-center rounded-2xl bg-red-500 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-600 focus:outline-none focus:ring-4 focus:ring-red-100 dark:focus:ring-red-950',
    'ghost' => 'inline-flex items-center justify-center rounded-2xl bg-transparent px-4 py-2.5 text-sm font-semibold text-blue-600 dark:text-blue-400 shadow-none transition hover:bg-blue-50 dark:hover:bg-blue-900/30 focus:outline-none focus:ring-4 focus:ring-blue-100 dark:focus:ring-blue-950',
    default => 'inline-flex items-center justify-center rounded-2xl bg-blue-600 dark:bg-blue-500 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 dark:hover:bg-blue-600 focus:outline-none focus:ring-4 focus:ring-blue-100 dark:focus:ring-blue-950',
};
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</button>
