<div>
    <div class="flex flex-col gap-6">
        {{-- Header with actions --}}
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <h2 class="text-xl font-bold" style="font-family: var(--font-heading)">Services & Events</h2>
                <span class="text-sm text-[#a09aa4]">{{ $services->total() }} total</span>
            </div>
            
            <div class="flex items-center gap-2">
                <x-button variant="ghost" wire:click="quickSundayService">
                    <i class='bx bx-calendar-plus'></i> Quick Sunday Service
                </x-button>
                <x-button variant="primary" onclick="document.getElementById('create-service-modal').showModal()">
                    <i class='bx bx-plus'></i> Create Service
                </x-button>
            </div>
        </div>

        {{-- Filters --}}
        <div class="flex flex-wrap items-center gap-4 bg-[#f5f4f6] p-4 rounded-lg border border-[#e4e0e2]">
            <div class="flex-1 min-w-[200px]">
                <input type="text" 
                       wire:model.live="search" 
                       placeholder="Search services..." 
                       class="w-full px-3 py-2 border border-[#e4e0e2] rounded-md focus:outline-none focus:ring-2 focus:ring-[#635BFF]">
            </div>
            
            <select wire:model.live="filterCategory" class="px-3 py-2 border border-[#e4e0e2] rounded-md focus:outline-none focus:ring-2 focus:ring-[#635BFF]">
                <option value="all">All Categories</option>
                <option value="Sunday Morning">Sunday Morning</option>
                <option value="Sunday Afternoon">Sunday Afternoon</option>
                <option value="Saturday">Saturday</option>
                <option value="Youth">Youth</option>
                <option value="Special">Special Events</option>
            </select>
            
            <select wire:model.live="filterSpecial" class="px-3 py-2 border border-[#e4e0e2] rounded-md focus:outline-none focus:ring-2 focus:ring-[#635BFF]">
                <option value="all">All Types</option>
                <option value="regular">Regular Services</option>
                <option value="special">Special Events</option>
            </select>
        </div>

        {{-- Services list --}}
        @if($services->count() > 0)
            <div class="space-y-3">
                @foreach($services as $service)
                    <x-card :padding="false">
                        <div class="p-4 flex items-start justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <h3 class="font-semibold text-[15px] truncate">{{ $service->name }}</h3>
                                    @if($service->is_special_event)
                                        <x-feedback-status.status-indicator variant="warning">Special</x-feedback-status.status-indicator>
                                    @endif
                                </div>
                                
                                <div class="flex flex-wrap gap-x-3 gap-y-1 text-[12px] text-[#a09aa4]">
                                    <span class="flex items-center gap-1">
                                        <i class='bx bx-calendar text-[11px]'></i>
                                        {{ $service->date->format('M d, Y') }}
                                    </span>
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
                                    <div class="text-[11px] font-bold uppercase tracking-[0.1em] text-[#a09aa4]">Attendees</div>
                                    <div class="text-[20px] font-bold text-[#1c1c1e]" style="font-family:'Oswald',sans-serif">
                                        {{ $service->attendee_count }}
                                    </div>
                                </div>
                                
                                <div class="flex items-center gap-1">
                                    <x-button href="{{ route('services.checkin', $service) }}" variant="sm-primary">
                                        <i class='bx bx-log-in-circle'></i> Check-in
                                    </x-button>
                                    <x-button variant="table-danger" wire:click="deleteService({{ $service->id }})">
                                        <i class='bx bx-trash'></i>
                                    </x-button>
                                </div>
                            </div>
                        </div>
                    </x-card>
                @endforeach
            </div>
            
            {{ $services->links() }}
        @else
            <x-empty-state icon="bx bx-calendar-x" title="No services yet" message="Create your first service to start tracking attendance.">
                <x-button variant="primary" onclick="document.getElementById('create-service-modal').showModal()">
                    <i class='bx bx-plus'></i> Create Service
                </x-button>
            </x-empty-state>
        @endif
    </div>

    {{-- Create Service Modal --}}
    <dialog id="create-service-modal" class="modal">
        <div class="modal-box max-w-lg">
            <x-modal.header title="Create New Service" />
            
            <form method="dialog" class="py-4">
                <div class="space-y-4">
                    <x-form.field label="Service Name">
                        <x-form.input name="name" type="text" placeholder="e.g., Sunday Morning Service" required />
                    </x-form.field>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <x-form.field label="Date">
                            <x-form.date-picker name="date" required />
                        </x-form.field>
                        
                        <x-form.field label="Time">
                            <x-form.input name="time" type="time" />
                        </x-form.field>
                    </div>
                    
                    <x-form.field label="Location">
                        <x-form.input name="location" type="text" placeholder="e.g., Main Sanctuary" />
                    </x-form.field>
                    
                    <x-form.field label="Category">
                        <x-form.select name="service_category">
                            <option value="">Select category</option>
                            <option value="Sunday Morning">Sunday Morning</option>
                            <option value="Sunday Afternoon">Sunday Afternoon</option>
                            <option value="Saturday">Saturday</option>
                            <option value="Youth">Youth</option>
                            <option value="Special">Special Event</option>
                        </x-form.select>
                    </x-form.field>
                    
                    <x-form.field label="Notes">
                        <x-form.textarea name="notes" rows="3" />
                    </x-form.field>
                    
                    <x-form.checkbox name="is_special_event" label="This is a special event" />
                </div>
            </form>
            
            <x-modal.footer>
                <form method="dialog">
                    <x-button variant="ghost">Cancel</x-button>
                    <button type="submit" form="create-service-form" class="btn btn-primary">Create Service</button>
                </form>
            </x-modal.footer>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>
</div>