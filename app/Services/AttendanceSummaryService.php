<?php

namespace App\Services;

use App\Models\AttendanceSummary;
use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use App\Models\Person;
use Carbon\Carbon;

class AttendanceSummaryService
{
    /**
     * Recompute summary for a single person after a session is saved.
     */
    public function recompute(int $personId): void
    {
        $totalSessions = AttendanceSession::count();

        $records = AttendanceRecord::where('person_id', $personId)
            ->where('status', 'present')
            ->with('session')
            ->get();

        $totalPresent   = $records->count();
        $attendanceRate = $totalSessions > 0
            ? round(($totalPresent / $totalSessions) * 100, 2)
            : 0.00;

        $lastAttended = $records->sortByDesc(fn($r) => $r->session?->date)
            ->first()?->session?->date;

        $streak = $this->computeStreak($personId);

        AttendanceSummary::updateOrCreate(
            ['person_id' => $personId],
            [
                'total_present'    => $totalPresent,
                'total_sessions'   => $totalSessions,
                'attendance_rate'  => $attendanceRate,
                'streak'           => $streak,
                'last_attended_at' => $lastAttended,
            ]
        );
    }

    /**
     * Recompute summaries for all people in a session (called after save).
     */
    public function recomputeForSession(int $sessionId): void
    {
        $personIds = AttendanceRecord::where('attendance_session_id', $sessionId)
            ->pluck('person_id');

        foreach ($personIds as $personId) {
            $this->recompute($personId);
        }
    }

    /**
     * Recompute all summaries (used for bulk refresh / artisan command).
     */
    public function recomputeAll(): void
    {
        Person::pluck('id')->each(fn($id) => $this->recompute($id));
    }

    /**
     * Compute current consecutive-session streak for a person.
     * Looks at all sessions ordered by date desc; counts until a miss.
     */
    private function computeStreak(int $personId): int
    {
        $allSessions = AttendanceSession::orderByDesc('date')->pluck('id');
        $attended    = AttendanceRecord::where('person_id', $personId)
            ->where('status', 'present')
            ->pluck('attendance_session_id')
            ->flip(); // use as a set for O(1) lookup

        $streak = 0;
        foreach ($allSessions as $sessionId) {
            if ($attended->has($sessionId)) {
                $streak++;
            } else {
                break; // streak broken
            }
        }

        return $streak;
    }
}
