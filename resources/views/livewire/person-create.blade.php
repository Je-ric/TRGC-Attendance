<div>
    {{-- Trigger --}}
    <x-button wire:click="open" variant="primary">
        <i class='bx bx-user-plus'></i> Add Person
    </x-button>

    {{-- Add / Edit Modal --}}
    <x-modal.dialog id="person-modal" maxWidth="max-w-lg">
        <x-modal.header modalId="person-modal">
            <div class="flex items-center gap-3">
                <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-[#fff0f0] text-[#ed213a] shrink-0">
                    <i class="bx bx-user text-base leading-none"></i>
                </span>
                {{ $editing ? 'Edit Person' : 'Add Person' }}
            </div>
        </x-modal.header>

        <x-modal.body class="flex flex-col gap-4">
            <p class="text-[13px] text-[#6b6570]">
                {{ $editing ? 'Update member details.' : 'Add a new congregation member.' }}
            </p>

            <div class="grid grid-cols-2 gap-3">
                <x-form.field label="First Name" :isRequired="true" error="first_name">
                    <x-form.input wire:model="first_name" placeholder="First name" />
                </x-form.field>
                <x-form.field label="Last Name" :isRequired="true" error="last_name">
                    <x-form.input wire:model="last_name" placeholder="Last name" />
                </x-form.field>
            </div>

            <x-form.field label="Birthdate" error="birthdate">
                <x-form.input type="date" wire:model="birthdate" max="{{ $today }}" />
            </x-form.field>

            <x-form.field label="Category">
                <x-form.select wire:model="category">
                    <option value="">Auto (by age)</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}">{{ $cat }}</option>
                    @endforeach
                </x-form.select>
            </x-form.field>

            <x-form.field label="Contact Number">
                <x-form.input wire:model="contact_number" placeholder="Contact number" />
            </x-form.field>

            <x-form.field label="Address">
                <x-form.input wire:model="address" placeholder="Address" />
            </x-form.field>

            <x-form.field label="Family" error="family_id">
                <x-form.select wire:model="family_id">
                    <option value="">No family</option>
                    @foreach($families as $family)
                        <option value="{{ $family->id }}">{{ $family->family_name }}</option>
                    @endforeach
                </x-form.select>
            </x-form.field>
        </x-modal.body>

        <x-modal.footer>
            <x-modal.close-button modalId="person-modal" text="Cancel" />
            <x-button wire:click="save" variant="primary" loading="Saving…">
                <i class='bx bx-save'></i> Save
            </x-button>
        </x-modal.footer>
    </x-modal.dialog>
</div>
