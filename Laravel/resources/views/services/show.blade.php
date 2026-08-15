@extends('layouts.app')

@section('content')
<div class="flex flex-col gap-6">
    <x-page-header 
        icon="bx-calendar-event" 
        title="{{ $service->name }}" 
        desc="{{ $service->date->format('M d, Y') }} @if($service->time){{ \Carbon\Carbon::parse($service->time)->format('g:i A') }}@endif">
        <x-button href="{{ route('services.index') }}" variant="back">
            <i class='bx bx-arrow-left'></i> Back to Services
        </x-button>
        <x-button href="{{ route('export.service', $service) }}" variant="ghost">
            <i class='bx bx-download'></i> Export CSV
        </x-button>
        <x-button href="{{ route('services.checkin', $service) }}" variant="primary">
            <i class='bx bx-log-in-circle'></i> Check-in
        </x-button>
    </x-page-header>

    <div class="grid gap-6 md:grid-cols-3">
        {{-- Service Details --}}
        <x-card>
            <h3 class="font-semibold mb-4">Service Details</h3>
            <div class="space-y-3">
                <div>
                    <div class="text-[12px] text-[#a09aa4] uppercase tracking-wider">Date</div>
                    <div class="font-medium">{{ $service->date->format('M d, Y') }}</div>
                </div>
                @if($service->time)
                    <div>
                        <div class="text-[12px] text-[#a09aa4] uppercase tracking-wider">Time</div>
                        <div class="font-medium">{{ \Carbon\Carbon::parse($service->time)->format('g:i A') }}</div>
                    </div>
                @endif
                @if($service->location)
                    <div>
                        <div class="text-[12px] text-[#a09aa4] uppercase tracking-wider">Location</div>
                        <div class="font-medium">{{ $service->location }}</div>
                    </div>
                @endif
                @if($service->service_category)
                    <div>
                        <div class="text-[12px] text-[#a09aa4] uppercase tracking-wider">Category</div>
                        <div class="font-medium">{{ $service->service_category }}</div>
                    </div>
                @endif
                <div>
                    <div class="text-[12px] text-[#a09aa4] uppercase tracking-wider">Type</div>
                    <div class="font-medium">
                        @if($service->is_special_event)
                            <x-feedback-status.status-indicator variant="warning">Special Event</x-feedback-status.status-indicator>
                        @else
                            <x-feedback-status.status-indicator variant="slate">Regular Service</x-feedback-status.status-indicator>
                        @endif
                    </div>
                </div>
            </div>
        </x-card>

        {{-- Attendance Stats --}}
        <x-card>
            <h3 class="font-semibold mb-4">Attendance</h3>
            <div class="text-center py-4">
                <div class="text-[48px] font-bold text-[#635BFF]" style="font-family:'Oswald',sans-serif">
                    {{ $service->attendee_count }}
                </div>
                <div class="text-[12px] text-[#a09aa4] uppercase tracking-wider">Total Attendees</div>
            </div>
            
            @if($service->category_breakdown)
                <div class="mt-4 pt-4 border-t border-[#e4e0e2]">
                    <div class="text-[12px] text-[#a09aa4] uppercase tracking-wider mb-2">Breakdown</div>
                    <div class="flex flex-wrap gap-2">
                        @foreach($service->category_breakdown as $category => $count)
                            <x-feedback-status.status-indicator variant="slate">
                                {{ $category }}: {{ $count }}
                            </x-feedback-status.status-indicator>
                        @endforeach
                    </div>
                </div>
            @endif
        </x-card>

        {{-- Actions --}}
        <x-card>
            <h3 class="font-semibold mb-4">Actions</h3>
            <div class="space-y-2">
                <x-button href="{{ route('services.checkin', $service) }}" variant="primary" class="w-full">
                    <i class='bx bx-log-in-circle'></i> Check-in People
                </x-button>
                <x-button href="{{ route('services.edit', $service) }}" variant="ghost" class="w-full">
                    <i class='bx bx-edit'></i> Edit Service
                </x-button>
                <form action="{{ route('services.destroy', $service) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this service?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full text-left px-4 py-2 rounded-lg border border-[#e4e0e2] hover:bg-[#f5f4f6] transition-colors text-red-500">
                        <i class='bx bx-trash'></i> Delete Service
                    </button>
                </form>
            </div>
        </x-card>
    </div>

    {{-- Attendees List --}}
    <x-card>
        <h3 class="font-semibold mb-4">Attendees</h3>
        @if($service->attendanceRecords->count() > 0)
            <x-table.container>
                <x-table.table>
                    <x-table.head>
                        <tr>
                            <x-table.th>Name</x-table.th>
                            <x-table.th>Category</x-table.th>
                            <x-table.th>Check-in Time</x-table.th>
                        </tr>
                    </x-table.head>
                    <x-table.body>
                        @foreach($service->attendanceRecords as $record)
                            <x-table.row :hover="true">
                                <x-table.td>
                                    <div class="font-medium">{{ $record->person->full_name }}</div>
                                    @if($record->person->family)
                                        <div class="text-[11px] text-[#a09aa4]">{{ $record->person->family->family_name }}</div>
                                    @endif
                                </x-table.td>
                                <x-table.td>
                                    <x-feedback-status.status-indicator variant="slate">
                                        {{ $record->person->effective_category }}
                                    </x-feedback-status.status-indicator>
                                </x-table.td>
                                <x-table.td>
                                    @if($record->check_in_time)
                                        {{ \Carbon\Carbon::parse($record->check_in_time)->format('g:i A') }}
                                    @else
                                        —
                                    @endif
                                </x-table.td>
                            </x-table.row>
                        @endforeach
                    </x-table.body>
                </x-table.table>
            </x-table.container>
        @else
            <x-empty-state icon="bx bx-user-x" title="No attendees yet" message="Start checking people in to see them here." />
        @endif
    </x-card>
</div>
@endsection