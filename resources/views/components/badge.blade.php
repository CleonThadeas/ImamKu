@props(['type' => 'primary'])

@php
    $classes = [
        'primary' => 'bg-primary/10 text-primary',
        'secondary' => 'bg-secondary/10 text-secondary',
        'tertiary' => 'bg-tertiary/10 text-tertiary',
        'error' => 'bg-error/10 text-error',
        'warning' => 'bg-tertiary/10 text-tertiary',
        'success' => 'bg-primary/10 text-primary',
    ][$type] ?? 'bg-surface-container-high text-on-surface-variant';
@endphp

<span {{ $attributes->merge(['class' => "px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-[0.1em] inline-flex items-center gap-1.5 $classes"]) }}>
    {{ $slot }}
</span>
