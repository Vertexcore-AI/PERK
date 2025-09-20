@props([
    'label' => null,
    'name' => null,
    'value' => null,
    'options' => [],
    'placeholder' => null,
    'required' => false,
    'disabled' => false,
    'size' => 'md',
    'help' => null,
    'error' => null,
    'multiple' => false
])

@php
$selectClasses = [
    'form-select',
    'form-select-' . $size => $size !== 'md',
    'is-invalid' => $error
];

$selectId = $name ?? 'select-' . uniqid();
$selectedValue = old($name, $value);
@endphp

<div class="mb-3">
    @if($label)
        <label for="{{ $selectId }}" class="form-label">
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif

    <select
        id="{{ $selectId }}"
        name="{{ $name }}{{ $multiple ? '[]' : '' }}"
        {{ $attributes->merge(['class' => implode(' ', array_filter($selectClasses))]) }}
        @if($required) required @endif
        @if($disabled) disabled @endif
        @if($multiple) multiple @endif
    >
        @if($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif

        @forelse($options as $optionValue => $optionLabel)
            @if(is_array($optionLabel))
                <optgroup label="{{ $optionValue }}">
                    @foreach($optionLabel as $groupValue => $groupLabel)
                        <option
                            value="{{ $groupValue }}"
                            @if(
                                ($multiple && is_array($selectedValue) && in_array($groupValue, $selectedValue)) ||
                                (!$multiple && $selectedValue == $groupValue)
                            ) selected @endif
                        >
                            {{ $groupLabel }}
                        </option>
                    @endforeach
                </optgroup>
            @else
                <option
                    value="{{ $optionValue }}"
                    @if(
                        ($multiple && is_array($selectedValue) && in_array($optionValue, $selectedValue)) ||
                        (!$multiple && $selectedValue == $optionValue)
                    ) selected @endif
                >
                    {{ $optionLabel }}
                </option>
            @endif
        @empty
            {{ $slot }}
        @endforelse
    </select>

    @if($help)
        <div class="form-text">{{ $help }}</div>
    @endif

    @if($error)
        <div class="invalid-feedback">{{ $error }}</div>
    @elseif($errors->has($name))
        <div class="invalid-feedback">{{ $errors->first($name) }}</div>
    @endif
</div>