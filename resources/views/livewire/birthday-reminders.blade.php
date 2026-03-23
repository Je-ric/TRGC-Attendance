<div>
    @if($upcomingBirthdays->count() > 0)
        <div style="display:flex;flex-direction:column;gap:8px">
            @foreach($upcomingBirthdays as $person)
                <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;padding:12px 14px;background:var(--surface);border-radius:8px;border:1px solid var(--border-soft)">
                    <div>
                        <div style="font-size:13.5px;font-weight:600;color:var(--ink);display:flex;align-items:center;gap:6px">
                            <i class='bx bx-cake' style="color:var(--red)"></i>
                            {{ $person->full_name }}
                        </div>
                        <div style="font-size:12px;color:var(--ink-faint);margin-top:3px">
                            {{ $person->next_birthday->format('M d, Y') }} · {{ $person->age_on_birthday }} yrs old
                            @if($person->family)
                                · {{ $person->family->family_name }}
                            @endif
                        </div>
                    </div>
                    <div>
                        @if($person->days_until == 0)
                            <span class="badge badge-red" style="background:var(--red);color:#fff;border-color:var(--red-dark)">Today 🎉</span>
                        @elseif($person->days_until == 1)
                            <span class="badge badge-red">Tomorrow</span>
                        @else
                            <span style="font-size:12px;font-weight:600;color:var(--ink-muted)">{{ $person->days_until }}d</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p style="font-size:13px;color:var(--ink-faint);font-style:italic;margin:0">
            No upcoming birthdays in the next {{ $daysAhead }} days.
        </p>
    @endif
</div>
