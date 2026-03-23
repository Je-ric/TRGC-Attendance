@extends('layouts.app')

@section('content')
<div style="display:flex;flex-direction:column;gap:24px">

    {{-- Header --}}
    <div style="display:flex;flex-wrap:wrap;align-items:flex-start;justify-content:space-between;gap:16px">
        <div>
            <div class="page-eyebrow">Dashboard</div>
            <h1 class="page-title" style="font-size:26px;margin:4px 0 6px">Attendance</h1>
            <p style="font-size:13px;color:var(--ink-faint);margin:0">Track services and manage congregation attendance.</p>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap">
            <a href="{{ route('attendance.records') }}" class="btn btn-ghost">
                <i class='bx bx-folder-open'></i> Records
            </a>
            <a href="{{ route('people.index') }}" class="btn btn-ghost">
                <i class='bx bx-group'></i> People
            </a>
            <button onclick="document.getElementById('type-modal').classList.remove('hidden')" class="btn btn-primary">
                <i class='bx bx-plus'></i> Add Service
            </button>
        </div>
    </div>

    @if(session()->has('success'))
        <div class="toast-success" style="display:flex;align-items:center;gap:8px">
            <i class='bx bx-check-circle'></i> {{ session('success') }}
        </div>
    @endif

    {{-- Service Cards --}}
    @if($types->isEmpty())
        <div class="card" style="padding:48px;text-align:center">
            <i class='bx bx-calendar-x' style="font-size:40px;color:var(--muted);display:block;margin-bottom:12px"></i>
            <p style="font-size:15px;font-weight:600;color:var(--ink-muted);margin:0 0 4px">No services yet</p>
            <p style="font-size:13px;color:var(--ink-faint);margin:0">Click "Add Service" to create your first one.</p>
        </div>
    @else
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px">
            @foreach($types as $type)
                @php $latestSession = $type->sessions->first(); @endphp
                <div class="card" style="padding:20px;display:flex;flex-direction:column;gap:14px">
                    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px">
                        <div>
                            <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.08em;color:var(--red);margin-bottom:4px">Service</div>
                            <h3 style="font-family:'Sora',sans-serif;font-size:17px;font-weight:700;color:var(--ink);margin:0">{{ $type->name }}</h3>
                            <p style="font-size:12px;color:var(--ink-faint);margin:3px 0 0">{{ $type->day_of_week ?? 'Flexible Schedule' }}</p>
                        </div>
                        @if($latestSession)
                            <span class="badge badge-muted">{{ $latestSession->date->format('M d') }}</span>
                        @endif
                    </div>

                    @if($latestSession?->service_name)
                        <div style="font-size:12px;color:var(--ink-muted);background:var(--surface);border-radius:6px;padding:6px 10px">
                            Latest: {{ $latestSession->service_name }}
                        </div>
                    @endif

                    <hr class="ui-divider">

                    <div style="display:flex;align-items:center;justify-content:space-between">
                        <a href="{{ route('attendance.show', $type) }}" class="btn btn-primary" style="font-size:12px;padding:7px 12px">
                            <i class='bx bx-log-in-circle'></i> Check-in
                        </a>
                        <form method="POST" action="{{ route('attendance-types.destroy', $type) }}"
                              onsubmit="return confirm('Delete this service and all its records?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger" style="font-size:12px;padding:7px 12px">
                                <i class='bx bx-trash'></i>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection

@push('modals')
<div id="type-modal" class="hidden" style="position:fixed;inset:0;background:rgba(28,28,30,0.45);backdrop-filter:blur(3px);z-index:50;display:none;align-items:center;justify-content:center;padding:16px"
     onclick="if(event.target===this)this.style.display='none'">
    <div class="modal-box" onclick="event.stopPropagation()">
        <h3 class="page-title" style="font-size:20px;margin:0 0 4px">Add Service</h3>
        <p style="font-size:13px;color:var(--ink-faint);margin:0 0 20px">Create a new attendance service or event.</p>
        <hr class="ui-divider" style="margin-bottom:20px">
        <form method="POST" action="{{ route('attendance-types.store') }}" style="display:flex;flex-direction:column;gap:14px">
            @csrf
            <div>
                <label class="form-label">Service Name *</label>
                <input type="text" name="name" required placeholder="e.g., Sunday Service" class="ui-input">
            </div>
            <div>
                <label class="form-label">Day of Week</label>
                <select name="day_of_week" class="ui-input">
                    <option value="">Flexible Schedule</option>
                    @foreach(['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $day)
                        <option>{{ $day }}</option>
                    @endforeach
                </select>
            </div>
            <div style="display:flex;justify-content:flex-end;gap:8px;padding-top:4px">
                <button type="button" onclick="document.getElementById('type-modal').style.display='none'" class="btn btn-ghost">
                    Cancel
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class='bx bx-save'></i> Save
                </button>
            </div>
        </form>
    </div>
</div>
<script>
    document.getElementById('type-modal').addEventListener('click', function(e) {
        if (e.target === this) this.classList.add('hidden');
    });
    document.querySelector('[onclick*="type-modal"]')?.addEventListener('click', function() {
        const m = document.getElementById('type-modal');
        m.classList.remove('hidden');
        m.style.display = 'flex';
    });
</script>
@endpush
