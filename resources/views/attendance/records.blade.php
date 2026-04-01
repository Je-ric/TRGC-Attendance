@extends('layouts.app')

@section('content')
<div class="flex flex-col gap-6">

    <x-page-header icon="bx-folder-open" title="Records" desc="All attendance sessions grouped by service.">
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
            <x-slot:title>{{ $typeSummary['type']->name }}</x-slot:title>
            <x-slot:action>
                <span class="text-[12px] text-[#a09aa4]">
                    <strong class="text-[#1c1c1e]">{{ $typeSummary['totalSessions'] }}</strong> sessions ·
                    <strong class="text-[#1c1c1e]">{{ $typeSummary['totalAttendees'] }}</strong> total
                </span>
            </x-slot:action>

            <x-table.container class="rounded-none border-0 shadow-none">
                <x-table.table>
                    <x-table.head>
                        <tr>
                            <x-table.th>Date</x-table.th>
                            <x-table.th>Service</x-table.th>
                            <x-table.th>Present</x-table.th>
                            <x-table.th>Breakdown</x-table.th>
                        </tr>
                    </x-table.head>
                    <x-table.body>
                        @forelse($typeSummary['sessions'] as $s)
                            <x-table.row :hover="true">
                                <x-table.td>
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-[#ed213a] shrink-0"></span>
                                        <span class="font-semibold">{{ $s['session']->date->format('M d, Y') }}</span>
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
