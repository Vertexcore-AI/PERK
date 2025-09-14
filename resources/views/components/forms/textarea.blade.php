@props([
    'label' => null,
    'name' => null,
    'value' => null,
    'placeholder' => null,
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'rows' => 4,
    'help' => null,
    'error' => null
])

@php
$textareaClasses = [
    'w-full',
    'px-4 py-2.5',
    'bg-white dark:bg-slate-800',
    'border border-slate-200 dark:border-slate-700',
    'rounded-xl',
    'text-slate-900 dark:text-slate-100',
    'placeholder-slate-400 dark:placeholder-slate-500',
    'transition-all duration-200',
    'focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent',
    'hover:border-slate-300 dark:hover:border-slate-600',
    'resize-vertical',
    $error ? 'border-rose-500 dark:border-rose-400 focus:ring-rose-500' : '',
    $disabled ? 'opacity-50 cursor-not-allowed' : '',
    $readonly ? 'bg-slate-50 dark:bg-slate-900' : ''
];

$textareaId = $name ?? 'textarea-' . uniqid();
@endphp

<div class="space-y-2">
    @if($label)
        <label for="{{ $textareaId }}" class="block text-sm font-medium text-slate-700 dark:text-slate-300">
            {{ $label }}
            @if($required)
                <span class="text-rose-500">*</span>
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

    @if($help && !$error)
        <p class="text-sm text-slate-500 dark:text-slate-400">{{ $help }}</p>
    @endif

    @if($error || $errors->has($name))
        <p class="text-sm text-rose-500 dark:text-rose-400 flex items-center gap-1">
            <i data-lucide="alert-circle" class="w-4 h-4"></i>
            {{ $error ?: $errors->first($name) }}
        </p>
    @endif
</div>