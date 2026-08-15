@props(['person', 'showFamily' => true, 'showActions' => true, 'compact' => false])

@php
    $msColors = [
        'Member'           => 'red',
        'Regular Attendee' => 'blue',
        'Visitor'          => 'amber',
        'Inactive'         => 'slate',
    ];
    $msColor = $msColors[$person->membership_status ?? ''] ?? 'slate';
@endphp

<div class="flex items-center justify-between gap-3 py-2.5 {{ $compact ? '' : 'px-1' }}">
    <div class="flex items-center gap-3 min-w-0">
        <div class="bg-brand-gradient w-9 h-9 rounded-full shrink-0 flex items-center justify-center
                    text-white text-[12px] font-bold">
            {{ strtoupper(substr($person->first_name, 0, 1)) }}{{ strtoupper(substr($person->last_name, 0, 1)) }}
        </div>
        <div class="min-w-0">
            <div class="text-[13px] font-semibold text-[#1c1c1e] truncate">{{ $person->full_name }}</div>
            <div class="flex items-center gap-1.5 flex-wrap mt-0.5">
                <x-feedback-status.status-indicator variant="red">{{ $person->effective_category }}</x-feedback-status.status-indicator>
                @if($person->membership_status && $person->membership_status !== 'Regular Attendee')
                    <x-feedback-status.status-indicator :variant="$msColor">{{ $person->membership_status }}</x-feedback-status.status-indicator>
                @endif
                @if($person->age)
                    <span class="text-[11px] text-[#a09aa4]">{{ $person->age }} yrs</span>
                @endif
                @if($showFamily && $person->family)
                    <span class="text-[11px] text-[#a09aa4]">· {{ $person->family->family_name }}</span>
                @endif
            </div>
        </div>
    </div>

    @if($showActions)
        <div class="flex gap-1.5 shrink-0">{{ $slot }}</div>
    @endif
</div>
