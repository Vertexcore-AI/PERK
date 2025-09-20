@props([
    'id' => 'modal-' . uniqid(),
    'title' => null,
    'size' => 'md',
    'centered' => false,
    'scrollable' => false,
    'backdrop' => 'true',
    'keyboard' => 'true'
])

@php
$modalClasses = [
    'modal-dialog',
    'modal-' . $size => $size !== 'md',
    'modal-dialog-centered' => $centered,
    'modal-dialog-scrollable' => $scrollable
];
@endphp

<div class="modal fade" id="{{ $id }}" tabindex="-1" data-bs-backdrop="{{ $backdrop }}" data-bs-keyboard="{{ $keyboard }}" aria-hidden="true">
    <div class="{{ implode(' ', array_filter($modalClasses)) }}">
        <div class="modal-content">
            @if($title || isset($header))
                <div class="modal-header">
                    @isset($header)
                        {{ $header }}
                    @else
                        <h5 class="modal-title">{{ $title }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    @endisset
                </div>
            @endif

            <div class="modal-body">
                {{ $slot }}
            </div>

            @isset($footer)
                <div class="modal-footer">
                    {{ $footer }}
                </div>
            @endisset
        </div>
    </div>
</div>