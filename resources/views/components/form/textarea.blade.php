{{-- x-form.textarea --}}
@props(['rows' => 3, 'placeholder' => ''])

<textarea
    rows="{{ $rows }}"
    placeholder="{{ $placeholder }}"
    {{ $attributes->merge([
        'class' => '
            w-full rounded-lg bg-white
            border border-[#e4e0e2]
            px-3 py-2 text-[13px] text-[#1c1c1e]
            placeholder:text-[#a09aa4]
            hover:border-[#f5a0a8]
            focus:border-[#ed213a] focus:outline-none
            disabled:bg-[#f5f4f6] disabled:text-[#a09aa4] disabled:cursor-not-allowed disabled:border-[#e4e0e2]
            resize-y transition-colors duration-150
        '
    ]) }}
    style="box-shadow: none;"
    onfocus="this.style.boxShadow='0 0 0 3px rgba(237,33,58,0.2)'"
    onblur="this.style.boxShadow='none'"
>{{ $slot }}</textarea>
