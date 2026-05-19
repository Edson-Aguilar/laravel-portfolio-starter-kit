@props(['variant' => 'primary', 'type' => 'button'])

@php
    $classes = [
        'primary' => 'btn-primary',
        'secondary' => 'btn-secondary',
        'danger' => 'btn-danger',
        'icon' => 'admin-icon-button',
    ][$variant] ?? 'btn-primary';
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</button>
