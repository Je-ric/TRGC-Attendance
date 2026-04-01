@props([
    'icon'  => null,
    'title',
    'desc'  => null,
    'class' => '',
])

<div class="w-full bg-white flex flex-col md:flex-row justify-between items-start md:items-center gap-4
            px-4 sm:px-6 py-4 border-b border-[#e4e0e2] {{ $class }}"
     style="box-shadow: 0 2px 16px rgba(0,0,0,.07);">

    <div class="flex items-start md:items-center gap-3 sm:gap-4">
        @if($icon)
            <span class="shrink-0 inline-flex items-center justify-center rounded-xl text-white w-10 h-10 sm:w-11 sm:h-11 text-xl"
                  style="background: linear-gradient(135deg, #93291e 0%, #ed213a 100%); box-shadow: 0 2px 8px rgba(237,33,58,0.25);">
                <i class="bx {{ $icon }}"></i>
            </span>
        @endif
        <div>
            <h1 class="text-[15px] sm:text-[18px] font-bold text-[#1c1c1e] leading-snug" style="font-family:'Oswald',sans-serif">{{ $title }}</h1>
            @if($desc)
                <p class="text-[13px] text-[#6b6570] mt-0.5 leading-relaxed">{{ $desc }}</p>
            @endif
        </div>
    </div>

    @if($slot->isNotEmpty())
        <div class="shrink-0 w-full md:w-auto flex flex-row md:justify-end gap-2 mt-1 md:mt-0">
            {{ $slot }}
        </div>
    @endif
</div>
