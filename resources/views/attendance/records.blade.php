@extends('layouts.app')

@section('content')
    <div class="min-h-screen from-slate-50 to-blue-50 py-8 px-4">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-3xl font-serif font-bold text-slate-800 mb-8 text-center">Attendance Records</h2>

            <div class="space-y-8">
                @foreach($typeSummaries as $typeSummary)
                    <div class="bg-white rounded-xl shadow-lg p-6 border border-slate-200 hover:shadow-xl transition-shadow duration-300">
                        <h3 class="font-serif font-bold text-xl text-slate-800 mb-4">{{ $typeSummary['type']->name }}</h3>

                        <div class="space-y-4">
                            @foreach($typeSummary['sessions'] as $sessionSummary)
                                <div class="border-l-4 border-indigo-500 pl-6 py-3 bg-slate-50 rounded-r-lg">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <div class="font-semibold text-slate-800">
                                                {{ $sessionSummary['session']->date->format('M d, Y') }}
                                                @if($sessionSummary['session']->service_name)
                                                    <span class="text-indigo-600 ml-2">| {{ $sessionSummary['session']->service_name }}</span>
                                                @endif
                                            </div>
                                            <div class="text-sm text-slate-600 mt-2">
                                                Total: <strong class="text-slate-800">{{ $sessionSummary['attendeeCount'] }}</strong>
                                                @if($sessionSummary['categoryCounts']->isNotEmpty())
                                                    <span class="mx-2">|</span>
                                                    @foreach($sessionSummary['categoryCounts'] as $cat => $count)
                                                        <span class="text-slate-700">{{ $cat }}: {{ $count }}</span>
                                                        @if(!$loop->last), @endif
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6 pt-4 border-t border-slate-200 text-sm text-slate-600">
                            Total Sessions: <strong class="text-slate-800">{{ $typeSummary['totalSessions'] }}</strong>
                            <span class="mx-2">|</span>
                            Total Attendees: <strong class="text-slate-800">{{ $typeSummary['totalAttendees'] }}</strong>
                        </div>
                    </div>
                @endforeach
            </div>

            @if(empty($typeSummaries))
                <div class="bg-white rounded-xl shadow-lg p-8 text-center text-slate-500 border border-slate-200">
                    <p class="text-lg">No attendance sessions yet.</p>
                    <p class="text-sm mt-2">Start by adding an event or service to track attendance.</p>
                </div>
            @endif
        </div>
    </div>
@endsection
