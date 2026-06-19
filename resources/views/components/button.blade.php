@props(['variant' => 'primary', 'type' => 'button'])

@php
$classes = match ($variant) {
    'secondary' => 'inline-flex items-center justify-center rounded-2xl border border-slate-100 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-slate-100',
    'danger' => 'inline-flex items-center justify-center rounded-2xl bg-red-500 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-600 focus:outline-none focus:ring-4 focus:ring-red-100',
    'ghost' => 'inline-flex items-center justify-center rounded-2xl bg-transparent px-4 py-2.5 text-sm font-semibold text-pink-600 shadow-none transition hover:bg-pink-50 focus:outline-none focus:ring-4 focus:ring-pink-100',
    default => 'inline-flex items-center justify-center rounded-2xl bg-gradient-to-r from-pink-500 to-rose-500 px-4 py-2.5 text-sm font-semibold text-white shadow-[0_4px_14px_rgb(236,72,153,0.35)] transition hover:from-pink-600 hover:to-rose-600 hover:shadow-[0_6px_20px_rgb(236,72,153,0.4)] focus:outline-none focus:ring-4 focus:ring-pink-100',
};
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</button>
