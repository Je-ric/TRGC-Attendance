<div>
    <button wire:click="open" class="px-3 py-2 rounded text-[#111111] gold-gradient font-semibold shadow">+ Add Person</button>

    @if($show)
    <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
        <div class="bg-white w-full max-w-lg rounded-xl border border-[#D4AF37]/30 shadow-xl p-6 space-y-4">
            <h2 class="brand-font text-2xl text-[#6B0F1A]">{{ $editing ? 'Edit Person' : 'Add Person' }}</h2>

            <div class="grid grid-cols-2 gap-2">
                <input wire:model.defer="first_name" placeholder="First name" class="border p-2 rounded">
                <input wire:model.defer="last_name" placeholder="Last name" class="border p-2 rounded">
            </div>

            <input type="date" wire:model.defer="birthdate" max="{{ $today }}" class="border p-2 rounded w-full">

            <select wire:model.defer="category" class="border p-2 rounded w-full">
                <option value="">Auto category</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat }}">{{ $cat }}</option>
                @endforeach
            </select>

            <input wire:model.defer="contact_number" placeholder="Contact number" class="border p-2 rounded w-full">
            <input wire:model.defer="address" placeholder="Address" class="border p-2 rounded w-full">

            <select wire:model.defer="family_id" class="border p-2 rounded w-full">
                <option value="">No family</option>
                @foreach($families as $family)
                    <option value="{{ $family->id }}">
                        {{ $family->family_name }}
                    </option>
                @endforeach
            </select>
            @error('family_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

            <div class="flex justify-end gap-2 pt-2">
                <button wire:click="$set('show', false)" class="px-4 py-2 border rounded">Cancel</button>
                <button wire:click="save" class="px-4 py-2 rounded text-[#111111] gold-gradient font-semibold">Save</button>
            </div>
        </div>
    </div>
    @endif
</div>
