@props(['label' => '', 'value' => 0, 'color' => null, 'soft' => false, 'icon' => null])

<div class="{{ $soft ? 'ui-card-soft' : 'ui-card' }} p-4">
    @if($icon)
        <div class="mb-1" style="color:var(--maroon)"><i class='bx {{ $icon }} text-xl'></i></div>
    @endif
    <div class="stat-label">{{ $label }}</div>
    <div class="stat-value" @if($color) style="color:{{ $color }}" @endif>{{ $value }}</div>
</div>
