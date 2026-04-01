@extends('layouts.app')

@section('content')
<div class="flex flex-col gap-6">

    <x-page-header icon="bx-group" title="People" desc="Manage congregation members, families, and categories.">
        <livewire:person-create />
    </x-page-header>

    {{-- Stats row --}}
    <div class="grid gap-3" style="grid-template-columns:repeat(auto-fill,minmax(140px,1fr))">
        <x-statistic-card variant="primary" icon="bx-users" title="Total People" value="{{ $totalPeople }}" />
        <x-statistic-card variant="dark" icon="bx-id-card" title="Members" value="{{ $totalMembers }}" />
        @foreach($categories as $cat)
            <x-statistic-card variant="muted" icon="bx-user" :title="$cat" :value="$categoryCounts[$cat] ?? 0" />
        @endforeach
    </div>

    {{-- Membership breakdown --}}
    @if($totalPeople > 0)
        <x-card title="Membership Breakdown" icon="id-card" color="red">
            <div class="grid gap-2" style="grid-template-columns:repeat(auto-fill,minmax(160px,1fr))">
                @php
                    $msColors = ['Member'=>'red','Regular Attendee'=>'blue','Visitor'=>'amber','Inactive'=>'slate'];
                @endphp
                @foreach($membershipCounts as $status => $count)
                    <div class="flex items-center justify-between bg-[#f5f4f6] rounded-lg px-3 py-2.5 border border-[#e4e0e2]">
                        <x-feedback-status.status-indicator :variant="$msColors[$status] ?? 'slate'">
                            {{ $status }}
                        </x-feedback-status.status-indicator>
                        <span class="text-[15px] font-bold text-[#1c1c1e]" style="font-family:'Oswald',sans-serif">{{ $count }}</span>
                    </div>
                @endforeach
            </div>
        </x-card>
    @endif

    {{-- People list --}}
    <livewire:people-list />

    {{-- Birthdays --}}
    <x-card title="Upcoming Birthdays" icon="cake" color="red">
        <livewire:birthday-reminders />
    </x-card>

</div>
@endsection
