@props([
    'class'   => '',
    'modalId' => null,
])

<header {{ $attributes->merge(['class' => "px-6 py-4 border-b border-[#e4e0e2] bg-white flex-shrink-0 $class"]) }}>
    <div class="flex items-center justify-between gap-4">
        <div class="flex-1 min-w-0 text-[15px] font-bold text-[#1c1c1e]" style="font-family:'Oswald',sans-serif">
            {{ $slot }}
        </div>

        @if ($modalId)
            <button
                type="button"
                onclick="document.getElementById('{{ $modalId }}').close()"
                class="shrink-0 rounded-lg p-1.5 text-[#a09aa4]
                       hover:bg-[#f5f4f6] hover:text-[#6b6570]
                       transition-colors duration-150"
                aria-label="Close">
                <i class="bx bx-x text-xl leading-none"></i>
            </button>
        @endif
    </div>
</header>
