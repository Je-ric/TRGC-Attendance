@extends('layouts.app')

@section('content')
<div style="display:flex;flex-direction:column;gap:24px">

    <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:12px">
        <div>
            <div class="page-eyebrow">Attendance</div>
            <h1 class="page-title" style="font-size:26px;margin:4px 0 0">Records</h1>
        </div>
        <a href="{{ route('attendance.index') }}" class="btn btn-ghost">
            <i class='bx bx-left-arrow-alt'></i> Dashboard
        </a>
    </div>

    @if(empty($typeSummaries))
        <div class="card" style="padding:48px;text-align:center">
            <i class='bx bx-folder-open' style="font-size:40px;color:var(--muted);display:block;margin-bottom:12px"></i>
            <p style="font-size:15px;font-weight:600;color:var(--ink-muted);margin:0 0 4px">No records yet</p>
            <p style="font-size:13px;color:var(--ink-faint);margin:0">Start by running a check-in from the dashboard.</p>
        </div>
    @endif

    @foreach($typeSummaries as $typeSummary)
        <div class="card" style="overflow:hidden">
            <div style="padding:18px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;gap:12px">
                <h3 class="page-title" style="font-size:17px;margin:0;color:var(--ink)">
                    {{ $typeSummary['type']->name }}
                </h3>
                <div style="display:flex;gap:16px;font-size:12px;color:var(--ink-faint)">
                    <span><strong style="color:var(--ink)">{{ $typeSummary['totalSessions'] }}</strong> sessions</span>
                    <span><strong style="color:var(--ink)">{{ $typeSummary['totalAttendees'] }}</strong> total attendees</span>
                </div>
            </div>

            <div style="padding:12px 20px;display:flex;flex-direction:column;gap:8px">
                @foreach($typeSummary['sessions'] as $s)
                    <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 12px;border-radius:8px;background:var(--surface);gap:12px;flex-wrap:wrap">
                        <div style="display:flex;align-items:center;gap:10px">
                            <div style="width:8px;height:8px;border-radius:50%;background:var(--red);flex-shrink:0"></div>
                            <div>
                                <div style="font-size:13.5px;font-weight:600;color:var(--ink)">
                                    {{ $s['session']->date->format('M d, Y') }}
                                </div>
                                @if($s['session']->service_name)
                                    <div style="font-size:11.5px;color:var(--ink-faint)">{{ $s['session']->service_name }}</div>
                                @endif
                            </div>
                        </div>
                        <div style="display:flex;align-items:center;gap:12px;font-size:12px;color:var(--ink-muted)">
                            <span><strong style="color:var(--ink)">{{ $s['attendeeCount'] }}</strong> present</span>
                            @foreach($s['categoryCounts'] as $cat => $count)
                                <span class="badge badge-muted">{{ $cat }}: {{ $count }}</span>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
</div>
@endsection
