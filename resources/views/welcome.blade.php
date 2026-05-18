<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel Portfolio Starter Kit') }}</title>
    @unless (app()->environment('testing'))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endunless
</head>
<body class="bg-stone-50 text-zinc-950 antialiased">
    <header class="border-b border-zinc-200 bg-white">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-5">
            <a href="{{ route('home') }}" class="font-semibold tracking-tight">Portfolio Starter Kit</a>
            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="rounded bg-zinc-950 px-3 py-2 text-sm font-medium text-white">Panel</a>
                @else
                    <a href="{{ route('login') }}" class="rounded border border-zinc-300 px-3 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-100">Entrar</a>
                @endauth
            </div>
        </div>
    </header>

    <main>
        <section class="mx-auto grid max-w-6xl gap-10 px-6 py-16 lg:grid-cols-[1fr_26rem] lg:items-center">
            <div>
                <p class="text-sm font-medium uppercase tracking-wide text-zinc-500">Laravel, Livewire, Tailwind CSS</p>
                <h1 class="mt-4 text-4xl font-semibold tracking-tight md:text-6xl">Starter kit profesional para administrar portafolios.</h1>
                <p class="mt-6 max-w-2xl text-lg text-zinc-600">Una base Laravel enfocada con roles, panel admin, CRUD de proyectos, subida de imágenes, filtros, datos demo y pruebas Pest.</p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('login') }}" class="rounded bg-zinc-950 px-4 py-2.5 text-sm font-semibold text-white hover:bg-zinc-800">Abrir admin</a>
                    <a href="#projects" class="rounded border border-zinc-300 px-4 py-2.5 text-sm font-semibold text-zinc-700 hover:bg-white">Ver proyectos</a>
                </div>
            </div>
            <div class="rounded border border-zinc-200 bg-white p-6 shadow-sm">
                <dl class="grid grid-cols-2 gap-4">
                    <div class="rounded bg-stone-100 p-4">
                        <dt class="text-sm text-zinc-500">Roles</dt>
                        <dd class="mt-2 text-2xl font-semibold">3</dd>
                    </div>
                    <div class="rounded bg-stone-100 p-4">
                        <dt class="text-sm text-zinc-500">CRUDs</dt>
                        <dd class="mt-2 text-2xl font-semibold">2</dd>
                    </div>
                    <div class="rounded bg-stone-100 p-4">
                        <dt class="text-sm text-zinc-500">Tests</dt>
                        <dd class="mt-2 text-2xl font-semibold">Pest</dd>
                    </div>
                    <div class="rounded bg-stone-100 p-4">
                        <dt class="text-sm text-zinc-500">UI</dt>
                        <dd class="mt-2 text-2xl font-semibold">Livewire</dd>
                    </div>
                </dl>
            </div>
        </section>

        <section id="projects" class="border-t border-zinc-200 bg-white">
            <div class="mx-auto max-w-6xl px-6 py-14">
                <div class="mb-8 flex items-end justify-between gap-4">
                    <div>
                        <p class="text-sm font-medium text-zinc-500">Trabajo publicado</p>
                        <h2 class="mt-2 text-2xl font-semibold tracking-tight">Proyectos recientes</h2>
                    </div>
                </div>
                <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                    @forelse ($projects as $project)
                        <article class="overflow-hidden rounded border border-zinc-200 bg-white">
                            <div class="aspect-video bg-zinc-100">
                                @if ($project->image_path)
                                    <img src="{{ Storage::url($project->image_path) }}" alt="" class="h-full w-full object-cover">
                                @endif
                            </div>
                            <div class="p-5">
                                <h3 class="font-semibold">{{ $project->title }}</h3>
                                <p class="mt-2 line-clamp-3 text-sm text-zinc-600">{{ $project->description }}</p>
                            </div>
                        </article>
                    @empty
                        <p class="text-sm text-zinc-500">Ejecuta los seeders demo para mostrar proyectos de ejemplo.</p>
                    @endforelse
                </div>
            </div>
        </section>
    </main>
</body>
</html>
