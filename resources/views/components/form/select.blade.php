{{-- x-form.select --}}
@props(['name' => null])

<div class="relative">
    <select
        @if($name) name="{{ $name }}" @endif
        {{ $attributes->merge([
            'class' => '
                w-full appearance-none rounded-lg bg-white
                border border-[#e4e0e2]
                px-3 py-2 pr-9 text-[13px] text-[#1c1c1e]
                hover:border-[#f5a0a8]
                focus:border-[#ed213a] focus:outline-none
                disabled:bg-[#f5f4f6] disabled:text-[#a09aa4] disabled:cursor-not-allowed disabled:border-[#e4e0e2]
                transition-colors duration-150
            '
        ]) }}
        style="box-shadow: none;"
        onfocus="this.style.boxShadow='0 0 0 3px rgba(237,33,58,0.2)'"
        onblur="this.style.boxShadow='none'"
    >
        {{ $slot }}
    </select>

    <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2.5 text-[#a09aa4]">
        <i class="bx bx-chevron-down text-base leading-none"></i>
    </span>
</div>
