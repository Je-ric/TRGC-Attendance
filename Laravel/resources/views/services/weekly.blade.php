@extends('layouts.app')

@section('content')
<div class="flex flex-col gap-6">
    <x-page-header 
        icon="bx-calendar-week" 
        title="Weekly View" 
        desc="Week of {{ $startDate->format('M d') }} - {{ $endDate->format('M d, Y') }}">
        <div class="flex items-center gap-2">
            <x-button href="{{ route('services.weekly', $prevWeek) }}" variant="ghost">
                <i class='bx bx-chevron-left'></i> Previous
            </x-button>
            <x-button href="{{ route('services.weekly', $nextWeek) }}" variant="ghost">
                Next <i class='bx bx-chevron-right'></i>
            </x-button>
            <x-button href="{{ route('export.weekly', $weekIdentifier) }}" variant="primary">
                <i class='bx bx-download'></i> Export CSV
            </x-button>
            <x-button href="{{ route('services.index') }}" variant="ghost">
                <i class='bx bx-grid-alt'></i> All Services
            </x-button>
        </div>
    </x-page-header>

    {{-- Week Info --}}
    <div class="bg-[#f5f4f6] p-4 rounded-lg border border-[#e4e0e2]">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-[12px] text-[#a09aa4] uppercase tracking-wider">Week Identifier</div>
                <div class="font-semibold">{{ $weekIdentifier }}</div>
            </div>
            <div class="flex gap-6">
                <div class="text-center">
                    <div class="text-[24px] font-bold text-[#635BFF]" style="font-family:'Oswald',sans-serif">{{ $services->count() }}</div>
                    <div class="text-[11px] text-[#a09aa4] uppercase tracking-wider">Services</div>
                </div>
                <div class="text-center">
                    <div class="text-[24px] font-bold text-[#00C48C]" style="font-family:'Oswald',sans-serif">{{ $services->sum(fn($s) => $s->attendee_count) }}</div>
                    <div class="text-[11px] text-[#a09aa4] uppercase tracking-wider">Total Attendees</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Services grouped by day --}}
    @if($services->count() > 0)
        <div class="space-y-6">
            @php
                $servicesByDay = $services->groupBy(function($service) {
                    return $service->date->format('l, F j');
                });
            @endphp
            
            @foreach($servicesByDay as $day => $dayServices)
                <div>
                    <h3 class="text-lg font-semibold mb-3" style="font-family: var(--font-heading)">{{ $day }}</h3>
                    <div class="space-y-3">
                        @foreach($dayServices as $service)
                            <x-card :padding="false">
                                <div class="p-4 flex items-start justify-between gap-4">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-1">
                                            <h4 class="font-semibold text-[15px]">{{ $service->name }}</h4>
                                            @if($service->is_special_event)
                                                <x-feedback-status.status-indicator variant="warning">Special</x-feedback-status.status-indicator>
                                            @endif
                                        </div>
                                        
                                        <div class="flex flex-wrap gap-x-3 gap-y-1 text-[12px] text-[#a09aa4]">
                                            @if($service->time)
                                                <span class="flex items-center gap-1">
                                                    <i class='bx bx-time text-[11px]'></i>
                                                    {{ \Carbon\Carbon::parse($service->time)->format('g:i A') }}
                                                </span>
                                            @endif
                                            @if($service->location)
                                                <span class="flex items-center gap-1">
                                                    <i class='bx bx-map-pin text-[11px]'></i>
                                                    {{ $service->location }}
                                                </span>
                                            @endif
                                            @if($service->service_category)
                                                <span class="flex items-center gap-1">
                                                    <i class='bx bx-tag text-[11px]'></i>
                                                    {{ $service->service_category }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center gap-3">
                                        <div class="text-center">
                                            <div class="text-[20px] font-bold text-[#1c1c1e]" style="font-family:'Oswald',sans-serif">
                                                {{ $service->attendee_count }}
                                            </div>
                                            <div class="text-[11px] text-[#a09aa4] uppercase tracking-wider">Attendees</div>
                                        </div>
                                        
                                        <div class="flex items-center gap-1">
                                            <x-button href="{{ route('services.checkin', $service) }}" variant="sm-primary">
                                                <i class='bx bx-log-in-circle'></i> Check-in
                                            </x-button>
                                            <x-button href="{{ route('services.show', $service) }}" variant="ghost">
                                                <i class='bx bx-show'></i>
                                            </x-button>
                                        </div>
                                    </div>
                                </div>
                            </x-card>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <x-empty-state icon="bx bx-calendar-x" title="No services this week" message="There are no services scheduled for this week.">
            <x-button href="{{ route('services.create') }}" variant="primary">
                <i class='bx bx-plus'></i> Create Service
            </x-button>
        </x-empty-state>
    @endif
</div>
@endsection