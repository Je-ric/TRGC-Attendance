<div class="flex flex-col gap-4">

    {{-- Toolbar --}}
    <x-card>
        <div class="flex flex-wrap justify-between items-center gap-3">
            <div class="flex gap-2">
                @foreach([['flat','bx-list-ul','Flat'],['family','bx-home-heart','Family'],['category','bx-category','Category']] as [$mode,$icon,$label])
                    <x-button wire:click="setViewMode('{{ $mode }}')" type="button"
                              variant="{{ $viewMode === $mode ? 'sm-primary' : 'sm-ghost' }}">
                        <i class='bx {{ $icon }}'></i> {{ $label }}
                    </x-button>
                @endforeach
            </div>
            <div class="flex gap-2 flex-wrap">
                <x-form.input wire:model.live="search" placeholder="Search…" class="w-auto min-w-[180px]" />
                <x-form.select wire:model.live="filterFamily" class="w-auto min-w-[140px]">
                    <option value="">All Families</option>
                    @foreach($families as $family)
                        <option value="{{ $family->id }}">{{ $family->family_name }} ({{ $family->people_count }})</option>
                    @endforeach
                </x-form.select>
                <x-form.select wire:model.live="filterCategory" class="w-auto min-w-[130px]">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}">{{ $cat }}</option>
                    @endforeach
                </x-form.select>
            </div>
        </div>
    </x-card>

    {{-- Flat --}}
    @if($viewMode === 'flat')
        <x-table.container>
            <x-table.table>
                <x-table.head>
                    <tr>
                        <x-table.th>Name</x-table.th>
                        <x-table.th>Category</x-table.th>
                        <x-table.th>Family</x-table.th>
                        <x-table.th>Birthdate</x-table.th>
                        <x-table.th align="right">Actions</x-table.th>
                    </tr>
                </x-table.head>
                <x-table.body>
                    @forelse($people as $person)
                        <x-table.row :hover="true">
                            <x-table.td class="font-semibold">{{ $person->full_name }}</x-table.td>
                            <x-table.td>
                                <x-feedback-status.status-indicator variant="red">
                                    {{ $person->effective_category }}
                                </x-feedback-status.status-indicator>
                            </x-table.td>
                            <x-table.td class="text-[#6b6570]">{{ $person->family->family_name ?? '—' }}</x-table.td>
                            <x-table.td class="text-[#6b6570]">
                                @if($person->birthdate)
                                    {{ $person->birthdate->format('M d, Y') }}
                                    @if($person->age)
                                        <span class="text-[#a09aa4]">· {{ $person->age }} yrs</span>
                                    @endif
                                @else —
                                @endif
                            </x-table.td>
                            <x-table.td align="right">
                                <div class="flex gap-1.5 justify-end">
                                    <x-button wire:click="$dispatchTo('person-create', 'editPerson', { id: {{ $person->id }} })" variant="table-edit">
                                        <i class='bx bx-edit-alt'></i> Edit
                                    </x-button>
                                    <x-button wire:click="confirmDelete({{ $person->id }})" variant="table-danger">
                                        <i class='bx bx-trash'></i>
                                    </x-button>
                                </div>
                            </x-table.td>
                        </x-table.row>
                    @empty
                        <x-table.empty colspan="5" message="No people match the current filters." />
                    @endforelse
                </x-table.body>
            </x-table.table>
        </x-table.container>

    {{-- Family --}}
    @elseif($viewMode === 'family')
        @php $peopleByFamily = $people->groupBy(fn($p) => $p->family_id ?: 'no-family'); @endphp
        @forelse($peopleByFamily as $familyKey => $familyPeople)
            <x-card :padding="false">
                <div class="px-4 py-3 border-b border-[#e4e0e2] flex justify-between items-start">
                    <div>
                        <h3 class="page-title text-[15px] flex items-center gap-1.5">
                            <i class='bx {{ $familyKey === "no-family" ? "bx-user-x" : "bx-buildings" }}' style="color:#ed213a"></i>
                            @if($familyKey === 'no-family') No Family
                            @else {{ $familyPeople->first()->family?->family_name ?? 'Unknown' }}
                            @endif
                        </h3>
                        @if($familyKey !== 'no-family' && $familyPeople->first()->family?->address)
                            <p class="text-[12px] text-[#a09aa4] mt-0.5">{{ $familyPeople->first()->family->address }}</p>
                        @endif
                    </div>
                    <x-feedback-status.status-indicator variant="red">
                        {{ $familyPeople->count() }} {{ Str::plural('person', $familyPeople->count()) }}
                    </x-feedback-status.status-indicator>
                </div>
                <x-table.container class="rounded-none border-0 shadow-none">
                    <x-table.table>
                        <x-table.body>
                            @foreach($familyPeople as $person)
                                <x-table.row :hover="true">
                                    <x-table.td class="font-medium">{{ $person->full_name }}</x-table.td>
                                    <x-table.td class="text-[#6b6570]">
                                        {{ $person->effective_category }}@if($person->age) · {{ $person->age }} yrs @endif
                                    </x-table.td>
                                    <x-table.td align="right">
                                        <div class="flex gap-1.5 justify-end">
                                            <x-button wire:click="$dispatchTo('person-create', 'editPerson', { id: {{ $person->id }} })" variant="table-edit">
                                                <i class='bx bx-edit-alt'></i>
                                            </x-button>
                                            <x-button wire:click="confirmDelete({{ $person->id }})" variant="table-danger">
                                                <i class='bx bx-trash'></i>
                                            </x-button>
                                        </div>
                                    </x-table.td>
                                </x-table.row>
                            @endforeach
                        </x-table.body>
                    </x-table.table>
                </x-table.container>
            </x-card>
        @empty
            <x-empty-state icon="bx bx-home-heart" title="No families found" message="No family groups match the current filters." />
        @endforelse

    {{-- Category --}}
    @elseif($viewMode === 'category')
        @foreach(array_merge($categories, ['Unknown']) as $cat)
            @php $catPeople = $people->filter(fn($p) => $p->effective_category === $cat); @endphp
            @if($catPeople->count() > 0)
                <div>
                    <div class="flex items-center gap-2 mb-2 px-0.5">
                        <span class="page-title text-[13px]">{{ $cat }}</span>
                        <x-feedback-status.status-indicator variant="red">{{ $catPeople->count() }}</x-feedback-status.status-indicator>
                    </div>
                    <x-table.container>
                        <x-table.table>
                            <x-table.body>
                                @foreach($catPeople as $person)
                                    <x-table.row :hover="true">
                                        <x-table.td class="font-semibold">{{ $person->full_name }}</x-table.td>
                                        <x-table.td class="text-[#6b6570]">
                                            {{ $person->family->family_name ?? '—' }}
                                            @if($person->age) · {{ $person->age }} yrs @endif
                                        </x-table.td>
                                        <x-table.td align="right">
                                            <div class="flex gap-1.5 justify-end">
                                                <x-button wire:click="$dispatchTo('person-create', 'editPerson', { id: {{ $person->id }} })" variant="table-edit">
                                                    <i class='bx bx-edit-alt'></i> Edit
                                                </x-button>
                                                <x-button wire:click="confirmDelete({{ $person->id }})" variant="table-danger">
                                                    <i class='bx bx-trash'></i>
                                                </x-button>
                                            </div>
                                        </x-table.td>
                                    </x-table.row>
                                @endforeach
                            </x-table.body>
                        </x-table.table>
                    </x-table.container>
                </div>
            @endif
        @endforeach
    @endif

    {{-- ── Delete Confirmation Modal ────────────────────────────────── --}}
    <x-modal.dialog id="person-delete-modal" maxWidth="max-w-md">
        <x-modal.header modalId="person-delete-modal">
            <div class="flex items-center gap-3">
                <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-[#ffe4e6] text-[#e11d48] shrink-0">
                    <i class="bx bx-trash text-base leading-none"></i>
                </span>
                <span class="text-[#9f1239]">Delete Person</span>
            </div>
        </x-modal.header>

        <x-modal.body class="space-y-4">
            <p class="text-[13px] text-[#6b6570]">Are you sure you want to remove this person?</p>

            <div class="rounded-xl border border-[#e4e0e2] bg-[#f5f4f6] p-4">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#a09aa4]">Name</span>
                    <span class="text-[13px] font-semibold text-[#1c1c1e]">{{ $confirmDeleteName }}</span>
                </div>
            </div>

            <x-feedback-status.alert type="error" :showTitle="false">
                This will permanently remove the person and all their attendance records.
            </x-feedback-status.alert>
        </x-modal.body>

        <x-modal.footer>
            <x-modal.close-button modalId="person-delete-modal" text="Cancel" />
            <x-button wire:click="deletePerson" variant="danger" loading="Deleting…">
                <i class='bx bx-trash'></i> Delete
            </x-button>
        </x-modal.footer>
    </x-modal.dialog>

</div>
