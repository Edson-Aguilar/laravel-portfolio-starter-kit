@php
    $brand = \App\Support\BrandTheme::get();
    $nav = [
        ['label' => __('Panel'), 'route' => 'dashboard', 'icon' => 'chart-bar', 'visible' => true],
        ['label' => __('Usuarios'), 'route' => 'admin.users', 'icon' => 'users', 'visible' => auth()->user()?->hasRole('admin')],
        ['label' => __('Proyectos'), 'route' => 'admin.projects', 'icon' => 'folder', 'visible' => config('starter.modules.projects') && auth()->user()?->hasAnyRole(['admin', 'editor'])],
        ['label' => __('Apariencia'), 'route' => 'admin.appearance', 'icon' => 'paint-brush', 'visible' => config('starter.modules.appearance') && auth()->user()?->hasRole('admin')],
    ];
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" style="{{ \App\Support\BrandTheme::cssVariables() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name', 'Laravel Portfolio Starter Kit') }}</title>
    <script>
        (() => {
            const mode = localStorage.getItem('theme-mode') || 'light';
            const isDark = mode === 'dark';

            document.documentElement.classList.toggle('dark', isDark);
            document.documentElement.dataset.theme = isDark ? 'dark' : 'light';

            if (localStorage.getItem('admin-sidebar-collapsed') === 'true') {
                document.documentElement.classList.add('sidebar-collapsed');
            }
        })();
    </script>
    @unless (app()->environment('testing'))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endunless
</head>
<body class="admin-shell min-h-screen font-sans text-zinc-950 antialiased dark:text-zinc-100">
    <x-admin.toast />

    <div class="admin-layout min-h-screen lg:grid">
        <div class="admin-sidebar-backdrop fixed inset-0 z-30 hidden bg-zinc-950/50 backdrop-blur-sm lg:hidden" onclick="toggleAdminSidebar()"></div>

        <aside class="admin-sidebar fixed inset-y-0 left-0 z-40 flex w-72 -translate-x-full flex-col border-r border-zinc-200 bg-white text-zinc-950 shadow-2xl shadow-zinc-950/10 backdrop-blur-xl transition-transform duration-200 dark:border-white/10 dark:bg-zinc-950 dark:text-zinc-100 lg:sticky lg:top-0 lg:h-screen lg:translate-x-0 lg:shadow-none">
            <div class="flex h-20 items-center gap-3 px-5">
                <div class="flex h-11 w-11 items-center justify-center overflow-hidden rounded-2xl bg-gradient-to-br from-[var(--brand-primary)] to-[var(--brand-secondary)] text-white shadow-lg">
                    @if ($brand['logo_path'])
                        <img src="{{ Storage::url($brand['logo_path']) }}" alt="" class="h-full w-full object-cover">
                    @else
                        <x-icon name="sparkles" class="h-6 w-6" />
                    @endif
                </div>
                <div class="min-w-0">
                    <p class="truncate text-sm font-bold text-zinc-950 dark:text-white">{{ $brand['brand_name'] }}</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Área administrativa') }}</p>
                </div>
            </div>

            <nav class="flex-1 space-y-1 px-3">
                @foreach ($nav as $item)
                    @if ($item['visible'])
                        <a href="{{ route($item['route']) }}" @class([
                            'group flex items-center gap-3 rounded-2xl px-3 py-2.5 text-sm font-semibold transition',
                            'bg-gradient-to-r from-[var(--brand-primary)] to-[var(--brand-secondary)] text-white shadow-lg shadow-zinc-950/10' => request()->routeIs($item['route']),
                            'text-zinc-600 hover:bg-zinc-100 hover:text-zinc-950 dark:text-zinc-300 dark:hover:bg-white/10 dark:hover:text-white' => ! request()->routeIs($item['route']),
                        ])>
                            <x-icon :name="$item['icon']" class="h-5 w-5" />
                            {{ $item['label'] }}
                        </a>
                    @endif
                @endforeach
            </nav>

            <div class="m-3 rounded-2xl border border-zinc-200 bg-zinc-50 p-4 dark:border-white/10 dark:bg-white/5">
                <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400">{{ __('Sesión iniciada como') }}</p>
                <p class="mt-1 truncate text-sm font-semibold text-zinc-950 dark:text-white">{{ auth()->user()?->name }}</p>
                <span class="mt-3 inline-flex rounded-full bg-white px-2.5 py-1 text-xs font-semibold text-zinc-600 shadow-sm dark:bg-white/10 dark:text-zinc-200">{{ auth()->user()?->roles->first()?->name ?? 'user' }}</span>
            </div>
        </aside>

        <div class="min-w-0">
            <header class="admin-topbar sticky top-0 z-20 border-b border-zinc-200 bg-white/95 text-zinc-950 backdrop-blur-xl dark:border-white/10 dark:bg-zinc-950/90 dark:text-zinc-100">
                <div class="flex h-20 items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center gap-3">
                        <button type="button" onclick="toggleAdminSidebar()" class="admin-icon-button lg:hidden" aria-label="{{ __('Abrir menú') }}">
                            <x-icon name="bars-3" class="h-5 w-5" />
                        </button>
                        <button type="button" onclick="toggleDesktopSidebar()" class="admin-icon-button hidden lg:inline-flex" aria-label="{{ __('Ocultar o mostrar menú') }}">
                            <x-icon name="bars-3" class="h-5 w-5" />
                        </button>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-[var(--brand-primary)]">{{ now()->format('d/m/Y') }}</p>
                            <h1 class="text-xl font-bold tracking-tight text-zinc-950 dark:text-white">{{ __($title ?? 'Panel') }}</h1>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button" onclick="toggleThemeMode()" class="admin-icon-button" aria-label="{{ __('Cambiar modo de color') }}">
                            <x-icon name="sparkles" class="h-5 w-5" />
                        </button>
                        <a href="{{ route('locale.switch', app()->getLocale() === 'es' ? 'en' : 'es') }}" class="btn-secondary hidden px-3 py-2 sm:inline-flex">
                            {{ strtoupper(app()->getLocale() === 'es' ? 'en' : 'es') }}
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="btn-secondary">{{ __('Salir') }}</button>
                        </form>
                    </div>
                </div>
            </header>

            <main class="px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
