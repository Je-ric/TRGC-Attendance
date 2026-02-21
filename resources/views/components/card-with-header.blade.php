@props(['title' => '', 'icon' => null, 'eyebrow' => null, 'actions' => null])

<div class="ui-card">
    <div class="flex items-center justify-between px-5 py-4" style="border-bottom:1px solid var(--border)">
        <div>
            @if($eyebrow)
                <div class="page-eyebrow">{{ $eyebrow }}</div>
            @endif
            <h3 class="page-title text-xl flex items-center gap-2" style="color:var(--ink)">
                @if($icon)
                    <i class='bx {{ $icon }} text-[#6B0F1A]'></i>
                @endif
                {{ $title }}
            </h3>
        </div>
        @if($actions ?? null)
            <div class="flex gap-2">
                {{ $actions }}
            </div>
        @endif
    </div>
    <div class="p-5">
        {{ $slot }}
    </div>
</div>
