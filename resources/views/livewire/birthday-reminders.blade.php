<div>
    @if($upcomingBirthdays->count() > 0)
        <div class="space-y-4">
            @foreach($upcomingBirthdays as $person)
                <div class="flex justify-between items-center p-4 border border-slate-200 rounded-lg hover:bg-indigo-50 transition-colors duration-200">
                    <div>
                        <div class="font-semibold text-slate-800">{{ $person->full_name }}</div>
                        <div class="text-sm text-slate-600 mt-1">
                            {{ $person->next_birthday->format('M d, Y') }}
                            ({{ $person->age_on_birthday }} years old)
                            @if($person->family)
                                - Family: {{ $person->family->family_name }}
                            @endif
                        </div>
                    </div>
                    <div class="text-right">
                        @if($person->days_until == 0)
                            <span class="bg-emerald-100 text-emerald-800 px-3 py-1 rounded-full text-sm font-semibold uppercase tracking-wide">Today!</span>
                        @elseif($person->days_until == 1)
                            <span class="bg-amber-100 text-amber-800 px-3 py-1 rounded-full text-sm font-medium">Tomorrow</span>
                        @else
                            <span class="text-slate-600 text-sm font-medium">{{ $person->days_until }} days</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-slate-500 text-sm italic">
            No upcoming birthdays in the next {{ $daysAhead }} days.
        </p>
    @endif
</div>
