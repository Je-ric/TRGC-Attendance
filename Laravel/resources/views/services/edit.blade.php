@extends('layouts.app')

@section('content')
<div class="flex flex-col gap-6">
    <x-page-header icon="bx-edit" title="Edit Service" desc="Update service details.">
        <x-button href="{{ route('services.show', $service) }}" variant="back">
            <i class='bx bx-arrow-left'></i> Back to Service
        </x-button>
    </x-page-header>

    <x-card>
        <form action="{{ route('services.update', $service) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <x-form.field label="Service Name" required>
                <x-form.input name="name" type="text" placeholder="e.g., Sunday Morning Service" value="{{ old('name', $service->name) }}" required />
                @error('name') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
            </x-form.field>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-form.field label="Date" required>
                    <x-form.date-picker name="date" value="{{ old('date', $service->date->format('Y-m-d')) }}" required />
                    @error('date') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
                </x-form.field>
                
                <x-form.field label="Time">
                    <x-form.input name="time" type="time" value="{{ old('time', $service->time ? $service->time->format('H:i') : '') }}" />
                    @error('time') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
                </x-form.field>
            </div>
            
            <x-form.field label="Location">
                <x-form.input name="location" type="text" placeholder="e.g., Main Sanctuary" value="{{ old('location', $service->location) }}" />
                @error('location') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
            </x-form.field>
            
            <x-form.field label="Category">
                <x-form.select name="service_category">
                    <option value="">Select category</option>
                    <option value="Sunday Morning" {{ old('service_category', $service->service_category) == 'Sunday Morning' ? 'selected' : '' }}>Sunday Morning</option>
                    <option value="Sunday Afternoon" {{ old('service_category', $service->service_category) == 'Sunday Afternoon' ? 'selected' : '' }}>Sunday Afternoon</option>
                    <option value="Saturday" {{ old('service_category', $service->service_category) == 'Saturday' ? 'selected' : '' }}>Saturday</option>
                    <option value="Youth" {{ old('service_category', $service->service_category) == 'Youth' ? 'selected' : '' }}>Youth</option>
                    <option value="Special" {{ old('service_category', $service->service_category) == 'Special' ? 'selected' : '' }}>Special Event</option>
                </x-form.select>
                @error('service_category') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
            </x-form.field>
            
            <x-form.field label="Notes">
                <x-form.textarea name="notes" rows="3" placeholder="Any additional notes...">{{ old('notes', $service->notes) }}</x-form.textarea>
                @error('notes') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
            </x-form.field>
            
            <x-form.checkbox name="is_special_event" label="This is a special event" checked="{{ old('is_special_event', $service->is_special_event) ? true : false }}" />
            
            <div class="flex items-center gap-3 pt-4">
                <x-button type="submit" variant="primary">
                    <i class='bx bx-save'></i> Update Service
                </x-button>
                <x-button href="{{ route('services.show', $service) }}" variant="ghost">
                    Cancel
                </x-button>
            </div>
        </form>
    </x-card>
</div>
@endsection