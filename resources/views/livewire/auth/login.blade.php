<section class="w-full max-w-md rounded border border-white/10 bg-white p-8 text-zinc-950 shadow-2xl">
    <div class="mb-8">
        <p class="text-sm font-medium text-zinc-500">Laravel Admin Starter Kit</p>
        <h1 class="mt-2 text-2xl font-semibold tracking-tight">Iniciar sesión</h1>
    </div>

    <form wire:submit="login" class="space-y-5">
        <div>
            <label for="email" class="block text-sm font-medium text-zinc-700">Email</label>
            <input id="email" type="email" wire:model="email" class="mt-2 w-full rounded border border-zinc-300 px-3 py-2 text-sm outline-none focus:border-zinc-950">
            @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-zinc-700">Contraseña</label>
            <input id="password" type="password" wire:model="password" class="mt-2 w-full rounded border border-zinc-300 px-3 py-2 text-sm outline-none focus:border-zinc-950">
            @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <label class="flex items-center gap-2 text-sm text-zinc-600">
            <input type="checkbox" wire:model="remember" class="rounded border-zinc-300">
            Recordarme
        </label>

        <button class="w-full rounded bg-zinc-950 px-4 py-2.5 text-sm font-semibold text-white hover:bg-zinc-800">Entrar</button>
    </form>

    <div class="mt-6 rounded bg-zinc-100 p-3 text-sm text-zinc-600">
        Demo: admin@example.com / password
    </div>
</section>
