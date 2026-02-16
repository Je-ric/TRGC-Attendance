@extends('layouts.app')

@section('content')
    <div class="min-h-screen from-slate-50 to-blue-50 py-8 px-4">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-3xl font-serif font-bold text-slate-800 mb-6 text-center">Attendance</h2>

            @if (session()->has('success'))
                <div class="bg-emerald-100 border border-emerald-300 text-emerald-800 px-6 py-4 rounded-lg mb-6 shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="flex flex-wrap justify-between items-center mb-8 gap-4">
                <div class="flex gap-3">
                    <a href="{{ route('attendance.records') }}" class="text-indigo-600 hover:text-indigo-800 font-medium px-4 py-2 rounded-md hover:bg-indigo-50 transition-colors duration-200">View Attendance Records</a>
                    <a href="{{ route('people.index') }}" class="bg-amber-500 text-white px-4 py-2 rounded-md hover:bg-amber-600 shadow-md transition-all duration-200">Manage People</a>
                </div>
                <!-- Add Attendance Type Modal Button -->
                <button onclick="document.getElementById('type-modal').classList.remove('hidden')"
                    class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 shadow-md transition-all duration-200">
                    + Add Event/Service
                </button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($types as $type)
                    @php
                        $latestSession = $type->sessions->first();
                    @endphp
                    <div class="p-6 bg-white rounded-xl shadow-lg hover:shadow-xl relative transition-all duration-300 border border-slate-200 hover:border-indigo-200">
                        <div class="absolute top-3 right-3 text-xs text-slate-500 font-medium bg-slate-100 px-3 py-1 rounded-full">
                            @if ($latestSession)
                                {{ $latestSession->date->format('M d, Y') }}
                            @else
                                No sessions
                            @endif
                        </div>
                        <a href="{{ route('attendance.show', $type) }}" class="block">
                            <h3 class="font-serif font-semibold pr-20 text-xl text-slate-800">{{ $type->name }}</h3>
                            <p class="text-sm text-slate-600 mt-2">
                                {{ $type->day_of_week ?? 'Flexible Schedule' }}
                            </p>
                            @if ($latestSession && $latestSession->service_name)
                                <p class="text-xs text-indigo-600 mt-2 font-medium">
                                    Latest: {{ $latestSession->service_name }}
                                </p>
                            @endif
                        </a>
                        <div class="mt-4 flex justify-end">
                            <form method="POST" action="{{ route('attendance-types.destroy', $type) }}"
                                onsubmit="return confirm('Are you sure you want to delete this event/service? This will also delete all associated attendance records.');"
                                class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="event.stopPropagation()"
                                    class="text-red-600 hover:text-red-800 text-sm font-medium hover:bg-red-50 px-2 py-1 rounded transition-colors duration-200">Delete</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Modal -->
            <div id="type-modal" class="hidden fixed inset-0 bg-black/50 items-center justify-center z-50 transition-opacity duration-300"
                onclick="if(event.target === this) this.classList.add('hidden')">
                <div class="bg-white w-full max-w-lg rounded-xl shadow-2xl p-8 transform scale-95 transition-transform duration-300" onclick="event.stopPropagation()">
                    <h2 class="text-2xl font-serif font-bold text-slate-800 mb-6 text-center">Add Event / Service</h2>
                    <form method="POST" action="{{ route('attendance-types.store') }}">
                        @csrf
                        <div class="mb-5">
                            <label class="block text-sm font-medium text-slate-700 mb-2">Event / Service Name *</label>
                            <input type="text" name="name" placeholder="e.g., Sunday Service"
                                class="border border-slate-300 p-3 rounded-lg w-full focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200" required>
                        </div>
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-slate-700 mb-2">Day of Week</label>
                            <select name="day_of_week" class="border border-slate-300 p-3 rounded-lg w-full focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">
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
                        <div class="flex justify-end gap-3">
                            <button type="button" onclick="document.getElementById('type-modal').classList.add('hidden')"
                                class="px-5 py-2 border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors duration-200">Cancel</button>
                            <button type="submit" class="px-5 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 shadow-md transition-all duration-200">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
