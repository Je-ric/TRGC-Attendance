<div class="flex flex-col gap-6">

    <x-page-header icon="bx-home-heart" title="Families" desc="Manage family groups and member associations.">
        <x-button wire:click="open" variant="primary">
            <i class='bx bx-plus'></i> Add Family
        </x-button>
    </x-page-header>

    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-3">
        <x-statistic-card variant="primary" icon="bx-buildings" title="Total Families" value="{{ $families->count() }}" />
        @foreach($categories as $cat)
            <x-statistic-card variant="grad-aurora" icon="bx-user" :title="$cat" :value="$categoryCounts[$cat] ?? 0" />
        @endforeach
    </div>

    {{-- Family list --}}
    @if($families->isEmpty())
        <x-empty-state icon="bx bx-home-heart" title="No families yet" message='Click "Add Family" to create one.' />
    @else
        {{-- Bento grid: columns sized to content, not equal height --}}
        <div class="columns-1 sm:columns-2 lg:columns-3 gap-4 space-y-4">
            @foreach($families as $family)
                <div class="break-inside-avoid">
                    <x-family.card :family="$family">
                        <x-button wire:click="edit({{ $family->id }})" variant="table-edit">
                            <i class='bx bx-edit-alt'></i> Edit
                        </x-button>
                        <x-button wire:click="confirmDelete({{ $family->id }})" variant="table-danger">
                            <i class='bx bx-trash'></i>
                        </x-button>
                    </x-family.card>
                </div>
            @endforeach
        </div>
    @endif

    @include('livewire.modals.familyFormModal')
    @include('livewire.modals.familyDeleteModal')

</div>
