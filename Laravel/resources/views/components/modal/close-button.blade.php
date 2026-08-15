@props([
    'modalId' => null,
    'text'    => 'Close',
    'class'   => '',
])

<button
    type="button"
    @if($modalId) onclick="document.getElementById('{{ $modalId }}').close()" @endif
    {{ $attributes->merge(['class' => "inline-flex items-center gap-2 px-4 py-2.5 text-[13px] font-semibold
        rounded-lg border border-[#e4e0e2] bg-white text-[#6b6570]
        hover:bg-[#f5f4f6] hover:border-[#c9c4c6] hover:text-[#1c1c1e]
        transition-colors duration-150 $class"]) }}
>
    {{ $text }}
</button>
