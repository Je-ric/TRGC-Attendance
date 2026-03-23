@extends('layouts.app')

@section('content')
<div style="display:flex;flex-direction:column;gap:24px">

    <div style="display:flex;flex-wrap:wrap;align-items:flex-start;justify-content:space-between;gap:16px">
        <div>
            <div class="page-eyebrow">Directory</div>
            <h1 class="page-title" style="font-size:26px;margin:4px 0 6px">People</h1>
            <p style="font-size:13px;color:var(--ink-faint);margin:0">Manage individuals, family links, and categories.</p>
        </div>
        <livewire:person-create />
    </div>

    {{-- Stats --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:12px">
        <x-statistic-card variant="primary" icon="bx-users" title="Total People" value="{{ $totalPeople }}" />
        @foreach($categories as $cat)
            <x-statistic-card variant="muted" icon="bx-user" :title="$cat" :value="$categoryCounts[$cat] ?? 0" />
        @endforeach
    </div>

    {{-- Family distribution --}}
    @if($familyCounts->count() > 0)
        <div class="card" style="padding:20px">
            <h3 class="page-title" style="font-size:15px;margin:0 0 14px;display:flex;align-items:center;gap:6px">
                <i class='bx bx-network-chart' style="color:var(--red)"></i> People Per Family
            </h3>
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:8px">
                @foreach($familyCounts as $family)
                    <div style="background:var(--surface);border:1px solid var(--border);border-radius:8px;padding:10px 12px">
                        <div style="font-size:13px;font-weight:600;color:var(--ink)">{{ $family->family_name }}</div>
                        <div style="font-size:11.5px;color:var(--ink-faint);margin-top:2px">
                            {{ $family->people_count }} {{ Str::plural('person', $family->people_count) }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <livewire:people-list />

    {{-- Birthdays --}}
    <div class="card" style="padding:20px">
        <h3 class="page-title" style="font-size:15px;margin:0 0 14px;display:flex;align-items:center;gap:6px">
            <i class='bx bx-cake' style="color:var(--red)"></i> Upcoming Birthdays
        </h3>
        <hr class="ui-divider" style="margin-bottom:14px">
        <livewire:birthday-reminders />
    </div>
</div>
@endsection
