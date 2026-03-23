<div>
    <button wire:click="open" class="btn btn-primary">
        <i class='bx bx-user-plus'></i> Add Person
    </button>

    @if($show)
        <div style="position:fixed;inset:0;background:rgba(28,28,30,0.45);backdrop-filter:blur(3px);z-index:50;display:flex;align-items:center;justify-content:center;padding:16px">
            <div style="background:#fff;border-radius:14px;box-shadow:0 8px 32px rgba(28,28,30,0.13);width:100%;max-width:460px;padding:28px">
                <h2 class="page-title" style="font-size:20px;margin:0 0 4px">{{ $editing ? 'Edit Person' : 'Add Person' }}</h2>
                <p style="font-size:13px;color:var(--ink-faint);margin:0 0 20px">{{ $editing ? 'Update member details.' : 'Add a new congregation member.' }}</p>
                <hr class="ui-divider" style="margin-bottom:18px">

                <div style="display:flex;flex-direction:column;gap:12px">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
                        <div>
                            <label class="form-label">First Name</label>
                            <input wire:model.defer="first_name" placeholder="First name" class="ui-input">
                        </div>
                        <div>
                            <label class="form-label">Last Name</label>
                            <input wire:model.defer="last_name" placeholder="Last name" class="ui-input">
                        </div>
                    </div>
                    <div>
                        <label class="form-label">Birthdate</label>
                        <input type="date" wire:model.defer="birthdate" max="{{ $today }}" class="ui-input">
                    </div>
                    <div>
                        <label class="form-label">Category</label>
                        <select wire:model.defer="category" class="ui-input">
                            <option value="">Auto (by age)</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Contact Number</label>
                        <input wire:model.defer="contact_number" placeholder="Contact number" class="ui-input">
                    </div>
                    <div>
                        <label class="form-label">Address</label>
                        <input wire:model.defer="address" placeholder="Address" class="ui-input">
                    </div>
                    <div>
                        <label class="form-label">Family</label>
                        <select wire:model.defer="family_id" class="ui-input">
                            <option value="">No family</option>
                            @foreach($families as $family)
                                <option value="{{ $family->id }}">{{ $family->family_name }}</option>
                            @endforeach
                        </select>
                        @error('family_id') <span style="font-size:12px;color:var(--red);margin-top:4px;display:block">{{ $message }}</span> @enderror
                    </div>
                    <div style="display:flex;justify-content:flex-end;gap:8px;padding-top:6px">
                        <button wire:click="$set('show', false)" class="btn btn-ghost">Cancel</button>
                        <button wire:click="save" class="btn btn-primary"><i class='bx bx-save'></i> Save</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
