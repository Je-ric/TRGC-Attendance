@extends('layouts.app')

@section('content')
    <div class="min-h-screen from-slate-50 to-blue-50 py-8 px-4">
        <div class="mx-auto">
            <div class="mb-6">
                <a href="{{ route('attendance.index') }}" class="text-indigo-600 hover:text-indigo-800 font-medium mb-3 inline-block transition-colors duration-200">
                    &larr; Back to Attendance
                </a>
                <h2 class="text-3xl font-serif font-bold text-slate-800">
                    {{ isset($type) && is_object($type) ? $type->name : 'Attendance' }}
                </h2>
                @if(isset($type) && is_object($type) && isset($type->day_of_week) && $type->day_of_week)
                    <p class="text-slate-600 mt-1">{{ $type->day_of_week }}</p>
                @endif
            </div>

            @if(isset($type) && is_object($type))
                <div class="bg-white rounded-xl shadow-lg p-6 border border-slate-200">
                    <livewire:attendance-checkin :attendanceType="$type" />
                </div>
            @endif
        </div>
    </div>
@endsection
