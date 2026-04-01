<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceType;

class AttendanceTypeController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'description'  => 'nullable|string|max:500',
            'day_of_week'  => 'nullable|string',
            'start_time'   => 'nullable|date_format:H:i',
            'location'     => 'nullable|string|max:255',
            'is_recurring' => 'boolean',
        ]);

        $data['is_recurring'] = $request->boolean('is_recurring', true);
        $data['is_active']    = true;

        AttendanceType::create($data);

        return redirect()->back()->with('toast', [
            'type'    => 'success',
            'message' => 'Service "' . $request->name . '" created.',
        ]);
    }

    public function update(Request $request, AttendanceType $attendanceType)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'day_of_week' => 'nullable|string',
            'start_time'  => 'nullable|date_format:H:i',
            'location'    => 'nullable|string|max:255',
        ]);

        $attendanceType->update($data);

        return redirect()->back()->with('toast', [
            'type'    => 'success',
            'message' => '"' . $attendanceType->name . '" updated.',
        ]);
    }

    public function destroy(AttendanceType $attendanceType)
    {
        $name = $attendanceType->name;
        $attendanceType->delete();

        return redirect()->route('attendance.index')->with('toast', [
            'type'    => 'success',
            'message' => '"' . $name . '" deleted.',
        ]);
    }
}
