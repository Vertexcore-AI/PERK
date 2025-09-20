@props([
    'type' => 'info',
    'dismissible' => false,
    'title' => null,
    'icon' => true
])

@php
$iconMap = [
    'success' => 'check-circle',
    'danger' => 'x-circle',
    'warning' => 'alert-triangle',
    'info' => 'info'
];

$alertIcon = $icon === true ? ($iconMap[$type] ?? 'info') : $icon;
@endphp

<div {{ $attributes->merge(['class' => "alert alert-{$type}" . ($dismissible ? ' alert-dismissible' : '')]) }} role="alert">
    @if($alertIcon)
        <i data-lucide="{{ $alertIcon }}" class="me-2"></i>
    @endif

    @if($title)
        <h6 class="alert-heading">{{ $title }}</h6>
    @endif

    {{ $slot }}

    @if($dismissible)
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    @endif
</div>