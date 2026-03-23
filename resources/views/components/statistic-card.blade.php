@props(['icon', 'title', 'value', 'href' => '#', 'variant' => 'default'])

@php
    $variants = [
        'default' => ['bg' => '#ffffff', 'iconBg' => '#f5f4f6', 'iconColor' => '#93291e', 'border' => '#e4e0e2'],
        'primary' => ['bg' => '#ed213a', 'iconBg' => 'rgba(255,255,255,0.2)', 'iconColor' => '#fff', 'border' => '#93291e'],
        'dark'    => ['bg' => '#93291e', 'iconBg' => 'rgba(255,255,255,0.15)', 'iconColor' => '#fff', 'border' => '#7a1f15'],
        'muted'   => ['bg' => '#f5f4f6', 'iconBg' => '#ffffff', 'iconColor' => '#93291e', 'border' => '#e4e0e2'],
        // legacy aliases
        'deep-rose' => ['bg' => '#ed213a', 'iconBg' => 'rgba(255,255,255,0.2)', 'iconColor' => '#fff', 'border' => '#93291e'],
        'sunset'    => ['bg' => '#f5f4f6', 'iconBg' => '#ffffff', 'iconColor' => '#93291e', 'border' => '#e4e0e2'],
    ];
    $v = $variants[$variant] ?? $variants['default'];
    $isLight = in_array($variant, ['default', 'muted', 'sunset']);
@endphp

<a href="{{ $href }}"
   style="display:block;background:{{ $v['bg'] }};border:1px solid {{ $v['border'] }};border-radius:12px;padding:16px 18px;box-shadow:0 1px 3px rgba(28,28,30,0.08);transition:box-shadow 0.15s,transform 0.15s;text-decoration:none"
   onmouseover="this.style.boxShadow='0 4px 16px rgba(28,28,30,0.12)';this.style.transform='translateY(-1px)'"
   onmouseout="this.style.boxShadow='0 1px 3px rgba(28,28,30,0.08)';this.style.transform=''">
    <div style="display:flex;align-items:center;justify-content:space-between;gap:12px">
        <div>
            <div style="font-size:10.5px;font-weight:600;text-transform:uppercase;letter-spacing:0.07em;margin-bottom:6px;color:{{ $isLight ? '#a09aa4' : 'rgba(255,255,255,0.65)' }}">
                {{ $title }}
            </div>
            <div style="font-family:'Sora',sans-serif;font-size:26px;font-weight:700;line-height:1;color:{{ $isLight ? '#1c1c1e' : '#fff' }}">
                {{ $value }}
            </div>
        </div>
        <div style="width:40px;height:40px;border-radius:10px;background:{{ $v['iconBg'] }};display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i class="bx {{ $icon }}" style="font-size:20px;color:{{ $v['iconColor'] }}"></i>
        </div>
    </div>
</a>
