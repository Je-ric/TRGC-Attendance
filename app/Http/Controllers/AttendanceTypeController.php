<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceType;

class AttendanceTypeController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'day_of_week' => 'nullable|string',
            'is_recurring' => 'boolean',
        ]);

        AttendanceType::create([
            'name' => $request->name,
            'day_of_week' => $request->day_of_week,
            'is_recurring' => $request->is_recurring ?? true,
        ]);

        return redirect()->back()->with('success', 'Attendance type added!');
    }

    public function destroy(AttendanceType $attendanceType)
    {
        $attendanceType->delete();
        return redirect()->back()->with('success', 'Event/Service deleted successfully!');
    }
}
