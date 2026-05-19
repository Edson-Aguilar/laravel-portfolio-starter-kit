@php
    $brand = \App\Support\BrandTheme::get();
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" style="{{ \App\Support\BrandTheme::cssVariables() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $brand['brand_name'] }} - Starter Kit Laravel</title>
    @unless (app()->environment('testing'))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endunless
</head>
<body class="bg-slate-50 font-sans text-zinc-950 antialiased dark:bg-zinc-950 dark:text-zinc-100">
    <header class="border-b border-zinc-200 bg-white/90 backdrop-blur dark:border-white/10 dark:bg-zinc-950/90">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-5">
            <a href="{{ route('home') }}" class="font-semibold tracking-tight">{{ $brand['brand_name'] }}</a>
            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="rounded-lg bg-zinc-950 px-3 py-2 text-sm font-medium text-white hover:bg-zinc-800 dark:bg-white dark:text-zinc-950">Ver dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="rounded-lg border border-zinc-300 px-3 py-2 text-sm font-medium text-zinc-700 hover:bg-white dark:border-white/10 dark:text-zinc-200 dark:hover:bg-white/10">Iniciar sesión demo</a>
                @endauth
            </div>
        </div>
    </header>

    <main>
        <section class="mx-auto grid max-w-6xl gap-10 px-6 py-16 lg:grid-cols-[1fr_26rem] lg:items-center">
            <div>
                <p class="text-sm font-bold uppercase tracking-wide text-[var(--brand-primary)]">Laravel, Livewire, Tailwind CSS</p>
                <h1 class="mt-4 text-4xl font-semibold tracking-tight md:text-6xl">Starter kit profesional para iniciar proyectos Laravel más rápido.</h1>
                <p class="mt-6 max-w-2xl text-lg text-zinc-600 dark:text-zinc-300">Una base reutilizable con dashboard admin, roles y permisos, CRUD generator, API Sanctum, feature flags, dark mode, responsive, tests Pest y decisiones de seguridad listas para extender.</p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('dashboard') }}" class="rounded-lg bg-zinc-950 px-4 py-2.5 text-sm font-semibold text-white hover:bg-zinc-800 dark:bg-white dark:text-zinc-950">Ver dashboard</a>
                    <a href="{{ route('docs') }}" class="rounded-lg border border-zinc-300 px-4 py-2.5 text-sm font-semibold text-zinc-700 hover:bg-white dark:border-white/10 dark:text-zinc-200 dark:hover:bg-white/10">Leer documentación</a>
                    <a href="{{ route('login') }}" class="rounded-lg border border-transparent px-4 py-2.5 text-sm font-semibold text-[var(--brand-primary)] hover:bg-white dark:hover:bg-white/10">Iniciar sesión demo</a>
                </div>
            </div>
            <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-white/5">
                <dl class="grid grid-cols-2 gap-4">
                    <div class="rounded-xl bg-slate-100 p-4 dark:bg-white/10">
                        <dt class="text-sm text-zinc-500 dark:text-zinc-400">Roles</dt>
                        <dd class="mt-2 text-2xl font-semibold">3</dd>
                    </div>
                    <div class="rounded-xl bg-slate-100 p-4 dark:bg-white/10">
                        <dt class="text-sm text-zinc-500 dark:text-zinc-400">CRUD generator</dt>
                        <dd class="mt-2 text-2xl font-semibold">Artisan</dd>
                    </div>
                    <div class="rounded-xl bg-slate-100 p-4 dark:bg-white/10">
                        <dt class="text-sm text-zinc-500 dark:text-zinc-400">Tests</dt>
                        <dd class="mt-2 text-2xl font-semibold">Pest</dd>
                    </div>
                    <div class="rounded-xl bg-slate-100 p-4 dark:bg-white/10">
                        <dt class="text-sm text-zinc-500 dark:text-zinc-400">API</dt>
                        <dd class="mt-2 text-2xl font-semibold">Sanctum</dd>
                    </div>
                    <div class="rounded-xl bg-slate-100 p-4 dark:bg-white/10">
                        <dt class="text-sm text-zinc-500 dark:text-zinc-400">Flags</dt>
                        <dd class="mt-2 text-2xl font-semibold">.env</dd>
                    </div>
                    <div class="rounded-xl bg-slate-100 p-4 dark:bg-white/10">
                        <dt class="text-sm text-zinc-500 dark:text-zinc-400">UI</dt>
                        <dd class="mt-2 text-2xl font-semibold">Livewire</dd>
                    </div>
                </dl>
            </div>
        </section>

        <section class="border-t border-zinc-200 bg-white dark:border-white/10 dark:bg-zinc-900/40">
            <div class="mx-auto grid max-w-6xl gap-5 px-6 py-14 md:grid-cols-2 lg:grid-cols-3">
                @foreach ([
                    ['Laravel + Livewire', 'Base admin con componentes reutilizables, rutas protegidas y flujo CRUD completo.'],
                    ['Roles y permisos', 'Spatie Permission configurado para admin, editor y user con autorización real en backend.'],
                    ['Setup y módulos', 'starter:setup, make:admin-crud, feature flags, API Sanctum y documentación técnica.'],
                ] as [$title, $description])
                    <article class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-white/5">
                        <h2 class="text-lg font-bold">{{ $title }}</h2>
                        <p class="mt-2 text-sm leading-6 text-zinc-600 dark:text-zinc-300">{{ $description }}</p>
                    </article>
                @endforeach
            </div>
        </section>

        <section id="projects" class="border-t border-zinc-200 bg-slate-50 dark:border-white/10 dark:bg-zinc-950">
            <div class="mx-auto max-w-6xl px-6 py-14">
                <div class="mb-8 flex items-end justify-between gap-4">
                    <div>
                        <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Datos incluidos para probar el CRUD</p>
                        <h2 class="mt-2 text-2xl font-semibold tracking-tight">Proyectos demo</h2>
                    </div>
                </div>
                <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                    @forelse ($projects as $project)
                        <article class="overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-sm dark:border-white/10 dark:bg-white/5">
                            <div class="aspect-video bg-zinc-100">
                                @if ($project->image_path)
                                    <img src="{{ Storage::url($project->image_path) }}" alt="" class="h-full w-full object-cover">
                                @endif
                            </div>
                            <div class="p-5">
                                <h3 class="font-semibold">{{ $project->title }}</h3>
                                <p class="mt-2 line-clamp-3 text-sm text-zinc-600 dark:text-zinc-300">{{ $project->description }}</p>
                            </div>
                        </article>
                    @empty
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">Ejecuta los seeders demo para mostrar registros de ejemplo.</p>
                    @endforelse
                </div>
            </div>
        </section>
    </main>
</body>
</html>
