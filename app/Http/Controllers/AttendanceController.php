<?php

namespace App\Http\Controllers;

use App\Models\AttendanceType;
use App\Models\AttendanceSession;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    // Landing page: list of attendance types
    public function index()
    {
        $types = AttendanceType::with(['sessions' => function ($q) {
            $q->orderBy('date', 'desc')->orderBy('created_at', 'desc');
        }])->orderBy('name')->get();

        return view('attendance.index', compact('types'));
    }

    // Show sessions of a type
    public function show(AttendanceType $type)
    {
        return view('attendance.show', compact('type'));
    }

    // Show all sessions & their attendance records
    public function records()
    {
        $sessions = AttendanceSession::with(['attendanceType', 'attendanceRecords.person.family'])
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $sessionsByType = $sessions->groupBy('attendance_type_id');

        // Calculate summaries for each type
        $typeSummaries = [];
        foreach ($sessionsByType as $typeId => $typeSessions) {
            $type = $typeSessions->first()->attendanceType;
            $totalAttendees = $typeSessions->sum(function ($session) {
                return $session->attendanceRecords->count();
            });

            $sessionSummaries = [];
            foreach ($typeSessions as $session) {
                $attendeeCount = $session->attendanceRecords->count();
                $categoryCounts = $session->attendanceRecords->groupBy(function ($record) {
                    return $record->person?->effective_category ?? 'Unknown';
                })->map->count();

                $sessionSummaries[] = [
                    'session' => $session,
                    'attendeeCount' => $attendeeCount,
                    'categoryCounts' => $categoryCounts,
                ];
            }

            $typeSummaries[] = [
                'type' => $type,
                'sessions' => $sessionSummaries,
                'totalSessions' => $typeSessions->count(),
                'totalAttendees' => $totalAttendees,
            ];
        }

        return view('attendance.records', compact('typeSummaries'));
    }
}
