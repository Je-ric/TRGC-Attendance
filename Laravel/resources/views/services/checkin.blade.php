@extends('layouts.app')

@section('content')
<div class="flex flex-col gap-6">
    <x-page-header 
        icon="bx-log-in-circle" 
        title="Check-in: {{ $service->name }}" 
        desc="{{ $service->date->format('M d, Y') }} @if($service->time){{ \Carbon\Carbon::parse($service->time)->format('g:i A') }}@endif">
        <x-button href="{{ route('services.index') }}" variant="back">
            <i class='bx bx-arrow-left'></i> Back to Services
        </x-button>
    </x-page-header>

    @livewire('attendance-checkin', ['service' => $service])
</div>
@endsection