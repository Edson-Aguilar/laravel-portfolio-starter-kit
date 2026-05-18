@props(['show' => false, 'title' => 'Confirmar acción', 'description' => 'Esta acción no se puede deshacer.', 'confirm' => 'Confirmar', 'cancel' => 'Cancelar', 'action'])

@if ($show)
    <div class="fixed inset-0 z-50 flex items-end justify-center bg-zinc-950/50 p-4 backdrop-blur-sm sm:items-center" role="dialog" aria-modal="true" aria-labelledby="confirm-modal-title">
        <div class="w-full max-w-md rounded-2xl border border-zinc-200 bg-white p-5 shadow-2xl dark:border-white/10 dark:bg-zinc-950">
            <div class="flex items-start gap-4">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-red-50 text-red-600 dark:bg-red-500/10">
                    <x-icon name="trash" class="h-5 w-5" />
                </div>
                <div>
                    <h2 id="confirm-modal-title" class="text-base font-semibold text-zinc-950 dark:text-white">{{ $title }}</h2>
                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ $description }}</p>
                </div>
            </div>
            <div class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                <button type="button" wire:click="$set('{{ $attributes->get('close-property', 'confirmingDeleteId') }}', null)" wire:loading.attr="disabled" wire:target="{{ $action }}" class="btn-secondary">{{ $cancel }}</button>
                <button type="button" wire:click="{{ $action }}" wire:loading.attr="disabled" wire:target="{{ $action }}" class="btn-danger bg-red-600 text-white hover:bg-red-700 dark:bg-red-600 dark:text-white dark:hover:bg-red-700">
                    <x-icon name="arrow-path" class="hidden h-4 w-4 animate-spin" wire:loading.class.remove="hidden" wire:target="{{ $action }}" />
                    {{ $confirm }}
                </button>
            </div>
        </div>
    </div>
@endif
