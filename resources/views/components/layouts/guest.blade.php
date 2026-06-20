@props(['title' => 'Aviona Sync'])

<!DOCTYPE html>
<html lang="id" x-data="{ 
    darkMode: localStorage.getItem('darkMode') === 'true' || (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)
}" :class="{ 'dark': darkMode }" x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    <script>
        if (localStorage.getItem('darkMode') === 'true' || (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
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

