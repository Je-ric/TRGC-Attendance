@extends('layouts.app')

@section('content')
<h2 class="text-2xl font-bold mb-4">People Management</h2>

<div class="mb-4 flex justify-end">
    <livewire:person-create />
</div>

<!-- Statistics -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4 mb-6">
    <div class="bg-white p-4 rounded shadow">
        <div class="text-sm text-gray-500">Total People</div>
        <div class="text-2xl font-bold">{{ $totalPeople }}</div>
    </div>
    @foreach($categories as $cat)
        <div class="bg-white p-4 rounded shadow">
            <div class="text-sm text-gray-500">{{ $cat }}</div>
            <div class="text-2xl font-bold">{{ $categoryCounts[$cat] ?? 0 }}</div>
        </div>
    @endforeach
</div>

<!-- Family Summary -->
@if($familyCounts->count() > 0)
<div class="bg-white border rounded p-4 mb-6 shadow">
    <h3 class="font-bold mb-3">People per Family</h3>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
        @foreach($familyCounts as $family)
            <div class="border rounded p-2">
                <div class="font-semibold text-sm">{{ $family->family_name }}</div>
                <div class="text-xs text-gray-500">{{ $family->people_count }} {{ Str::plural('person', $family->people_count) }}</div>
            </div>
        @endforeach
    </div>
</div>
@endif

<livewire:people-list />

<!-- Birthday Reminders -->
<div class="mt-6">
    <livewire:birthday-reminders />
</div>
@endsection
