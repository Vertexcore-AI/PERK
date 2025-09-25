@props([
    'title' => null,
    'subtitle' => null,
    'headerClass' => '',
    'bodyClass' => '',
    'footerSlot' => null,
    'noPadding' => false,
    'theme' => 'default'
])

@php
$themeClasses = match($theme) {
    'customer' => 'bg-gradient-to-br from-pink-500/5 via-purple-500/5 to-pink-500/5 border border-pink-400/20 shadow-xl shadow-pink-500/10 hover:shadow-2xl hover:shadow-pink-500/15',
    'inventory' => 'bg-gradient-to-br from-blue-500/5 via-cyan-500/5 to-blue-500/5 border border-blue-400/20 shadow-xl shadow-blue-500/10 hover:shadow-2xl hover:shadow-blue-500/15',
    'sales' => 'bg-gradient-to-br from-emerald-500/5 via-green-500/5 to-emerald-500/5 border border-emerald-400/20 shadow-xl shadow-emerald-500/10 hover:shadow-2xl hover:shadow-emerald-500/15',
    'vendor' => 'bg-gradient-to-br from-orange-500/5 via-amber-500/5 to-orange-500/5 border border-orange-400/20 shadow-xl shadow-orange-500/10 hover:shadow-2xl hover:shadow-orange-500/15',
    'category' => 'bg-gradient-to-br from-violet-500/5 via-indigo-500/5 to-violet-500/5 border border-violet-400/20 shadow-xl shadow-violet-500/10 hover:shadow-2xl hover:shadow-violet-500/15',
    'store' => 'bg-gradient-to-br from-teal-500/5 via-slate-500/5 to-teal-500/5 border border-teal-400/20 shadow-xl shadow-teal-500/10 hover:shadow-2xl hover:shadow-teal-500/15',
    default => 'bg-gradient-to-br from-slate-500/5 via-gray-500/5 to-slate-500/5 border border-slate-400/20 shadow-xl shadow-slate-500/10 hover:shadow-2xl hover:shadow-slate-500/15'
};
@endphp

<div {{ $attributes->merge(['class' => 'card backdrop-blur-md transition-all duration-300 ' . $themeClasses]) }}>
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