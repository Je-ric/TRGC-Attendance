<x-modal.dialog id="add-service-modal">
    <x-modal.header modalId="add-service-modal">Add Service</x-modal.header>
    <form method="POST" action="{{ route('attendance-types.store') }}">
        @csrf
        <x-modal.body class="flex flex-col gap-4">
            <p class="text-[13px] text-[#6b6570]">Create a new attendance service or event.</p>
            <x-form.field label="Service Name" :isRequired="true" error="name">
                <x-form.input name="name" placeholder="e.g., Sunday Service" />
            </x-form.field>
            <x-form.field label="Day of Week">
                <x-form.select name="day_of_week">
                    <option value="">Flexible Schedule</option>
                    @foreach(['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $day)
                        <option>{{ $day }}</option>
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
