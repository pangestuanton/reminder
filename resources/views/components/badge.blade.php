@props(['type' => 'default'])

@php
$classes = match ($type) {
    'pending' => 'bg-pink-50 text-pink-700',
    'selesai' => 'bg-emerald-50 text-emerald-700',
    'dibatalkan' => 'bg-slate-100 text-slate-600',
    'terlambat' => 'bg-red-50 text-red-700',
    'mendesak' => 'bg-amber-50 text-amber-700',
    default => 'bg-pink-50 text-pink-700',
};
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold '.$classes]) }}>
    {{ $slot }}
</span>
