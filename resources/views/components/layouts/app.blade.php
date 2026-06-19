@props(['title' => 'Aviona Sync'])

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 font-sans text-slate-900 antialiased">
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
