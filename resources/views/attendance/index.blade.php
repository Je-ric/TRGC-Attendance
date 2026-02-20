@extends('layouts.app')

@section('content')
    <div class="space-y-8">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h2 class="brand-font text-3xl text-[#6B0F1A]">Attendance Dashboard</h2>
                <p class="text-sm text-slate-600 mt-1">Track services, review records, and manage congregation attendance.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('attendance.records') }}" class="px-4 py-2 rounded-lg border border-[#D4AF37]/40 text-[#6B0F1A] hover:bg-[#D4AF37]/10 transition">
                    View Records
                </a>
                <a href="{{ route('people.index') }}" class="px-4 py-2 rounded-lg text-[#111111] gold-gradient font-semibold shadow-md">
                    Manage People
                </a>
                <button onclick="document.getElementById('type-modal').classList.remove('hidden')"
                        class="px-4 py-2 rounded-lg bg-[#6B0F1A] text-white hover:bg-[#560b15] transition shadow-md">
                    + Add Event/Service
                </button>
            </div>
        </div>

        @if (session()->has('success'))
            <div class="rounded-xl border border-emerald-300 bg-emerald-50 px-4 py-3 text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach ($types as $type)
                @php
                    $latestSession = $type->sessions->first();
                @endphp
                <div class="rounded-2xl border border-[#D4AF37]/25 bg-white p-5 shadow-sm hover:shadow-lg transition">
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <h3 class="brand-font text-xl text-[#111111]">{{ $type->name }}</h3>
                        <span class="text-xs font-medium px-2 py-1 rounded-full bg-[#111111] text-[#F5D76E]">
                            @if ($latestSession)
                                {{ $latestSession->date->format('M d, Y') }}
                            @else
                                No sessions
                            @endif
                        </span>
                    </div>

                    <p class="text-sm text-slate-600">{{ $type->day_of_week ?? 'Flexible Schedule' }}</p>
                    @if ($latestSession && $latestSession->service_name)
                        <p class="text-xs text-[#6B0F1A] mt-2 font-medium">Latest: {{ $latestSession->service_name }}</p>
                    @endif

                    <div class="mt-4 flex items-center justify-between">
                        <a href="{{ route('attendance.show', $type) }}" class="text-sm font-semibold text-[#6B0F1A] hover:underline">
                            Open Check-in
                        </a>
                        <form method="POST" action="{{ route('attendance-types.destroy', $type) }}"
                              onsubmit="return confirm('Delete this event/service and all its records?');" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm text-red-600 hover:text-red-800">Delete</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div id="type-modal" class="hidden fixed inset-0 bg-black/50 z-50 p-4" onclick="if(event.target === this) this.classList.add('hidden')">
            <div class="max-w-lg mx-auto mt-24 rounded-2xl border border-[#D4AF37]/40 bg-white p-7 shadow-2xl" onclick="event.stopPropagation()">
                <h3 class="brand-font text-2xl text-[#6B0F1A] mb-5">Add Event / Service</h3>
                <form method="POST" action="{{ route('attendance-types.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Event / Service Name *</label>
                        <input type="text" name="name" required placeholder="e.g., Sunday Service"
                               class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#D4AF37]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Day of Week</label>
                        <select name="day_of_week" class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#D4AF37]">
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
                                class="px-4 py-2 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 rounded-lg text-[#111111] gold-gradient font-semibold">
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
