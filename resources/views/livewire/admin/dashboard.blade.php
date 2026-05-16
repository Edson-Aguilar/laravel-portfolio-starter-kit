<div class="space-y-6">
    <div class="grid gap-4 md:grid-cols-3">
        <div class="rounded border border-zinc-200 bg-white p-5">
            <p class="text-sm text-zinc-500">Users</p>
            <p class="mt-2 text-3xl font-semibold">{{ $usersCount }}</p>
        </div>
        <div class="rounded border border-zinc-200 bg-white p-5">
            <p class="text-sm text-zinc-500">Projects</p>
            <p class="mt-2 text-3xl font-semibold">{{ $projectsCount }}</p>
        </div>
        <div class="rounded border border-zinc-200 bg-white p-5">
            <p class="text-sm text-zinc-500">Published</p>
            <p class="mt-2 text-3xl font-semibold">{{ $publishedCount }}</p>
        </div>
    </div>

    <section class="rounded border border-zinc-200 bg-white">
        <div class="border-b border-zinc-200 px-5 py-4">
            <h2 class="font-semibold">Latest projects</h2>
        </div>
        <div class="divide-y divide-zinc-100">
            @forelse ($latestProjects as $project)
                <div class="flex items-center justify-between px-5 py-4">
                    <div>
                        <p class="font-medium">{{ $project->title }}</p>
                        <p class="text-sm text-zinc-500">{{ $project->slug }}</p>
                    </div>
                    <span class="rounded bg-zinc-100 px-2 py-1 text-xs font-medium text-zinc-600">{{ $project->status }}</span>
                </div>
            @empty
                <p class="px-5 py-8 text-sm text-zinc-500">No projects yet.</p>
            @endforelse
        </div>
    </section>
</div>
