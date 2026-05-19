<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" style="{{ \App\Support\BrandTheme::cssVariables() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name', 'Laravel Admin Starter Kit') }}</title>
    @unless (app()->environment('testing'))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endunless
</head>
<body class="min-h-screen bg-zinc-950 font-sans text-zinc-100 antialiased">
    <main class="flex min-h-screen items-center justify-center px-6 py-12">
        {{ $slot }}
    </main>
</body>
</html>
