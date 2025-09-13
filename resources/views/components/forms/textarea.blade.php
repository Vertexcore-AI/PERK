@props([
    'label' => null,
    'name' => null,
    'value' => null,
    'placeholder' => null,
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'rows' => 3,
    'help' => null,
    'error' => null,
    'resize' => true
])

@php
$textareaClasses = [
    'form-control',
    'is-invalid' => $error,
    'resize-none' => !$resize
];

$textareaId = $name ?? 'textarea-' . uniqid();
@endphp

<div class="mb-3">
    @if($label)
        <label for="{{ $textareaId }}" class="form-label">
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif

    <textarea
        id="{{ $textareaId }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        {{ $attributes->merge(['class' => implode(' ', array_filter($textareaClasses))]) }}
        @if($required) required @endif
        @if($disabled) disabled @endif
        @if($readonly) readonly @endif
    >{{ old($name, $value) }}</textarea>

    @if($help)
        <div class="form-text">{{ $help }}</div>
    @endif

    @if($error)
        <div class="invalid-feedback">{{ $error }}</div>
    @elseif($errors->has($name))
        <div class="invalid-feedback">{{ $errors->first($name) }}</div>
    @endif
</div>