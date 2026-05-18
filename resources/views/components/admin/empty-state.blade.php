@props(['icon' => 'sparkles', 'title', 'description' => null])

<div class="flex flex-col items-center justify-center px-6 py-14 text-center">
    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-[color-mix(in_srgb,var(--brand-primary)_12%,white)] text-[var(--brand-primary)] dark:bg-white/10">
        <x-icon :name="$icon" class="h-7 w-7" />
    </div>
    <h3 class="mt-4 text-sm font-semibold text-zinc-950 dark:text-white">{{ $title }}</h3>
    @if ($description)
        <p class="mt-1 max-w-sm text-sm text-zinc-500 dark:text-zinc-400">{{ $description }}</p>
    @endif
</div>
