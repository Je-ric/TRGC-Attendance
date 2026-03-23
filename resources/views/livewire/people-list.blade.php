<div>
    {{-- Toolbar --}}
    <div class="card" style="padding:14px 16px;margin-bottom:14px">
        <div style="display:flex;flex-wrap:wrap;justify-content:space-between;align-items:center;gap:10px">
            <div style="display:flex;gap:6px">
                @foreach([['flat','bx-list-ul','Flat'],['family','bx-home-heart','Family'],['category','bx-category','Category']] as [$mode,$icon,$label])
                    <button wire:click="setViewMode('{{ $mode }}')" type="button"
                            class="btn {{ $viewMode === $mode ? 'btn-secondary' : 'btn-ghost' }}"
                            style="font-size:12px;padding:6px 10px">
                        <i class='bx {{ $icon }}'></i> {{ $label }}
                    </button>
                @endforeach
            </div>
            <div style="display:flex;gap:8px;flex-wrap:wrap">
                <input type="text" wire:model.live="search" placeholder="Search…" class="ui-input" style="width:auto;min-width:180px">
                <select wire:model.live="filterFamily" class="ui-input" style="width:auto;min-width:140px">
                    <option value="">All Families</option>
                    @foreach($families as $family)
                        <option value="{{ $family->id }}">{{ $family->family_name }} ({{ $family->people_count }})</option>
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

    {{-- Flat --}}
    @if($viewMode === 'flat')
        @forelse($people as $person)
            <div class="card" style="padding:14px 16px;margin-bottom:8px">
                <div style="display:flex;justify-content:space-between;align-items:center;gap:12px">
                    <div style="min-width:0">
                        <div style="font-size:14px;font-weight:600;color:var(--ink)">{{ $person->full_name }}</div>
                        <div style="font-size:12px;color:var(--ink-faint);margin-top:2px">
                            <span class="badge badge-red">{{ $person->effective_category }}</span>
                            <span style="margin-left:6px">{{ $person->family->family_name ?? 'No family' }}</span>
                            @if($person->age) · {{ $person->age }} yrs @endif
                        </div>
                        @if($person->birthdate)
                            <div style="font-size:11.5px;color:var(--ink-faint);margin-top:2px">🎂 {{ $person->birthdate->format('M d, Y') }}</div>
                        @endif
                    </div>
                    <div style="display:flex;gap:6px;flex-shrink:0">
                        <button wire:click="$dispatch('editPerson', { id: {{ $person->id }} })" class="btn btn-edit" style="font-size:12px;padding:6px 10px">
                            <i class='bx bx-edit-alt'></i> Edit
                        </button>
                        <button wire:click="deletePerson({{ $person->id }})"
                                onclick="return confirm('Delete {{ addslashes($person->full_name) }}?')"
                                class="btn btn-danger" style="font-size:12px;padding:6px 10px">
                            <i class='bx bx-trash'></i>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="card-soft" style="padding:32px;text-align:center;font-size:13px;color:var(--ink-faint)">No people match the current filters.</div>
        @endforelse

    {{-- Family --}}
    @elseif($viewMode === 'family')
        @php $peopleByFamily = $people->groupBy(fn($p) => $p->family_id ?: 'no-family'); @endphp
        @forelse($peopleByFamily as $familyKey => $familyPeople)
            <div class="card" style="padding:16px;margin-bottom:12px">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:12px">
                    <div>
                        <h3 style="font-size:15px;font-weight:700;color:var(--ink);margin:0;display:flex;align-items:center;gap:6px">
                            <i class='bx {{ $familyKey === "no-family" ? "bx-user-x" : "bx-buildings" }}' style="color:var(--red)"></i>
                            @if($familyKey === 'no-family') No Family
                            @else {{ $familyPeople->first()->family?->family_name ?? 'Unknown' }}
                            @endif
                        </h3>
                        @if($familyKey !== 'no-family' && $familyPeople->first()->family?->address)
                            <p style="font-size:12px;color:var(--ink-faint);margin:2px 0 0">{{ $familyPeople->first()->family->address }}</p>
                        @endif
                    </div>
                    <span class="badge badge-red">{{ $familyPeople->count() }} {{ Str::plural('person', $familyPeople->count()) }}</span>
                </div>
                <hr class="ui-divider" style="margin-bottom:10px">
                <ul style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:4px;list-style:none;margin:0;padding:0">
                    @foreach($familyPeople as $person)
                        <li style="display:flex;justify-content:space-between;align-items:center;padding:8px 6px;border-radius:6px;border-bottom:1px solid var(--border-soft)">
                            <div style="min-width:0">
                                <div style="font-size:13px;font-weight:500;color:var(--ink)">{{ $person->full_name }}</div>
                                <div style="font-size:11.5px;color:var(--ink-faint)">{{ $person->effective_category }}@if($person->age) · {{ $person->age }} yrs @endif</div>
                            </div>
                            <div style="display:flex;gap:4px;flex-shrink:0;margin-left:8px">
                                <button wire:click="$dispatch('editPerson', { id: {{ $person->id }} })" class="btn btn-edit" style="font-size:11px;padding:4px 8px"><i class='bx bx-edit-alt'></i></button>
                                <button wire:click="deletePerson({{ $person->id }})" onclick="return confirm('Delete {{ addslashes($person->full_name) }}?')" class="btn btn-danger" style="font-size:11px;padding:4px 8px"><i class='bx bx-trash'></i></button>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        @empty
            <div class="card-soft" style="padding:32px;text-align:center;font-size:13px;color:var(--ink-faint)">No family groups match the current filters.</div>
        @endforelse

    {{-- Category --}}
    @elseif($viewMode === 'category')
        @foreach(array_merge($categories, ['Unknown']) as $cat)
            @php $catPeople = $people->filter(fn($p) => $p->effective_category === $cat); @endphp
            @if($catPeople->count() > 0)
                <div style="margin-bottom:20px">
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;padding:0 2px">
                        <span style="font-size:13px;font-weight:700;color:var(--ink)">{{ $cat }}</span>
                        <span class="badge badge-red">{{ $catPeople->count() }}</span>
                    </div>
                    @foreach($catPeople as $person)
                        <div class="card" style="padding:14px 16px;margin-bottom:6px">
                            <div style="display:flex;justify-content:space-between;align-items:center;gap:12px">
                                <div style="min-width:0">
                                    <div style="font-size:14px;font-weight:600;color:var(--ink)">{{ $person->full_name }}</div>
                                    <div style="font-size:12px;color:var(--ink-faint);margin-top:2px">
                                        {{ $person->family->family_name ?? 'No family' }}
                                        @if($person->age) · {{ $person->age }} yrs @endif
                                    </div>
                                </div>
                                <div style="display:flex;gap:6px;flex-shrink:0">
                                    <button wire:click="$dispatch('editPerson', { id: {{ $person->id }} })" class="btn btn-edit" style="font-size:12px;padding:6px 10px"><i class='bx bx-edit-alt'></i> Edit</button>
                                    <button wire:click="deletePerson({{ $person->id }})" onclick="return confirm('Delete {{ addslashes($person->full_name) }}?')" class="btn btn-danger" style="font-size:12px;padding:6px 10px"><i class='bx bx-trash'></i></button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        @endforeach
    @endif
</div>
