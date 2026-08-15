@extends('layouts.app')

@section('content')
<div class="flex flex-col gap-6">
    <x-page-header icon="bx-check-shield" title="Services & Events" desc="Manage services and track attendance.">
        <x-button href="{{ route('services.weekly') }}" variant="ghost">
            <i class='bx bx-calendar-week'></i> Weekly View
        </x-button>
        <x-button href="{{ route('export.date-range') }}?start_date={{ \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d') }}&end_date={{ \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d') }}" variant="ghost">
            <i class='bx bx-download'></i> Export This Month
        </x-button>
        <x-button href="{{ route('people.index') }}" variant="ghost">
            <i class='bx bx-group'></i> People
        </x-button>
    </x-page-header>

    @livewire('service-management')
</div>
@endsection