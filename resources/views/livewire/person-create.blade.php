<div>
    <button wire:click="open" class="ui-btn ui-btn-primary">
        <i class='bx bx-user-plus'></i>
        Add Person
    </button>

    @if($show)
    <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50" style="z-index:9999">
        <div class="ui-card-soft w-full max-w-lg shadow-xl p-6 space-y-4">
            <h2 class="page-title text-2xl text-[#6B0F1A] flex items-center gap-2">
                <i class='bx {{ $editing ? 'bx-edit' : 'bx-user-plus' }} text-[#D4AF37]'></i>
                {{ $editing ? 'Edit Person' : 'Add Person' }}
            </h2>

            <hr class="ui-divider">

            <div class="grid grid-cols-2 gap-2">
                <input wire:model.defer="first_name" placeholder="First name" class="ui-input">
                <input wire:model.defer="last_name" placeholder="Last name" class="ui-input">
            </div>

            <input type="date" wire:model.defer="birthdate" max="{{ $today }}" class="ui-input">

            <select wire:model.defer="category" class="ui-input">
                <option value="">Auto category</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat }}">{{ $cat }}</option>
                @endforeach
            </select>

            <input wire:model.defer="contact_number" placeholder="Contact number" class="ui-input">
            <input wire:model.defer="address" placeholder="Address" class="ui-input">

            <select wire:model.defer="family_id" class="ui-input">
                <option value="">No family</option>
                @foreach($families as $family)
                    <option value="{{ $family->id }}">
                        {{ $family->family_name }}
                    </option>
                @endforeach
            </select>
            @error('family_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

            <div class="flex justify-end gap-2 pt-2">
                <button wire:click="$set('show', false)" class="ui-btn ui-btn-ghost">
                    <i class='bx bx-x'></i>
                    Cancel
                </button>
                <button wire:click="save" class="ui-btn ui-btn-primary">
                    <i class='bx bx-save'></i>
                    Save
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
