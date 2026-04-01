@props([
    'id',
    'maxWidth' => 'max-w-xl',
    'width'    => 'w-11/12',
    'class'    => '',
])

{{--
    DaisyUI v5 modal.
    Open:  document.getElementById('{{ $id }}').showModal()
    Close: document.getElementById('{{ $id }}').close()
           OR clicking the modal-backdrop form below.

    For Livewire: dispatch('open-modal', id: 'my-id') from PHP
    The layout's Livewire.on('open-modal') bridge handles it.
--}}
<dialog id="{{ $id }}" class="modal" {{ $attributes }}>
    <div class="modal-box {{ $width }} {{ $maxWidth }} max-h-[90vh] p-0 overflow-hidden rounded-2xl bg-white flex flex-col {{ $class }}"
         style="box-shadow: 0 8px 40px rgba(0,0,0,0.18);">
        {{ $slot }}
    </div>
    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
</dialog>
