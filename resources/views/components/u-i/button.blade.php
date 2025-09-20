@props([
    'variant' => 'primary',
    'size' => 'md',
    'icon' => null,
    'iconPosition' => 'left',
    'loading' => false,
    'disabled' => false,
    'type' => 'button'
])

@php
$classes = [
    'btn',
    'btn-' . $variant,
    'btn-' . $size => $size !== 'md'
];

$iconClasses = [
    'me-2' => $icon && $iconPosition === 'left' && $slot->isNotEmpty(),
    'ms-2' => $icon && $iconPosition === 'right' && $slot->isNotEmpty()
];
@endphp

<button
    type="{{ $type }}"
    {{ $attributes->merge(['class' => implode(' ', array_filter($classes))]) }}
    @if($disabled || $loading) disabled @endif
>
    @if($loading)
        <i class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></i>
    @elseif($icon && $iconPosition === 'left')
        <i data-lucide="{{ $icon }}" class="{{ implode(' ', array_filter($iconClasses)) }}"></i>
    @endif

    {{ $slot }}

    @if($icon && $iconPosition === 'right' && !$loading)
        <i data-lucide="{{ $icon }}" class="{{ implode(' ', array_filter($iconClasses)) }}"></i>
    @endif
</button>