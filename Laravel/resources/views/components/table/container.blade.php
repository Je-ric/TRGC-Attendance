@props(['class' => ''])

<div {{ $attributes->merge(['class' => "overflow-x-auto rounded-xl border border-[#e4e0e2] bg-white $class"]) }}
     style="box-shadow: 0 2px 16px rgba(0,0,0,.07);">
    {{ $slot }}
</div>
