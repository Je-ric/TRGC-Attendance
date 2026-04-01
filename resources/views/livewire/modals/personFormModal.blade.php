{{-- Include in: livewire/person-create.blade.php --}}
{{-- Open via: Livewire dispatch('open-modal', id: 'person-modal') --}}

<x-modal.dialog id="person-modal" maxWidth="max-w-2xl">
    <x-modal.header modalId="person-modal">
        <div class="flex items-center gap-3">
            <span class="flex items-center justify-center w-9 h-9 rounded-xl shrink-0"
                  style="background:linear-gradient(135deg,#93291e,#ed213a)">
                <i class="bx bx-user text-white text-lg leading-none"></i>
            </span>
            <div>
                <p class="text-[15px] font-bold text-[#1c1c1e]">{{ $editing ? 'Edit Person' : 'Add Person' }}</p>
                <p class="text-[12px] text-[#a09aa4]">{{ $editing ? 'Update member details.' : 'Add a new congregation member.' }}</p>
            </div>
        </div>
    </x-modal.header>

    <x-modal.body class="flex flex-col gap-5">

        {{-- ── Basic Info ── --}}
        <div>
            <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#a09aa4] mb-3">Basic Information</p>
            <div class="grid grid-cols-2 gap-3">
                <x-form.field label="First Name" :isRequired="true" error="first_name">
                    <x-form.input wire:model="first_name" placeholder="First name" />
                </x-form.field>
                <x-form.field label="Last Name" :isRequired="true" error="last_name">
                    <x-form.input wire:model="last_name" placeholder="Last name" />
                </x-form.field>
            </div>
        </div>

        {{-- ── Personal Details ── --}}
        <div>
            <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#a09aa4] mb-3">Personal Details</p>
            <div class="grid grid-cols-2 gap-3">
                <x-form.field label="Birthdate" error="birthdate">
                    <x-form.input type="date" wire:model="birthdate" max="{{ $today }}" />
                </x-form.field>
                <x-form.field label="Gender">
                    <x-form.select wire:model="gender">
                        <option value="">Not specified</option>
                        @foreach($genders as $g)
                            <option value="{{ $g }}">{{ $g }}</option>
                        @endforeach
                    </x-form.select>
                </x-form.field>
                <x-form.field label="Civil Status">
                    <x-form.select wire:model="civil_status">
                        <option value="">Not specified</option>
                        @foreach($civilStatuses as $cs)
                            <option value="{{ $cs }}">{{ $cs }}</option>
                        @endforeach
                    </x-form.select>
                </x-form.field>
                <x-form.field label="Category">
                    <x-form.select wire:model="category">
                        <option value="">Auto (by age)</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}">{{ $cat }}</option>
                        @endforeach
                    </x-form.select>
                </x-form.field>
            </div>
        </div>

        {{-- ── Church Info ── --}}
        <div>
            <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#a09aa4] mb-3">Church Information</p>
            <div class="grid grid-cols-2 gap-3">
                <x-form.field label="Membership Status">
                    <x-form.select wire:model="membership_status">
                        @foreach($membershipStatuses as $ms)
                            <option value="{{ $ms }}">{{ $ms }}</option>
                        @endforeach
                    </x-form.select>
                </x-form.field>
                <x-form.field label="Family">
                    <x-form.select wire:model="family_id">
                        <option value="">No family</option>
                        @foreach($families as $fam)
                            <option value="{{ $fam->id }}">{{ $fam->family_name }}</option>
                        @endforeach
                    </x-form.select>
                </x-form.field>
                <x-form.field label="Date Joined" error="joined_date">
                    <x-form.input type="date" wire:model="joined_date" max="{{ $today }}" />
                </x-form.field>
                <x-form.field label="Date of Baptism" error="date_of_baptism">
                    <x-form.input type="date" wire:model="date_of_baptism" max="{{ $today }}" />
                </x-form.field>
            </div>
        </div>

        {{-- ── Contact ── --}}
        <div>
            <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#a09aa4] mb-3">Contact & Location</p>
            <div class="grid grid-cols-2 gap-3">
                <x-form.field label="Contact Number">
                    <x-form.input wire:model="contact_number" placeholder="+63 9XX XXX XXXX" />
                </x-form.field>
                <x-form.field label="Email" error="email">
                    <x-form.input type="email" wire:model="email" placeholder="email@example.com" />
                </x-form.field>
            </div>
            <div class="mt-3">
                <x-form.field label="Address">
                    <x-form.input wire:model="address" placeholder="Home address" />
                </x-form.field>
            </div>
        </div>

        {{-- ── Notes ── --}}
        <x-form.field label="Notes">
            <x-form.textarea wire:model="notes" rows="2" placeholder="Any additional notes…" />
        </x-form.field>

    </x-modal.body>

    <x-modal.footer>
        <x-modal.close-button modalId="person-modal" text="Cancel" />
        <x-button wire:click="save" variant="primary">
            <i class='bx bx-save'></i> {{ $editing ? 'Update' : 'Save' }}
        </x-button>
    </x-modal.footer>
</x-modal.dialog>
