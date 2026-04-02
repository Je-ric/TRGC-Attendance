@props([
    'id',
    'maxWidth' => 'max-w-xl',
    'width'    => 'w-11/12',
    'class'    => '',
])

{{--
    DaisyUI v5 modal — fixed for Livewire re-render ghost overlay.

    The modal-backdrop <form method="dialog"> is kept OUTSIDE the modal-box
    so Livewire re-renders don't leave a stale invisible overlay blocking clicks.

    Open:  document.getElementById('id').showModal()
    Close: document.getElementById('id').close()
           OR clicking the backdrop area outside the box.

    Livewire bridge in layout:
      Livewire.on('open-modal',  ({ id }) => document.getElementById(id)?.showModal())
      Livewire.on('close-modal', ({ id }) => document.getElementById(id)?.close())
--}}
<dialog id="{{ $id }}" class="modal" {{ $attributes }}>
    <div class="modal-box {{ $width }} {{ $maxWidth }} max-h-[90vh] p-0 overflow-hidden rounded-2xl bg-white flex flex-col {{ $class }}"
         style="box-shadow: 0 8px 40px rgba(0,0,0,0.18);">
        {{ $slot }}
    </div>
    <form method="dialog" class="modal-backdrop">
        <button type="submit">close</button>
    </form>
</dialog>
