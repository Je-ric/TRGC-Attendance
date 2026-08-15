@extends('layouts.app')

@section('content')
<div class="flex flex-col gap-6">

    <x-page-header icon="bx-check-shield" title="Attendance" desc="Track services and manage congregation attendance.">
        <x-button href="{{ route('attendance.records') }}" variant="ghost">
            <i class='bx bx-folder-open'></i> Records
        </x-button>
        <x-button href="{{ route('people.index') }}" variant="ghost">
            <i class='bx bx-group'></i> People
        </x-button>
        <x-button variant="primary" onclick="document.getElementById('add-service-modal').showModal()">
            <i class='bx bx-plus'></i> Add Service
        </x-button>
    </x-page-header>

    @if($types->isEmpty())
        <x-empty-state icon="bx bx-calendar-x" title="No services yet" message='Click "Add Service" to create your first one.'>
            <x-button variant="primary" onclick="document.getElementById('add-service-modal').showModal()">
                <i class='bx bx-plus'></i> Add Service
            </x-button>
        </x-empty-state>
    @else
        <div class="grid gap-4" style="grid-template-columns:repeat(auto-fill,minmax(300px,1fr))">
            @foreach($types as $type)
                @php $latestSession = $type->sessions->first(); @endphp
                <x-card :padding="false">
                    {{-- Header --}}
                    <div class="px-4 py-3 border-b border-[#e4e0e2] bg-[#f5f4f6] flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <div class="page-eyebrow mb-1">Service</div>
                            <h3 class="page-title text-[17px] truncate">{{ $type->name }}</h3>
                            <div class="flex flex-wrap gap-x-3 gap-y-0.5 mt-1">
                                @if($type->day_of_week)
                                    <span class="text-[12px] text-[#a09aa4] flex items-center gap-1">
                                        <i class='bx bx-calendar text-[11px]'></i>
                                        {{ $type->day_of_week }}s
                                        @if($type->start_time) · {{ \Carbon\Carbon::parse($type->start_time)->format('g:i A') }} @endif
                                    </span>
                                @endif
                                @if($type->location)
                                    <span class="text-[12px] text-[#a09aa4] flex items-center gap-1">
                                        <i class='bx bx-map-pin text-[11px]'></i> {{ $type->location }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        @if($latestSession)
                            <x-feedback-status.status-indicator variant="slate" class="shrink-0">
                                {{ $latestSession->date->format('M d') }}
                            </x-feedback-status.status-indicator>
                        @endif
                    </div>

                    {{-- Body --}}
                    <div class="p-4 flex flex-col gap-3">
                        @if($type->description)
                            <p class="text-[12px] text-[#6b6570] leading-relaxed">{{ $type->description }}</p>
                        @endif

                        {{-- Stats --}}
                        <div class="grid grid-cols-2 gap-2">
                            <div class="bg-[#f5f4f6] rounded-lg px-3 py-2 border border-[#e4e0e2] text-center">
                                <div class="text-[11px] font-bold uppercase tracking-[0.1em] text-[#a09aa4]">Sessions</div>
                                <div class="text-[20px] font-bold text-[#1c1c1e]" style="font-family:'Oswald',sans-serif">
                                    {{ $type->sessions->count() }}
                                </div>
                            </div>
                            <div class="bg-[#f5f4f6] rounded-lg px-3 py-2 border border-[#e4e0e2] text-center">
                                <div class="text-[11px] font-bold uppercase tracking-[0.1em] text-[#a09aa4]">Last Session</div>
                                <div class="text-[13px] font-semibold text-[#1c1c1e]">
                                    {{ $latestSession ? $latestSession->date->format('M d, Y') : '—' }}
                                </div>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center justify-between pt-1 border-t border-[#e4e0e2]">
                            <x-button href="{{ route('attendance.show', $type) }}" variant="sm-primary">
                                <i class='bx bx-log-in-circle'></i> Check-in
                            </x-button>
                            <x-button variant="table-danger"
                                      onclick="document.getElementById('delete-service-{{ $type->id }}').showModal()">
                                <i class='bx bx-trash'></i>
                            </x-button>
                        </div>
                    </div>
                </x-card>
            @endforeach
        </div>
    @endif

</div>
@endsection

@push('modals')
    @include('attendance.modals.addServiceModal')

    @foreach($types as $type)
        @include('attendance.modals.deleteServiceModal', ['type' => $type])
    @endforeach

    @if($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                document.getElementById('add-service-modal').showModal();
            });
        </script>
    @endif
@endpush
