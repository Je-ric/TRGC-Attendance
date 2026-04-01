@props(['align' => 'end'])

@php
    $alignClass = match($align) {
        'start'  => 'justify-start',
        'center' => 'justify-center',
        default  => 'justify-end',
    };
@endphp

<footer {{ $attributes->class([
    'border-t border-[#e4e0e2] bg-[#f5f4f6] px-6 py-4 flex gap-3 flex-shrink-0',
    $alignClass,
]) }}>
    {{ $slot }}
</footer>
