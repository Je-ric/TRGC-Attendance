<div style="display:flex;flex-direction:column;gap:24px">

    <div style="display:flex;flex-wrap:wrap;align-items:flex-start;justify-content:space-between;gap:16px">
        <div>
            <div class="page-eyebrow">Directory</div>
            <h1 class="page-title" style="font-size:26px;margin:4px 0 6px">Families</h1>
            <p style="font-size:13px;color:var(--ink-faint);margin:0">Manage family groups and member associations.</p>
        </div>
        <button wire:click="open" class="btn btn-primary">
            <i class='bx bx-plus'></i> Add Family
        </button>
    </div>

    {{-- Stats --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:12px">
        <x-statistic-card variant="primary" icon="bx-buildings" title="Total Families" value="{{ $families->count() }}" />
        @foreach($categories as $cat)
            <x-statistic-card variant="muted" icon="bx-user" :title="$cat" :value="$categoryCounts[$cat] ?? 0" />
        @endforeach
    </div>

    {{-- Family list --}}
    @if($families->isEmpty())
        <div class="card" style="padding:48px;text-align:center">
            <i class='bx bx-home-heart' style="font-size:40px;color:var(--muted);display:block;margin-bottom:12px"></i>
            <p style="font-size:15px;font-weight:600;color:var(--ink-muted);margin:0 0 4px">No families yet</p>
            <p style="font-size:13px;color:var(--ink-faint);margin:0">Click "Add Family" to create one.</p>
        </div>
    @else
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:14px">
            @foreach($families as $family)
                <div class="card" style="padding:18px;display:flex;flex-direction:column;gap:12px">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:8px">
                        <div style="min-width:0">
                            <h4 style="font-size:15px;font-weight:700;color:var(--ink);margin:0;display:flex;align-items:center;gap:6px">
                                <i class='bx bx-buildings' style="color:var(--red);flex-shrink:0"></i>
                                {{ $family->family_name }}
                            </h4>
                            @if($family->address)
                                <p style="font-size:12px;color:var(--ink-faint);margin:3px 0 0">{{ $family->address }}</p>
                            @endif
                            @if($family->contact_person)
                                <p style="font-size:12px;color:var(--ink-faint);margin:2px 0 0">{{ $family->contact_person }}</p>
                            @endif
                        </div>
                        <div style="display:flex;gap:6px;flex-shrink:0">
                            <button wire:click="edit({{ $family->id }})" class="btn btn-edit" style="font-size:12px;padding:6px 10px">
                                <i class='bx bx-edit-alt'></i>
                            </button>
                            <button wire:click="deleteFamily({{ $family->id }})"
                                    onclick="return confirm('Remove family and unlink all members?')"
                                    class="btn btn-danger" style="font-size:12px;padding:6px 10px">
                                <i class='bx bx-trash'></i>
                            </button>
                        </div>
                    </div>

                    <div style="font-size:12px;color:var(--ink-faint)">
                        <strong style="color:var(--ink)">{{ $family->people_count }}</strong>
                        {{ Str::plural('person', $family->people_count) }}
                    </div>

                    @if($family->people->count() > 0)
                        <hr class="ui-divider">
                        <ul style="list-style:none;margin:0;padding:0;display:flex;flex-direction:column;gap:2px">
                            @foreach($family->people as $person)
                                <li style="font-size:12.5px;color:var(--ink);padding:4px 0;border-bottom:1px solid var(--border-soft);display:flex;justify-content:space-between">
                                    {{ $person->full_name }}
                                    <span style="color:var(--ink-faint);font-size:11px">{{ $person->effective_category }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    {{-- Modal --}}
    @if($show)
        <div style="position:fixed;inset:0;background:rgba(28,28,30,0.45);backdrop-filter:blur(3px);z-index:50;display:flex;align-items:center;justify-content:center;padding:16px"
             wire:click="$set('show', false)">
            <div style="background:#fff;border-radius:14px;box-shadow:0 8px 32px rgba(28,28,30,0.13);width:100%;max-width:460px;padding:28px"
                 wire:click.stop>
                <h2 class="page-title" style="font-size:20px;margin:0 0 4px">{{ $editing ? 'Edit Family' : 'Add Family' }}</h2>
                <p style="font-size:13px;color:var(--ink-faint);margin:0 0 20px">{{ $editing ? 'Update family details.' : 'Create a new family group.' }}</p>
                <hr class="ui-divider" style="margin-bottom:18px">

                <div style="display:flex;flex-direction:column;gap:12px">
                    <div>
                        <label class="form-label">Family Name *</label>
                        <input type="text" wire:model.defer="family_name" placeholder="e.g., Santos Family" class="ui-input">
                        @error('family_name') <span style="font-size:12px;color:var(--red);margin-top:4px;display:block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="form-label">Address</label>
                        <input type="text" wire:model.defer="address" placeholder="Address" class="ui-input">
                    </div>
                    <div>
                        <label class="form-label">Contact Person</label>
                        <input type="text" wire:model.defer="contact_person" placeholder="Contact person name" class="ui-input">
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
