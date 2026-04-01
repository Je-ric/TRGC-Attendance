<?php

namespace App\Http\Controllers;

use App\Models\AttendanceType;
use App\Models\AttendanceSession;

class AttendanceController extends Controller
{
    public function index()
    {
        $types = AttendanceType::with(['sessions' => fn($q) =>
            $q->orderByDesc('date')->orderByDesc('created_at')
        ])->orderBy('name')->get();

        return view('attendance.index', compact('types'));
    }

    public function show(AttendanceType $type)
    {
        return view('attendance.show', compact('type'));
    }

    public function records()
    {
        $sessions = AttendanceSession::with(['attendanceType', 'attendanceRecords.person'])
            ->orderByDesc('date')
            ->orderByDesc('created_at')
            ->get();

        $typeSummaries = $sessions->groupBy('attendance_type_id')
            ->map(function ($typeSessions) {
                $type           = $typeSessions->first()->attendanceType;
                $totalAttendees = $typeSessions->sum(fn($s) => $s->attendanceRecords->count());
                $totalSessions  = $typeSessions->count();

                $sessionSummaries = $typeSessions->map(function ($session) {
                    return [
                        'session'        => $session,
                        'attendeeCount'  => $session->attendanceRecords->count(),
                        'categoryCounts' => $session->attendanceRecords
                            ->groupBy(fn($r) => $r->person?->effective_category ?? 'Unknown')
                            ->map->count(),
                    ];
                })->values()->all();

                return [
                    'type'           => $type,
                    'sessions'       => $sessionSummaries,
                    'totalSessions'  => $totalSessions,
                    'totalAttendees' => $totalAttendees,
                    'avgAttendance'  => $totalSessions > 0 ? round($totalAttendees / $totalSessions) : 0,
                ];
            })->values()->all();

        return view('attendance.records', compact('typeSummaries'));
    }
}
