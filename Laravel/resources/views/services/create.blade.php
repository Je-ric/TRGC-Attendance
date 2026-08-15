@extends('layouts.app')

@section('content')
<div class="flex flex-col gap-6">
    <x-page-header icon="bx-plus" title="Create Service" desc="Add a new service or event.">
        <x-button href="{{ route('services.index') }}" variant="back">
            <i class='bx bx-arrow-left'></i> Back to Services
        </x-button>
    </x-page-header>

    <x-card>
        <form action="{{ route('services.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <x-form.field label="Service Name" required>
                <x-form.input name="name" type="text" placeholder="e.g., Sunday Morning Service" value="{{ old('name') }}" required />
                @error('name') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
            </x-form.field>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-form.field label="Date" required>
                    <x-form.date-picker name="date" value="{{ old('date') }}" required />
                    @error('date') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
                </x-form.field>
                
                <x-form.field label="Time">
                    <x-form.input name="time" type="time" value="{{ old('time') }}" />
                    @error('time') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
                </x-form.field>
            </div>
            
            <x-form.field label="Location">
                <x-form.input name="location" type="text" placeholder="e.g., Main Sanctuary" value="{{ old('location') }}" />
                @error('location') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
            </x-form.field>
            
            <x-form.field label="Category">
                <x-form.select name="service_category">
                    <option value="">Select category</option>
                    <option value="Sunday Morning" {{ old('service_category') == 'Sunday Morning' ? 'selected' : '' }}>Sunday Morning</option>
                    <option value="Sunday Afternoon" {{ old('service_category') == 'Sunday Afternoon' ? 'selected' : '' }}>Sunday Afternoon</option>
                    <option value="Saturday" {{ old('service_category') == 'Saturday' ? 'selected' : '' }}>Saturday</option>
                    <option value="Youth" {{ old('service_category') == 'Youth' ? 'selected' : '' }}>Youth</option>
                    <option value="Special" {{ old('service_category') == 'Special' ? 'selected' : '' }}>Special Event</option>
                </x-form.select>
                @error('service_category') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
            </x-form.field>
            
            <x-form.field label="Notes">
                <x-form.textarea name="notes" rows="3" placeholder="Any additional notes...">{{ old('notes') }}</x-form.textarea>
                @error('notes') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
            </x-form.field>
            
            <x-form.checkbox name="is_special_event" label="This is a special event" checked="{{ old('is_special_event') ? true : false }}" />
            
            <div class="flex items-center gap-3 pt-4">
                <x-button type="submit" variant="primary">
                    <i class='bx bx-plus'></i> Create Service
                </x-button>
                <x-button href="{{ route('services.index') }}" variant="ghost">
                    Cancel
                </x-button>
            </div>
        </form>
    </x-card>
</div>
@endsection