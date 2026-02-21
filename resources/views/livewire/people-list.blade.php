<div>

    {{-- ── Toolbar ── --}}
    <div class="ui-card p-4 mb-4">
        <div class="flex flex-wrap justify-between items-center gap-3">

            {{-- View mode tabs --}}
            <div class="flex gap-1.5">
                <button wire:click="setViewMode('flat')" type="button"
                        class="ui-btn text-sm py-1.5 px-3 {{ $viewMode === 'flat' ? 'ui-btn-maroon' : 'ui-btn-ghost' }}">
                    <i class='bx bx-list-ul'></i> Flat
                </button>
                <button wire:click="setViewMode('family')" type="button"
                        class="ui-btn text-sm py-1.5 px-3 {{ $viewMode === 'family' ? 'ui-btn-maroon' : 'ui-btn-ghost' }}">
                    <i class='bx bx-home-heart'></i> By Family
                </button>
                <button wire:click="setViewMode('category')" type="button"
                        class="ui-btn text-sm py-1.5 px-3 {{ $viewMode === 'category' ? 'ui-btn-maroon' : 'ui-btn-ghost' }}">
                    <i class='bx bx-category'></i> By Category
                </button>
            </div>

            {{-- Server-side filters --}}
            <div class="flex gap-2 flex-wrap">
                <input type="text" wire:model.live="search"
                       placeholder="Search name or contact…"
                       class="ui-input" style="width:auto;min-width:180px">
                <select wire:model.live="filterFamily" class="ui-input" style="width:auto;min-width:140px">
                    <option value="">All Families</option>
                    @foreach($families as $family)
                        <option value="{{ $family->id }}">
                            {{ $family->family_name }} ({{ $family->people_count }})
                        </option>
                    @endforeach
                </select>
                <select wire:model.live="filterCategory" class="ui-input" style="width:auto;min-width:130px">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}">{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- ══ FLAT VIEW ══ --}}
    @if($viewMode === 'flat')
        @forelse($people as $person)
            <div class="ui-card p-4 mb-2">
                <div class="flex justify-between items-start gap-3">
                    <div class="min-w-0">
                        <div class="font-semibold flex items-center gap-1.5" style="color:var(--ink)">
                            <i class='bx bx-user text-[#6B0F1A]'></i>
                            {{ $person->full_name }}
                        </div>
                        <div class="dm-sans text-sm mt-0.5" style="color:var(--ink-muted)">
                            <span class="badge badge-gold mr-1">{{ $person->effective_category }}</span>
                            {{ $person->family->family_name ?? 'No family' }}
                            @if($person->age) · {{ $person->age }} yrs @endif
                        </div>
                        @if($person->birthdate)
                            <div class="dm-sans text-xs mt-1" style="color:var(--ink-faint)">
                                🎂 {{ $person->birthdate->format('M d, Y') }}
                            </div>
                        @endif
                    </div>
                    <div class="flex gap-2 flex-shrink-0">
                        <button wire:click="$dispatch('editPerson', { id: {{ $person->id }} })"
                                class="ui-btn ui-btn-edit text-sm py-1.5 px-3">
                            <i class='bx bx-edit-alt'></i> Edit
                        </button>
                        <button wire:click="deletePerson({{ $person->id }})"
                                onclick="return confirm('Delete {{ addslashes($person->full_name) }}?')"
                                class="ui-btn ui-btn-delete text-sm py-1.5 px-3">
                            <i class='bx bx-trash'></i> Delete
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="ui-card-soft p-6 text-center dm-sans" style="color:var(--ink-muted)">No people match the current filters.</div>
        @endforelse

    {{-- ══ FAMILY VIEW ══ --}}
    @elseif($viewMode === 'family')
        @php
            $peopleByFamily = $people->groupBy(fn($p) => $p->family_id ?: 'no-family');
        @endphp
        @forelse($peopleByFamily as $familyKey => $familyPeople)
            <div class="ui-card p-4 mb-4">
                {{-- Family header --}}
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <h3 class="font-bold text-lg flex items-center gap-1.5" style="color:var(--ink)">
                            @if($familyKey === 'no-family')
                                <i class='bx bx-user-x text-[#6B0F1A]'></i>No Family
                            @else
                                @php $fam = $familyPeople->first()->family; @endphp
                                <i class='bx bx-buildings text-[#6B0F1A]'></i>
                                {{ $fam?->family_name ?? 'Unknown Family' }}
                            @endif
                        </h3>
                        @if(isset($fam) && $fam?->address)
                            <p class="dm-sans text-xs mt-0.5" style="color:var(--ink-muted)">{{ $fam->address }}</p>
                        @endif
                    </div>
                    <span class="badge badge-gold">
                        {{ $familyPeople->count() }} {{ Str::plural('person', $familyPeople->count()) }}
                    </span>
                </div>
                <hr class="ui-divider mb-3">

                {{-- Two-column person grid --}}
                <ul class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-1">
                    @foreach($familyPeople as $person)
                        <li class="flex justify-between items-center py-2 px-1 rounded hover:bg-[var(--surface-soft)] transition-colors"
                            style="border-bottom:1px solid var(--border)">
                            <div class="min-w-0">
                                <div class="font-medium text-sm" style="color:var(--ink)">{{ $person->full_name }}</div>
                                <div class="dm-sans text-xs" style="color:var(--ink-faint)">
                                    {{ $person->effective_category }}
                                    @if($person->age) · {{ $person->age }} yrs @endif
                                </div>
                                @if($person->birthdate)
                                    <div class="dm-sans text-xs" style="color:var(--ink-faint)">
                                        🎂 {{ $person->birthdate->format('M d, Y') }}
                                    </div>
                                @endif
                            </div>
                            <div class="flex gap-1.5 flex-shrink-0 ml-2">
                                <button wire:click="$dispatch('editPerson', { id: {{ $person->id }} })"
                                        class="ui-btn ui-btn-edit text-xs py-1 px-2">
                                    <i class='bx bx-edit-alt'></i>
                                </button>
                                <button wire:click="deletePerson({{ $person->id }})"
                                        onclick="return confirm('Delete {{ addslashes($person->full_name) }}?')"
                                        class="ui-btn ui-btn-delete text-xs py-1 px-2">
                                    <i class='bx bx-trash'></i>
                                </button>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        @empty
            <div class="ui-card-soft p-6 text-center dm-sans" style="color:var(--ink-muted)">No family groups match the current filters.</div>
        @endforelse

    {{-- ══ CATEGORY VIEW ══ --}}
    @elseif($viewMode === 'category')
        @php
            $displayCategories = array_merge($categories, ['Unknown']);
        @endphp
        @foreach($displayCategories as $cat)
            @php $catPeople = $people->filter(fn($p) => $p->effective_category === $cat); @endphp
            @if($catPeople->count() > 0)
                <div class="mb-5">
                    <div class="flex items-center gap-2 mb-2 px-1">
                        <i class='bx bx-category-alt text-[#6B0F1A] text-lg'></i>
                        <span class="font-bold" style="color:var(--ink)">{{ $cat }}</span>
                        <span class="badge badge-gold">{{ $catPeople->count() }}</span>
                    </div>
                    @foreach($catPeople as $person)
                        <div class="ui-card p-4 mb-2">
                            <div class="flex justify-between items-start gap-3">
                                <div class="min-w-0">
                                    <div class="font-semibold flex items-center gap-1.5" style="color:var(--ink)">
                                        <i class='bx bx-user text-[#6B0F1A]'></i>
                                        {{ $person->full_name }}
                                    </div>
                                    <div class="dm-sans text-sm mt-0.5" style="color:var(--ink-muted)">
                                        {{ $person->family->family_name ?? 'No family' }}
                                        @if($person->age) · {{ $person->age }} yrs @endif
                                    </div>
                                    @if($person->birthdate)
                                        <div class="dm-sans text-xs mt-1" style="color:var(--ink-faint)">
                                            🎂 {{ $person->birthdate->format('M d, Y') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="flex gap-2 flex-shrink-0">
                                    <button wire:click="$dispatch('editPerson', { id: {{ $person->id }} })"
                                            class="ui-btn ui-btn-edit text-sm py-1.5 px-3">
                                        <i class='bx bx-edit-alt'></i> Edit
                                    </button>
                                    <button wire:click="deletePerson({{ $person->id }})"
                                            onclick="return confirm('Delete {{ addslashes($person->full_name) }}?')"
                                            class="ui-btn ui-btn-delete text-sm py-1.5 px-3">
                                        <i class='bx bx-trash'></i> Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        @endforeach
    @endif

</div>

