<div class="space-y-6">
    <!-- Header with Add Button -->
    <div class="flex justify-between items-center">
        <h3 class="text-xl font-bold">Family Management</h3>
        <button wire:click="open" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
            + Add Family
        </button>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        <div class="bg-white p-4 rounded shadow">
            <div class="text-sm text-gray-500">Total Families</div>
            <div class="text-2xl font-bold">{{ $families->count() }}</div>
        </div>
        @foreach($categories as $cat)
            <div class="bg-white p-4 rounded shadow">
                <div class="text-sm text-gray-500">{{ $cat }}</div>
                <div class="text-2xl font-bold">{{ $categoryCounts[$cat] ?? 0 }}</div>
            </div>
        @endforeach
    </div>

    <!-- Family List -->
    <div class="space-y-4">
        @forelse($families as $family)
            <div class="bg-white border rounded p-4 shadow">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <h4 class="font-bold text-lg">{{ $family->family_name }}</h4>
                        @if($family->address)
                            <p class="text-sm text-gray-500">{{ $family->address }}</p>
                        @endif
                        @if($family->contact_person)
                            <p class="text-sm text-gray-500">Contact: {{ $family->contact_person }}</p>
                        @endif
                    </div>
                    <div class="flex gap-2">
                        <button
                            wire:click="edit({{ $family->id }})"
                            class="text-blue-600 hover:underline text-sm">
                            Edit
                        </button>
                        <button
                            wire:click="deleteFamily({{ $family->id }})"
                            onclick="return confirm('Are you sure? This will remove the family association from all people in this family.');"
                            class="text-red-600 hover:underline text-sm">
                            Delete
                        </button>
                    </div>
                </div>
                <div class="mt-2">
                    <span class="text-sm text-gray-600">
                        <strong>{{ $family->people_count }}</strong>
                        {{ Str::plural('person', $family->people_count) }} in this family
                    </span>
                </div>
                @if($family->people->count() > 0)
                    <ul class="mt-2 space-y-1">
                        @foreach($family->people as $person)
                            <li class="text-sm text-gray-700 border-b pb-1">
                                {{ $person->full_name }}
                                <span class="text-gray-500">({{ $person->effective_category }})</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @empty
            <div class="bg-white border rounded p-8 text-center text-gray-500">
                No families found. Click "Add Family" to create one.
            </div>
        @endforelse
    </div>

    <!-- Add/Edit Modal -->
    @if($show)
    <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50" wire:click="$set('show', false)">
        <div class="bg-white w-full max-w-lg rounded shadow p-6 space-y-4" wire:click.stop>
            <h2 class="text-xl font-bold">{{ $editing ? 'Edit Family' : 'Add Family' }}</h2>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Family Name *</label>
                <input
                    type="text"
                    wire:model.defer="family_name"
                    placeholder="Family Name"
                    class="border p-2 rounded w-full">
                @error('family_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                <input
                    type="text"
                    wire:model.defer="address"
                    placeholder="Address"
                    class="border p-2 rounded w-full">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Contact Person</label>
                <input
                    type="text"
                    wire:model.defer="contact_person"
                    placeholder="Contact Person"
                    class="border p-2 rounded w-full">
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button
                    wire:click="$set('show', false)"
                    class="px-4 py-2 border rounded hover:bg-gray-50">
                    Cancel
                </button>
                <button
                    wire:click="save"
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Save
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
