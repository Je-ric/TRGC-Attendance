@extends('layouts.app')

@section('content')
    <div class="space-y-8">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h2 class="page-title text-3xl text-[#6B0F1A] flex items-center gap-2">
                    <i class='bx bx-line-chart text-[#C9A84C]'></i>
                    Attendance Dashboard
                </h2>
                <p class="dm-sans text-sm mt-1" style="color:var(--ink-muted)">Track services, review records, and manage congregation attendance.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('attendance.records') }}" class="ui-btn ui-btn-ghost">
                    <i class='bx bx-spreadsheet text-base'></i>
                    View Records
                </a>
                <a href="{{ route('people.index') }}" class="ui-btn ui-btn-primary">
                    <i class='bx bx-group text-base'></i>
                    Manage People
                </a>
                <button onclick="document.getElementById('type-modal').classList.remove('hidden')"
                        class="ui-btn ui-btn-maroon">
                    <i class='bx bx-calendar-plus text-base'></i>
                    Add Event/Service
                </button>
            </div>
        </div>

        <hr class="ui-divider">

        @if (session()->has('success'))
            <div class="toast-success flex items-center gap-2">
                <i class='bx bx-check-circle'></i>
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach ($types as $type)
                @php
                    $latestSession = $type->sessions->first();
                @endphp
                <div class="ui-card-soft p-5 transition">
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <h3 class="page-title text-xl text-[#111111] flex items-center gap-2">
                            <i class='bx bx-calendar-event text-[#6B0F1A]'></i>
                            {{ $type->name }}
                        </h3>
                        @if ($latestSession)
                            <span class="badge badge-gold">{{ $latestSession->date->format('M d, Y') }}</span>
                        @else
                            <span class="badge" style="background:var(--surface-soft);color:var(--ink-faint)">No sessions</span>
                        @endif
                    </div>

                    <p class="text-sm dm-sans mt-1" style="color:var(--ink-muted)">{{ $type->day_of_week ?? 'Flexible Schedule' }}</p>
                    @if ($latestSession && $latestSession->service_name)
                        <p class="text-xs mt-2 font-medium" style="color:var(--maroon)">Latest: {{ $latestSession->service_name }}</p>
                    @endif

                    <hr class="ui-divider my-4">

                    <div class="flex items-center justify-between">
                        <a href="{{ route('attendance.show', $type) }}" class="ui-btn ui-btn-ghost text-sm py-2 px-3">
                            <i class='bx bx-log-in-circle'></i>
                            Open Check-in
                        </a>
                        <form method="POST" action="{{ route('attendance-types.destroy', $type) }}"
                              onsubmit="return confirm('Delete this event/service and all its records?');" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="ui-btn ui-btn-delete text-sm py-2 px-3">
                                <i class='bx bx-trash'></i>
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection

@push('modals')
    <div id="type-modal" class="hidden fixed inset-0 bg-black/50 z-50 p-4" onclick="if(event.target === this) this.classList.add('hidden')">
            <div class="max-w-lg mx-auto mt-24 ui-card-soft p-7 shadow-xl" onclick="event.stopPropagation()">
                <h3 class="page-title text-2xl text-[#6B0F1A] mb-5 flex items-center gap-2">
                    <i class='bx bx-calendar-plus text-[#D4AF37]'></i>
                    Add Event / Service
                </h3>
                <form method="POST" action="{{ route('attendance-types.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="form-label">Event / Service Name *</label>
                        <input type="text" name="name" required placeholder="e.g., Sunday Service"
                               class="ui-input">
                    </div>
                    <div>
                        <label class="form-label">Day of Week</label>
                        <select name="day_of_week" class="ui-input">
                            <option value="">Flexible Schedule</option>
                            <option>Sunday</option>
                            <option>Monday</option>
                            <option>Tuesday</option>
                            <option>Wednesday</option>
                            <option>Thursday</option>
                            <option>Friday</option>
                            <option>Saturday</option>
                        </select>
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" onclick="document.getElementById('type-modal').classList.add('hidden')"
                                class="ui-btn ui-btn-ghost">
                            <i class='bx bx-x'></i>
                            Cancel
                        </button>
                        <button type="submit" class="ui-btn ui-btn-primary">
                            <i class='bx bx-save'></i>
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
@endpush
