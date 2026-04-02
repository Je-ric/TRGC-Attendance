{{-- Included in: livewire/family-management.blade.php --}}

<x-modal.dialog id="family-form-modal" maxWidth="max-w-lg" wire:ignore.self>
    <x-modal.header modalId="family-form-modal">
        <div class="flex items-center gap-3">
            <span class="flex items-center justify-center w-9 h-9 rounded-xl shrink-0"
                  style="background:linear-gradient(135deg,#93291e,#ed213a)">
                <i class="bx bx-buildings text-white text-lg leading-none"></i>
            </span>
            <div>
                <p class="text-[15px] font-bold text-[#1c1c1e]">{{ $editing ? 'Edit Family' : 'Add Family' }}</p>
                <p class="text-[12px] text-[#a09aa4]">{{ $editing ? 'Update family details.' : 'Create a new family group.' }}</p>
            </div>
        </div>
    </x-modal.header>

    <x-modal.body class="flex flex-col gap-4">

        <x-form.field label="Family Name" :isRequired="true" error="family_name">
            <x-form.input wire:model="family_name" placeholder="e.g., Santos Family" />
        </x-form.field>

        <div class="grid grid-cols-2 gap-3">
            <x-form.field label="Contact Person">
                <x-form.input wire:model="contact_person" placeholder="Primary contact name" />
            </x-form.field>
            <x-form.field label="Contact Number">
                <x-form.input wire:model="contact_number" placeholder="+63 9XX XXX XXXX" />
            </x-form.field>
        </div>

        <x-form.field label="Address">
            <x-form.input wire:model="address" placeholder="Street address" />
        </x-form.field>

        <x-form.field label="Barangay">
            <x-form.input wire:model="barangay" placeholder="Barangay" />
        </x-form.field>

        <x-form.field label="Notes">
            <x-form.textarea wire:model="notes" rows="2"
                placeholder="Any notes about this family (e.g., special needs, location details)…" />
        </x-form.field>

    </x-modal.body>

    <x-modal.footer>
        <x-modal.close-button modalId="family-form-modal" text="Cancel" />
        <x-button wire:click="save" variant="primary">
            <i class='bx bx-save'></i> {{ $editing ? 'Update Family' : 'Save Family' }}
        </x-button>
    </x-modal.footer>
</x-modal.dialog>
