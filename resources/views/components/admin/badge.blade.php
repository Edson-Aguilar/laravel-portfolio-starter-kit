@props(['variant' => 'neutral'])

@php
    $classes = [
        'neutral' => 'bg-zinc-100 text-zinc-700 dark:bg-white/10 dark:text-zinc-200',
        'success' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-400/10 dark:text-emerald-300',
        'warning' => 'bg-amber-50 text-amber-700 dark:bg-amber-400/10 dark:text-amber-300',
        'danger' => 'bg-red-50 text-red-700 dark:bg-red-400/10 dark:text-red-300',
    ][$variant] ?? 'bg-zinc-100 text-zinc-700 dark:bg-white/10 dark:text-zinc-200';
@endphp

<span {{ $attributes->merge(['class' => "rounded-full px-3 py-1 text-xs font-bold {$classes}"]) }}>
    {{ $slot }}
</span>
