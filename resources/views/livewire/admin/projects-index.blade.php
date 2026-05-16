<div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_26rem]">
    <section class="min-w-0 rounded border border-zinc-200 bg-white">
        <div class="flex flex-col gap-3 border-b border-zinc-200 p-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="font-semibold">Project management</h2>
                <p class="text-sm text-zinc-500">Manage portfolio content, images and publishing status.</p>
            </div>
            <button wire:click="create" class="rounded bg-zinc-950 px-3 py-2 text-sm font-medium text-white hover:bg-zinc-800">New project</button>
        </div>

        <div class="grid gap-3 border-b border-zinc-200 p-4 md:grid-cols-[minmax(0,1fr)_12rem]">
            <input type="search" wire:model.live.debounce.300ms="search" placeholder="Search projects" class="rounded border border-zinc-300 px-3 py-2 text-sm outline-none focus:border-zinc-950">
            <select wire:model.live="status" class="rounded border border-zinc-300 px-3 py-2 text-sm outline-none focus:border-zinc-950">
                <option value="">All statuses</option>
                <option value="draft">Draft</option>
                <option value="published">Published</option>
                <option value="archived">Archived</option>
            </select>
        </div>

        @if (session('status'))
            <div class="border-b border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('status') }}</div>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-200 text-sm">
                <thead class="bg-zinc-50 text-left text-xs uppercase text-zinc-500">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Project</th>
                        <th class="px-4 py-3 font-semibold">Status</th>
                        <th class="px-4 py-3 font-semibold">Published</th>
                        <th class="px-4 py-3 text-right font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100">
                    @forelse ($projects as $project)
                        <tr wire:key="project-{{ $project->id }}">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="h-12 w-16 overflow-hidden rounded bg-zinc-100">
                                        @if ($project->image_path)
                                            <img src="{{ Storage::url($project->image_path) }}" alt="" class="h-full w-full object-cover">
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-medium">{{ $project->title }}</p>
                                        <p class="text-xs text-zinc-500">{{ $project->slug }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="rounded bg-zinc-100 px-2 py-1 text-xs font-medium text-zinc-700">{{ $project->status }}</span>
                            </td>
                            <td class="px-4 py-3 text-zinc-600">{{ $project->published_at?->format('M d, Y') ?? '-' }}</td>
                            <td class="px-4 py-3 text-right">
                                <button wire:click="edit({{ $project->id }})" class="rounded px-2 py-1 text-sm font-medium text-zinc-700 hover:bg-zinc-100">Edit</button>
                                <button wire:click="delete({{ $project->id }})" wire:confirm="Delete this project?" class="rounded px-2 py-1 text-sm font-medium text-red-600 hover:bg-red-50">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-zinc-500">No projects found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-zinc-200 px-4 py-3">
            {{ $projects->links() }}
        </div>
    </section>

    <aside class="rounded border border-zinc-200 bg-white p-5">
        <h2 class="font-semibold">{{ $editingId ? 'Edit project' : 'Create project' }}</h2>
        <form wire:submit="save" class="mt-5 space-y-4">
            <div>
                <label class="block text-sm font-medium text-zinc-700">Title</label>
                <input wire:model.live="title" class="mt-2 w-full rounded border border-zinc-300 px-3 py-2 text-sm outline-none focus:border-zinc-950">
                @error('title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-zinc-700">Slug</label>
                <input wire:model="slug" class="mt-2 w-full rounded border border-zinc-300 px-3 py-2 text-sm outline-none focus:border-zinc-950">
                @error('slug') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-zinc-700">Description</label>
                <textarea wire:model="description" rows="5" class="mt-2 w-full rounded border border-zinc-300 px-3 py-2 text-sm outline-none focus:border-zinc-950"></textarea>
                @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-zinc-700">Status</label>
                <select wire:model="projectStatus" class="mt-2 w-full rounded border border-zinc-300 px-3 py-2 text-sm outline-none focus:border-zinc-950">
                    <option value="draft">Draft</option>
                    <option value="published">Published</option>
                    <option value="archived">Archived</option>
                </select>
                @error('projectStatus') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-zinc-700">Image</label>
                <input type="file" wire:model="image" accept="image/*" class="mt-2 w-full rounded border border-zinc-300 px-3 py-2 text-sm">
                @error('image') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                <div wire:loading wire:target="image" class="mt-2 text-sm text-zinc-500">Uploading...</div>
                @if ($image)
                    <img src="{{ $image->temporaryUrl() }}" alt="" class="mt-3 h-28 w-full rounded object-cover">
                @elseif ($currentImagePath)
                    <img src="{{ Storage::url($currentImagePath) }}" alt="" class="mt-3 h-28 w-full rounded object-cover">
                @endif
            </div>
            <div class="flex gap-2">
                <button class="rounded bg-zinc-950 px-3 py-2 text-sm font-medium text-white hover:bg-zinc-800">Save</button>
                <button type="button" wire:click="resetForm" class="rounded border border-zinc-300 px-3 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-50">Cancel</button>
            </div>
        </form>
    </aside>
</div>
