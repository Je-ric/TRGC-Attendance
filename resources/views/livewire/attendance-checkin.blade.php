<div class="space-y-5">

    {{-- ── Flash ── --}}
    @if(session()->has('success'))
        <div class="toast-success flex items-center gap-2">
            <i class='bx bx-check-circle text-base'></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- ── Session Header Card ── --}}
    <div class="ui-card p-5">
        <div class="flex flex-wrap justify-between items-start gap-4 mb-4">
            <div>
                <div class="page-eyebrow">Check-in</div>
                <h3 class="page-title text-2xl text-[#6B0F1A] flex items-center gap-2">
                    <i class='bx bx-calendar-check text-[#C9A84C]'></i>
                    {{ $attendanceType->name }}
                </h3>
                <p class="text-sm dm-sans mt-0.5" style="color:var(--ink-muted)">
                    {{ $attendanceType->day_of_week ?? 'Flexible Schedule' }}
                </p>
            </div>
            @if($latestSession)
                <div class="text-right">
                    <div class="text-xs" style="color:var(--ink-faint)">Latest Session</div>
                    <div class="text-sm font-semibold" style="color:var(--ink)">{{ $latestSession->date->format('M d, Y') }}</div>
                    @if($latestSession->service_name)
                        <span class="badge badge-gold mt-1">{{ $latestSession->service_name }}</span>
                    @endif
                </div>
            @endif
        </div>

        <hr class="ui-divider mb-4">

        <div class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[140px]">
                <label class="form-label">Date</label>
                <input type="date" wire:model.live="date" max="{{ now()->toDateString() }}" class="ui-input">
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="form-label">
                    Service Name
                    <span style="color:var(--ink-faint);font-weight:400;text-transform:none;letter-spacing:0">(optional)</span>
                </label>
                @if($currentSession && $currentSession->service_name)
                    <div class="flex items-center gap-2">
                        <span class="flex-1 ui-input" style="color:var(--ink-muted)">{{ $currentSession->service_name }}</span>
                        <button type="button" wire:click="$set('service_name', '')"
                                class="ui-btn ui-btn-edit" style="white-space:nowrap">
                            <i class='bx bx-edit-alt'></i> Edit
                        </button>
                    </div>
                @else
                    <input type="text" wire:model.live="service_name"
                           placeholder="e.g., Morning Service"
                           class="ui-input">
                @endif
            </div>
        </div>
    </div>

    {{-- ── Stats Row ── --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-3">
        <div class="ui-card-soft p-4">
            <div class="stat-label">Total</div>
            <div class="stat-value">{{ $totalCount }}</div>
        </div>
        <div class="p-4 rounded-[10px]" style="background:#F0FDF4;border:1px solid #86EFAC;">
            <div class="stat-label" style="color:#15803D">Present</div>
            <div class="stat-value" style="color:#15803D">{{ $presentCount }}</div>
        </div>
        @foreach($categories as $cat)
            <div class="ui-card p-4">
                <div class="stat-label">{{ $cat }}</div>
                <div class="stat-value" style="color:var(--maroon)">{{ $categoryCounts[$cat] ?? 0 }}</div>
            </div>
        @endforeach
    </div>

    {{-- ── Attendee Selector ── --}}
    <div class="ui-card p-5">

        {{-- View mode + Filters --}}
        <div class="flex flex-wrap justify-between items-center gap-3 mb-4">
            <div class="flex gap-1.5">
                <button wire:click="setViewMode('flat')"
                        class="ui-btn text-xs py-1.5 px-3 {{ $viewMode === 'flat' ? 'ui-btn-maroon' : 'ui-btn-ghost' }}">
                    <i class='bx bx-list-ul'></i> Flat
                </button>
                <button wire:click="setViewMode('family')"
                        class="ui-btn text-xs py-1.5 px-3 {{ $viewMode === 'family' ? 'ui-btn-maroon' : 'ui-btn-ghost' }}">
                    <i class='bx bx-home-heart'></i> Family
                </button>
                <button wire:click="setViewMode('category')"
                        class="ui-btn text-xs py-1.5 px-3 {{ $viewMode === 'category' ? 'ui-btn-maroon' : 'ui-btn-ghost' }}">
                    <i class='bx bx-category'></i> Category
                </button>
            </div>
            <div class="flex gap-2 flex-wrap">
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

        {{-- Add person + search --}}
        <div class="flex items-center justify-between gap-3 mb-3">
            <label class="form-label mb-0">Select Attendees</label>
            <livewire:person-create />
        </div>

        <input type="text"
               wire:model.debounce.300ms="search"
               placeholder="Search name or contact…"
               class="ui-input mb-3">

        <hr class="ui-divider mb-0">

        {{-- Scrollable list --}}
        <div class="rounded-[8px] border border-[var(--border)] bg-white max-h-[420px] overflow-y-auto">
            @if($viewMode === 'flat')
                @forelse($allPeople as $person)
                    @php $isChecked = isset($checked[$person->id]) && $checked[$person->id]; @endphp
                    <label class="checkin-row {{ $isChecked ? 'is-checked' : '' }}">
                        <input type="checkbox"
                               wire:click="togglePerson({{ $person->id }})"
                               @checked($isChecked)
                               class="mr-3 w-4 h-4 flex-shrink-0" style="accent-color:var(--maroon)">
                        <div class="flex-1 min-w-0">
                            <div class="font-medium text-sm" style="color:var(--ink)">{{ $person->full_name }}</div>
                            <div class="text-xs" style="color:var(--ink-faint)">
                                {{ $person->effective_category }}
                                @if($person->age) · {{ $person->age }} yrs @endif
                                @if($person->family) · {{ $person->family->family_name }} @endif
                            </div>
                        </div>
                        @if($isChecked)
                            <span class="badge badge-present ml-2">Present</span>
                        @endif
                    </label>
                @empty
                    <div class="p-5 text-sm text-center" style="color:var(--ink-muted)">No people match the current filters.</div>
                @endforelse

            @elseif($viewMode === 'family')
                @php
                    $peopleByFamily = $allPeople->groupBy(fn($p) => $p->family_id ?: 'no-family');
                @endphp
                @foreach($peopleByFamily as $familyId => $people)
                    <div>
                        <div class="px-3 py-2 text-xs font-semibold uppercase tracking-wide"
                             style="background:var(--surface-soft);color:var(--ink-muted);border-bottom:1px solid var(--border)">
                            @if($familyId !== 'no-family')
                                @php $fam = $people->first()->family; @endphp
                                <i class='bx bx-buildings mr-1'></i>{{ $fam?->family_name ?? 'Unknown Family' }}
                                <span style="font-weight:400">({{ $people->count() }})</span>
                            @else
                                <i class='bx bx-user mr-1'></i>No Family ({{ $people->count() }})
                            @endif
                        </div>
                        @foreach($people as $person)
                            @php $isChecked = isset($checked[$person->id]) && $checked[$person->id]; @endphp
                            <label class="checkin-row {{ $isChecked ? 'is-checked' : '' }}">
                                <input type="checkbox"
                                       wire:click="togglePerson({{ $person->id }})"
                                       @checked($isChecked)
                                       class="mr-3 w-4 h-4 flex-shrink-0" style="accent-color:var(--maroon)">
                                <div class="flex-1 min-w-0">
                                    <div class="font-medium text-sm" style="color:var(--ink)">{{ $person->full_name }}</div>
                                    <div class="text-xs" style="color:var(--ink-faint)">{{ $person->effective_category }}</div>
                                </div>
                                @if($isChecked) <span class="badge badge-present ml-2">Present</span> @endif
                            </label>
                        @endforeach
                    </div>
                @endforeach

            @elseif($viewMode === 'category')
                @php $peopleByCategory = $allPeople->groupBy('effective_category'); @endphp
                @foreach($peopleByCategory as $category => $people)
                    <div>
                        <div class="px-3 py-2 text-xs font-semibold uppercase tracking-wide"
                             style="background:var(--surface-soft);color:var(--ink-muted);border-bottom:1px solid var(--border)">
                            <i class='bx bx-category-alt mr-1'></i>{{ $category }}
                            <span style="font-weight:400">({{ $people->count() }})</span>
                        </div>
                        @foreach($people as $person)
                            @php $isChecked = isset($checked[$person->id]) && $checked[$person->id]; @endphp
                            <label class="checkin-row {{ $isChecked ? 'is-checked' : '' }}">
                                <input type="checkbox"
                                       wire:click="togglePerson({{ $person->id }})"
                                       @checked($isChecked)
                                       class="mr-3 w-4 h-4 flex-shrink-0" style="accent-color:var(--maroon)">
                                <div class="flex-1 min-w-0">
                                    <div class="font-medium text-sm" style="color:var(--ink)">{{ $person->full_name }}</div>
                                    @if($person->family)
                                        <div class="text-xs" style="color:var(--ink-faint)">{{ $person->family->family_name }}</div>
                                    @endif
                                </div>
                                @if($isChecked) <span class="badge badge-present ml-2">Present</span> @endif
                            </label>
                        @endforeach
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    {{-- ── Save Bar ── --}}
    <div class="ui-card p-4 flex items-center justify-between gap-4">
        <div class="dm-sans text-sm" style="color:var(--ink-muted)">
            <span class="font-semibold" style="color:var(--ink)">{{ $presentCount }}</span>
            {{ Str::plural('person', $presentCount) }} selected
        </div>
        <button wire:click="save" class="ui-btn ui-btn-primary">
            <i class='bx bx-save'></i>
            Save Attendance
        </button>
    </div>

</div>
