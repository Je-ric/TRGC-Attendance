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
        <div class="grid gap-4" style="grid-template-columns:repeat(auto-fill,minmax(280px,1fr))">
            @foreach($types as $type)
                @php $latestSession = $type->sessions->first(); @endphp
                <x-card :padding="false">
                    <div class="px-4 py-3 border-b border-[#e4e0e2] bg-[#f5f4f6] flex items-start justify-between gap-3">
                        <div>
                            <div class="page-eyebrow mb-1">Service</div>
                            <h3 class="page-title text-[17px]">{{ $type->name }}</h3>
                            <p class="text-[12px] text-[#a09aa4] mt-0.5">{{ $type->day_of_week ?? 'Flexible Schedule' }}</p>
                        </div>
                        @if($latestSession)
                            <x-feedback-status.status-indicator variant="slate">
                                {{ $latestSession->date->format('M d') }}
                            </x-feedback-status.status-indicator>
                        @endif
                    </div>
                    <div class="p-4 flex flex-col gap-3">
                        @if($latestSession?->service_name)
                            <div class="text-[12px] text-[#6b6570] bg-[#f5f4f6] rounded-lg px-3 py-2 border border-[#e4e0e2]">
                                <span class="text-[11px] font-bold uppercase tracking-[0.1em] text-[#a09aa4] block mb-0.5">Latest</span>
                                {{ $latestSession->service_name }}
                            </div>
                        @endif
                        <div class="flex items-center justify-between mt-auto pt-1">
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

{{-- ── Add Service Modal ──────────────────────────────────────────── --}}
<x-modal.dialog id="add-service-modal" maxWidth="max-w-md">
    <x-modal.header modalId="add-service-modal">
        <div class="flex items-center gap-3">
            <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-[#fff0f0] text-[#ed213a] shrink-0">
                <i class="bx bx-calendar-plus text-base leading-none"></i>
            </span>
            Add Service
        </div>
    </x-modal.header>
    <form method="POST" action="{{ route('attendance-types.store') }}">
        @csrf
        <x-modal.body class="flex flex-col gap-4">
            <p class="text-[13px] text-[#6b6570]">Create a new attendance service or event.</p>
            <x-form.field label="Service Name" :isRequired="true" error="name">
                <x-form.input name="name" placeholder="e.g., Sunday Service" value="{{ old('name') }}" />
            </x-form.field>
            <x-form.field label="Day of Week">
                <x-form.select name="day_of_week">
                    <option value="">Flexible Schedule</option>
                    @foreach(['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $day)
                        <option {{ old('day_of_week') === $day ? 'selected' : '' }}>{{ $day }}</option>
                    @endforeach
                </x-form.select>
            </x-form.field>
        </x-modal.body>
        <x-modal.footer>
            <x-modal.close-button modalId="add-service-modal" text="Cancel" />
            <x-button type="submit" variant="primary">
                <i class='bx bx-save'></i> Save
            </x-button>
        </x-modal.footer>
    </form>
</x-modal.dialog>

{{-- ── Delete Service Modals (one per service) ───────────────────── --}}
@foreach($types as $type)
    <x-modal.dialog id="delete-service-{{ $type->id }}" maxWidth="max-w-md">
        <x-modal.header modalId="delete-service-{{ $type->id }}">
            <div class="flex items-center gap-3">
                <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-[#ffe4e6] text-[#e11d48] shrink-0">
                    <i class="bx bx-trash text-base leading-none"></i>
                </span>
                <span class="text-[#9f1239]">Delete Service</span>
            </div>
        </x-modal.header>
        <x-modal.body class="space-y-4">
            <p class="text-[13px] text-[#6b6570]">Are you sure you want to delete this service?</p>
            <div class="rounded-xl border border-[#e4e0e2] bg-[#f5f4f6] p-4 space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#a09aa4]">Service</span>
                    <span class="text-[13px] font-semibold text-[#1c1c1e]">{{ $type->name }}</span>
                </div>
                @if($type->day_of_week)
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#a09aa4]">Schedule</span>
                        <span class="text-[13px] text-[#6b6570]">{{ $type->day_of_week }}</span>
                    </div>
                @endif
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#a09aa4]">Sessions</span>
                    <span class="text-[13px] font-semibold text-[#ed213a]">{{ $type->sessions->count() }}</span>
                </div>
            </div>
            <x-feedback-status.alert type="error" :showTitle="false">
                This will permanently delete the service and all its attendance sessions and records.
            </x-feedback-status.alert>
        </x-modal.body>
        <x-modal.footer>
            <x-modal.close-button modalId="delete-service-{{ $type->id }}" text="Cancel" />
            <form method="POST" action="{{ route('attendance-types.destroy', $type) }}">
                @csrf @method('DELETE')
                <x-button type="submit" variant="danger">
                    <i class='bx bx-trash'></i> Delete
                </x-button>
            </form>
        </x-modal.footer>
    </x-modal.dialog>
@endforeach

@if($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('add-service-modal').showModal();
        });
    </script>
@endif

@endpush
