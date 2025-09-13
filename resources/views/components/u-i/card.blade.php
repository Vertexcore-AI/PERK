@props([
    'title' => null,
    'subtitle' => null,
    'headerClass' => '',
    'bodyClass' => '',
    'footerSlot' => null,
    'noPadding' => false
])

<div {{ $attributes->merge(['class' => 'card']) }}>
    @if($title || $subtitle || isset($header))
        <div class="card-header {{ $headerClass }}">
            @isset($header)
                {{ $header }}
            @else
                @if($title)
                    <h5 class="card-title">{{ $title }}</h5>
                @endif
                @if($subtitle)
                    <p class="card-subtitle text-muted">{{ $subtitle }}</p>
                @endif
            @endisset
        </div>
    @endif

    <div class="card-body {{ $noPadding ? 'p-0' : '' }} {{ $bodyClass }}">
        {{ $slot }}
    </div>

    @if($footerSlot || isset($footer))
        <div class="card-footer">
            {{ $footerSlot ?? $footer }}
        </div>
    @endif
</div>