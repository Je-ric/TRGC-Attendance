@extends('layouts.app')

@section('content')
<div class="flex flex-col gap-6">

    <x-page-header icon="bx-home-alt-2" title="Dashboard"
        desc="Overview of congregation attendance — {{ now()->format('l, F j, Y') }}.">
        <x-button href="{{ route('attendance.index') }}" variant="primary">
            <i class='bx bx-log-in-circle'></i> Check-in
        </x-button>
    </x-page-header>

    {{-- ── KPI Row ──────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <x-statistic-card variant="primary" icon="bx-users"          title="Total People"    value="{{ $totalPeople }}" />
        <x-statistic-card variant="dark"    icon="bx-id-card"        title="Members"         value="{{ $totalMembers }}" />
        <x-statistic-card variant="muted"   icon="bx-buildings"      title="Families"        value="{{ $totalFamilies }}" />
        <x-statistic-card variant="muted"   icon="bx-calendar-check" title="Sessions"        value="{{ $totalSessions }}" />
        <x-statistic-card variant="muted"   icon="bx-trending-up"    title="Avg / Session"   value="{{ $avgAttendance }}" />
        <x-statistic-card variant="muted"   icon="bx-user-plus"      title="New This Month"  value="{{ $newThisMonth }}" />
    </div>

    {{-- ── Last Session + Trend ────────────────────────────────────── --}}
    <div class="grid gap-4 lg:grid-cols-3">

        {{-- Last Session --}}
        <x-card color="red" :padding="false">
            <div class="px-4 py-3 border-b border-[#e4e0e2] bg-[#fff0f0]">
                <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#ed213a] mb-1">Most Recent</p>
                <h3 class="font-['Oswald'] text-[16px] font-bold text-[#1c1c1e]">Last Session</h3>
            </div>
            <div class="p-4">
                @if($lastSession)
                    <div class="flex items-start justify-between gap-3 mb-4">
                        <div>
                            <p class="font-['Oswald'] text-[15px] font-bold text-[#1c1c1e]">
                                {{ $lastSession->attendanceType?->name ?? '—' }}
                            </p>
                            @if($lastSession->service_name)
                                <p class="text-[12px] text-[#6b6570] mt-0.5">{{ $lastSession->service_name }}</p>
                            @endif
                            <p class="text-[12px] text-[#a09aa4] mt-1 flex items-center gap-1">
                                <i class='bx bx-calendar text-[11px]'></i>
                                {{ $lastSession->date->format('l, F j, Y') }}
                            </p>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="font-['Oswald'] text-[36px] font-bold text-[#ed213a] leading-none">
                                {{ $lastSessionCount }}
                            </p>
                            <p class="text-[11px] font-bold uppercase tracking-[0.1em] text-[#a09aa4]">present</p>
                        </div>
                    </div>

                    @if(!empty($categoryBreakdown))
                        <div class="border-t border-[#e4e0e2] pt-3 flex flex-col gap-1.5">
                            @foreach($categoryBreakdown as $cat => $count)
                                <div class="flex items-center justify-between">
                                    <span class="text-[12px] text-[#6b6570]">{{ $cat }}</span>
                                    <div class="flex items-center gap-2">
                                        {{-- Dynamic width must stay as style= --}}
                                        <div class="h-1.5 rounded-full bg-[#ed213a]"
                                             style="width:{{ max(8, ($count / max($lastSessionCount,1)) * 80) }}px"></div>
                                        <span class="font-semibold text-[12px] text-[#1c1c1e] w-6 text-right">{{ $count }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                @else
                    <p class="text-[13px] text-[#a09aa4] italic">No sessions recorded yet.</p>
                @endif
            </div>
        </x-card>

        {{-- Attendance Trend --}}
        <x-card title="Attendance Trend" icon="trending-up" color="slate" class="lg:col-span-2">
            @if($recentSessions->isEmpty())
                <p class="text-[13px] text-[#a09aa4] italic">No sessions yet.</p>
            @else
                @php $maxCount = $recentSessions->max('count') ?: 1; @endphp
                <div class="flex items-end gap-2 h-32">
                    @foreach($recentSessions as $s)
                        @php $pct = max(4, ($s['count'] / $maxCount) * 100); @endphp
                        <div class="flex-1 flex flex-col items-center gap-1 group">
                            <span class="text-[11px] font-bold text-[#1c1c1e] opacity-0 group-hover:opacity-100 transition-opacity">
                                {{ $s['count'] }}
                            </span>
                            {{-- Dynamic height must stay as style= --}}
                            <div class="w-full rounded-t-md bg-brand-gradient transition-all duration-300"
                                 style="height:{{ $pct }}%;min-height:4px"></div>
                            <span class="text-[10px] text-[#a09aa4] text-center leading-tight">{{ $s['label'] }}</span>
                        </div>
                    @endforeach
                </div>
                <div class="mt-3 pt-3 border-t border-[#e4e0e2] flex items-center justify-between text-[12px] text-[#a09aa4]">
                    <span>Last {{ $recentSessions->count() }} sessions</span>
                    <span>Peak: <strong class="text-[#1c1c1e]">{{ $recentSessions->max('count') }}</strong></span>
                    <span>Avg: <strong class="text-[#1c1c1e]">{{ round($recentSessions->avg('count')) }}</strong></span>
                </div>
            @endif
        </x-card>

    </div>

    {{-- ── Services Overview ───────────────────────────────────────── --}}
    <x-card title="Services Overview" icon="check-shield" color="slate" :padding="false">
        <x-table.container class="rounded-none border-0 shadow-none">
            <x-table.table>
                <x-table.head>
                    <tr>
                        <x-table.th>Service</x-table.th>
                        <x-table.th>Schedule</x-table.th>
                        <x-table.th>Location</x-table.th>
                        <x-table.th align="center">Sessions</x-table.th>
                        <x-table.th>Last Held</x-table.th>
                        <x-table.th align="center">Last Count</x-table.th>
                        <x-table.th align="right">Action</x-table.th>
                    </tr>
                </x-table.head>
                <x-table.body>
                    @forelse($serviceSummaries as $s)
                        <x-table.row :hover="true">
                            <x-table.td class="font-semibold">{{ $s['type']->name }}</x-table.td>
                            <x-table.td class="text-[#6b6570]">
                                @if($s['type']->day_of_week)
                                    {{ $s['type']->day_of_week }}s
                                    @if($s['type']->start_time)
                                        · {{ \Carbon\Carbon::parse($s['type']->start_time)->format('g:i A') }}
                                    @endif
                                @else —
                                @endif
                            </x-table.td>
                            <x-table.td class="text-[#6b6570]">{{ $s['type']->location ?? '—' }}</x-table.td>
                            <x-table.td align="center">
                                <span class="font-bold text-[#1c1c1e]">{{ $s['sessions'] }}</span>
                            </x-table.td>
                            <x-table.td class="text-[#6b6570]">
                                {{ $s['last_date'] ? $s['last_date']->format('M d, Y') : '—' }}
                            </x-table.td>
                            <x-table.td align="center">
                                @if($s['last_count'] > 0)
                                    <x-feedback-status.status-indicator status="present">
                                        {{ $s['last_count'] }}
                                    </x-feedback-status.status-indicator>
                                @else
                                    <span class="text-[#a09aa4]">—</span>
                                @endif
                            </x-table.td>
                            <x-table.td align="right">
                                <x-button href="{{ route('attendance.show', $s['type']) }}" variant="table-edit">
                                    <i class='bx bx-log-in-circle'></i> Check-in
                                </x-button>
                            </x-table.td>
                        </x-table.row>
                    @empty
                        <x-table.empty colspan="7" message="No services created yet." />
                    @endforelse
                </x-table.body>
            </x-table.table>
        </x-table.container>
    </x-card>

    {{-- ── Top Attendees + Streaks ─────────────────────────────────── --}}
    <div class="grid gap-4 lg:grid-cols-2">

        {{-- Top Attendees --}}
        <x-card title="Top Attendees" icon="trophy" color="red" :padding="false">
            @if($topAttendees->isEmpty())
                <div class="p-4">
                    <p class="text-[13px] text-[#a09aa4] italic">No attendance data yet.</p>
                </div>
            @else
                <div class="divide-y divide-[#ede9eb]">
                    @foreach($topAttendees as $i => $summary)
                        <div class="flex items-center gap-3 px-4 py-3 hover:bg-[#fff5f5] transition-colors">
                            <div class="w-6 text-center shrink-0">
                                @if($i === 0) <span class="text-base">🥇</span>
                                @elseif($i === 1) <span class="text-base">🥈</span>
                                @elseif($i === 2) <span class="text-base">🥉</span>
                                @else <span class="text-[12px] font-bold text-[#a09aa4]">{{ $i + 1 }}</span>
                                @endif
                            </div>
                            <div class="bg-brand-gradient w-8 h-8 rounded-full flex items-center justify-center
                                        text-white text-[11px] font-bold shrink-0">
                                {{ strtoupper(substr($summary->person->first_name, 0, 1)) }}{{ strtoupper(substr($summary->person->last_name, 0, 1)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-[13px] font-semibold text-[#1c1c1e] truncate">{{ $summary->person->full_name }}</p>
                                <p class="text-[11px] text-[#a09aa4]">
                                    {{ $summary->total_present }} / {{ $summary->total_sessions }} sessions
                                </p>
                            </div>
                            <div class="text-right shrink-0">
                                <p class="font-['Oswald'] text-[15px] font-bold text-[#ed213a]">
                                    {{ number_format($summary->attendance_rate, 0) }}%
                                </p>
                                <div class="w-16 h-1.5 bg-[#f5f4f6] rounded-full mt-1">
                                    {{-- Dynamic width stays as style= --}}
                                    <div class="h-1.5 rounded-full bg-[#ed213a]"
                                         style="width:{{ $summary->attendance_rate }}%"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-card>

        <div class="flex flex-col gap-4">

            {{-- Streak Leaders --}}
            <x-card title="Current Streaks 🔥" icon="trending-up" color="slate">
                @if($streakLeaders->isEmpty())
                    <p class="text-[13px] text-[#a09aa4] italic">No streak data yet.</p>
                @else
                    <div class="flex flex-col gap-2">
                        @foreach($streakLeaders as $summary)
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex items-center gap-2 min-w-0">
                                    <div class="bg-brand-gradient w-7 h-7 rounded-full flex items-center justify-center
                                                text-white text-[10px] font-bold shrink-0">
                                        {{ strtoupper(substr($summary->person->first_name, 0, 1)) }}
                                    </div>
                                    <span class="text-[13px] font-medium text-[#1c1c1e] truncate">{{ $summary->person->full_name }}</span>
                                </div>
                                <div class="flex items-center gap-1 shrink-0">
                                    <span class="font-['Oswald'] text-[15px] font-bold text-[#ed213a]">{{ $summary->streak }}</span>
                                    <span class="text-[11px] text-[#a09aa4]">in a row</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-card>

            {{-- Upcoming Birthdays --}}
            @if($upcomingBirthdays->isNotEmpty())
                <x-card title="Birthdays This Week 🎂" icon="cake" color="slate">
                    <div class="flex flex-col gap-2">
                        @foreach($upcomingBirthdays as $person)
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex items-center gap-2 min-w-0">
                                    <div class="bg-brand-gradient w-7 h-7 rounded-full flex items-center justify-center
                                                text-white text-[10px] font-bold shrink-0">
                                        {{ strtoupper(substr($person->first_name, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-[13px] font-medium text-[#1c1c1e] truncate">{{ $person->full_name }}</p>
                                        <p class="text-[11px] text-[#a09aa4]">
                                            {{ $person->birthdate->copy()->setYear(now()->year)->format('M d') }}
                                        </p>
                                    </div>
                                </div>
                                @if($person->days_until == 0)
                                    <x-feedback-status.status-indicator status="today">Today!</x-feedback-status.status-indicator>
                                @elseif($person->days_until == 1)
                                    <x-feedback-status.status-indicator status="tomorrow">Tomorrow</x-feedback-status.status-indicator>
                                @else
                                    <span class="text-[12px] font-semibold text-[#6b6570]">in {{ $person->days_until }}d</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </x-card>
            @endif

        </div>
    </div>

    {{-- ── Inactive Members ─────────────────────────────────────────── --}}
    @if($inactiveMembers->isNotEmpty())
        <x-card color="amber" :padding="false">
            <x-slot:title>
                <span class="flex items-center gap-2">
                    <i class='bx bx-error text-[#f59e0b]'></i>
                    Inactive Members
                    <x-feedback-status.status-indicator variant="amber">
                        {{ $inactiveMembers->count() }} not seen in 30+ days
                    </x-feedback-status.status-indicator>
                </span>
            </x-slot:title>

            <x-table.container class="rounded-none border-0 shadow-none">
                <x-table.table>
                    <x-table.head>
                        <tr>
                            <x-table.th>Person</x-table.th>
                            <x-table.th>Status</x-table.th>
                            <x-table.th>Last Seen</x-table.th>
                            <x-table.th align="center">Attendance Rate</x-table.th>
                            <x-table.th>Contact</x-table.th>
                        </tr>
                    </x-table.head>
                    <x-table.body>
                        @foreach($inactiveMembers as $person)
                            @php
                                $summary  = $person->attendanceSummary;
                                $lastSeen = $summary?->last_attended_at;
                                $daysAgo  = $lastSeen ? now()->diffInDays($lastSeen) : null;
                            @endphp
                            <x-table.row :hover="true">
                                <x-table.td>
                                    <x-person.card :person="$person" :showFamily="false" :showActions="false" compact />
                                </x-table.td>
                                <x-table.td>
                                    <x-feedback-status.status-indicator variant="amber">
                                        {{ $person->membership_status }}
                                    </x-feedback-status.status-indicator>
                                </x-table.td>
                                <x-table.td>
                                    @if($lastSeen)
                                        <p class="text-[13px] text-[#1c1c1e]">{{ $lastSeen->format('M d, Y') }}</p>
                                        <p class="text-[11px] text-[#a09aa4]">{{ $daysAgo }} days ago</p>
                                    @else
                                        <span class="text-[12px] text-[#a09aa4] italic">Never recorded</span>
                                    @endif
                                </x-table.td>
                                <x-table.td align="center">
                                    @if($summary)
                                        <div class="flex items-center gap-2 justify-center">
                                            <div class="w-16 h-1.5 bg-[#f5f4f6] rounded-full">
                                                {{-- Dynamic width stays as style= --}}
                                                <div class="h-1.5 rounded-full bg-[#f59e0b]"
                                                     style="width:{{ $summary->attendance_rate }}%"></div>
                                            </div>
                                            <span class="text-[12px] font-semibold text-[#92400e]">
                                                {{ number_format($summary->attendance_rate, 0) }}%
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-[#a09aa4]">—</span>
                                    @endif
                                </x-table.td>
                                <x-table.td>
                                    @if($person->contact_number)
                                        <p class="text-[12px] text-[#1c1c1e]">{{ $person->contact_number }}</p>
                                    @endif
                                    @if($person->email)
                                        <p class="text-[11px] text-[#a09aa4]">{{ $person->email }}</p>
                                    @endif
                                    @if(!$person->contact_number && !$person->email)
                                        <span class="text-[#a09aa4]">—</span>
                                    @endif
                                </x-table.td>
                            </x-table.row>
                        @endforeach
                    </x-table.body>
                </x-table.table>
            </x-table.container>
        </x-card>
    @endif

</div>
@endsection
