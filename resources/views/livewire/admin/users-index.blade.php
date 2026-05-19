<div class="space-y-6">
    <x-admin.card class="relative min-w-0 overflow-hidden">
        <x-admin.skeleton-table />

        <x-admin.page-header eyebrow="Control de acceso" title="Gestión de usuarios" description="Busca, filtra y administra roles de cuenta.">
            <x-slot:actions>
                <x-admin.button wire:click="create" wire:loading.attr="disabled" wire:target="create,save,delete">
                    <x-icon name="plus" class="h-4 w-4" />
                    Nuevo usuario
                </x-admin.button>
            </x-slot:actions>
        </x-admin.page-header>

        <div class="grid gap-3 border-b border-zinc-100 p-5 dark:border-white/10 md:grid-cols-[minmax(0,1fr)_12rem]">
            <label class="relative">
                <x-icon name="magnifying-glass" class="pointer-events-none absolute left-3 top-2.5 h-5 w-5 text-zinc-400" />
                <x-admin.input type="search" wire:model.live.debounce.300ms="search" placeholder="Buscar usuarios" class="pl-10" />
            </label>
            <x-admin.select wire:model.live="role">
                <option value="">Todos los roles</option>
                @foreach ($roles as $roleOption)
                    <option value="{{ $roleOption->name }}">{{ ucfirst($roleOption->name) }}</option>
                @endforeach
            </x-admin.select>
        </div>

        <x-admin.table min-width="720px">
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
                                <x-admin.badge>{{ $user->roles->pluck('name')->join(', ') ?: 'user' }}</x-admin.badge>
                            </td>
                            <td class="text-right">
                                <x-admin.button variant="secondary" wire:click="edit({{ $user->id }})" wire:loading.attr="disabled" wire:target="edit({{ $user->id }})" class="mr-2 px-3 py-1.5">Editar</x-admin.button>
                                <x-admin.button variant="danger" wire:click="confirmDelete({{ $user->id }})" wire:loading.attr="disabled" wire:target="confirmDelete({{ $user->id }})" class="px-3 py-1.5">Eliminar</x-admin.button>
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
        </x-admin.table>

        <div class="border-t border-zinc-100 px-5 py-4 dark:border-white/10">
            {{ $users->links() }}
        </div>
    </x-admin.card>

    <x-admin.modal :show="$showForm" id="user-form" :title="$editingId ? 'Editar usuario' : 'Crear usuario'" description="Mantén credenciales y roles ordenados." class="max-w-xl">
        <x-slot:close>
            <x-admin.button variant="icon" wire:click="resetForm" aria-label="Cerrar formulario">
                <x-icon name="x-mark" class="h-5 w-5" />
            </x-admin.button>
        </x-slot:close>
                <form wire:submit="save" class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-200">Nombre</label>
                        <x-admin.input wire:model="name" class="mt-2" />
                        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-200">Email</label>
                        <x-admin.input type="email" wire:model="email" class="mt-2" />
                        @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-200">Contraseña</label>
                        <x-admin.input type="password" wire:model="password" class="mt-2" />
                        @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-200">Rol</label>
                        <x-admin.select wire:model="selectedRole" class="mt-2">
                            @foreach ($roles as $roleOption)
                                <option value="{{ $roleOption->name }}">{{ ucfirst($roleOption->name) }}</option>
                            @endforeach
                        </x-admin.select>
                        @error('selectedRole') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex flex-col gap-2 sm:flex-row">
                        <x-admin.button type="submit" wire:loading.attr="disabled" wire:target="save">
                            <x-icon name="arrow-path" class="hidden h-4 w-4 animate-spin" wire:loading.class.remove="hidden" wire:target="save" />Guardar</x-admin.button>
                        <x-admin.button variant="secondary" wire:click="resetForm" wire:loading.attr="disabled" wire:target="save">Cancelar</x-admin.button>
                    </div>
                </form>
    </x-admin.modal>

    <x-admin.confirm-modal
        :show="$confirmingDeleteId !== null"
        title="Eliminar usuario"
        description="Este usuario se eliminará permanentemente."
        confirm="Eliminar usuario"
        action="delete"
    />
</div>
