<div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_24rem]">
    <section class="ui-card relative min-w-0 overflow-hidden">
        <x-admin.skeleton-table />

        <div class="flex flex-col gap-4 border-b border-zinc-100 p-5 dark:border-white/10 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wide text-[var(--brand-primary)]">Control de acceso</p>
                <h2 class="mt-1 text-lg font-bold text-zinc-950 dark:text-white">Gestión de usuarios</h2>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">Busca, filtra y administra roles de cuenta.</p>
            </div>
            <button wire:click="create" wire:loading.attr="disabled" wire:target="create,save,delete" class="btn-primary">
                <x-icon name="plus" class="h-4 w-4" />
                Nuevo usuario
            </button>
        </div>

        <div class="grid gap-3 border-b border-zinc-100 p-5 dark:border-white/10 md:grid-cols-[minmax(0,1fr)_12rem]">
            <label class="relative">
                <x-icon name="magnifying-glass" class="pointer-events-none absolute left-3 top-2.5 h-5 w-5 text-zinc-400" />
                <input type="search" wire:model.live.debounce.300ms="search" placeholder="Buscar usuarios" class="form-control pl-10">
            </label>
            <select wire:model.live="role" class="form-control">
                <option value="">Todos los roles</option>
                @foreach ($roles as $roleOption)
                    <option value="{{ $roleOption->name }}">{{ ucfirst($roleOption->name) }}</option>
                @endforeach
            </select>
        </div>

        <div class="overflow-x-auto">
            <table class="table-modern min-w-[720px]">
                <thead>
                    <tr>
                        <th class="text-left font-bold">Nombre</th>
                        <th class="text-left font-bold">Email</th>
                        <th class="text-left font-bold">Rol</th>
                        <th class="text-right font-bold">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr wire:key="user-{{ $user->id }}" class="transition hover:bg-zinc-50/80 dark:hover:bg-white/5">
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-gradient-to-br from-[var(--brand-primary)] to-[var(--brand-secondary)] text-sm font-bold text-white">
                                        {{ str($user->name)->substr(0, 1)->upper() }}
                                    </div>
                                    <span class="font-semibold text-zinc-950 dark:text-white">{{ $user->name }}</span>
                                </div>
                            </td>
                            <td class="text-zinc-600 dark:text-zinc-300">{{ $user->email }}</td>
                            <td>
                                <span class="rounded-full bg-zinc-100 px-3 py-1 text-xs font-bold text-zinc-700 dark:bg-white/10 dark:text-zinc-200">{{ $user->roles->pluck('name')->join(', ') ?: 'user' }}</span>
                            </td>
                            <td class="text-right">
                                <button wire:click="edit({{ $user->id }})" wire:loading.attr="disabled" wire:target="edit({{ $user->id }})" class="btn-secondary mr-2 px-3 py-1.5">Editar</button>
                                <button wire:click="confirmDelete({{ $user->id }})" wire:loading.attr="disabled" wire:target="confirmDelete({{ $user->id }})" class="btn-danger px-3 py-1.5">Eliminar</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <x-admin.empty-state icon="users" title="No se encontraron usuarios" description="Ajusta la búsqueda o crea un usuario nuevo." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-zinc-100 px-5 py-4 dark:border-white/10">
            {{ $users->links() }}
        </div>
    </section>

    <aside class="ui-card h-fit p-5">
        <div class="mb-5">
            <h2 class="text-lg font-bold text-zinc-950 dark:text-white">{{ $editingId ? 'Editar usuario' : 'Crear usuario' }}</h2>
            <p class="text-sm text-zinc-500 dark:text-zinc-400">Mantén credenciales y roles ordenados.</p>
        </div>
        <form wire:submit="save" class="space-y-4">
            <div>
                <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-200">Nombre</label>
                <input wire:model="name" class="form-control mt-2">
                @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-200">Email</label>
                <input type="email" wire:model="email" class="form-control mt-2">
                @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-200">Contraseña</label>
                <input type="password" wire:model="password" class="form-control mt-2">
                @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-200">Rol</label>
                <select wire:model="selectedRole" class="form-control mt-2">
                    @foreach ($roles as $roleOption)
                        <option value="{{ $roleOption->name }}">{{ ucfirst($roleOption->name) }}</option>
                    @endforeach
                </select>
                @error('selectedRole') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
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
        title="Eliminar usuario"
        description="Este usuario se eliminará permanentemente."
        confirm="Eliminar usuario"
        action="delete"
    />
</div>
