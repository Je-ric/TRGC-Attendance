@extends('layouts.app')

@section('content')
<div class="flex flex-col gap-5">
    <div class="flex items-center gap-3 flex-wrap">
        <x-button href="{{ route('attendance.index') }}" variant="back">
            <i class='bx bx-arrow-left'></i> Back
        </x-button>
        <div>
            <div class="page-eyebrow">Check-in</div>
            <h1 class="page-title text-[22px]">
                {{ isset($type) && is_object($type) ? $type->name : 'Attendance' }}
            </h1>
        </div>
    </div>

    @if(isset($type) && is_object($type))
        <livewire:attendance-checkin :attendanceType="$type" />
    @endif
</div>
@endsection
