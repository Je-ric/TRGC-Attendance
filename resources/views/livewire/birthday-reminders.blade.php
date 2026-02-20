<div>
    @if($upcomingBirthdays->count() > 0)
        <div class="space-y-3">
            @foreach($upcomingBirthdays as $person)
                <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-[#D4AF37]/25 bg-white p-4">
                    <div>
                        <div class="font-semibold text-[#111111]">{{ $person->full_name }}</div>
                        <div class="text-sm text-slate-600 mt-1">
                            {{ $person->next_birthday->format('M d, Y') }} ({{ $person->age_on_birthday }} years old)
                            @if($person->family)
                                <span class="ml-1 text-[#6B0F1A]">| Family: {{ $person->family->family_name }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="text-right">
                        @if($person->days_until == 0)
                            <span class="inline-block rounded-full bg-[#6B0F1A] px-3 py-1 text-xs font-semibold uppercase tracking-wide text-[#F5D76E]">
                                Today
                            </span>
                        @elseif($person->days_until == 1)
                            <span class="inline-block rounded-full bg-[#D4AF37]/25 px-3 py-1 text-xs font-semibold text-[#6B0F1A]">
                                Tomorrow
                            </span>
                        @else
                            <span class="text-sm font-medium text-slate-600">{{ $person->days_until }} days</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-sm italic text-slate-500">
            No upcoming birthdays in the next {{ $daysAhead }} days.
        </p>
    @endif
</div>
