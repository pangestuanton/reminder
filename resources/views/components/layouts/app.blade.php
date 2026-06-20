@props(['title' => 'Aviona Sync'])

@php
    $userTheme = auth()->check() ? auth()->user()->theme_preference : null;
@endphp

<!DOCTYPE html>
<html lang="id" x-data="{
    themeMode: '{{ $userTheme ?? 'system' }}',
    darkMode: false,
    initTheme() {
        const stored = localStorage.getItem('theme');
        this.themeMode = stored || '{{ $userTheme ?? 'system' }}';
        this.applyTheme();
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
            if (this.themeMode === 'system') this.applyTheme();
        });
        this.$watch('themeMode', (val) => {
            localStorage.setItem('theme', val);
            this.applyTheme();
            this.syncTheme(val);
        });
    },
    applyTheme() {
        if (this.themeMode === 'system') {
            this.darkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
        } else {
            this.darkMode = this.themeMode === 'dark';
        }
        document.documentElement.classList.toggle('dark', this.darkMode);
    },
    setTheme(mode) {
        this.themeMode = mode;
    },
    syncTheme(mode) {
        const token = document.querySelector('meta[name=\"csrf-token\"]');
        if (token) {
            fetch('/theme', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token.content,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ theme: mode })
            }).catch(() => {});
        }
    }
}" x-init="initTheme()" :class="{ 'dark': darkMode }" x-cloak>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }}</title>
    <script>
        (function() {
            var mode = localStorage.getItem('theme') || '{{ $userTheme ?? 'system' }}';
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
