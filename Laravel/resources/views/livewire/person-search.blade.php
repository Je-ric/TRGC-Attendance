<div class="flex flex-col gap-2">
    <x-form.input wire:model.debounce.300ms="search" placeholder="Search by name or contact…" />

    <div class="border border-[#e4e0e2] rounded-lg bg-white overflow-hidden" style="max-height:260px;overflow-y:auto">
        @forelse($results as $person)
            <label class="checkin-row">
                <input type="checkbox" wire:click="toggle({{ $person->id }})"
                       class="mr-2.5 w-4 h-4 shrink-0 accent-[#ed213a]">
                <div>
                    <div class="text-[13px] font-semibold text-[#1c1c1e]">{{ $person->full_name }}</div>
                    <div class="text-[11px] text-[#a09aa4]">
                        {{ $person->category ?? 'N/A' }} · {{ $person->age ?? 'N/A' }} yrs
                        @if($person->family) · {{ $person->family->family_name }} @endif
                    </div>
                </div>
            </label>
        @empty
            <p class="py-4 text-[13px] text-[#a09aa4] text-center italic">No results found.</p>
        @endforelse
    </div>
</div>
