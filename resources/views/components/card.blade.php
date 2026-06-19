@props(['padding' => 'p-5 md:p-6'])

<div {{ $attributes->merge(['class' => 'rounded-3xl border border-slate-100 bg-white shadow-[0_8px_30px_rgb(0,0,0,0.04)] transition hover:shadow-[0_12px_40px_rgb(236,72,153,0.06)] '.$padding]) }}>
    {{ $slot }}
</div>
