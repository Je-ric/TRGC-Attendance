<div class="space-y-2">
    <input type="text" wire:model.debounce.300ms="search" placeholder="Search by name or contact number..." class="ui-input">

    <div class="border border-[#D4AF37]/25 rounded bg-white max-h-64 overflow-y-auto">
        @forelse($results as $person)
            <label class="flex items-center p-2 border-b border-slate-200 cursor-pointer hover:bg-[#D4AF37]/10 transition-colors">
                <input type="checkbox"
                       wire:click="toggle({{ $person->id }})"
                       class="mr-3 w-4 h-4 text-[#6B0F1A] border-gray-300 rounded focus:ring-[#D4AF37]">
                <div class="flex-1">
                    <div class="font-semibold text-gray-900">{{ $person->full_name }}</div>
                    <div class="text-xs text-gray-500">
                        {{ $person->category ?? 'N/A' }} | {{ $person->age ?? 'N/A' }} yrs
                        @if($person->family)
                            | Family: {{ $person->family->family_name }}
                        @endif
                    </div>
                </div>
            </label>
        @empty
            <p class="p-4 text-gray-500 text-sm text-center">No results found. Try a different search term.</p>
        @endforelse
    </div>

    @if($search && $results->count() === 0)
        <p class="text-sm text-gray-500 mt-2">No people match your search.</p>
    @endif
</div>
