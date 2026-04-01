<div class="flex flex-col gap-4">

    {{-- Session config --}}
    <x-card color="red" :padding="false">
        <div class="px-4 py-3 border-b border-[#e4e0e2] bg-[#fff0f0] flex flex-wrap justify-between items-start gap-3">
            <div>
                <div class="page-eyebrow mb-1">Check-in</div>
                <h3 class="page-title text-[18px]">{{ $attendanceType->name }}</h3>
                <div class="flex flex-wrap gap-x-3 mt-0.5">
                    @if($attendanceType->day_of_week)
                        <span class="text-[12px] text-[#a09aa4] flex items-center gap-1">
                            <i class='bx bx-calendar text-[11px]'></i>
                            {{ $attendanceType->day_of_week }}s
                            @if($attendanceType->start_time) · {{ \Carbon\Carbon::parse($attendanceType->start_time)->format('g:i A') }} @endif
                        </span>
                    @else
                        <span class="text-[12px] text-[#a09aa4]">Flexible Schedule</span>
                    @endif
                    @if($attendanceType->location)
                        <span class="text-[12px] text-[#a09aa4] flex items-center gap-1">
                            <i class='bx bx-map-pin text-[11px]'></i> {{ $attendanceType->location }}
                        </span>
                    @endif
                </div>
            </div>
            @if($latestSession)
                <div class="text-right">
                    <div class="text-[11px] font-bold uppercase tracking-[0.1em] text-[#a09aa4] mb-1">Latest Session</div>
                    <div class="text-[13px] font-semibold text-[#1c1c1e]">{{ $latestSession->date->format('M d, Y') }}</div>
                    @if($latestSession->service_name)
                        <x-feedback-status.status-indicator variant="red" class="mt-1">
                            {{ $latestSession->service_name }}
                        </x-feedback-status.status-indicator>
                    @endif
                </div>
            @endif
        </div>
        <div class="p-4">
            <div class="flex flex-wrap gap-3">
                <div class="flex-1 min-w-[140px]">
                    <x-form.field label="Date">
                        <x-form.input type="date" wire:model.live="date" max="{{ now()->toDateString() }}" />
                    </x-form.field>
                </div>
                <div class="flex-[2] min-w-[200px]">
                    <x-form.field label="Service Name">
                        @if($currentSession && $currentSession->service_name)
                            <div class="flex gap-2 items-center">
                                <span class="flex-1 px-3 py-2 text-[13px] text-[#6b6570] bg-[#f5f4f6] border border-[#e4e0e2] rounded-lg">
                                    {{ $currentSession->service_name }}
                                </span>
                                <x-button variant="sm-ghost" wire:click="$set('service_name', '')">
                                    <i class='bx bx-edit-alt'></i> Edit
                                </x-button>
                            </div>
                        @else
                            <x-form.input wire:model.live="service_name" placeholder="e.g., Morning Service" />
                        @endif
                    </x-form.field>
                </div>
            </div>
        </div>
    </x-card>

    {{-- Stats --}}
    <div class="grid gap-3" style="grid-template-columns:repeat(auto-fill,minmax(120px,1fr))">
        <x-statistic-card variant="muted" icon="bx-group" title="Total" value="{{ $totalCount }}" />
        <x-statistic-card variant="primary" icon="bx-check-circle" title="Present" value="{{ $presentCount }}" />
        @foreach($categories as $cat)
            <x-statistic-card variant="muted" icon="bx-user" :title="$cat" :value="$categoryCounts[$cat] ?? 0" />
        @endforeach
    </div>

    {{-- Attendee selector --}}
    <x-card :padding="false">
        <div class="px-4 py-3 border-b border-[#e4e0e2] flex flex-wrap justify-between items-center gap-3">
            <div class="flex gap-2">
                @foreach([['flat','bx-list-ul','Flat'],['family','bx-home-heart','Family'],['category','bx-category','Category']] as [$mode,$icon,$label])
                    <x-button wire:click="setViewMode('{{ $mode }}')"
                              variant="{{ $viewMode === $mode ? 'sm-primary' : 'sm-ghost' }}">
                        <i class='bx {{ $icon }}'></i> {{ $label }}
                    </x-button>
                @endforeach
            </div>
            <div class="flex gap-2 flex-wrap">
                <x-form.select wire:model.live="filterFamily" class="w-auto min-w-[130px]">
                    <option value="">All Families</option>
                    @foreach($families as $family)
                        <option value="{{ $family->id }}">{{ $family->family_name }}</option>
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

        <div class="p-4 flex flex-col gap-3">
            <div class="flex items-center justify-between gap-3">
                <x-form.label>Attendees</x-form.label>
                <livewire:person-create />
            </div>

            <x-form.input wire:model.debounce.300ms="search" placeholder="Search name or contact…" />

            <div class="border border-[#e4e0e2] rounded-lg overflow-hidden" style="max-height:420px;overflow-y:auto">
                @if($viewMode === 'flat')
                    @forelse($allPeople as $person)
                        @php $isChecked = isset($checked[$person->id]) && $checked[$person->id]; @endphp
                        <label class="checkin-row {{ $isChecked ? 'is-checked' : '' }}">
                            <input type="checkbox" wire:click="togglePerson({{ $person->id }})" @checked($isChecked)
                                   class="mr-2.5 w-4 h-4 shrink-0 accent-[#ed213a]">
                            <div class="flex-1 min-w-0">
                                <div class="text-[13px] font-semibold text-[#1c1c1e]">{{ $person->full_name }}</div>
                                <div class="text-[11px] text-[#a09aa4]">
                                    {{ $person->effective_category }}
                                    @if($person->age) · {{ $person->age }} yrs @endif
                                    @if($person->family) · {{ $person->family->family_name }} @endif
                                </div>
                            </div>
                            @if($isChecked)
                                <x-feedback-status.status-indicator status="present" class="ml-2">Present</x-feedback-status.status-indicator>
                            @endif
                        </label>
                    @empty
                        <div class="py-8 text-center text-[13px] text-[#a09aa4] italic">No people match the current filters.</div>
                    @endforelse

                @elseif($viewMode === 'family')
                    @php $peopleByFamily = $allPeople->groupBy(fn($p) => $p->family_id ?: 'no-family'); @endphp
                    @foreach($peopleByFamily as $familyId => $people)
                        <div class="bg-[#f5f4f6] px-4 py-2 text-[11px] font-bold uppercase tracking-[0.1em] text-[#6b6570] border-b border-[#e4e0e2]">
                            {{ $familyId !== 'no-family' ? ($people->first()->family?->family_name ?? 'Unknown') : 'No Family' }}
                            ({{ $people->count() }})
                        </div>
                        @foreach($people as $person)
                            @php $isChecked = isset($checked[$person->id]) && $checked[$person->id]; @endphp
                            <label class="checkin-row {{ $isChecked ? 'is-checked' : '' }}">
                                <input type="checkbox" wire:click="togglePerson({{ $person->id }})" @checked($isChecked)
                                       class="mr-2.5 w-4 h-4 shrink-0 accent-[#ed213a]">
                                <div class="flex-1 min-w-0">
                                    <div class="text-[13px] font-semibold text-[#1c1c1e]">{{ $person->full_name }}</div>
                                    <div class="text-[11px] text-[#a09aa4]">{{ $person->effective_category }}</div>
                                </div>
                                @if($isChecked)
                                    <x-feedback-status.status-indicator status="present" class="ml-2">Present</x-feedback-status.status-indicator>
                                @endif
                            </label>
                        @endforeach
                    @endforeach

                @elseif($viewMode === 'category')
                    @php $peopleByCategory = $allPeople->groupBy('effective_category'); @endphp
                    @foreach($peopleByCategory as $category => $people)
                        <div class="bg-[#f5f4f6] px-4 py-2 text-[11px] font-bold uppercase tracking-[0.1em] text-[#6b6570] border-b border-[#e4e0e2]">
                            {{ $category }} ({{ $people->count() }})
                        </div>
                        @foreach($people as $person)
                            @php $isChecked = isset($checked[$person->id]) && $checked[$person->id]; @endphp
                            <label class="checkin-row {{ $isChecked ? 'is-checked' : '' }}">
                                <input type="checkbox" wire:click="togglePerson({{ $person->id }})" @checked($isChecked)
                                       class="mr-2.5 w-4 h-4 shrink-0 accent-[#ed213a]">
                                <div class="flex-1 min-w-0">
                                    <div class="text-[13px] font-semibold text-[#1c1c1e]">{{ $person->full_name }}</div>
                                    @if($person->family)
                                        <div class="text-[11px] text-[#a09aa4]">{{ $person->family->family_name }}</div>
                                    @endif
                                </div>
                                @if($isChecked)
                                    <x-feedback-status.status-indicator status="present" class="ml-2">Present</x-feedback-status.status-indicator>
                                @endif
                            </label>
                        @endforeach
                    @endforeach
                @endif
            </div>
        </div>
    </x-card>

    {{-- Save bar --}}
    <x-card>
        <div class="flex items-center justify-between gap-3">
            <div class="text-[13px] text-[#6b6570]">
                <span class="font-bold text-[#1c1c1e]">{{ $presentCount }}</span>
                {{ Str::plural('person', $presentCount) }} selected
            </div>
            <x-button wire:click="save" variant="primary" loading="Saving…">
                <i class='bx bx-save'></i> Save Attendance
            </x-button>
        </div>
    </x-card>

</div>
