<?php

namespace App\Services;

use App\Models\AttendanceSummary;
use App\Models\AttendanceRecord;
use App\Models\Service;
use App\Models\Person;
use Carbon\Carbon;

class AttendanceSummaryService
{
    /**
     * Recompute summary for a single person after a service is saved.
     */
    public function recompute(int $personId): void
    {
        $totalServices = Service::count();

        $records = AttendanceRecord::where('person_id', $personId)
            ->where('status', 'present')
            ->with('service')
            ->get();

        $totalPresent   = $records->count();
        $attendanceRate = $totalServices > 0
            ? round(($totalPresent / $totalServices) * 100, 2)
            : 0.00;

        $lastAttended = $records->sortByDesc(fn($r) => $r->service?->date)
            ->first()?->service?->date;

        $streak = $this->computeStreak($personId);

        AttendanceSummary::updateOrCreate(
            ['person_id' => $personId],
            [
                'total_present'    => $totalPresent,
                'total_sessions'   => $totalServices,
                'attendance_rate'  => $attendanceRate,
                'streak'           => $streak,
                'last_attended_at' => $lastAttended,
            ]
        );
    }

    /**
     * Recompute summaries for all people in a service (called after save).
     */
    public function recomputeForService(int $serviceId): void
    {
        $personIds = AttendanceRecord::where('service_id', $serviceId)
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
     * Compute current consecutive-service streak for a person.
     * Looks at all services ordered by date desc; counts until a miss.
     */
    private function computeStreak(int $personId): int
    {
        $allServices = Service::orderByDesc('date')->orderByDesc('time')->pluck('id');
        $attended    = AttendanceRecord::where('person_id', $personId)
            ->where('status', 'present')
            ->pluck('service_id')
            ->flip(); // use as a set for O(1) lookup

        $streak = 0;
        foreach ($allServices as $serviceId) {
            if ($attended->has($serviceId)) {
                $streak++;
            } else {
                break; // streak broken
            }
        }

        return $streak;
    }
}
