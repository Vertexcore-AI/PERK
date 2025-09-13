@props([
    'label' => null,
    'name' => null,
    'type' => 'text',
    'value' => null,
    'placeholder' => null,
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'size' => 'md',
    'help' => null,
    'error' => null,
    'icon' => null,
    'iconPosition' => 'left'
])

@php
$inputClasses = [
    'form-control',
    'form-control-' . $size => $size !== 'md',
    'is-invalid' => $error
];

$inputId = $name ?? 'input-' . uniqid();
@endphp

<div class="mb-3">
    @if($label)
        <label for="{{ $inputId }}" class="form-label">
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif

    @if($icon)
        <div class="input-group">
            @if($iconPosition === 'left')
                <span class="input-group-text">
                    <i data-lucide="{{ $icon }}"></i>
                </span>
            @endif

            <input
                id="{{ $inputId }}"
                name="{{ $name }}"
                type="{{ $type }}"
                value="{{ old($name, $value) }}"
                placeholder="{{ $placeholder }}"
                {{ $attributes->merge(['class' => implode(' ', array_filter($inputClasses))]) }}
                @if($required) required @endif
                @if($disabled) disabled @endif
                @if($readonly) readonly @endif
            />

            @if($iconPosition === 'right')
                <span class="input-group-text">
                    <i data-lucide="{{ $icon }}"></i>
                </span>
            @endif
        </div>
    @else
        <input
            id="{{ $inputId }}"
            name="{{ $name }}"
            type="{{ $type }}"
            value="{{ old($name, $value) }}"
            placeholder="{{ $placeholder }}"
            {{ $attributes->merge(['class' => implode(' ', array_filter($inputClasses))]) }}
            @if($required) required @endif
            @if($disabled) disabled @endif
            @if($readonly) readonly @endif
        />
    @endif

    @if($help)
        <div class="form-text">{{ $help }}</div>
    @endif

    @if($error)
        <div class="invalid-feedback">{{ $error }}</div>
    @elseif($errors->has($name))
        <div class="invalid-feedback">{{ $errors->first($name) }}</div>
    @endif
</div>