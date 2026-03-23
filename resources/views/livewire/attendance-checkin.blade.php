<div style="display:flex;flex-direction:column;gap:16px">

    @if(session()->has('success'))
        <div class="toast-success" style="display:flex;align-items:center;gap:8px">
            <i class='bx bx-check-circle'></i> {{ session('success') }}
        </div>
    @endif

    {{-- Session config --}}
    <div class="card" style="padding:20px">
        <div style="display:flex;flex-wrap:wrap;justify-content:space-between;align-items:flex-start;gap:12px;margin-bottom:16px">
            <div>
                <div class="page-eyebrow">Check-in</div>
                <h3 class="page-title" style="font-size:20px;margin:4px 0 2px">{{ $attendanceType->name }}</h3>
                <p style="font-size:12px;color:var(--ink-faint);margin:0">{{ $attendanceType->day_of_week ?? 'Flexible Schedule' }}</p>
            </div>
            @if($latestSession)
                <div style="text-align:right">
                    <div style="font-size:10.5px;color:var(--ink-faint);margin-bottom:2px">Latest Session</div>
                    <div style="font-size:13px;font-weight:600;color:var(--ink)">{{ $latestSession->date->format('M d, Y') }}</div>
                    @if($latestSession->service_name)
                        <span class="badge badge-red" style="margin-top:4px">{{ $latestSession->service_name }}</span>
                    @endif
                </div>
            @endif
        </div>

        <hr class="ui-divider" style="margin-bottom:16px">

        <div style="display:flex;flex-wrap:wrap;gap:12px">
            <div style="flex:1;min-width:140px">
                <label class="form-label">Date</label>
                <input type="date" wire:model.live="date" max="{{ now()->toDateString() }}" class="ui-input">
            </div>
            <div style="flex:2;min-width:200px">
                <label class="form-label">Service Name <span style="font-weight:400;text-transform:none;letter-spacing:0;color:var(--ink-faint)">(optional)</span></label>
                @if($currentSession && $currentSession->service_name)
                    <div style="display:flex;gap:8px;align-items:center">
                        <span class="ui-input" style="color:var(--ink-muted);display:block">{{ $currentSession->service_name }}</span>
                        <button type="button" wire:click="$set('service_name', '')" class="btn btn-ghost" style="white-space:nowrap;font-size:12px">
                            <i class='bx bx-edit-alt'></i> Edit
                        </button>
                    </div>
                @else
                    <input type="text" wire:model.live="service_name" placeholder="e.g., Morning Service" class="ui-input">
                @endif
            </div>
        </div>
    </div>

    {{-- Stats --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(120px,1fr));gap:10px">
        <div class="card" style="padding:14px 16px">
            <div class="stat-label">Total</div>
            <div class="stat-value">{{ $totalCount }}</div>
        </div>
        <div style="padding:14px 16px;border-radius:12px;background:#f0fdf4;border:1px solid #86efac">
            <div class="stat-label" style="color:#15803d">Present</div>
            <div class="stat-value" style="color:#15803d">{{ $presentCount }}</div>
        </div>
        @foreach($categories as $cat)
            <div class="card" style="padding:14px 16px">
                <div class="stat-label">{{ $cat }}</div>
                <div class="stat-value" style="color:var(--red-dark)">{{ $categoryCounts[$cat] ?? 0 }}</div>
            </div>
        @endforeach
    </div>

    {{-- Attendee selector --}}
    <div class="card" style="padding:20px">
        <div style="display:flex;flex-wrap:wrap;justify-content:space-between;align-items:center;gap:10px;margin-bottom:14px">
            <div style="display:flex;gap:6px">
                @foreach([['flat','bx-list-ul','Flat'],['family','bx-home-heart','Family'],['category','bx-category','Category']] as [$mode,$icon,$label])
                    <button wire:click="setViewMode('{{ $mode }}')"
                            class="btn {{ $viewMode === $mode ? 'btn-secondary' : 'btn-ghost' }}"
                            style="font-size:12px;padding:6px 10px">
                        <i class='bx {{ $icon }}'></i> {{ $label }}
                    </button>
                @endforeach
            </div>
            <div style="display:flex;gap:8px;flex-wrap:wrap">
                <select wire:model.live="filterFamily" class="ui-input" style="width:auto;min-width:130px">
                    <option value="">All Families</option>
                    @foreach($families as $family)
                        <option value="{{ $family->id }}">{{ $family->family_name }}</option>
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

        <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:10px">
            <label class="form-label" style="margin:0">Attendees</label>
            <livewire:person-create />
        </div>

        <input type="text" wire:model.debounce.300ms="search" placeholder="Search name or contact…" class="ui-input" style="margin-bottom:10px">

        <div style="border:1px solid var(--border);border-radius:8px;overflow:hidden;max-height:420px;overflow-y:auto">
            @if($viewMode === 'flat')
                @forelse($allPeople as $person)
                    @php $isChecked = isset($checked[$person->id]) && $checked[$person->id]; @endphp
                    <label class="checkin-row {{ $isChecked ? 'is-checked' : '' }}">
                        <input type="checkbox" wire:click="togglePerson({{ $person->id }})" @checked($isChecked)
                               style="margin-right:10px;width:15px;height:15px;accent-color:var(--red);flex-shrink:0">
                        <div style="flex:1;min-width:0">
                            <div style="font-size:13.5px;font-weight:500;color:var(--ink)">{{ $person->full_name }}</div>
                            <div style="font-size:11.5px;color:var(--ink-faint)">
                                {{ $person->effective_category }}
                                @if($person->age) · {{ $person->age }} yrs @endif
                                @if($person->family) · {{ $person->family->family_name }} @endif
                            </div>
                        </div>
                        @if($isChecked)
                            <span class="badge badge-present" style="margin-left:8px">Present</span>
                        @endif
                    </label>
                @empty
                    <div style="padding:24px;text-align:center;font-size:13px;color:var(--ink-faint)">No people match the current filters.</div>
                @endforelse

            @elseif($viewMode === 'family')
                @php $peopleByFamily = $allPeople->groupBy(fn($p) => $p->family_id ?: 'no-family'); @endphp
                @foreach($peopleByFamily as $familyId => $people)
                    <div style="background:var(--surface);padding:7px 14px;font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:var(--ink-muted);border-bottom:1px solid var(--border)">
                        @if($familyId !== 'no-family')
                            {{ $people->first()->family?->family_name ?? 'Unknown' }} ({{ $people->count() }})
                        @else
                            No Family ({{ $people->count() }})
                        @endif
                    </div>
                    @foreach($people as $person)
                        @php $isChecked = isset($checked[$person->id]) && $checked[$person->id]; @endphp
                        <label class="checkin-row {{ $isChecked ? 'is-checked' : '' }}">
                            <input type="checkbox" wire:click="togglePerson({{ $person->id }})" @checked($isChecked)
                                   style="margin-right:10px;width:15px;height:15px;accent-color:var(--red);flex-shrink:0">
                            <div style="flex:1;min-width:0">
                                <div style="font-size:13.5px;font-weight:500;color:var(--ink)">{{ $person->full_name }}</div>
                                <div style="font-size:11.5px;color:var(--ink-faint)">{{ $person->effective_category }}</div>
                            </div>
                            @if($isChecked) <span class="badge badge-present" style="margin-left:8px">Present</span> @endif
                        </label>
                    @endforeach
                @endforeach

            @elseif($viewMode === 'category')
                @php $peopleByCategory = $allPeople->groupBy('effective_category'); @endphp
                @foreach($peopleByCategory as $category => $people)
                    <div style="background:var(--surface);padding:7px 14px;font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:var(--ink-muted);border-bottom:1px solid var(--border)">
                        {{ $category }} ({{ $people->count() }})
                    </div>
                    @foreach($people as $person)
                        @php $isChecked = isset($checked[$person->id]) && $checked[$person->id]; @endphp
                        <label class="checkin-row {{ $isChecked ? 'is-checked' : '' }}">
                            <input type="checkbox" wire:click="togglePerson({{ $person->id }})" @checked($isChecked)
                                   style="margin-right:10px;width:15px;height:15px;accent-color:var(--red);flex-shrink:0">
                            <div style="flex:1;min-width:0">
                                <div style="font-size:13.5px;font-weight:500;color:var(--ink)">{{ $person->full_name }}</div>
                                @if($person->family)
                                    <div style="font-size:11.5px;color:var(--ink-faint)">{{ $person->family->family_name }}</div>
                                @endif
                            </div>
                            @if($isChecked) <span class="badge badge-present" style="margin-left:8px">Present</span> @endif
                        </label>
                    @endforeach
                @endforeach
            @endif
        </div>
    </div>

    {{-- Save bar --}}
    <div class="card" style="padding:14px 20px;display:flex;align-items:center;justify-content:space-between;gap:12px">
        <div style="font-size:13px;color:var(--ink-muted)">
            <span style="font-weight:700;color:var(--ink)">{{ $presentCount }}</span>
            {{ Str::plural('person', $presentCount) }} selected
        </div>
        <button wire:click="save" class="btn btn-primary">
            <i class='bx bx-save'></i> Save Attendance
        </button>
    </div>
</div>
