<?php

namespace App\Http\Controllers;

use App\Models\{Person, Family, AttendanceSession, AttendanceRecord, AttendanceSummary, AttendanceType};
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $thirtyDaysAgo = $today->copy()->subDays(30);
        $ninetyDaysAgo = $today->copy()->subDays(90);

        // ── Congregation overview ──────────────────────────────────────
        $totalPeople   = Person::count();
        $totalFamilies = Family::count();
        $totalMembers  = Person::where('membership_status', 'Member')->count();
        $totalActive   = Person::whereIn('membership_status', ['Member', 'Regular Attendee'])->count();

        // ── Attendance overview ────────────────────────────────────────
        $totalSessions     = AttendanceSession::count();
        $sessionsThisMonth = AttendanceSession::whereMonth('date', $today->month)
            ->whereYear('date', $today->year)->count();
        $totalCheckins     = AttendanceRecord::where('status', 'present')->count();

        // Average attendance per session (all time)
        $avgAttendance = $totalSessions > 0
            ? round($totalCheckins / $totalSessions)
            : 0;

        // ── Last session ───────────────────────────────────────────────
        $lastSession = AttendanceSession::with('attendanceType')
            ->orderByDesc('date')->first();
        $lastSessionCount = $lastSession
            ? AttendanceRecord::where('attendance_session_id', $lastSession->id)
                ->where('status', 'present')->count()
            : 0;

        // ── Attendance trend (last 8 sessions) ────────────────────────
        $recentSessions = AttendanceSession::with('attendanceType')
            ->orderByDesc('date')
            ->take(8)
            ->get()
            ->reverse()
            ->map(fn($s) => [
                'label' => $s->date->format('M d'),
                'type'  => $s->attendanceType?->name ?? '—',
                'count' => AttendanceRecord::where('attendance_session_id', $s->id)
                    ->where('status', 'present')->count(),
            ])->values();

        // ── Category breakdown (last session) ─────────────────────────
        $categoryBreakdown = [];
        if ($lastSession) {
            $records = AttendanceRecord::where('attendance_session_id', $lastSession->id)
                ->where('status', 'present')
                ->with('person')
                ->get();
            foreach ($records as $r) {
                $cat = $r->person?->effective_category ?? 'Unknown';
                $categoryBreakdown[$cat] = ($categoryBreakdown[$cat] ?? 0) + 1;
            }
        }

        // ── Top attendees (by attendance_rate, min 3 sessions) ────────
        $topAttendees = AttendanceSummary::with('person')
            ->where('total_present', '>=', 3)
            ->orderByDesc('attendance_rate')
            ->orderByDesc('total_present')
            ->take(10)
            ->get();

        // ── Inactive members (no attendance in 30+ days, were active) ─
        $inactiveMembers = Person::with(['attendanceSummary', 'family'])
            ->whereIn('membership_status', ['Member', 'Regular Attendee'])
            ->where(fn($q) =>
                $q->whereHas('attendanceSummary', fn($q2) =>
                    $q2->where(fn($q3) =>
                        $q3->whereNull('last_attended_at')
                           ->orWhere('last_attended_at', '<', $thirtyDaysAgo)
                    )
                )->orWhereDoesntHave('attendanceSummary')
            )
            ->orderBy('last_name')
            ->take(15)
            ->get();

        // ── Streak leaders ─────────────────────────────────────────────
        $streakLeaders = AttendanceSummary::with('person')
            ->where('streak', '>', 0)
            ->orderByDesc('streak')
            ->take(5)
            ->get();

        // ── New people this month ──────────────────────────────────────
        $newThisMonth = Person::whereMonth('created_at', $today->month)
            ->whereYear('created_at', $today->year)
            ->count();

        // ── Upcoming birthdays (next 7 days) ──────────────────────────
        $upcomingBirthdays = Person::whereNotNull('birthdate')
            ->get()
            ->filter(function ($p) use ($today) {
                if (!$p->birthdate) return false;
                $next = $p->birthdate->copy()->setYear($today->year);
                if ($next->lt($today)) $next->addYear();
                $days = $today->diffInDays($next, false);
                $p->days_until = $days;
                return $days >= 0 && $days <= 7;
            })
            ->sortBy('days_until')
            ->take(5)
            ->values();

        // ── Per-service summary ────────────────────────────────────────
        $serviceSummaries = AttendanceType::withCount('sessions')
            ->with(['sessions' => fn($q) => $q->orderByDesc('date')->take(1)])
            ->orderBy('name')
            ->get()
            ->map(function ($type) {
                $lastS = $type->sessions->first();
                $lastCount = $lastS
                    ? AttendanceRecord::where('attendance_session_id', $lastS->id)
                        ->where('status', 'present')->count()
                    : 0;
                return [
                    'type'       => $type,
                    'sessions'   => $type->sessions_count,
                    'last_date'  => $lastS?->date,
                    'last_count' => $lastCount,
                ];
            });

        return view('dashboard.index', compact(
            'totalPeople', 'totalFamilies', 'totalMembers', 'totalActive',
            'totalSessions', 'sessionsThisMonth', 'avgAttendance',
            'lastSession', 'lastSessionCount',
            'recentSessions', 'categoryBreakdown',
            'topAttendees', 'inactiveMembers', 'streakLeaders',
            'newThisMonth', 'upcomingBirthdays', 'serviceSummaries'
        ));
    }
}
