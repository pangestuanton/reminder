<!DOCTYPE html>
<html lang="id" x-data="{ 
    darkMode: localStorage.getItem('darkMode') === 'true' || (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)
}" :class="{ 'dark': darkMode }" x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Aviona Sync' }}</title>
    <script>
        if (localStorage.getItem('darkMode') === 'true' || (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 dark:bg-slate-950 font-sans text-slate-900 dark:text-slate-100 antialiased transition-colors duration-200">
    <main class="flex min-h-screen items-center justify-center px-4 py-8 sm:px-6 lg:px-8">
        <div class="w-full max-w-md space-y-6">
            <div class="text-center">
                <x-app-logo />
            </div>
            <x-alert />
            {{ $slot }}
        </div>
    </main>
</body>
</html>
