<?php

namespace App\Http\Controllers;

use App\Models\{Person, Family, Service, AttendanceRecord, AttendanceSummary};
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $thirtyDaysAgo = $today->copy()->subDays(30);

        // ── Congregation overview ──────────────────────────────────────
        $totalPeople   = Person::count();
        $totalFamilies = Family::count();
        $totalMembers  = Person::where('membership_status', 'Member')->count();
        $totalActive   = Person::whereIn('membership_status', ['Member', 'Regular Attendee'])->count();

        // ── Attendance overview ────────────────────────────────────────
        $totalSessions     = Service::count();
        $sessionsThisMonth = Service::whereMonth('date', $today->month)
            ->whereYear('date', $today->year)->count();
        $totalCheckins     = AttendanceRecord::where('status', 'present')->count();

        // Average attendance per session (all time)
        $avgAttendance = $totalSessions > 0
            ? round($totalCheckins / $totalSessions)
            : 0;

        // ── Last session ───────────────────────────────────────────────
        $lastSession = Service::orderByDesc('date')->orderByDesc('time')->first();
        $lastSessionCount = $lastSession
            ? AttendanceRecord::where('service_id', $lastSession->id)
                ->where('status', 'present')->count()
            : 0;

        // ── Attendance trend (last 8 sessions) ────────────────────────
        $recentSessions = Service::orderByDesc('date')->orderByDesc('time')
            ->take(8)
            ->get()
            ->reverse()
            ->map(fn($s) => [
                'label' => $s->date->format('M d'),
                'type'  => $s->name,
                'count' => AttendanceRecord::where('service_id', $s->id)
                    ->where('status', 'present')->count(),
            ])->values();

        // ── Category breakdown (last session) ─────────────────────────
        $categoryBreakdown = [];
        if ($lastSession) {
            $records = AttendanceRecord::where('service_id', $lastSession->id)
                ->where('status', 'present')
                ->with('person')
                ->get();
            foreach ($records as $r) {
                $cat = $r->person?->effective_category ?? 'Unknown';
                $categoryBreakdown[$cat] = ($categoryBreakdown[$cat] ?? 0) + 1;
            }
        }

        // ── Top attendees (by attendance_rate, min 3 services) ────────
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

        // ── Per-service-category summary (Services Overview table) ────
        // The view expects $s['type'] to behave like a service type object.
        // We build a pseudo-object per unique service_category using a stdClass.
        $serviceSummaries = Service::select('service_category')
            ->selectRaw('COUNT(*) as total_sessions')
            ->selectRaw('MAX(date) as last_date')
            ->whereNotNull('service_category')
            ->groupBy('service_category')
            ->orderBy('service_category')
            ->get()
            ->map(function ($cat) {
                $lastS = Service::where('service_category', $cat->service_category)
                    ->orderByDesc('date')
                    ->orderByDesc('time')
                    ->first();
                $lastCount = $lastS
                    ? AttendanceRecord::where('service_id', $lastS->id)
                        ->where('status', 'present')->count()
                    : 0;

                // Build a pseudo-type object matching what the view accesses
                $type = new \stdClass();
                $type->id          = $cat->service_category; // used in route()
                $type->name        = $cat->service_category;
                $type->day_of_week = null;
                $type->start_time  = $lastS?->time;
                $type->location    = $lastS?->location;

                return [
                    'type'       => $type,
                    'sessions'   => $cat->total_sessions,
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
