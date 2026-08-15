@props(['title', 'description' => null, 'icon' => null])

<div style="display:flex;flex-wrap:wrap;justify-content:space-between;align-items:flex-start;gap:16px;margin-bottom:20px">
    <div>
        <div class="page-eyebrow" style="margin-bottom:4px">{{ $title }}</div>
        <h2 class="page-title" style="font-size:24px;margin:0;display:flex;align-items:center;gap:8px">
            @if($icon)
                <i class='bx {{ $icon }}' style="color:var(--red);font-size:22px"></i>
            @endif
            {{ $title }}
        </h2>
        @if($description)
            <p style="font-size:13px;color:var(--ink-faint);margin:4px 0 0">{{ $description }}</p>
        @endif
    </div>
    <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
        {{ $slot }}
    </div>
</div>
