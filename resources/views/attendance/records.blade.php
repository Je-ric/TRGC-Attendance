@extends('layouts.app')

@section('content')
    <div class="space-y-7">
        <div class="flex items-center justify-between">
            <h2 class="brand-font text-3xl text-[#6B0F1A]">Attendance Records</h2>
            <a href="{{ route('attendance.index') }}" class="text-sm font-medium text-[#6B0F1A] hover:underline">&larr; Dashboard</a>
        </div>

        @if(empty($typeSummaries))
            <div class="rounded-2xl border border-[#D4AF37]/35 bg-white p-8 text-center text-slate-600">
                <p class="text-lg">No attendance sessions yet.</p>
                <p class="text-sm mt-2">Start by creating an event or service from the dashboard.</p>
            </div>
        @endif

        <div class="space-y-6">
            @foreach($typeSummaries as $typeSummary)
                <section class="rounded-2xl border border-[#D4AF37]/30 bg-white p-6 shadow-sm">
                    <h3 class="brand-font text-2xl text-[#111111] mb-4">{{ $typeSummary['type']->name }}</h3>

                    <div class="space-y-3">
                        @foreach($typeSummary['sessions'] as $sessionSummary)
                            <article class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                                <div class="font-semibold text-[#6B0F1A]">
                                    {{ $sessionSummary['session']->date->format('M d, Y') }}
                                    @if($sessionSummary['session']->service_name)
                                        <span class="text-slate-700 ml-2">| {{ $sessionSummary['session']->service_name }}</span>
                                    @endif
                                </div>
                                <div class="text-sm text-slate-700 mt-2">
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

                    <div class="mt-5 pt-4 border-t border-slate-200 text-sm text-slate-700">
                        Total Sessions: <strong>{{ $typeSummary['totalSessions'] }}</strong>
                        <span class="mx-2">|</span>
                        Total Attendees: <strong>{{ $typeSummary['totalAttendees'] }}</strong>
                    </div>
                </section>
            @endforeach
        </div>
    </div>
@endsection
