<div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_24rem]">
    <section class="ui-card p-5 sm:p-6">
        <div class="mb-6">
            <p class="text-xs font-bold uppercase tracking-wide text-[var(--brand-primary)]">Sistema de marca</p>
            <h2 class="mt-1 text-xl font-bold text-zinc-950 dark:text-white">Ajustes de apariencia</h2>
            <p class="text-sm text-zinc-500 dark:text-zinc-400">Sube un logo, genera sugerencias de color y ajusta el tema del starter kit.</p>
        </div>

        <form wire:submit="save" class="grid gap-6 lg:grid-cols-2">
            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-200">Nombre de marca</label>
                    <input wire:model="brandName" class="form-control mt-2">
                    @error('brandName') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-200">Logo</label>
                    <div class="mt-2 rounded-2xl border border-dashed border-zinc-300 bg-zinc-50 p-4 dark:border-white/10 dark:bg-white/5">
                        <input type="file" wire:model="logo" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" class="form-control bg-white dark:bg-zinc-950">
                        <div wire:loading wire:target="logo" class="mt-3 h-24 animate-pulse rounded-2xl bg-zinc-200 dark:bg-white/10"></div>
                        @if ($logo)
                            <img src="{{ $logo->temporaryUrl() }}" alt="" class="mt-3 h-24 w-24 rounded-2xl object-cover">
                        @elseif ($logoPath)
                            <img src="{{ Storage::url($logoPath) }}" alt="" class="mt-3 h-24 w-24 rounded-2xl object-cover">
                        @else
                            <div class="mt-3 flex h-24 w-24 items-center justify-center rounded-2xl bg-gradient-to-br from-[var(--brand-primary)] to-[var(--brand-secondary)] text-white">
                                <x-icon name="sparkles" class="h-8 w-8" />
                            </div>
                        @endif
                    </div>
                    @error('logo') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-200">Fuente</label>
                    <select wire:model="fontFamily" class="form-control mt-2">
                        @foreach ($fonts as $font)
                            <option value="{{ $font }}">{{ $font }}</option>
                        @endforeach
                    </select>
                    @error('fontFamily') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="space-y-5">
                <div class="grid gap-4 sm:grid-cols-2">
                    <label class="block">
                        <span class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">Primario</span>
                        <input type="color" wire:model.live="primary" class="mt-2 h-11 w-full rounded-xl border border-zinc-200 bg-white p-1 dark:border-white/10 dark:bg-zinc-950">
                        <input wire:model.live="primary" class="form-control mt-2">
                    </label>
                    <label class="block">
                        <span class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">Secundario</span>
                        <input type="color" wire:model.live="secondary" class="mt-2 h-11 w-full rounded-xl border border-zinc-200 bg-white p-1 dark:border-white/10 dark:bg-zinc-950">
                        <input wire:model.live="secondary" class="form-control mt-2">
                    </label>
                    <label class="block">
                        <span class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">Acento</span>
                        <input type="color" wire:model.live="accent" class="mt-2 h-11 w-full rounded-xl border border-zinc-200 bg-white p-1 dark:border-white/10 dark:bg-zinc-950">
                        <input wire:model.live="accent" class="form-control mt-2">
                    </label>
                    <label class="block">
                        <span class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">Superficie oscura</span>
                        <input type="color" wire:model.live="darkSurface" class="mt-2 h-11 w-full rounded-xl border border-zinc-200 bg-white p-1 dark:border-white/10 dark:bg-zinc-950">
                        <input wire:model.live="darkSurface" class="form-control mt-2">
                    </label>
                </div>

                @if ($suggestedColors)
                    <div class="rounded-2xl border border-zinc-200 p-4 dark:border-white/10">
                        <p class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">Sugeridos desde el logo</p>
                        <div class="mt-3 grid grid-cols-4 gap-2">
                            @foreach ($suggestedColors as $color)
                                <button type="button" wire:click="usePaletteColor('primary', '{{ $color }}')" class="h-12 rounded-xl border border-white/40 shadow-sm" style="background: {{ $color }}" title="{{ $color }}"></button>
                            @endforeach
                        </div>
                        <p class="mt-2 text-xs text-zinc-500 dark:text-zinc-400">Haz clic en un color para aplicarlo como primario y luego ajusta el resto manualmente.</p>
                    </div>
                @endif

                <button class="btn-primary w-full" wire:loading.attr="disabled" wire:target="save,logo">
                    <x-icon name="arrow-path" class="hidden h-4 w-4 animate-spin" wire:loading.class.remove="hidden" wire:target="save,logo" />
                    Guardar apariencia
                </button>
            </div>
        </form>
    </section>

    <aside class="space-y-6">
        <div class="ui-card overflow-hidden">
            <div class="h-28 bg-gradient-to-br from-[var(--brand-primary)] via-[var(--brand-secondary)] to-[var(--brand-accent)]"></div>
            <div class="p-5">
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center overflow-hidden rounded-2xl bg-zinc-100 dark:bg-white/10">
                        @if ($logo)
                            <img src="{{ $logo->temporaryUrl() }}" alt="" class="h-full w-full object-cover">
                        @elseif ($logoPath)
                            <img src="{{ Storage::url($logoPath) }}" alt="" class="h-full w-full object-cover">
                        @else
                            <x-icon name="sparkles" class="h-6 w-6 text-[var(--brand-primary)]" />
                        @endif
                    </div>
                    <div>
                        <p class="font-bold text-zinc-950 dark:text-white">{{ $brandName ?: 'Laravel Admin Starter Kit' }}</p>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $fontFamily }}</p>
                    </div>
                </div>
                <button type="button" class="btn-primary mt-5 w-full">Acción principal</button>
            </div>
        </div>

        <div class="ui-card p-5">
            <h3 class="font-bold text-zinc-950 dark:text-white">Paleta</h3>
            <div class="mt-4 space-y-3">
                @foreach ([['Primario', $primary], ['Secundario', $secondary], ['Acento', $accent], ['Oscuro', $darkSurface]] as [$label, $color])
                    <div class="flex items-center justify-between rounded-2xl border border-zinc-100 p-3 dark:border-white/10">
                        <span class="text-sm font-medium text-zinc-600 dark:text-zinc-300">{{ $label }}</span>
                        <span class="flex items-center gap-2 text-sm font-semibold text-zinc-950 dark:text-white">
                            <span class="h-6 w-6 rounded-lg border border-white/40" style="background: {{ $color }}"></span>
                            {{ $color }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </aside>
</div>
