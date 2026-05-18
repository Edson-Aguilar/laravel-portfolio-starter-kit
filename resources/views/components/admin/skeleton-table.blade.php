<div wire:loading.delay class="absolute inset-0 z-10 rounded-2xl bg-white/75 p-4 backdrop-blur dark:bg-zinc-950/70">
    <div class="space-y-3">
        @for ($i = 0; $i < 6; $i++)
            <div class="h-12 animate-pulse rounded-xl bg-zinc-200/80 dark:bg-white/10"></div>
        @endfor
    </div>
</div>
