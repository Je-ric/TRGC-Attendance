{{-- Include in: attendance/index.blade.php inside @foreach($types as $type) --}}
{{-- Open via: onclick="document.getElementById('delete-service-{{ $type->id }}').showModal()" --}}

<x-modal.dialog id="delete-service-{{ $type->id }}" maxWidth="max-w-md">
    <x-modal.header modalId="delete-service-{{ $type->id }}">
        <div class="flex items-center gap-3">
            <span class="flex items-center justify-center w-9 h-9 rounded-xl bg-[#ffe4e6] text-[#e11d48] shrink-0">
                <i class="bx bx-trash text-lg leading-none"></i>
            </span>
            <div>
                <p class="text-[15px] font-bold text-[#9f1239]">Delete Service</p>
                <p class="text-[12px] text-[#a09aa4]">This action cannot be undone.</p>
            </div>
        </div>
    </x-modal.header>

    <x-modal.body class="space-y-4">
        <p class="text-[13px] text-[#6b6570]">Are you sure you want to delete this service?</p>

        <div class="rounded-xl border border-[#e4e0e2] bg-[#f5f4f6] p-4 space-y-2.5">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#a09aa4]">Service</span>
                <span class="text-[13px] font-semibold text-[#1c1c1e]">{{ $type->name }}</span>
            </div>
            @if($type->day_of_week)
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#a09aa4]">Schedule</span>
                    <span class="text-[13px] text-[#6b6570]">{{ $type->day_of_week }}s</span>
                </div>
            @endif
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#a09aa4]">Sessions</span>
                <span class="text-[13px] font-semibold {{ $type->sessions->count() > 0 ? 'text-[#ed213a]' : 'text-[#a09aa4]' }}">
                    {{ $type->sessions->count() }}
                </span>
            </div>
        </div>

        <x-feedback-status.alert type="error" :showTitle="false">
            This will permanently delete the service and all {{ $type->sessions->count() }} session(s) and their attendance records.
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
