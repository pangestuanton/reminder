@props(['padding' => 'p-5 md:p-6'])

<div {{ $attributes->merge(['class' => 'rounded-3xl border border-slate-100 bg-white shadow-sm transition hover:shadow-md '.$padding]) }}>
    {{ $slot }}
</div>
