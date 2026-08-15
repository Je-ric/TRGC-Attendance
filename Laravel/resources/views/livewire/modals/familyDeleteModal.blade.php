{{-- Included in: livewire/family-management.blade.php --}}

<x-modal.dialog id="family-delete-modal" maxWidth="max-w-md" wire:ignore.self>
    <x-modal.header modalId="family-delete-modal">
        <div class="flex items-center gap-3">
            <span class="flex items-center justify-center w-9 h-9 rounded-xl bg-[#ffe4e6] text-[#e11d48] shrink-0">
                <i class="bx bx-trash text-lg leading-none"></i>
            </span>
            <div>
                <p class="text-[15px] font-bold text-[#9f1239]">Delete Family</p>
                <p class="text-[12px] text-[#a09aa4]">This action cannot be undone.</p>
            </div>
        </div>
    </x-modal.header>

    <x-modal.body class="space-y-4">
        <p class="text-[13px] text-[#6b6570]">Are you sure you want to remove this family group?</p>

        <div class="rounded-xl border border-[#e4e0e2] bg-[#f5f4f6] p-4">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#a09aa4]">Family</span>
                <span class="text-[13px] font-semibold text-[#1c1c1e]">{{ $confirmDeleteName }}</span>
            </div>
        </div>

        <x-feedback-status.alert type="warning" :showTitle="false">
            All members will be unlinked from this family. Their attendance records will remain.
        </x-feedback-status.alert>
    </x-modal.body>

    <x-modal.footer>
        <x-modal.close-button modalId="family-delete-modal" text="Cancel" />
        <x-button wire:click="deleteFamily" variant="danger">
            <i class='bx bx-trash'></i> Delete Family
        </x-button>
    </x-modal.footer>
</x-modal.dialog>
