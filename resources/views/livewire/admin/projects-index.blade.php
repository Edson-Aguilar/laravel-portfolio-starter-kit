<div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_26rem]">
    <section class="ui-card relative min-w-0 overflow-hidden">
        <x-admin.skeleton-table />

        <div class="flex flex-col gap-4 border-b border-zinc-100 p-5 dark:border-white/10 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wide text-[var(--brand-primary)]">Portfolio</p>
                <h2 class="mt-1 text-lg font-bold text-zinc-950 dark:text-white">Gestión de proyectos</h2>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">Administra contenido, imágenes y estado de publicación.</p>
            </div>
            <button wire:click="create" wire:loading.attr="disabled" wire:target="create,save,delete" class="btn-primary">
                <x-icon name="plus" class="h-4 w-4" />
                Nuevo proyecto
            </button>
        </div>

        <div class="grid gap-3 border-b border-zinc-100 p-5 dark:border-white/10 md:grid-cols-[minmax(0,1fr)_12rem]">
            <label class="relative">
                <x-icon name="magnifying-glass" class="pointer-events-none absolute left-3 top-2.5 h-5 w-5 text-zinc-400" />
                <input type="search" wire:model.live.debounce.300ms="search" placeholder="Buscar proyectos" class="form-control pl-10">
            </label>
            <select wire:model.live="status" class="form-control">
                <option value="">Todos los estados</option>
                <option value="draft">Borrador</option>
                <option value="published">Publicado</option>
                <option value="archived">Archivado</option>
            </select>
        </div>

        <div class="overflow-x-auto">
            <table class="table-modern min-w-[780px]">
                <thead>
                    <tr>
                        <th class="text-left font-bold">Proyecto</th>
                        <th class="text-left font-bold">Estado</th>
                        <th class="text-left font-bold">Publicado</th>
                        <th class="text-right font-bold">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($projects as $project)
                        <tr wire:key="project-{{ $project->id }}" class="transition hover:bg-zinc-50/80 dark:hover:bg-white/5">
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="flex h-14 w-20 items-center justify-center overflow-hidden rounded-2xl bg-gradient-to-br from-zinc-100 to-zinc-200 text-zinc-400 dark:from-white/10 dark:to-white/5">
                                        @if ($project->image_path)
                                            <img src="{{ Storage::url($project->image_path) }}" alt="" class="h-full w-full object-cover">
                                        @else
                                            <x-icon name="folder" class="h-5 w-5" />
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-semibold text-zinc-950 dark:text-white">{{ $project->title }}</p>
                                        <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $project->slug }}</p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span @class([
                                    'rounded-full px-3 py-1 text-xs font-bold',
                                    'bg-zinc-100 text-zinc-700 dark:bg-white/10 dark:text-zinc-200' => $project->status === 'draft',
                                    'bg-emerald-50 text-emerald-700 dark:bg-emerald-400/10 dark:text-emerald-300' => $project->status === 'published',
                                    'bg-amber-50 text-amber-700 dark:bg-amber-400/10 dark:text-amber-300' => $project->status === 'archived',
                                ])>{{ ['draft' => 'Borrador', 'published' => 'Publicado', 'archived' => 'Archivado'][$project->status] ?? $project->status }}</span>
                            </td>
                            <td class="text-zinc-600 dark:text-zinc-300">{{ $project->published_at?->format('d/m/Y') ?? '-' }}</td>
                            <td class="text-right">
                                <button wire:click="edit({{ $project->id }})" wire:loading.attr="disabled" wire:target="edit({{ $project->id }})" class="btn-secondary mr-2 px-3 py-1.5">Editar</button>
                                <button wire:click="confirmDelete({{ $project->id }})" wire:loading.attr="disabled" wire:target="confirmDelete({{ $project->id }})" class="btn-danger px-3 py-1.5">Eliminar</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <x-admin.empty-state icon="folder" title="No se encontraron proyectos" description="Crea tu primer proyecto o ajusta los filtros activos." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-zinc-100 px-5 py-4 dark:border-white/10">
            {{ $projects->links() }}
        </div>
    </section>

    <aside class="ui-card h-fit p-5">
        <div class="mb-5">
            <h2 class="text-lg font-bold text-zinc-950 dark:text-white">{{ $editingId ? 'Editar proyecto' : 'Crear proyecto' }}</h2>
            <p class="text-sm text-zinc-500 dark:text-zinc-400">Mantén el contenido del portafolio claro y visual.</p>
        </div>
        <form wire:submit="save" class="space-y-4">
            <div>
                <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-200">Título</label>
                <input wire:model.live="title" class="form-control mt-2">
                @error('title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-200">Slug</label>
                <input wire:model="slug" class="form-control mt-2">
                @error('slug') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-200">Descripción</label>
                <textarea wire:model="description" rows="5" class="form-control mt-2"></textarea>
                @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-200">Estado</label>
                <select wire:model="projectStatus" class="form-control mt-2">
                    <option value="draft">Borrador</option>
                    <option value="published">Publicado</option>
                    <option value="archived">Archivado</option>
                </select>
                @error('projectStatus') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-200">Imagen</label>
                <input type="file" wire:model="image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" class="form-control mt-2">
                @error('image') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                <div wire:loading wire:target="image" class="mt-3 h-28 animate-pulse rounded-2xl bg-zinc-200 dark:bg-white/10"></div>
                @if ($image)
                    <img src="{{ $image->temporaryUrl() }}" alt="" class="mt-3 h-32 w-full rounded-2xl object-cover">
                @elseif ($currentImagePath)
                    <img src="{{ Storage::url($currentImagePath) }}" alt="" class="mt-3 h-32 w-full rounded-2xl object-cover">
                @endif
            </div>
            <div class="flex flex-col gap-2 sm:flex-row">
                <button class="btn-primary" wire:loading.attr="disabled" wire:target="save">
                    <x-icon name="arrow-path" class="hidden h-4 w-4 animate-spin" wire:loading.class.remove="hidden" wire:target="save" />Guardar</button>
                <button type="button" wire:click="resetForm" wire:loading.attr="disabled" wire:target="save" class="btn-secondary">Cancelar</button>
            </div>
        </form>
    </aside>

    <x-admin.confirm-modal
        :show="$confirmingDeleteId !== null"
        title="Eliminar proyecto"
        description="Este proyecto y su imagen se eliminarán."
        confirm="Eliminar proyecto"
        action="delete"
    />
</div>
