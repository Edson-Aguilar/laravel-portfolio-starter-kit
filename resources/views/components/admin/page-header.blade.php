@props(['eyebrow' => null, 'title', 'description' => null])

<div {{ $attributes->merge(['class' => 'flex flex-col gap-4 border-b border-zinc-100 p-5 dark:border-white/10 md:flex-row md:items-center md:justify-between']) }}>
    <div>
        @if ($eyebrow)
            <p class="text-xs font-bold uppercase tracking-wide text-[var(--brand-primary)]">{{ $eyebrow }}</p>
        @endif
        <h2 class="mt-1 text-lg font-bold text-zinc-950 dark:text-white">{{ $title }}</h2>
        @if ($description)
            <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $description }}</p>
        @endif
    </div>

    @if ($actions ?? false)
        <div class="flex flex-wrap items-center gap-2">
            {{ $actions }}
        </div>
    @endif
</div>
