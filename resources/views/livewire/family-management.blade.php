<div class="space-y-6">
    <!-- Header with Add Button -->
    <x-title-with-header title="Family Management"
        description="Manage family groups, categories, and member associations.">
        <button wire:click="open" class="ui-btn ui-btn-primary">
            <i class='bx bx-plus-circle'></i>
            Add Family
        </button>
    </x-title-with-header>


    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
            <x-statistic-card 
                    gradientVariant="deep-rose" 
                    icon="bx-users" 
                    title="Total Families" 
                    value="{{ $families->count() }}">

            </x-statistic-card>
            @foreach($categories as $cat)
                <x-statistic-card 
                    icon="bx-user"
                    :title="$cat"
                    :value="$categoryCounts[$cat] ?? 0"
                    gradientVariant="sunset"
                />
            @endforeach
        </div>

    <!-- Family List -->
    <div class="columns-1 md:columns-2 gap-4 space-y-4">
        @forelse($families as $family)
            <div class="ui-card p-4 break-inside-avoid">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <h4 class="font-bold text-lg flex items-center gap-2">
                            <i class='bx bx-buildings text-[#6B0F1A]'></i>
                            {{ $family->family_name }}
                        </h4>
                        @if ($family->address)
                            <p class="dm-sans text-sm" style="color:var(--ink-muted)">{{ $family->address }}</p>
                        @endif
                        @if ($family->contact_person)
                            <p class="dm-sans text-sm" style="color:var(--ink-muted)">Contact:
                                {{ $family->contact_person }}</p>
                        @endif
                    </div>
                    <div class="flex gap-2">
                        <button wire:click="edit({{ $family->id }})" class="ui-btn ui-btn-edit text-sm py-1.5 px-3">
                            <i class='bx bx-edit-alt'></i>
                            Edit
                        </button>
                        <button wire:click="deleteFamily({{ $family->id }})"
                            onclick="return confirm('Are you sure? This will remove the family association from all people in this family.');"
                            class="ui-btn ui-btn-delete text-sm py-1.5 px-3">
                            <i class='bx bx-trash'></i>
                            Delete
                        </button>
                    </div>
                </div>
                <div class="mt-2">
                    <span class="dm-sans text-sm" style="color:var(--ink-muted)">
                        <strong style="color:var(--ink)">{{ $family->people_count }}</strong>
                        {{ Str::plural('person', $family->people_count) }} in this family
                    </span>
                </div>
                @if ($family->people->count() > 0)
                    <hr class="ui-divider my-3">
                    <ul>
                        @foreach ($family->people as $person)
                            <li class="dm-sans text-sm py-1"
                                style="color:var(--ink);border-bottom:1px solid var(--border)">
                                {{ $person->full_name }}
                                <span class="text-xs"
                                    style="color:var(--ink-faint)">({{ $person->effective_category }})</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @empty
            <div class="ui-card-soft p-8 text-center dm-sans" style="color:var(--ink-muted)">
                No families found. Click "Add Family" to create one.
            </div>
        @endforelse
    </div>

    <!-- Add/Edit Modal -->
    @if ($show)
        <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50" style="z-index:9999"
            wire:click="$set('show', false)">
            <div class="ui-card-soft w-full max-w-lg shadow-xl p-6 space-y-4" wire:click.stop>
                <h2 class="page-title text-2xl text-[#6B0F1A] flex items-center gap-2">
                    <i class='bx {{ $editing ? 'bx-edit' : 'bx-plus-circle' }} text-[#C9A84C]'></i>
                    {{ $editing ? 'Edit Family' : 'Add Family' }}
                </h2>

                <hr class="ui-divider">

                <div>
                    <label class="form-label">Family Name *</label>
                    <input type="text" wire:model.defer="family_name" placeholder="Family Name" class="ui-input">
                    @error('family_name')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="form-label">Address</label>
                    <input type="text" wire:model.defer="address" placeholder="Address" class="ui-input">
                </div>

                <div>
                    <label class="form-label">Contact Person</label>
                    <input type="text" wire:model.defer="contact_person" placeholder="Contact Person"
                        class="ui-input">
                </div>

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
