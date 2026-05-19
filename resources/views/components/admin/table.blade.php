@props(['minWidth' => '720px'])

<div class="overflow-x-auto">
    <table {{ $attributes->merge(['class' => 'table-modern'])->style("min-width: {$minWidth};") }}>
        {{ $slot }}
    </table>
</div>
