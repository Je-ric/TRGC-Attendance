<div> <!-- Root wrapper starts -->

    <!-- Filter and View Mode -->
    <div class="flex flex-wrap justify-between items-center mb-4 gap-4">
        <div class="flex gap-2">
            <button wire:click="setViewMode('flat')"
                    class="px-3 py-1 border rounded {{ $viewMode === 'flat' ? 'bg-[#6B0F1A] text-[#F5D76E]' : 'bg-white' }}">
                Flat
            </button>
            <button wire:click="setViewMode('family')"
                    class="px-3 py-1 border rounded {{ $viewMode === 'family' ? 'bg-[#6B0F1A] text-[#F5D76E]' : 'bg-white' }}">
                By Family
            </button>
            <button wire:click="setViewMode('category')"
                    class="px-3 py-1 border rounded {{ $viewMode === 'category' ? 'bg-[#6B0F1A] text-[#F5D76E]' : 'bg-white' }}">
                By Category
            </button>
        </div>

        <div class="flex gap-2">
            <select wire:model.live="filterFamily" class="border p-2 rounded">
                <option value="">All Families</option>
                @foreach($families as $family)
                    <option value="{{ $family->id }}">
                        {{ $family->family_name }} ({{ $family->people_count }})
                    </option>
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

    <!-- People List -->
    @if($viewMode === 'flat')
        @forelse($people as $person)
            <div class="border border-[#D4AF37]/25 rounded-xl p-4 bg-white shadow-sm flex justify-between items-center mb-2">
                <div>
                    <strong>{{ $person->full_name }}</strong>
                    <span class="text-gray-500 text-sm">
                        {{ $person->effective_category }} |
                        Family: {{ $person->family->family_name ?? 'No family' }}
                    </span>
                </div>
                <div class="flex gap-2">
                    <button wire:click="$dispatch('editPerson', { id: {{ $person->id }} })" class="text-[#6B0F1A] hover:underline">Edit</button>
                    <button wire:click="deletePerson({{ $person->id }})" class="text-red-600 hover:underline">Delete</button>
                </div>
            </div>
        @empty
            <div class="border border-[#D4AF37]/25 rounded-xl p-4 bg-white text-gray-500">No people match the current filters.</div>
        @endforelse

    @elseif($viewMode === 'family')
        @php
            $peopleByFamily = $people->groupBy(function ($person) {
                return $person->family_id ?: 'no-family';
            });
        @endphp
        @forelse($peopleByFamily as $familyKey => $familyPeople)
            <div class="border border-[#D4AF37]/25 rounded-xl p-4 bg-white shadow-sm mb-4">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        @if($familyKey === 'no-family')
                            <h3 class="font-bold text-lg">No Family</h3>
                        @else
                            @php $family = $familyPeople->first()->family; @endphp
                            <h3 class="font-bold text-lg">{{ $family?->family_name ?? 'Unknown Family' }}</h3>
                            @if($family?->address)
                                <p class="text-sm text-gray-500">{{ $family->address }}</p>
                            @endif
                        @endif
                    </div>
                    <div class="text-right">
                        <span class="text-sm text-gray-600">
                            <strong>{{ $familyPeople->count() }}</strong>
                            {{ Str::plural('person', $familyPeople->count()) }}
                        </span>
                    </div>
                </div>
                <ul class="mt-2 space-y-1">
                    @foreach($familyPeople as $person)
                        <li class="flex justify-between items-center border-b p-2 hover:bg-gray-50">
                            <div>
                                <strong>{{ $person->full_name }}</strong>
                                <span class="text-gray-500 text-xs">({{ $person->effective_category }})</span>
                            </div>
                            <div class="flex gap-2">
                                <button wire:click="$dispatch('editPerson', { id: {{ $person->id }} })" class="text-[#6B0F1A] hover:underline text-sm">Edit</button>
                                <button wire:click="deletePerson({{ $person->id }})" class="text-red-600 hover:underline text-sm">Delete</button>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        @empty
            <div class="border border-[#D4AF37]/25 rounded-xl p-4 bg-white text-gray-500">No family groups match the current filters.</div>
        @endforelse

    @elseif($viewMode === 'category')
        @foreach(array_merge($categories, ['Unknown']) as $cat)
            @php
                $catPeople = $people->filter(fn ($person) => $person->effective_category === $cat);
            @endphp
            @if($catPeople->count() > 0)
                <div class="mb-4">
                    <h3 class="font-bold text-lg mb-2">
                        {{ $cat }}
                        <span class="text-gray-500 text-sm font-normal">({{ $catPeople->count() }} {{ Str::plural('person', $catPeople->count()) }})</span>
                    </h3>
                    @foreach($catPeople as $person)
                        <div class="border border-[#D4AF37]/25 rounded-xl p-4 bg-white shadow-sm flex justify-between items-center mb-2">
                            <div>
                                <strong>{{ $person->full_name }}</strong>
                                <span class="text-gray-500 text-xs">Family: {{ $person->family->family_name ?? 'No family' }}</span>
                            </div>
                            <div class="flex gap-2">
                                <button wire:click="$dispatch('editPerson', { id: {{ $person->id }} })" class="text-[#6B0F1A] hover:underline text-sm">Edit</button>
                                <button wire:click="deletePerson({{ $person->id }})" class="text-red-600 hover:underline text-sm">Delete</button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        @endforeach
    @endif

</div> <!-- Root wrapper ends -->
