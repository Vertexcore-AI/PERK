@props([
    'variant' => 'primary',
    'size' => 'md',
    'rounded' => false
])

@php
$classes = [
    'badge',
    'bg-' . $variant,
    'badge-' . $size => $size !== 'md',
    'rounded-pill' => $rounded
];
@endphp

<span {{ $attributes->merge(['class' => implode(' ', array_filter($classes))]) }}>
    {{ $slot }}
</span>