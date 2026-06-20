@props(['padding' => 'p-5 md:p-6'])

<div {{ $attributes->merge(['class' => 'rounded-3xl border border-slate-100 dark:border-slate-700/60 bg-white dark:bg-slate-800 shadow-sm transition hover:shadow-md '.$padding]) }}>
    {{ $slot }}
</div>
