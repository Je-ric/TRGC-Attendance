@extends('layouts.app')

@section('content')
<div class="flex flex-col gap-6">

    <x-page-header icon="bx-folder-open" title="Attendance Records" desc="All sessions grouped by service.">
        <x-button href="{{ route('attendance.index') }}" variant="back">
            <i class='bx bx-arrow-left'></i> Dashboard
        </x-button>
    </x-page-header>

    @if(empty($typeSummaries))
        <x-empty-state icon="bx bx-folder-open" title="No records yet"
            message="Start by running a check-in from the dashboard." />
    @endif

    @foreach($typeSummaries as $typeSummary)
        <x-card :padding="false">
            {{-- Service header --}}
            <div class="px-4 py-3 border-b border-[#e4e0e2] bg-[#f5f4f6] flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h3 class="page-title text-[15px]">{{ $typeSummary['type']->name }}</h3>
                    <div class="flex gap-3 mt-0.5 flex-wrap">
                        @if($typeSummary['type']->location)
                            <span class="text-[12px] text-[#a09aa4] flex items-center gap-1">
                                <i class='bx bx-map-pin text-[11px]'></i> {{ $typeSummary['type']->location }}
                            </span>
                        @endif
                        @if($typeSummary['type']->day_of_week)
                            <span class="text-[12px] text-[#a09aa4] flex items-center gap-1">
                                <i class='bx bx-calendar text-[11px]'></i> {{ $typeSummary['type']->day_of_week }}s
                            </span>
                        @endif
                    </div>
                </div>
                <div class="flex gap-4 text-[12px] text-[#a09aa4]">
                    <span><strong class="text-[#1c1c1e] text-[15px]" style="font-family:'Oswald',sans-serif">{{ $typeSummary['totalSessions'] }}</strong> sessions</span>
                    <span><strong class="text-[#1c1c1e] text-[15px]" style="font-family:'Oswald',sans-serif">{{ $typeSummary['totalAttendees'] }}</strong> total attendees</span>
                    <span><strong class="text-[#1c1c1e] text-[15px]" style="font-family:'Oswald',sans-serif">{{ $typeSummary['avgAttendance'] }}</strong> avg/session</span>
                </div>
            </div>

            <x-table.container class="rounded-none border-0 shadow-none">
                <x-table.table>
                    <x-table.head>
                        <tr>
                            <x-table.th>#</x-table.th>
                            <x-table.th>Date</x-table.th>
                            <x-table.th>Sermon / Occasion</x-table.th>
                            <x-table.th>Present</x-table.th>
                            <x-table.th>Breakdown</x-table.th>
                        </tr>
                    </x-table.head>
                    <x-table.body>
                        @forelse($typeSummary['sessions'] as $idx => $s)
                            <x-table.row :hover="true">
                                <x-table.td class="text-[#a09aa4]">{{ $idx + 1 }}</x-table.td>
                                <x-table.td>
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-[#ed213a] shrink-0"></span>
                                        <div>
                                            <div class="font-semibold text-[13px]">{{ $s['session']->date->format('M d, Y') }}</div>
                                            <div class="text-[11px] text-[#a09aa4]">{{ $s['session']->date->format('l') }}</div>
                                        </div>
                                    </div>
                                </x-table.td>
                                <x-table.td class="text-[#6b6570]">
                                    {{ $s['session']->service_name ?? '—' }}
                                </x-table.td>
                                <x-table.td>
                                    <x-feedback-status.status-indicator status="present">
                                        {{ $s['attendeeCount'] }} present
                                    </x-feedback-status.status-indicator>
                                </x-table.td>
                                <x-table.td>
                                    <div class="flex gap-1.5 flex-wrap">
                                        @foreach($s['categoryCounts'] as $cat => $count)
                                            <x-feedback-status.status-indicator variant="slate">
                                                {{ $cat }}: {{ $count }}
                                            </x-feedback-status.status-indicator>
                                        @endforeach
                                    </div>
                                </x-table.td>
                            </x-table.row>
                        @empty
                            <x-table.empty colspan="4" message="No sessions recorded yet." />
                        @endforelse
                    </x-table.body>
                </x-table.table>
            </x-table.container>
        </x-card>
    @endforeach

</div>
@endsection
