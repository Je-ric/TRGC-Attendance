<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ServiceController extends Controller
{
    /**
     * Display all services
     */
    public function index()
    {
        $services = Service::with('attendanceRecords.person')
            ->orderByDesc('date')
            ->orderByDesc('time')
            ->get();

        return view('services.index', compact('services'));
    }

    /**
     * Show the form for creating a new service
     */
    public function create()
    {
        return view('services.create');
    }

    /**
     * Store a newly created service
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date|before_or_equal:today',
            'time' => 'nullable|date_format:H:i',
            'location' => 'nullable|string|max:255',
            'is_special_event' => 'boolean',
            'service_category' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $service = Service::create([
            'name' => $validated['name'],
            'date' => $validated['date'],
            'time' => $validated['time'] ?? null,
            'location' => $validated['location'] ?? null,
            'is_special_event' => $request->has('is_special_event'),
            'service_category' => $validated['service_category'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('services.checkin', $service)
            ->with('success', 'Service created successfully.');
    }

    /**
     * Display the specified service
     */
    public function show(Service $service)
    {
        $service->load('attendanceRecords.person');

        return view('services.show', compact('service'));
    }

    /**
     * Show the check-in page for a service
     */
    public function checkin(Service $service)
    {
        return view('services.checkin', compact('service'));
    }

    /**
     * Show the form for editing a service
     */
    public function edit(Service $service)
    {
        return view('services.edit', compact('service'));
    }

    /**
     * Update the specified service
     */
    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date|before_or_equal:today',
            'time' => 'nullable|date_format:H:i',
            'location' => 'nullable|string|max:255',
            'is_special_event' => 'boolean',
            'service_category' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $service->update([
            'name' => $validated['name'],
            'date' => $validated['date'],
            'time' => $validated['time'] ?? null,
            'location' => $validated['location'] ?? null,
            'is_special_event' => $request->has('is_special_event'),
            'service_category' => $validated['service_category'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('services.show', $service)
            ->with('success', 'Service updated successfully.');
    }

    /**
     * Remove the specified service
     */
    public function destroy(Service $service)
    {
        $service->delete();

        return redirect()->route('services.index')
            ->with('success', 'Service deleted successfully.');
    }

    /**
     * Quick create Sunday service
     */
    public function quickSundayService()
    {
        $nextSunday = Carbon::now()->next(Carbon::SUNDAY);
        
        $service = Service::create([
            'name' => 'Sunday Morning Service',
            'date' => $nextSunday->format('Y-m-d'),
            'time' => '09:00',
            'location' => 'Main Sanctuary',
            'is_special_event' => false,
            'service_category' => 'Sunday Morning',
            'notes' => null,
        ]);

        return redirect()->route('services.checkin', $service)
            ->with('success', 'Sunday service created successfully.');
    }

    /**
     * Display weekly view
     */
    public function weekly($weekIdentifier = null)
    {
        if ($weekIdentifier) {
            // Parse week identifier (e.g., "2026-W32")
            $year = substr($weekIdentifier, 0, 4);
            $week = substr($weekIdentifier, 6);
            $startDate = Carbon::now()->setISODate($year, $week)->startOfWeek();
            $endDate = $startDate->copy()->endOfWeek();
        } else {
            // Default to current week
            $startDate = Carbon::now()->startOfWeek();
            $endDate = Carbon::now()->endOfWeek();
            $weekIdentifier = $startDate->format('o-\WW');
        }

        $services = Service::inDateRange($startDate, $endDate)
            ->orderBy('date')
            ->orderBy('time')
            ->with('attendanceRecords.person')
            ->get();

        // Get previous and next week identifiers
        $prevWeek = $startDate->copy()->subWeek()->format('o-\WW');
        $nextWeek = $startDate->copy()->addWeek()->format('o-\WW');

        return view('services.weekly', compact(
            'services', 
            'startDate', 
            'endDate', 
            'weekIdentifier',
            'prevWeek',
            'nextWeek'
        ));
    }
}
