<?php

namespace App\Http\Controllers;

use App\Models\Service;

class AttendanceController extends Controller
{
    public function index()
    {
        // Redirect to services index which now handles all attendance
        return redirect()->route('services.index');
    }

    public function show(Service $service)
    {
        // Redirect to service show
        return redirect()->route('services.show', $service);
    }

    public function records()
    {
        // Redirect to services index which now shows all records
        return redirect()->route('services.index');
    }
}
