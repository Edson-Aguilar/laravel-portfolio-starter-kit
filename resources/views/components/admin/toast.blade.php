@if (session('status'))
    <div class="fixed right-4 top-4 z-50 max-w-sm rounded-2xl border border-emerald-200 bg-white/95 p-4 text-sm text-emerald-800 shadow-xl shadow-emerald-950/10 backdrop-blur dark:border-emerald-400/20 dark:bg-zinc-950/95 dark:text-emerald-200">
        <div class="flex gap-3">
            <x-icon name="check-circle" class="h-5 w-5 shrink-0" />
            <p>{{ session('status') }}</p>
        </div>
    </div>
@endif
