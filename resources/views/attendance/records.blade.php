@extends('layouts.app')

@section('content')
    <div class="space-y-7">
        <div class="flex items-center justify-between">
            <div>
                <div class="page-eyebrow">Attendance</div>
                <h2 class="page-title text-3xl text-[#6B0F1A] flex items-center gap-2">
                    <i class='bx bx-folder-open text-[#C9A84C]'></i>
                    Records
                </h2>
            </div>
            <a href="{{ route('attendance.index') }}" class="ui-btn ui-btn-ghost text-sm py-2 px-3">
                <i class='bx bx-left-arrow-alt'></i>
                Dashboard
            </a>
        </div>

        <hr class="ui-divider">

        @if(empty($typeSummaries))
            <div class="ui-card-soft p-8 text-center dm-sans" style="color:var(--ink-muted)">
                <p class="text-lg">No attendance sessions yet.</p>
                <p class="text-sm mt-2" style="color:var(--ink-faint)">Start by creating an event or service from the dashboard.</p>
            </div>
        @endif

        <div class="space-y-6">
            @foreach($typeSummaries as $typeSummary)
                <section class="ui-card p-6">
                    <h3 class="page-title text-2xl mb-4 flex items-center gap-2" style="color:var(--ink)">
                        <i class='bx bx-calendar-event text-[#6B0F1A]'></i>
                        {{ $typeSummary['type']->name }}
                    </h3>

                    <div class="space-y-3">
                        @foreach($typeSummary['sessions'] as $sessionSummary)
                            <article class="ui-card-soft p-4">
                                <div class="font-semibold" style="color:var(--maroon)">
                                    {{ $sessionSummary['session']->date->format('M d, Y') }}
                                    @if($sessionSummary['session']->service_name)
                                        <span class="badge badge-gold ml-2">{{ $sessionSummary['session']->service_name }}</span>
                                    @endif
                                </div>
                                <div class="text-sm dm-sans mt-2" style="color:var(--ink-muted)">
                                    Total: <strong>{{ $sessionSummary['attendeeCount'] }}</strong>
                                    @if($sessionSummary['categoryCounts']->isNotEmpty())
                                        <span class="mx-2">|</span>
                                        @foreach($sessionSummary['categoryCounts'] as $cat => $count)
                                            <span>{{ $cat }}: {{ $count }}</span>@if(!$loop->last), @endif
                                        @endforeach
                                    @endif
                                </div>
                            </article>
                        @endforeach
                    </div>

                    <div class="mt-5 pt-4 border-t dm-sans text-sm" style="border-color:var(--border);color:var(--ink-muted)">
                        Total Sessions: <strong>{{ $typeSummary['totalSessions'] }}</strong>
                        <span class="mx-2">|</span>
                        Total Attendees: <strong>{{ $typeSummary['totalAttendees'] }}</strong>
                    </div>
                </section>
            @endforeach
        </div>
    </div>
@endsection
