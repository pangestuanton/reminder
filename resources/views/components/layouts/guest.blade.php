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
    cycleTheme() {
        const modes = ['light', 'dark', 'system'];
        const next = modes[(modes.indexOf(this.themeMode) + 1) % modes.length];
        this.setTheme(next);
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
<body class="min-h-screen bg-gradient-to-br from-pink-50 via-rose-50/30 to-slate-50 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950 font-sans text-slate-900 dark:text-slate-100 antialiased transition-colors duration-200">
    <main class="relative flex min-h-screen items-center justify-center overflow-hidden px-4 py-10 sm:px-6 lg:px-8">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(236,72,153,0.06),_transparent_50%),radial-gradient(circle_at_bottom_right,_rgba(251,113,133,0.05),_transparent_50%)] dark:bg-[radial-gradient(circle_at_top_left,_rgba(236,72,153,0.12),_transparent_50%),radial-gradient(circle_at_bottom_right,_rgba(251,113,133,0.08),_transparent_50%)]"></div>
        <div class="relative w-full max-w-md">
            <x-alert />
            {{ $slot }}
        </div>
    </main>
</body>
</html>
