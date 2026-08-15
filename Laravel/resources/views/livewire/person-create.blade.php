<div>
    <x-button wire:click="open" variant="primary">
        <i class='bx bx-user-plus'></i> Add Person
    </x-button>

    @include('livewire.modals.personFormModal')
</div>
