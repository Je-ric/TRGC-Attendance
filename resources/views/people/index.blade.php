@extends('layouts.app')

@section('content')
    <div class="space-y-6">

        <x-card-with-header title="Management" description="Manage individuals, family links, and category distribution.">
            <livewire:person-create />
        </x-card-with-header>

        <hr class="ui-divider">

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
            <div class="ui-card-soft p-4">
                <div class="stat-label">Total People</div>
                <div class="stat-value">{{ $totalPeople }}</div>
            </div>
            @foreach($categories as $cat)
                <div class="ui-card p-4">
                    <div class="stat-label">{{ $cat }}</div>
                    <div class="stat-value" style="color:var(--maroon)">{{ $categoryCounts[$cat] ?? 0 }}</div>
                </div>
            @endforeach
        </div>

        @if($familyCounts->count() > 0)
            <section class="ui-card p-4">
                <h3 class="page-title text-xl text-[#111111] mb-3 flex items-center gap-2">
                    <i class='bx bx-network-chart text-[#6B0F1A]'></i>
                    People Per Family
                </h3>
                <hr class="ui-divider mb-3">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-3">
                    @foreach($familyCounts as $family)
                        <div class="rounded-[10px] p-3" style="border:1px solid var(--border);background:var(--surface-card)">
                            <div class="font-semibold text-sm" style="color:var(--ink)">{{ $family->family_name }}</div>
                            <div class="text-xs dm-sans" style="color:var(--ink-faint)">
                                {{ $family->people_count }} {{ Str::plural('person', $family->people_count) }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        <livewire:people-list />

        <section class="ui-card p-4">
            <h3 class="page-title text-xl text-[#111111] mb-4 flex items-center gap-2">
                <i class='bx bx-cake text-[#6B0F1A]'></i>
                Upcoming Birthdays
            </h3>
            <hr class="ui-divider mb-4">
            <livewire:birthday-reminders />
        </section>
    </div>
@endsection
