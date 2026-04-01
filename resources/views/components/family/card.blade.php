@props(['family', 'showMembers' => true, 'showActions' => true])

<x-card :padding="false">
    <div class="px-4 py-3 border-b border-[#e4e0e2] flex justify-between items-start gap-3">
        <div class="min-w-0 flex-1">
            <h4 class="page-title text-[15px] flex items-center gap-2">
                <span class="flex items-center justify-center w-7 h-7 rounded-lg shrink-0"
                      style="background:linear-gradient(135deg,#93291e,#ed213a)">
                    <i class='bx bx-buildings text-white text-sm leading-none'></i>
                </span>
                {{ $family->family_name }}
            </h4>
            <div class="mt-1 space-y-0.5">
                @if($family->address || $family->barangay)
                    <p class="text-[12px] text-[#a09aa4] flex items-center gap-1">
                        <i class='bx bx-map-pin text-[11px] shrink-0'></i>
                        {{ collect([$family->address, $family->barangay])->filter()->implode(', ') }}
                    </p>
                @endif
                @if($family->contact_person)
                    <p class="text-[12px] text-[#a09aa4] flex items-center gap-1">
                        <i class='bx bx-user text-[11px] shrink-0'></i>
                        {{ $family->contact_person }}
                        @if($family->contact_number)
                            · {{ $family->contact_number }}
                        @endif
                    </p>
                @elseif($family->contact_number)
                    <p class="text-[12px] text-[#a09aa4] flex items-center gap-1">
                        <i class='bx bx-phone text-[11px] shrink-0'></i>
                        {{ $family->contact_number }}
                    </p>
                @endif
            </div>
        </div>
        <div class="flex items-start gap-2 shrink-0 flex-col items-end">
            <x-feedback-status.status-indicator variant="slate">
                {{ $family->people_count ?? $family->people->count() }}
                {{ Str::plural('member', $family->people_count ?? $family->people->count()) }}
            </x-feedback-status.status-indicator>
            @if($showActions)
                <div class="flex gap-1.5">{{ $slot }}</div>
            @endif
        </div>
    </div>

    @if($showMembers && $family->people->count() > 0)
        <div class="px-4 py-2 divide-y divide-[#ede9eb]">
            @foreach($family->people as $person)
                <div class="flex justify-between items-center py-2">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-full flex items-center justify-center text-white text-[10px] font-bold shrink-0"
                             style="background:linear-gradient(135deg,#93291e,#ed213a)">
                            {{ strtoupper(substr($person->first_name, 0, 1)) }}
                        </div>
                        <div>
                            <span class="text-[12px] font-medium text-[#1c1c1e]">{{ $person->full_name }}</span>
                            @if($person->age)
                                <span class="text-[11px] text-[#a09aa4]"> · {{ $person->age }} yrs</span>
                            @endif
                        </div>
                    </div>
                    <x-feedback-status.status-indicator variant="slate">
                        {{ $person->effective_category }}
                    </x-feedback-status.status-indicator>
                </div>
            @endforeach
        </div>
    @elseif($showMembers)
        <div class="px-4 py-3 text-[12px] text-[#a09aa4] italic">No members yet.</div>
    @endif

    @if($family->notes)
        <div class="px-4 py-2 border-t border-[#ede9eb]">
            <p class="text-[12px] text-[#a09aa4] italic">{{ $family->notes }}</p>
        </div>
    @endif
</x-card>
