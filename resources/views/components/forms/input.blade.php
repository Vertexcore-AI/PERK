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
$sizeClasses = [
    'sm' => 'px-3 py-1.5 text-sm',
    'md' => 'px-4 py-2.5',
    'lg' => 'px-5 py-3 text-lg'
];

$inputClasses = [
    'w-full',
    'bg-white dark:bg-slate-800',
    'border border-slate-200 dark:border-slate-700',
    'rounded-xl',
    'text-slate-900 dark:text-slate-100',
    'placeholder-slate-400 dark:placeholder-slate-500',
    'transition-all duration-200',
    'focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent',
    'hover:border-slate-300 dark:hover:border-slate-600',
    $sizeClasses[$size],
    $error ? 'border-rose-500 dark:border-rose-400 focus:ring-rose-500' : '',
    $disabled ? 'opacity-50 cursor-not-allowed' : '',
    $readonly ? 'bg-slate-50 dark:bg-slate-900' : ''
];

$inputId = $name ?? 'input-' . uniqid();
@endphp

<div class="space-y-2">
    @if($label)
        <label for="{{ $inputId }}" class="block text-sm font-medium text-slate-700 dark:text-slate-300">
            {{ $label }}
            @if($required)
                <span class="text-rose-500">*</span>
            @endif
        </label>
    @endif

    @if($icon)
        <div class="relative">
            @if($iconPosition === 'left')
                <div class="absolute left-3 top-1/2 transform -translate-y-1/2 pointer-events-none">
                    <i data-lucide="{{ $icon }}" class="w-5 h-5 text-slate-400"></i>
                </div>
            @endif

            <input
                id="{{ $inputId }}"
                name="{{ $name }}"
                type="{{ $type }}"
                value="{{ old($name, $value) }}"
                placeholder="{{ $placeholder }}"
                {{ $attributes->merge(['class' => implode(' ', array_filter($inputClasses)) . ($iconPosition === 'left' ? ' pl-11' : ' pr-11')]) }}
                @if($required) required @endif
                @if($disabled) disabled @endif
                @if($readonly) readonly @endif
            />

            @if($iconPosition === 'right')
                <div class="absolute right-3 top-1/2 transform -translate-y-1/2 pointer-events-none">
                    <i data-lucide="{{ $icon }}" class="w-5 h-5 text-slate-400"></i>
                </div>
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