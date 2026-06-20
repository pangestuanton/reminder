@props(['variant' => 'primary', 'type' => 'button'])

@php
$classes = match ($variant) {
    'secondary' => 'inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-slate-100',
    'danger' => 'inline-flex items-center justify-center rounded-2xl bg-red-500 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-600 focus:outline-none focus:ring-4 focus:ring-red-100',
    'ghost' => 'inline-flex items-center justify-center rounded-2xl bg-transparent px-4 py-2.5 text-sm font-semibold text-blue-600 shadow-none transition hover:bg-blue-50 focus:outline-none focus:ring-4 focus:ring-blue-100',
    default => 'inline-flex items-center justify-center rounded-2xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-100',
};
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</button>
