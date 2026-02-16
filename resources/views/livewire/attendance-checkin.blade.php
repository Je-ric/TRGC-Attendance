<div class="space-y-6">
    @if(session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white p-4 rounded shadow">
        <div class="flex justify-between items-start mb-4">
            <div>
                <h3 class="text-xl font-bold">{{ $attendanceType->name }}</h3>
                <p class="text-sm text-gray-500">
                    @if($attendanceType->day_of_week)
                        {{ $attendanceType->day_of_week }}
                    @else
                        Flexible Schedule
                    @endif
                </p>
            </div>
            @if($latestSession)
                <div class="text-right">
                    <div class="text-xs text-gray-500">Latest Session</div>
                    <div class="text-sm font-semibold">{{ $latestSession->date->format('M d, Y') }}</div>
                    @if($latestSession->service_name)
                        <div class="text-xs text-gray-600">{{ $latestSession->service_name }}</div>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex gap-4 items-end">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                <input type="date" wire:model.live="date" max="{{ now()->toDateString() }}" class="border p-2 rounded w-full">
            </div>
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Service Name</label>
                @if($currentSession && $currentSession->service_name)
                    <div class="flex items-center gap-2">
                        <span class="flex-1 border p-2 rounded bg-gray-50">{{ $currentSession->service_name }}</span>
                        <button
                            type="button"
                            wire:click="$set('service_name', '')"
                            class="px-3 py-2 border rounded hover:bg-gray-100 text-sm">
                            Edit
                        </button>
                    </div>
                @else
                    <input
                        type="text"
                        wire:model.live="service_name"
                        placeholder="e.g., Morning Service, Evening Service"
                        class="border p-2 rounded w-full">
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
        <div class="bg-white p-4 rounded shadow">
            <div class="text-sm text-gray-500">Total People</div>
            <div class="text-2xl font-bold">{{ $totalCount }}</div>
        </div>
        <div class="bg-green-50 p-4 rounded shadow border border-green-200">
            <div class="text-sm text-green-700">Present</div>
            <div class="text-2xl font-bold text-green-700">{{ $presentCount }}</div>
        </div>
        @foreach($categories as $cat)
            <div class="bg-white p-4 rounded shadow">
                <div class="text-sm text-gray-500">{{ $cat }}</div>
                <div class="text-2xl font-bold">{{ $categoryCounts[$cat] ?? 0 }}</div>
            </div>
        @endforeach
    </div>

    <div class="bg-white p-4 rounded shadow">
        <div class="flex flex-wrap justify-between items-center gap-4 mb-4">
            <div class="flex gap-2">
                <button wire:click="setViewMode('flat')"
                        class="px-3 py-1 border rounded {{ $viewMode === 'flat' ? 'bg-blue-600 text-white' : 'bg-white' }}">
                    Flat
                </button>
                <button wire:click="setViewMode('family')"
                        class="px-3 py-1 border rounded {{ $viewMode === 'family' ? 'bg-blue-600 text-white' : 'bg-white' }}">
                    By Family
                </button>
                <button wire:click="setViewMode('category')"
                        class="px-3 py-1 border rounded {{ $viewMode === 'category' ? 'bg-blue-600 text-white' : 'bg-white' }}">
                    By Category
                </button>
            </div>

            <div class="flex gap-2">
                <select wire:model.live="filterFamily" class="border p-2 rounded">
                    <option value="">All Families</option>
                    @foreach($families as $family)
                        <option value="{{ $family->id }}">{{ $family->family_name }}</option>
                    @endforeach
                </select>

                <select wire:model.live="filterCategory" class="border p-2 rounded">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}">{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="flex justify-between items-center mb-4">
            <strong>Select Attendees</strong>
            <livewire:person-create />
        </div>

        <input
            type="text"
            wire:model.debounce.300ms="search"
            placeholder="Search by name or contact number..."
            class="border p-2 rounded w-full mb-4 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">

        <div class="border rounded bg-white max-h-96 overflow-y-auto">
            @if($viewMode === 'flat')
                @forelse($allPeople as $person)
                    <label class="flex items-center p-3 border-b cursor-pointer hover:bg-blue-50 transition-colors">
                        <input
                            type="checkbox"
                            wire:click="togglePerson({{ $person->id }})"
                            @checked(isset($checked[$person->id]) && $checked[$person->id])
                            class="mr-3 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <div class="flex-1">
                            <div class="font-semibold text-gray-900">{{ $person->full_name }}</div>
                            <div class="text-xs text-gray-500">
                                {{ $person->effective_category }} | {{ $person->age ?? 'N/A' }} yrs
                                @if($person->family)
                                    | {{ $person->family->family_name }}
                                @endif
                            </div>
                        </div>
                    </label>
                @empty
                    <div class="p-4 text-sm text-gray-500">No people match the current filters.</div>
                @endforelse
            @elseif($viewMode === 'family')
                @php
                    $peopleByFamily = $allPeople->groupBy(function ($person) {
                        return $person->family_id ?: 'no-family';
                    });
                @endphp
                @foreach($peopleByFamily as $familyId => $people)
                    <div class="border-b border-gray-200">
                        <div class="bg-gray-50 px-3 py-2 font-semibold text-sm">
                            @if($familyId !== 'no-family')
                                @php $family = $people->first()->family; @endphp
                                {{ $family?->family_name ?? 'Unknown Family' }} ({{ $people->count() }})
                            @else
                                No Family ({{ $people->count() }})
                            @endif
                        </div>
                        @foreach($people as $person)
                            <label class="flex items-center p-3 border-b cursor-pointer hover:bg-blue-50 transition-colors">
                                <input
                                    type="checkbox"
                                    wire:click="togglePerson({{ $person->id }})"
                                    @checked(isset($checked[$person->id]) && $checked[$person->id])
                                    class="mr-3 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <div class="flex-1">
                                    <div class="font-semibold text-gray-900">{{ $person->full_name }}</div>
                                    <div class="text-xs text-gray-500">{{ $person->effective_category }}</div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                @endforeach
            @elseif($viewMode === 'category')
                @php
                    $peopleByCategory = $allPeople->groupBy('effective_category');
                @endphp
                @foreach($peopleByCategory as $category => $people)
                    <div class="border-b border-gray-200">
                        <div class="bg-gray-50 px-3 py-2 font-semibold text-sm">
                            {{ $category }} ({{ $people->count() }})
                        </div>
                        @foreach($people as $person)
                            <label class="flex items-center p-3 border-b cursor-pointer hover:bg-blue-50 transition-colors">
                                <input
                                    type="checkbox"
                                    wire:click="togglePerson({{ $person->id }})"
                                    @checked(isset($checked[$person->id]) && $checked[$person->id])
                                    class="mr-3 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <div class="flex-1">
                                    <div class="font-semibold text-gray-900">{{ $person->full_name }}</div>
                                    @if($person->family)
                                        <div class="text-xs text-gray-500">{{ $person->family->family_name }}</div>
                                    @endif
                                </div>
                            </label>
                        @endforeach
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    <div class="flex justify-between items-center bg-white p-4 rounded shadow">
        <span class="text-sm text-gray-600">
            {{ $presentCount }} {{ Str::plural('person', $presentCount) }} selected
        </span>
        <button wire:click="save" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 font-semibold">
            Save Attendance
        </button>
    </div>
</div>
