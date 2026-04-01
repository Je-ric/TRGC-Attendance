{{-- Include in: attendance/index.blade.php --}}
{{-- Open via: onclick="document.getElementById('add-service-modal').showModal()" --}}

<x-modal.dialog id="add-service-modal" maxWidth="max-w-lg">
    <x-modal.header modalId="add-service-modal">
        <div class="flex items-center gap-3">
            <span class="flex items-center justify-center w-9 h-9 rounded-xl shrink-0"
                  style="background:linear-gradient(135deg,#93291e,#ed213a)">
                <i class="bx bx-calendar-plus text-white text-lg leading-none"></i>
            </span>
            <div>
                <p class="text-[15px] font-bold text-[#1c1c1e]">Add Service</p>
                <p class="text-[12px] text-[#a09aa4]">Create a new attendance service or event.</p>
            </div>
        </div>
    </x-modal.header>

    <form method="POST" action="{{ route('attendance-types.store') }}">
        @csrf
        <x-modal.body class="flex flex-col gap-4">

            <x-form.field label="Service Name" :isRequired="true" error="name">
                <x-form.input name="name" placeholder="e.g., Sunday Morning Service" value="{{ old('name') }}" />
            </x-form.field>

            <x-form.field label="Description" error="description">
                <x-form.textarea name="description" rows="2"
                    placeholder="Brief description of this service…">{{ old('description') }}</x-form.textarea>
            </x-form.field>

            <div class="grid grid-cols-2 gap-3">
                <x-form.field label="Day of Week">
                    <x-form.select name="day_of_week">
                        <option value="">Flexible / No fixed day</option>
                        @foreach(['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $day)
                            <option {{ old('day_of_week') === $day ? 'selected' : '' }}>{{ $day }}</option>
                        @endforeach
                    </x-form.select>
                </x-form.field>
                <x-form.field label="Start Time" error="start_time">
                    <x-form.input type="time" name="start_time" value="{{ old('start_time') }}" />
                </x-form.field>
            </div>

            <x-form.field label="Location" error="location">
                <x-form.input name="location" placeholder="e.g., Main Sanctuary" value="{{ old('location') }}" />
            </x-form.field>

        </x-modal.body>
        <x-modal.footer>
            <x-modal.close-button modalId="add-service-modal" text="Cancel" />
            <x-button type="submit" variant="primary">
                <i class='bx bx-save'></i> Save Service
            </x-button>
        </x-modal.footer>
    </form>
</x-modal.dialog>
