<div class="flex flex-col gap-6">

    <x-page-header icon="bx-home-heart" title="Families" desc="Manage family groups and member associations.">
        <x-button wire:click="open" variant="primary">
            <i class='bx bx-plus'></i> Add Family
        </x-button>
    </x-page-header>

    {{-- Stats --}}
    <div class="grid gap-3" style="grid-template-columns:repeat(auto-fill,minmax(150px,1fr))">
        <x-statistic-card variant="primary" icon="bx-buildings" title="Total Families" value="{{ $families->count() }}" />
        @foreach($categories as $cat)
            <x-statistic-card variant="muted" icon="bx-user" :title="$cat" :value="$categoryCounts[$cat] ?? 0" />
        @endforeach
    </div>

    {{-- Family list --}}
    @if($families->isEmpty())
        <x-empty-state icon="bx bx-home-heart" title="No families yet" message='Click "Add Family" to create one.' />
    @else
        <div class="grid gap-4" style="grid-template-columns:repeat(auto-fill,minmax(280px,1fr))">
            @foreach($families as $family)
                <x-card :padding="false">
                    <div class="px-4 py-3 border-b border-[#e4e0e2] flex justify-between items-start gap-3">
                        <div class="min-w-0">
                            <h4 class="page-title text-[15px] flex items-center gap-1.5">
                                <i class='bx bx-buildings text-[#ed213a] shrink-0'></i>
                                {{ $family->family_name }}
                            </h4>
                            @if($family->address)
                                <p class="text-[12px] text-[#a09aa4] mt-0.5">{{ $family->address }}</p>
                            @endif
                            @if($family->contact_person)
                                <p class="text-[12px] text-[#a09aa4]">{{ $family->contact_person }}</p>
                            @endif
                        </div>
                        <div class="flex gap-1.5 shrink-0">
                            <x-button wire:click="edit({{ $family->id }})" variant="table-edit">
                                <i class='bx bx-edit-alt'></i>
                            </x-button>
                            <x-button wire:click="confirmDelete({{ $family->id }})" variant="table-danger">
                                <i class='bx bx-trash'></i>
                            </x-button>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="text-[12px] text-[#a09aa4] mb-3">
                            <strong class="text-[#1c1c1e]">{{ $family->people_count }}</strong>
                            {{ Str::plural('person', $family->people_count) }}
                        </div>

                        @if($family->people->count() > 0)
                            <div class="border-t border-[#e4e0e2] pt-3 flex flex-col gap-0.5">
                                @foreach($family->people as $person)
                                    <div class="flex justify-between items-center py-1.5 border-b border-[#ede9eb] last:border-0">
                                        <span class="text-[12px] text-[#1c1c1e]">{{ $person->full_name }}</span>
                                        <x-feedback-status.status-indicator variant="slate">
                                            {{ $person->effective_category }}
                                        </x-feedback-status.status-indicator>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </x-card>
            @endforeach
        </div>
    @endif

    {{-- ── Add / Edit Modal ─────────────────────────────────────────── --}}
    <x-modal.dialog id="family-form-modal" maxWidth="max-w-md">
        <x-modal.header modalId="family-form-modal">
            <div class="flex items-center gap-3">
                <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-[#fff0f0] text-[#ed213a] shrink-0">
                    <i class="bx bx-buildings text-base leading-none"></i>
                </span>
                {{ $editing ? 'Edit Family' : 'Add Family' }}
            </div>
        </x-modal.header>

        <x-modal.body class="flex flex-col gap-4">
            <p class="text-[13px] text-[#6b6570]">
                {{ $editing ? 'Update family details.' : 'Create a new family group.' }}
            </p>

            <x-form.field label="Family Name" :isRequired="true" error="family_name">
                <x-form.input wire:model="family_name" placeholder="e.g., Santos Family" />
            </x-form.field>

            <x-form.field label="Address">
                <x-form.input wire:model="address" placeholder="Address" />
            </x-form.field>

            <x-form.field label="Contact Person">
                <x-form.input wire:model="contact_person" placeholder="Contact person name" />
            </x-form.field>
        </x-modal.body>

        <x-modal.footer>
            <x-modal.close-button modalId="family-form-modal" text="Cancel" />
            <x-button wire:click="save" variant="primary" loading="Saving…">
                <i class='bx bx-save'></i> Save
            </x-button>
        </x-modal.footer>
    </x-modal.dialog>

    {{-- ── Delete Confirmation Modal ────────────────────────────────── --}}
    <x-modal.dialog id="family-delete-modal" maxWidth="max-w-md">
        <x-modal.header modalId="family-delete-modal">
            <div class="flex items-center gap-3">
                <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-[#ffe4e6] text-[#e11d48] shrink-0">
                    <i class="bx bx-trash text-base leading-none"></i>
                </span>
                <span class="text-[#9f1239]">Delete Family</span>
            </div>
        </x-modal.header>

        <x-modal.body class="space-y-4">
            <p class="text-[13px] text-[#6b6570]">Are you sure you want to remove this family?</p>

            <div class="rounded-xl border border-[#e4e0e2] bg-[#f5f4f6] p-4">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#a09aa4]">Family</span>
                    <span class="text-[13px] font-semibold text-[#1c1c1e]">{{ $confirmDeleteName }}</span>
                </div>
            </div>

            <x-feedback-status.alert type="warning" :showTitle="false">
                All members will be unlinked from this family. This cannot be undone.
            </x-feedback-status.alert>
        </x-modal.body>

        <x-modal.footer>
            <x-modal.close-button modalId="family-delete-modal" text="Cancel" />
            <x-button wire:click="deleteFamily" variant="danger" loading="Deleting…">
                <i class='bx bx-trash'></i> Delete
            </x-button>
        </x-modal.footer>
    </x-modal.dialog>

</div>
