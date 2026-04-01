@props([
    'id',
    'maxWidth' => 'max-w-lg',
    'width'    => 'w-11/12',
    'class'    => '',
])

{{--
    DaisyUI modal pattern — open via: document.getElementById('{{ $id }}').showModal()
    Close via: document.getElementById('{{ $id }}').close()
    Or place a <form method="dialog"> inside to close on submit.
--}}

<dialog id="{{ $id }}" class="modal backdrop-blur-sm" {{ $attributes }}>
    <div class="modal-box {{ $width }} {{ $maxWidth }} max-h-[88vh] p-0 overflow-hidden rounded-xl bg-white flex flex-col {{ $class }}"
         style="box-shadow: 0 8px 32px rgba(237,33,58,0.15);">
        {{ $slot }}
    </div>
    {{-- Click outside to close --}}
    <form method="dialog" class="modal-backdrop">
        <button type="submit">close</button>
    </form>
</dialog>
