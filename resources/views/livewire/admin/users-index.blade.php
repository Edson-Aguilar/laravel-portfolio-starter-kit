<div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_24rem]">
    <section class="min-w-0 rounded border border-zinc-200 bg-white">
        <div class="flex flex-col gap-3 border-b border-zinc-200 p-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="font-semibold">User management</h2>
                <p class="text-sm text-zinc-500">Search, filter and manage user roles.</p>
            </div>
            <button wire:click="create" class="rounded bg-zinc-950 px-3 py-2 text-sm font-medium text-white hover:bg-zinc-800">New user</button>
        </div>

        <div class="grid gap-3 border-b border-zinc-200 p-4 md:grid-cols-[minmax(0,1fr)_12rem]">
            <input type="search" wire:model.live.debounce.300ms="search" placeholder="Search users" class="rounded border border-zinc-300 px-3 py-2 text-sm outline-none focus:border-zinc-950">
            <select wire:model.live="role" class="rounded border border-zinc-300 px-3 py-2 text-sm outline-none focus:border-zinc-950">
                <option value="">All roles</option>
                @foreach ($roles as $roleOption)
                    <option value="{{ $roleOption->name }}">{{ ucfirst($roleOption->name) }}</option>
                @endforeach
            </select>
        </div>

        @if (session('status'))
            <div class="border-b border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('status') }}</div>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-200 text-sm">
                <thead class="bg-zinc-50 text-left text-xs uppercase text-zinc-500">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Name</th>
                        <th class="px-4 py-3 font-semibold">Email</th>
                        <th class="px-4 py-3 font-semibold">Role</th>
                        <th class="px-4 py-3 text-right font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100">
                    @forelse ($users as $user)
                        <tr wire:key="user-{{ $user->id }}">
                            <td class="px-4 py-3 font-medium">{{ $user->name }}</td>
                            <td class="px-4 py-3 text-zinc-600">{{ $user->email }}</td>
                            <td class="px-4 py-3">
                                <span class="rounded bg-zinc-100 px-2 py-1 text-xs font-medium text-zinc-700">{{ $user->roles->pluck('name')->join(', ') ?: 'user' }}</span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <button wire:click="edit({{ $user->id }})" class="rounded px-2 py-1 text-sm font-medium text-zinc-700 hover:bg-zinc-100">Edit</button>
                                <button wire:click="delete({{ $user->id }})" wire:confirm="Delete this user?" class="rounded px-2 py-1 text-sm font-medium text-red-600 hover:bg-red-50">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-zinc-500">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-zinc-200 px-4 py-3">
            {{ $users->links() }}
        </div>
    </section>

    <aside class="rounded border border-zinc-200 bg-white p-5">
        <h2 class="font-semibold">{{ $editingId ? 'Edit user' : 'Create user' }}</h2>
        <form wire:submit="save" class="mt-5 space-y-4">
            <div>
                <label class="block text-sm font-medium text-zinc-700">Name</label>
                <input wire:model="name" class="mt-2 w-full rounded border border-zinc-300 px-3 py-2 text-sm outline-none focus:border-zinc-950">
                @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-zinc-700">Email</label>
                <input type="email" wire:model="email" class="mt-2 w-full rounded border border-zinc-300 px-3 py-2 text-sm outline-none focus:border-zinc-950">
                @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-zinc-700">Password</label>
                <input type="password" wire:model="password" class="mt-2 w-full rounded border border-zinc-300 px-3 py-2 text-sm outline-none focus:border-zinc-950">
                @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-zinc-700">Role</label>
                <select wire:model="selectedRole" class="mt-2 w-full rounded border border-zinc-300 px-3 py-2 text-sm outline-none focus:border-zinc-950">
                    @foreach ($roles as $roleOption)
                        <option value="{{ $roleOption->name }}">{{ ucfirst($roleOption->name) }}</option>
                    @endforeach
                </select>
                @error('selectedRole') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div class="flex gap-2">
                <button class="rounded bg-zinc-950 px-3 py-2 text-sm font-medium text-white hover:bg-zinc-800">Save</button>
                <button type="button" wire:click="resetForm" class="rounded border border-zinc-300 px-3 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-50">Cancel</button>
            </div>
        </form>
    </aside>
</div>
