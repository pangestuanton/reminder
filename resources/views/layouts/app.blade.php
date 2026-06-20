@php
    $userTheme = auth()->check() ? auth()->user()->theme_preference : null;
@endphp

<!DOCTYPE html>
<html lang="id" x-data="themeApp()" x-init="initTheme()" :class="{ 'dark': darkMode }" x-cloak>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-preference" content="{{ $userTheme ?? 'system' }}">
    <title>{{ $title ?? 'Aviona Sync' }}</title>
    <script>
        (function() {
            var mode = localStorage.getItem('theme') || '{{ addslashes($userTheme ?? 'system') }}';
            if (mode === 'system') {
                mode = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            }
            if (mode === 'dark') {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>
    <style>[x-cloak] { display: none !important; }</style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 dark:bg-slate-950 font-sans text-slate-900 dark:text-slate-100 antialiased transition-colors duration-200">
    <div class="min-h-screen">
        <x-navbar />
        <main class="px-4 py-6 sm:px-6 lg:px-8 md:py-8">
            <div class="mx-auto max-w-6xl space-y-6">
                <x-alert />
                {{ $slot }}
            </div>
        </main>
    </div>
</body>
</html>
