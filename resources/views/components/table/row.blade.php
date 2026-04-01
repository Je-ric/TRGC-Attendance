@props(['striped' => false, 'hover' => false, 'class' => ''])

@php
    $stripedClass = $striped ? 'odd:bg-white even:bg-[#f5f4f6]' : '';
    $hoverClass   = $hover   ? 'hover:bg-[#fff5f5] transition-colors duration-100' : '';
@endphp

<tr {{ $attributes->merge(['class' => "border-b border-[#ede9eb] last:border-0 $stripedClass $hoverClass $class"]) }}>
    {{ $slot }}
</tr>
