@props([
    'title'   => null,
    'icon'    => null,
    'color'   => 'slate',
    'padding' => true,
    'shadow'  => true,
])

@php
    $palette = [
        'slate'  => ['strip' => 'bg-[#f5f4f6] border-[#e4e0e2]', 'icon' => 'bg-[#e4e0e2] text-[#6b6570]',  'title' => 'text-[#1c1c1e]'],
        'red'    => ['strip' => 'bg-[#fff0f0] border-[#ffd0d0]', 'icon' => 'bg-[#ffe0e0] text-[#ed213a]',  'title' => 'text-[#93291e]'],
        'maroon' => ['strip' => 'bg-[#1a0a08] border-[#93291e]', 'icon' => 'bg-white/10 text-[#ed213a]',   'title' => 'text-white'],
        'amber'  => ['strip' => 'bg-[#fffbeb] border-[#fcd34d]', 'icon' => 'bg-[#fef3c7] text-[#d97706]',  'title' => 'text-[#92400e]'],
        'blue'   => ['strip' => 'bg-[#eff6ff] border-[#bfdbfe]', 'icon' => 'bg-[#dbeafe] text-[#1d4ed8]',  'title' => 'text-[#1e40af]'],
        'green'  => ['strip' => 'bg-[#f0fdf4] border-[#bbf7d0]', 'icon' => 'bg-[#dcfce7] text-[#16a34a]',  'title' => 'text-[#166534]'],
    ];
    $p = $palette[$color] ?? $palette['slate'];
@endphp

<div {{ $attributes->class([
        'rounded-xl border border-[#e4e0e2] bg-white overflow-hidden',
        $shadow ? 'shadow-[0_2px_16px_rgba(0,0,0,0.07)]' : '',
     ]) }}>

    @if ($title)
        <div class="flex items-center justify-between gap-3 px-4 py-3 border-b {{ $p['strip'] }}">
            <div class="flex items-center gap-2.5 min-w-0">
                @if ($icon)
                    <span class="shrink-0 flex items-center justify-center w-7 h-7 rounded-lg {{ $p['icon'] }}">
                        <i class="bx bx-{{ $icon }} text-sm leading-none"></i>
                    </span>
                @endif
                <h4 class="text-[13px] font-bold truncate {{ $p['title'] }}">{{ $title }}</h4>
            </div>

            @if (isset($action) && $action->isNotEmpty())
                <div class="shrink-0 flex items-center gap-2">{{ $action }}</div>
            @endif
        </div>
    @endif

    <div class="{{ $padding ? 'p-4' : '' }}">
        {{ $slot }}
    </div>

</div>
