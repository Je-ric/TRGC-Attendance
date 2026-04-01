@props(['icon', 'title', 'value', 'href' => '#', 'variant' => 'default'])

@php
    $variants = [
        'default' => ['bg' => '#ffffff',  'iconBg' => '#f5f4f6',               'iconColor' => '#93291e', 'border' => '#e4e0e2', 'light' => true],
        'primary' => ['bg' => '#ed213a',  'iconBg' => 'rgba(255,255,255,0.2)', 'iconColor' => '#fff',    'border' => '#93291e', 'light' => false],
        'dark'    => ['bg' => '#93291e',  'iconBg' => 'rgba(255,255,255,0.15)','iconColor' => '#fff',    'border' => '#7a1f15', 'light' => false],
        'muted'   => ['bg' => '#f5f4f6',  'iconBg' => '#ffffff',               'iconColor' => '#93291e', 'border' => '#e4e0e2', 'light' => true],
    ];
    $v = $variants[$variant] ?? $variants['default'];
@endphp

<a href="{{ $href }}"
   style="display:block;background:{{ $v['bg'] }};border:1px solid {{ $v['border'] }};border-radius:12px;padding:16px 18px;box-shadow:0 2px 16px rgba(0,0,0,.07);transition:box-shadow 0.15s,transform 0.15s;text-decoration:none"
   onmouseover="this.style.boxShadow='0 4px 16px rgba(237,33,58,0.15)';this.style.transform='translateY(-1px)'"
   onmouseout="this.style.boxShadow='0 2px 16px rgba(0,0,0,.07)';this.style.transform=''">
    <div style="display:flex;align-items:center;justify-content:space-between;gap:12px">
        <div>
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;margin-bottom:6px;color:{{ $v['light'] ? '#a09aa4' : 'rgba(255,255,255,0.65)' }}">
                {{ $title }}
            </div>
            <div style="font-family:'Oswald',sans-serif;font-size:26px;font-weight:700;line-height:1;color:{{ $v['light'] ? '#1c1c1e' : '#fff' }}">
                {{ $value }}
            </div>
        </div>
        <div style="width:40px;height:40px;border-radius:10px;background:{{ $v['iconBg'] }};display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i class="bx {{ $icon }}" style="font-size:20px;color:{{ $v['iconColor'] }}"></i>
        </div>
    </div>
</a>
