<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Aviona Sync' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 font-sans text-slate-900 antialiased">
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
