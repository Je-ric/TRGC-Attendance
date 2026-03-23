@extends('layouts.app')

@section('content')
<div style="display:flex;flex-direction:column;gap:20px">
    <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap">
        <a href="{{ route('attendance.index') }}" class="btn btn-ghost" style="font-size:12px;padding:6px 10px">
            <i class='bx bx-left-arrow-alt'></i> Back
        </a>
        <div>
            <div class="page-eyebrow">Check-in</div>
            <h1 class="page-title" style="font-size:22px;margin:2px 0 0">
                {{ isset($type) && is_object($type) ? $type->name : 'Attendance' }}
            </h1>
        </div>
    </div>

    @if(isset($type) && is_object($type))
        <livewire:attendance-checkin :attendanceType="$type" />
    @endif
</div>
@endsection
