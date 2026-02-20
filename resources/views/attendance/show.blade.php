@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div>
            <a href="{{ route('attendance.index') }}" class="text-sm font-medium text-[#6B0F1A] hover:underline">
                &larr; Back to Attendance
            </a>
            <h2 class="brand-font text-3xl text-[#6B0F1A] mt-2">
                {{ isset($type) && is_object($type) ? $type->name : 'Attendance' }}
            </h2>
            @if(isset($type) && is_object($type) && isset($type->day_of_week) && $type->day_of_week)
                <p class="text-slate-600 mt-1">{{ $type->day_of_week }}</p>
            @endif
        </div>

        @if(isset($type) && is_object($type))
            <div class="rounded-2xl border border-[#D4AF37]/30 bg-white p-6 shadow-sm">
                <livewire:attendance-checkin :attendanceType="$type" />
            </div>
        @endif
    </div>
@endsection
