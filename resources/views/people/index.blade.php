@extends('layouts.app')

@section('content')
<div class="flex flex-col gap-6">

    <x-page-header icon="bx-group" title="People" desc="Manage individuals, family links, and categories.">
        <livewire:person-create />
    </x-page-header>

    {{-- Stats --}}
    <div class="grid gap-3" style="grid-template-columns:repeat(auto-fill,minmax(150px,1fr))">
        <x-statistic-card variant="primary" icon="bx-users" title="Total People" value="{{ $totalPeople }}" />
        @foreach($categories as $cat)
            <x-statistic-card variant="muted" icon="bx-user" :title="$cat" :value="$categoryCounts[$cat] ?? 0" />
        @endforeach
    </div>

    {{-- Family distribution --}}
    @if($familyCounts->count() > 0)
        <x-card title="People Per Family" icon="network-chart" color="red">
            <div class="grid gap-2" style="grid-template-columns:repeat(auto-fill,minmax(160px,1fr))">
                @foreach($familyCounts as $family)
                    <div class="bg-[#f5f4f6] border border-[#e4e0e2] rounded-lg px-3 py-2.5">
                        <div class="text-[13px] font-semibold text-[#1c1c1e]">{{ $family->family_name }}</div>
                        <div class="text-[11px] text-[#a09aa4] mt-0.5">
                            {{ $family->people_count }} {{ Str::plural('person', $family->people_count) }}
                        </div>
                    </div>
                @endforeach
            </div>
        </x-card>
    @endif

    <livewire:people-list />

    {{-- Birthdays --}}
    <x-card title="Upcoming Birthdays" icon="cake" color="red">
        <livewire:birthday-reminders />
    </x-card>

</div>
@endsection
