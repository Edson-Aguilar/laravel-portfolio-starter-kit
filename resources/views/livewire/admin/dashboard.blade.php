<div class="space-y-8">
    <section class="overflow-hidden rounded-3xl admin-gradient p-6 text-white shadow-2xl shadow-zinc-950/10 sm:p-8">
        <div class="grid gap-8 lg:grid-cols-[1fr_20rem] lg:items-end">
            <div>
                <div class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-xs font-semibold backdrop-blur">
                    <x-icon name="sparkles" class="h-4 w-4" />
                    Starter kit listo
                </div>
                <h2 class="mt-5 max-w-3xl text-3xl font-bold tracking-tight sm:text-4xl">Gestiona el contenido de tu portafolio con un panel Laravel profesional.</h2>
                <p class="mt-3 max-w-2xl text-sm text-white/75 sm:text-base">Usuarios, roles, proyectos, subidas y apariencia listos para un flujo profesional.</p>
            </div>
            <div class="rounded-2xl border border-white/20 bg-white/12 p-4 backdrop-blur">
                <p class="text-sm font-medium text-white/70">Porcentaje publicado</p>
                <p class="mt-2 text-4xl font-bold">{{ $projectsCount ? round(($publishedCount / $projectsCount) * 100) : 0 }}%</p>
            </div>
        </div>
    </section>

    <div class="grid gap-4 md:grid-cols-3">
        <div class="ui-card p-5">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Usuarios</p>
                <div class="rounded-xl bg-blue-50 p-2 text-blue-600 dark:bg-blue-400/10 dark:text-blue-300">
                    <x-icon name="users" class="h-5 w-5" />
                </div>
            </div>
            <p class="mt-4 text-3xl font-bold text-zinc-950 dark:text-white">{{ $usersCount }}</p>
        </div>
        <div class="ui-card p-5">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Proyectos</p>
                <div class="rounded-xl bg-emerald-50 p-2 text-emerald-600 dark:bg-emerald-400/10 dark:text-emerald-300">
                    <x-icon name="folder" class="h-5 w-5" />
                </div>
            </div>
            <p class="mt-4 text-3xl font-bold text-zinc-950 dark:text-white">{{ $projectsCount }}</p>
        </div>
        <div class="ui-card p-5">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Publicados</p>
                <div class="rounded-xl bg-orange-50 p-2 text-orange-600 dark:bg-orange-400/10 dark:text-orange-300">
                    <x-icon name="check-circle" class="h-5 w-5" />
                </div>
            </div>
            <p class="mt-4 text-3xl font-bold text-zinc-950 dark:text-white">{{ $publishedCount }}</p>
        </div>
    </div>

    <section class="ui-card overflow-hidden">
        <div class="flex items-center justify-between border-b border-zinc-100 px-5 py-4 dark:border-white/10">
            <div>
                <h2 class="font-bold text-zinc-950 dark:text-white">Proyectos recientes</h2>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">Entradas del portafolio actualizadas recientemente.</p>
            </div>
            <a href="{{ route('admin.projects') }}" class="btn-secondary hidden sm:inline-flex">Ver todos</a>
        </div>
        <div class="divide-y divide-zinc-100 dark:divide-white/10">
            @forelse ($latestProjects as $project)
                <div class="flex flex-col gap-3 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="font-semibold text-zinc-950 dark:text-white">{{ $project->title }}</p>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $project->slug }}</p>
                    </div>
                    <span class="w-fit rounded-full bg-zinc-100 px-3 py-1 text-xs font-bold text-zinc-600 dark:bg-white/10 dark:text-zinc-200">{{ ['draft' => 'Borrador', 'published' => 'Publicado', 'archived' => 'Archivado'][$project->status] ?? $project->status }}</span>
                </div>
            @empty
                <x-admin.empty-state icon="folder" title="Aún no hay proyectos" description="Crea tu primer proyecto para alimentar este panel." />
            @endforelse
        </div>
    </section>
</div>
