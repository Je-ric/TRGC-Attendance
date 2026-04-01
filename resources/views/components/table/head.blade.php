@props(['sticky' => false, 'class' => ''])

@php $stickyClass = $sticky ? 'sticky top-0 z-10' : ''; @endphp

<thead {{ $attributes->merge(['class' => "bg-[#f5f4f6] border-b border-[#e4e0e2] $stickyClass $class"]) }}>
    {{ $slot }}
</thead>
