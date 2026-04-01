<div>
    @if($upcomingBirthdays->count() > 0)
        <div class="flex flex-col gap-2">
            @foreach($upcomingBirthdays as $person)
                <div class="flex items-center justify-between gap-3 px-3 py-2.5 bg-[#f5f4f6] rounded-lg border border-[#ede9eb]">
                    <div>
                        <div class="text-[13px] font-semibold text-[#1c1c1e] flex items-center gap-1.5">
                            <i class='bx bx-cake text-[#ed213a]'></i>
                            {{ $person->full_name }}
                        </div>
                        <div class="text-[11px] text-[#a09aa4] mt-0.5">
                            {{ $person->next_birthday->format('M d, Y') }} · {{ $person->age_on_birthday }} yrs old
                            @if($person->family) · {{ $person->family->family_name }} @endif
                        </div>
                    </div>
                    @if($person->days_until == 0)
                        <x-feedback-status.status-indicator status="today">Today 🎉</x-feedback-status.status-indicator>
                    @elseif($person->days_until == 1)
                        <x-feedback-status.status-indicator status="tomorrow">Tomorrow</x-feedback-status.status-indicator>
                    @else
                        <span class="text-[12px] font-semibold text-[#6b6570]">{{ $person->days_until }}d</span>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <p class="text-[13px] text-[#a09aa4] italic">
            No upcoming birthdays in the next {{ $daysAhead }} days.
        </p>
    @endif
</div>
