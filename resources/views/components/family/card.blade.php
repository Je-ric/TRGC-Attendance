@props(['family', 'showMembers' => true, 'showActions' => true])

<div class="rounded-xl border border-[#e4e0e2] bg-white overflow-hidden flex flex-col
            shadow-[0_2px_16px_rgba(0,0,0,0.07)]">

    {{-- Header --}}
    <div class="px-4 pt-4 pb-3">
        <div class="flex items-start gap-2.5">
            <div class="bg-brand-gradient w-9 h-9 rounded-xl shrink-0 flex items-center justify-center">
                <i class='bx bx-buildings text-white text-base leading-none'></i>
            </div>
            <div class="min-w-0 flex-1">
                <h4 class="font-['Oswald'] text-[15px] font-bold text-[#1c1c1e] leading-tight truncate">
                    {{ $family->family_name }}
                </h4>
                <div class="mt-1 space-y-0.5">
                    @if($family->address || $family->barangay)
                        <p class="text-[12px] text-[#a09aa4] flex items-center gap-1">
                            <i class='bx bx-map-pin text-[11px] shrink-0'></i>
                            {{ collect([$family->address, $family->barangay])->filter()->implode(', ') }}
                        </p>
                    @endif
                    @if($family->contact_person || $family->contact_number)
                        <p class="text-[12px] text-[#a09aa4] flex items-center gap-1">
                            <i class='bx {{ $family->contact_person ? "bx-user" : "bx-phone" }} text-[11px] shrink-0'></i>
                            {{ $family->contact_person }}
                            @if($family->contact_person && $family->contact_number) · @endif
                            {{ $family->contact_number }}
                        </p>
                    @endif
                </div>
            </div>
            <x-feedback-status.status-indicator variant="slate" class="shrink-0 mt-0.5">
                {{ $family->people_count ?? $family->people->count() }}
                {{ Str::plural('member', $family->people_count ?? $family->people->count()) }}
            </x-feedback-status.status-indicator>
        </div>
    </div>

    {{-- Members list --}}
    @if($showMembers && $family->people->count() > 0)
        <div class="border-t border-[#ede9eb] divide-y divide-[#ede9eb]">
            @foreach($family->people as $person)
                <div class="flex items-center justify-between gap-2 px-4 py-2">
                    <div class="flex items-center gap-2 min-w-0">
                        <div class="bg-brand-gradient w-6 h-6 rounded-full shrink-0 flex items-center justify-center
                                    text-white text-[10px] font-bold">
                            {{ strtoupper(substr($person->first_name, 0, 1)) }}
                        </div>
                        <span class="text-[12px] font-medium text-[#1c1c1e] truncate">{{ $person->full_name }}</span>
                        @if($person->age)
                            <span class="text-[11px] text-[#a09aa4] shrink-0">{{ $person->age }} yrs</span>
                        @endif
                    </div>
                    <x-feedback-status.status-indicator variant="slate" class="shrink-0">
                        {{ $person->effective_category }}
                    </x-feedback-status.status-indicator>
                </div>
            @endforeach
        </div>
    @elseif($showMembers)
        <div class="border-t border-[#ede9eb] px-4 py-3 text-[12px] text-[#a09aa4] italic">
            No members yet.
        </div>
    @endif

    @if($family->notes)
        <div class="border-t border-[#ede9eb] px-4 py-2">
            <p class="text-[12px] text-[#a09aa4] italic leading-relaxed">{{ $family->notes }}</p>
        </div>
    @endif

    {{-- Actions always at bottom --}}
    @if($showActions)
        <div class="mt-auto border-t border-[#ede9eb] px-4 py-2.5 flex items-center justify-end gap-1.5 bg-[#f5f4f6]">
            {{ $slot }}
        </div>
    @endif

</div>
