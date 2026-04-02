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
                <x-form.input wire:model.live="search" placeholder="Search name, contact, email…" class="w-auto min-w-[200px]" />
                <x-form.select wire:model.live="filterFamily" class="w-auto min-w-[140px]">
                    <option value="">All Families</option>
                    @foreach($families as $family)
                        <option value="{{ $family->id }}">{{ $family->family_name }} ({{ $family->people_count }})</option>
                    @endforeach
                </x-form.select>
                <x-form.select wire:model.live="filterCategory" class="w-auto min-w-[120px]">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}">{{ $cat }}</option>
                    @endforeach
                </x-form.select>
                <x-form.select wire:model.live="filterMembership" class="w-auto min-w-[140px]">
                    <option value="">All Statuses</option>
                    @foreach($membershipStatuses as $ms)
                        <option value="{{ $ms }}">{{ $ms }}</option>
                    @endforeach
                </x-form.select>
            </div>
        </div>
    </x-card>

    {{-- Count --}}
    <div class="text-[12px] text-[#a09aa4] px-1">
        Showing <strong class="text-[#1c1c1e]">{{ $people->count() }}</strong> {{ Str::plural('person', $people->count()) }}
    </div>

    {{-- Flat --}}
    @if($viewMode === 'flat')
        <x-table.container>
            <x-table.table>
                <x-table.head>
                    <tr>
                        <x-table.th>#</x-table.th>
                        <x-table.th>Person</x-table.th>
                        <x-table.th>Status</x-table.th>
                        <x-table.th>Family</x-table.th>
                        <x-table.th>Contact</x-table.th>
                        <x-table.th>Joined</x-table.th>
                        <x-table.th align="right">Actions</x-table.th>
                    </tr>
                </x-table.head>
                <x-table.body>
                    @forelse($people as $person)
                        <x-table.row :hover="true">
                            <x-table.td>{{ $loop->iteration }}</x-table.td>
                            <x-table.td>
                                <x-person.card :person="$person" :showFamily="false" :showActions="false" compact />
                            </x-table.td>
                            <x-table.td>
                                @php
                                    $msColors = ['Member'=>'red','Regular Attendee'=>'blue','Visitor'=>'amber','Inactive'=>'slate'];
                                    $msColor = $msColors[$person->membership_status ?? ''] ?? 'slate';
                                @endphp
                                <x-feedback-status.status-indicator :variant="$msColor">
                                    {{ $person->membership_status ?? 'Regular Attendee' }}
                                </x-feedback-status.status-indicator>
                            </x-table.td>
                            <x-table.td class="text-[#6b6570]">{{ $person->family->family_name ?? '—' }}</x-table.td>
                            <x-table.td>
                                @if($person->contact_number)
                                    <div class="text-[12px] text-[#1c1c1e]">{{ $person->contact_number }}</div>
                                @endif
                                @if($person->email)
                                    <div class="text-[11px] text-[#a09aa4]">{{ $person->email }}</div>
                                @endif
                                @if(!$person->contact_number && !$person->email)
                                    <span class="text-[#a09aa4]">—</span>
                                @endif
                            </x-table.td>
                            <x-table.td class="text-[#6b6570]">
                                {{ $person->joined_date ? $person->joined_date->format('M Y') : '—' }}
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
                        <x-table.empty colspan="6" message="No people match the current filters." />
                    @endforelse
                </x-table.body>
            </x-table.table>
        </x-table.container>

    {{-- Family --}}
    @elseif($viewMode === 'family')
        @php $peopleByFamily = $people->groupBy(fn($p) => $p->family_id ?: 'no-family'); @endphp
        @forelse($peopleByFamily as $familyKey => $familyPeople)
            <x-card :padding="false">
                <div class="px-4 py-3 border-b border-[#e4e0e2] flex justify-between items-center">
                    <h3 class="page-title text-[15px] flex items-center gap-2">
                        <i class='bx {{ $familyKey === "no-family" ? "bx-user-x" : "bx-buildings" }}' style="color:#ed213a"></i>
                        @if($familyKey === 'no-family') No Family
                        @else {{ $familyPeople->first()->family?->family_name ?? 'Unknown' }}
                        @endif
                    </h3>
                    <x-feedback-status.status-indicator variant="red">
                        {{ $familyPeople->count() }} {{ Str::plural('person', $familyPeople->count()) }}
                    </x-feedback-status.status-indicator>
                </div>
                <div class="px-4 py-1 divide-y divide-[#ede9eb]">
                    @foreach($familyPeople as $person)
                        <x-person.card :person="$person" :showFamily="false">
                            <x-button wire:click="$dispatchTo('person-create', 'editPerson', { id: {{ $person->id }} })" variant="table-edit">
                                <i class='bx bx-edit-alt'></i>
                            </x-button>
                            <x-button wire:click="confirmDelete({{ $person->id }})" variant="table-danger">
                                <i class='bx bx-trash'></i>
                            </x-button>
                        </x-person.card>
                    @endforeach
                </div>
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
                    <x-card :padding="false">
                        <div class="px-4 py-1 divide-y divide-[#ede9eb]">
                            @foreach($catPeople as $person)
                                <x-person.card :person="$person">
                                    <x-button wire:click="$dispatchTo('person-create', 'editPerson', { id: {{ $person->id }} })" variant="table-edit">
                                        <i class='bx bx-edit-alt'></i> Edit
                                    </x-button>
                                    <x-button wire:click="confirmDelete({{ $person->id }})" variant="table-danger">
                                        <i class='bx bx-trash'></i>
                                    </x-button>
                                </x-person.card>
                            @endforeach
                        </div>
                    </x-card>
                </div>
            @endif
        @endforeach
    @endif

    @include('livewire.modals.personDeleteModal')

</div>
