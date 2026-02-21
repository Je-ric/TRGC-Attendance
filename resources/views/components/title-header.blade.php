@props([
    'title'     => '',
    'subtitle'  => null,
    'eyebrow'   => null,
    'icon'      => null,
    'size'      => '3xl',
    'color'     => '#6B0F1A',
    'iconColor' => '#C9A84C',
])

<div>
    @if($eyebrow)
        <div class="page-eyebrow">{{ $eyebrow }}</div>
    @endif
    <h2 class="page-title text-{{ $size }} flex items-center gap-2" style="color:{{ $color }}">
        @if($icon)
            <i class='bx {{ $icon }}' style="color:{{ $iconColor }}"></i>
        @endif
        {{ $title }}
    </h2>
    @if($subtitle)
        <p class="dm-sans text-sm mt-1" style="color:var(--ink-muted)">{{ $subtitle }}</p>
    @endif
</div>