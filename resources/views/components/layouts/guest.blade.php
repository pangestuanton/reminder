@props(['title' => 'Aviona Sync'])

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-br from-pink-50 via-rose-50/30 to-slate-50 font-sans text-slate-900 antialiased">
    <main class="relative flex min-h-screen items-center justify-center overflow-hidden px-4 py-10 sm:px-6 lg:px-8">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(236,72,153,0.06),_transparent_50%),radial-gradient(circle_at_bottom_right,_rgba(251,113,133,0.05),_transparent_50%)]"></div>
        <div class="relative w-full max-w-md">
            <x-alert />
            {{ $slot }}
        </div>
    </main>
</body>
</html>
