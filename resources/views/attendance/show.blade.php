@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div>
            <a href="{{ route('attendance.index') }}" class="ui-btn ui-btn-ghost text-sm py-2 px-3">
                <i class='bx bx-left-arrow-alt'></i>
                Back to Attendance
            </a>
            <h2 class="page-title text-3xl text-[#6B0F1A] mt-3 flex items-center gap-2">
                <i class='bx bx-check-shield text-[#D4AF37]'></i>
                {{ isset($type) && is_object($type) ? $type->name : 'Attendance' }}
            </h2>
            @if(isset($type) && is_object($type) && isset($type->day_of_week) && $type->day_of_week)
                <p class="text-slate-600 mt-1">{{ $type->day_of_week }}</p>
            @endif
        </div>

        <hr class="ui-divider">

        @if(isset($type) && is_object($type))
            <div class="ui-card p-6">
                <livewire:attendance-checkin :attendanceType="$type" />
            </div>
        @endif
    </div>
@endsection
