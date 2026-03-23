<div style="display:flex;flex-direction:column;gap:8px">
    <input type="text" wire:model.debounce.300ms="search" placeholder="Search by name or contact…" class="ui-input">

    <div style="border:1px solid var(--border);border-radius:8px;background:#fff;max-height:260px;overflow-y:auto">
        @forelse($results as $person)
            <label style="display:flex;align-items:center;padding:10px 12px;border-bottom:1px solid var(--border-soft);cursor:pointer;transition:background 0.1s"
                   onmouseover="this.style.background='var(--surface)'" onmouseout="this.style.background=''">
                <input type="checkbox" wire:click="toggle({{ $person->id }})"
                       style="margin-right:10px;width:15px;height:15px;accent-color:var(--red)">
                <div>
                    <div style="font-size:13.5px;font-weight:600;color:var(--ink)">{{ $person->full_name }}</div>
                    <div style="font-size:11.5px;color:var(--ink-faint)">
                        {{ $person->category ?? 'N/A' }} · {{ $person->age ?? 'N/A' }} yrs
                        @if($person->family) · {{ $person->family->family_name }} @endif
                    </div>
                </div>
            </label>
        @empty
            <p style="padding:16px;font-size:13px;color:var(--ink-faint);text-align:center;margin:0">No results found.</p>
        @endforelse
    </div>
</div>
