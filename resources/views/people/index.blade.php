@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h2 class="brand-font text-3xl text-[#6B0F1A]">People Management</h2>
                <p class="text-sm text-slate-600 mt-1">Manage individuals, family links, and category distribution.</p>
            </div>
            <livewire:person-create />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
            <div class="bg-white border border-[#D4AF37]/30 p-4 rounded-xl shadow-sm">
                <div class="text-sm text-slate-500">Total People</div>
                <div class="text-2xl font-bold text-[#111111]">{{ $totalPeople }}</div>
            </div>
            @foreach($categories as $cat)
                <div class="bg-white border border-[#D4AF37]/20 p-4 rounded-xl shadow-sm">
                    <div class="text-sm text-slate-500">{{ $cat }}</div>
                    <div class="text-2xl font-bold text-[#6B0F1A]">{{ $categoryCounts[$cat] ?? 0 }}</div>
                </div>
            @endforeach
        </div>

        @if($familyCounts->count() > 0)
            <section class="bg-white border border-[#D4AF37]/25 rounded-xl p-4 shadow-sm">
                <h3 class="brand-font text-xl text-[#111111] mb-3">People Per Family</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-3">
                    @foreach($familyCounts as $family)
                        <div class="rounded-lg border border-slate-200 p-3">
                            <div class="font-semibold text-sm text-[#111111]">{{ $family->family_name }}</div>
                            <div class="text-xs text-slate-500">
                                {{ $family->people_count }} {{ Str::plural('person', $family->people_count) }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        <livewire:people-list />

        <section class="bg-white border border-[#D4AF37]/25 rounded-xl p-4 shadow-sm">
            <h3 class="brand-font text-xl text-[#111111] mb-4">Upcoming Birthdays</h3>
            <livewire:birthday-reminders />
        </section>
    </div>
@endsection
