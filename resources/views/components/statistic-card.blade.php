@props(['icon', 'title', 'value', 'href' => '#', 'variant' => 'default'])

@php
    $variants = [
        'default' => [
            'wrap'      => 'bg-white border-[#e4e0e2]',
            'iconWrap'  => 'bg-[#f5f4f6]',
            'iconColor' => 'text-[#93291e]',
            'label'     => 'text-[#a09aa4]',
            'value'     => 'text-[#1c1c1e]',
        ],
        'primary' => [
            'wrap'      => 'bg-[#ed213a] border-[#93291e]',
            'iconWrap'  => 'bg-white/20',
            'iconColor' => 'text-white',
            'label'     => 'text-white/65',
            'value'     => 'text-white',
        ],
        'dark' => [
            'wrap'      => 'bg-[#93291e] border-[#7a1f15]',
            'iconWrap'  => 'bg-white/15',
            'iconColor' => 'text-white',
            'label'     => 'text-white/65',
            'value'     => 'text-white',
        ],
        'muted' => [
            'wrap'      => 'bg-[#f5f4f6] border-[#e4e0e2]',
            'iconWrap'  => 'bg-white',
            'iconColor' => 'text-[#93291e]',
            'label'     => 'text-[#a09aa4]',
            'value'     => 'text-[#1c1c1e]',
        ],
    ];
    $v = $variants[$variant] ?? $variants['default'];
@endphp

<a href="{{ $href }}"
   class="block rounded-xl border p-4 no-underline transition-all duration-150
          hover:-translate-y-px hover:shadow-lg
          {{ $v['wrap'] }}"
   style="box-shadow: 0 2px 16px rgba(0,0,0,.07)">
    <div class="flex items-center justify-between gap-3">
        <div>
            <p class="text-[11px] font-bold uppercase tracking-[0.12em] mb-1.5 {{ $v['label'] }}">
                {{ $title }}
            </p>
            <p class="font-['Oswald'] text-[26px] font-bold leading-none {{ $v['value'] }}">
                {{ $value }}
            </p>
        </div>
        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 {{ $v['iconWrap'] }}">
            <i class="bx {{ $icon }} text-xl {{ $v['iconColor'] }}"></i>
        </div>
    </div>
</a>
