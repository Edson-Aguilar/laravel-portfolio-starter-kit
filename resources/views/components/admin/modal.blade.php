@props(['show' => false, 'title', 'description' => null, 'id' => 'admin-modal'])

@if ($show)
    <div class="fixed inset-0 z-50 flex items-end justify-center bg-zinc-950/50 p-4 backdrop-blur-sm sm:items-center" role="dialog" aria-modal="true" aria-labelledby="{{ $id }}-title">
        <section {{ $attributes->merge(['class' => 'ui-card max-h-[calc(100vh-2rem)] w-full overflow-y-auto p-5 sm:p-6']) }}>
            <div class="mb-5 flex items-start justify-between gap-4">
                <div>
                    <h2 id="{{ $id }}-title" class="text-lg font-bold text-zinc-950 dark:text-white">{{ $title }}</h2>
                    @if ($description)
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $description }}</p>
                    @endif
                </div>

                @if ($close ?? false)
                    {{ $close }}
                @endif
            </div>

            {{ $slot }}
        </section>
    </div>
@endif
