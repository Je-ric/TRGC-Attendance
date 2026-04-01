<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceType;

class AttendanceTypeController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'day_of_week'  => 'nullable|string',
            'is_recurring' => 'boolean',
        ]);

        AttendanceType::create([
            'name'         => $request->name,
            'day_of_week'  => $request->day_of_week,
            'is_recurring' => $request->is_recurring ?? true,
        ]);

        return redirect()->back()->with('toast', [
            'type'    => 'success',
            'message' => 'Service "' . $request->name . '" created.',
        ]);
    }

    public function destroy(AttendanceType $attendanceType)
    {
        $name = $attendanceType->name;
        $attendanceType->delete();

        return redirect()->back()->with('toast', [
            'type'    => 'success',
            'message' => '"' . $name . '" deleted.',
        ]);
    }
}
