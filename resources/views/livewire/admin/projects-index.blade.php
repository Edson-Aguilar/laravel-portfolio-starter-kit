<div class="space-y-6">
    <x-admin.card class="relative min-w-0 overflow-hidden">
        <x-admin.skeleton-table />

        <x-admin.page-header eyebrow="Datos demo" title="Proyectos demo" description="Administra registros de ejemplo para validar CRUD, filtros, uploads y estados.">
            <x-slot:actions>
                <x-admin.button wire:click="create" wire:loading.attr="disabled" wire:target="create,save,delete">
                    <x-icon name="plus" class="h-4 w-4" />
                    Nuevo registro
                </x-admin.button>
            </x-slot:actions>
        </x-admin.page-header>

        <div class="grid gap-3 border-b border-zinc-100 p-5 dark:border-white/10 md:grid-cols-[minmax(0,1fr)_12rem]">
            <label class="relative">
                <x-icon name="magnifying-glass" class="pointer-events-none absolute left-3 top-2.5 h-5 w-5 text-zinc-400" />
                <x-admin.input type="search" wire:model.live.debounce.300ms="search" placeholder="Buscar registros demo" class="pl-10" />
            </label>
            <x-admin.select wire:model.live="status">
                <option value="">Todos los estados</option>
                <option value="draft">Borrador</option>
                <option value="published">Publicado</option>
                <option value="archived">Archivado</option>
            </x-admin.select>
        </div>

        <x-admin.table min-width="780px">
                <thead>
                    <tr>
                        <th class="text-left font-bold">Registro demo</th>
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
                                <x-admin.badge :variant="['draft' => 'neutral', 'published' => 'success', 'archived' => 'warning'][$project->status] ?? 'neutral'">
                                    {{ ['draft' => 'Borrador', 'published' => 'Publicado', 'archived' => 'Archivado'][$project->status] ?? $project->status }}
                                </x-admin.badge>
                            </td>
                            <td class="text-zinc-600 dark:text-zinc-300">{{ $project->published_at?->format('d/m/Y') ?? '-' }}</td>
                            <td class="text-right">
                                <x-admin.button variant="secondary" wire:click="edit({{ $project->id }})" wire:loading.attr="disabled" wire:target="edit({{ $project->id }})" class="mr-2 px-3 py-1.5">Editar</x-admin.button>
                                <x-admin.button variant="danger" wire:click="confirmDelete({{ $project->id }})" wire:loading.attr="disabled" wire:target="confirmDelete({{ $project->id }})" class="px-3 py-1.5">Eliminar</x-admin.button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <x-admin.empty-state icon="folder" title="No se encontraron registros demo" description="Crea el primer registro demo o ajusta los filtros activos." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
        </x-admin.table>

        <div class="border-t border-zinc-100 px-5 py-4 dark:border-white/10">
            {{ $projects->links() }}
        </div>
    </x-admin.card>

    <x-admin.modal :show="$showForm" id="project-form" :title="$editingId ? 'Editar registro demo' : 'Crear registro demo'" description="Usa este módulo como referencia para CRUDs con imagen, filtros y estados." class="max-w-2xl">
        <x-slot:close>
            <x-admin.button variant="icon" wire:click="resetForm" aria-label="Cerrar formulario">
                <x-icon name="x-mark" class="h-5 w-5" />
            </x-admin.button>
        </x-slot:close>
                <form wire:submit="save" class="grid gap-4 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-200">Título</label>
                        <x-admin.input wire:model.live="title" class="mt-2" />
                        @error('title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-200">Slug</label>
                        <x-admin.input wire:model="slug" class="mt-2" />
                        @error('slug') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-200">Estado</label>
                        <x-admin.select wire:model="projectStatus" class="mt-2">
                            <option value="draft">Borrador</option>
                            <option value="published">Publicado</option>
                            <option value="archived">Archivado</option>
                        </x-admin.select>
                        @error('projectStatus') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-200">Descripción</label>
                        <textarea wire:model="description" rows="5" class="form-control mt-2"></textarea>
                        @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-200">Imagen</label>
                        <x-admin.input type="file" wire:model="image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" class="mt-2" />
                        @error('image') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        <div wire:loading wire:target="image" class="mt-3 h-28 animate-pulse rounded-2xl bg-zinc-200 dark:bg-white/10"></div>
                        @if ($image)
                            <img src="{{ $image->temporaryUrl() }}" alt="" class="mt-3 h-40 w-full rounded-2xl object-cover">
                        @elseif ($currentImagePath)
                            <img src="{{ Storage::url($currentImagePath) }}" alt="" class="mt-3 h-40 w-full rounded-2xl object-cover">
                        @endif
                    </div>
                    <div class="flex flex-col gap-2 md:col-span-2 sm:flex-row">
                        <x-admin.button type="submit" wire:loading.attr="disabled" wire:target="save">
                            <x-icon name="arrow-path" class="hidden h-4 w-4 animate-spin" wire:loading.class.remove="hidden" wire:target="save" />Guardar</x-admin.button>
                        <x-admin.button variant="secondary" wire:click="resetForm" wire:loading.attr="disabled" wire:target="save">Cancelar</x-admin.button>
                    </div>
                </form>
    </x-admin.modal>

    <x-admin.confirm-modal
        :show="$confirmingDeleteId !== null"
        title="Eliminar registro demo"
        description="Este registro demo y su imagen se eliminarán."
        confirm="Eliminar registro"
        action="delete"
    />
</div>
