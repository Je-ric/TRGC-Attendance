<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\AttendanceRecord;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ExportController extends Controller
{
    /**
     * Export weekly attendance to CSV
     */
    public function exportWeeklyAttendance($weekIdentifier)
    {
        // Parse week identifier (e.g., "2026-W32")
        $year = substr($weekIdentifier, 0, 4);
        $week = substr($weekIdentifier, 6);
        $startDate = Carbon::now()->setISODate($year, $week)->startOfWeek();
        $endDate = $startDate->copy()->endOfWeek();

        $services = Service::inDateRange($startDate, $endDate)
            ->orderBy('date')
            ->orderBy('time')
            ->with('attendanceRecords.person')
            ->get();

        $csvData = [];
        $csvData[] = ['Date', 'Service Name', 'Time', 'Location', 'Person Name', 'Category', 'Family', 'Check-in Time', 'Status'];

        foreach ($services as $service) {
            foreach ($service->attendanceRecords as $record) {
                $csvData[] = [
                    $service->date->format('Y-m-d'),
                    $service->name,
                    $service->time ? Carbon::parse($service->time)->format('g:i A') : '',
                    $service->location ?? '',
                    $record->person->full_name,
                    $record->person->effective_category,
                    $record->person->family?->family_name ?? '',
                    $record->check_in_time ? Carbon::parse($record->check_in_time)->format('g:i A') : '',
                    $record->status,
                ];
            }
        }

        $filename = "attendance_week_{$weekIdentifier}.csv";
        
        return $this->downloadCsv($csvData, $filename);
    }

    /**
     * Export attendance by date range to CSV
     */
    public function exportDateRange(Request $request)
    {
        $startDate = $request->query('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->query('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $category = $request->query('category');
        $familyId = $request->query('family_id');

        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);

        $query = Service::inDateRange($startDate, $endDate)
            ->orderBy('date')
            ->orderBy('time')
            ->with('attendanceRecords.person');

        if ($category) {
            $query->where('service_category', $category);
        }

        $services = $query->get();

        $csvData = [];
        $csvData[] = ['Date', 'Service Name', 'Time', 'Location', 'Person Name', 'Category', 'Family', 'Check-in Time', 'Status'];

        foreach ($services as $service) {
            foreach ($service->attendanceRecords as $record) {
                // Filter by family if specified
                if ($familyId && $record->person->family_id != $familyId) {
                    continue;
                }

                $csvData[] = [
                    $service->date->format('Y-m-d'),
                    $service->name,
                    $service->time ? Carbon::parse($service->time)->format('g:i A') : '',
                    $service->location ?? '',
                    $record->person->full_name,
                    $record->person->effective_category,
                    $record->person->family?->family_name ?? '',
                    $record->check_in_time ? Carbon::parse($record->check_in_time)->format('g:i A') : '',
                    $record->status,
                ];
            }
        }

        $filename = "attendance_{$startDate->format('Y-m-d')}_to_{$endDate->format('Y-m-d')}.csv";
        
        return $this->downloadCsv($csvData, $filename);
    }

    /**
     * Export specific service attendance to CSV
     */
    public function exportService(Service $service)
    {
        $service->load('attendanceRecords.person');

        $csvData = [];
        $csvData[] = ['Date', 'Service Name', 'Time', 'Location', 'Person Name', 'Category', 'Family', 'Check-in Time', 'Status'];

        foreach ($service->attendanceRecords as $record) {
            $csvData[] = [
                $service->date->format('Y-m-d'),
                $service->name,
                $service->time ? Carbon::parse($service->time)->format('g:i A') : '',
                $service->location ?? '',
                $record->person->full_name,
                $record->person->effective_category,
                $record->person->family?->family_name ?? '',
                $record->check_in_time ? Carbon::parse($record->check_in_time)->format('g:i A') : '',
                $record->status,
            ];
        }

        $filename = "attendance_{$service->date->format('Y-m-d')}_{$service->name}.csv";
        
        return $this->downloadCsv($csvData, $filename);
    }

    /**
     * Export person's attendance history to CSV
     */
    public function exportPersonAttendance($personId)
    {
        $records = AttendanceRecord::where('person_id', $personId)
            ->with('service', 'person')
            ->orderByDesc('created_at')
            ->get();

        if ($records->isEmpty()) {
            return redirect()->back()->with('error', 'No attendance records found for this person.');
        }

        $csvData = [];
        $csvData[] = ['Date', 'Service Name', 'Time', 'Location', 'Category', 'Check-in Time', 'Status', 'Remarks'];

        foreach ($records as $record) {
            $csvData[] = [
                $record->service->date->format('Y-m-d'),
                $record->service->name,
                $record->service->time ? Carbon::parse($record->service->time)->format('g:i A') : '',
                $record->service->location ?? '',
                $record->service->service_category ?? '',
                $record->check_in_time ? Carbon::parse($record->check_in_time)->format('g:i A') : '',
                $record->status,
                $record->remarks ?? '',
            ];
        }

        $personName = $records->first()->person->full_name ?? 'person';
        $filename = "attendance_{$personName}.csv";
        
        return $this->downloadCsv($csvData, $filename);
    }

    /**
     * Helper method to download CSV file
     */
    private function downloadCsv($data, $filename)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            foreach ($data as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}