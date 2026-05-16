<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name', 'Laravel Portfolio Starter Kit') }}</title>
    @unless (app()->environment('testing'))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endunless
</head>
<body class="min-h-screen bg-stone-50 text-zinc-950 antialiased">
    <div class="min-h-screen lg:flex">
        <aside class="border-b border-zinc-200 bg-white lg:min-h-screen lg:w-64 lg:border-b-0 lg:border-r">
            <div class="flex h-16 items-center justify-between px-6 lg:h-20">
                <a href="{{ route('dashboard') }}" class="font-semibold tracking-tight">Portfolio Kit</a>
                <span class="rounded bg-zinc-100 px-2 py-1 text-xs font-medium text-zinc-600">{{ auth()->user()?->roles->first()?->name ?? 'user' }}</span>
            </div>
            <nav class="flex gap-1 overflow-x-auto px-3 pb-3 lg:block lg:space-y-1 lg:px-4">
                <a href="{{ route('dashboard') }}" @class(['block rounded px-3 py-2 text-sm font-medium', 'bg-zinc-950 text-white' => request()->routeIs('dashboard'), 'text-zinc-600 hover:bg-zinc-100 hover:text-zinc-950' => ! request()->routeIs('dashboard')])>Dashboard</a>
                @role('admin')
                    <a href="{{ route('admin.users') }}" @class(['block rounded px-3 py-2 text-sm font-medium', 'bg-zinc-950 text-white' => request()->routeIs('admin.users'), 'text-zinc-600 hover:bg-zinc-100 hover:text-zinc-950' => ! request()->routeIs('admin.users')])>Users</a>
                @endrole
                @role('admin|editor')
                    <a href="{{ route('admin.projects') }}" @class(['block rounded px-3 py-2 text-sm font-medium', 'bg-zinc-950 text-white' => request()->routeIs('admin.projects'), 'text-zinc-600 hover:bg-zinc-100 hover:text-zinc-950' => ! request()->routeIs('admin.projects')])>Projects</a>
                @endrole
            </nav>
        </aside>

        <div class="flex min-w-0 flex-1 flex-col">
            <header class="flex h-16 items-center justify-between border-b border-zinc-200 bg-white px-6">
                <div>
                    <p class="text-sm text-zinc-500">Admin dashboard</p>
                    <h1 class="text-lg font-semibold">{{ $title ?? 'Dashboard' }}</h1>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="rounded bg-zinc-950 px-3 py-2 text-sm font-medium text-white hover:bg-zinc-800">Logout</button>
                </form>
            </header>

            <main class="flex-1 px-6 py-6">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
