<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    protected $fillable = [
        'name',
        'date',
        'time',
        'location',
        'is_special_event',
        'service_category',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'is_special_event' => 'boolean',
    ];

    /**
     * Get all attendance records for this service
     */
    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class, 'service_id');
    }

    /**
     * Get all people who attended this service
     */
    public function attendees()
    {
        return $this->belongsToMany(Person::class, 'attendance_records', 'service_id', 'person_id')
                    ->withPivot('status', 'check_in_time', 'check_out_time', 'remarks')
                    ->withTimestamps();
    }

    /**
     * Get the count of people who attended this service
     */
    public function getAttendeeCountAttribute(): int
    {
        return $this->attendanceRecords()->where('status', 'present')->count();
    }

    /**
     * Get attendance breakdown by category
     */
    public function getCategoryBreakdownAttribute(): array
    {
        return $this->attendanceRecords()
            ->with('person')
            ->get()
            ->groupBy(fn($record) => $record->person?->effective_category ?? 'Unknown')
            ->map(fn($records) => $records->count())
            ->toArray();
    }

    /**
     * Scope for regular (non-special) services
     */
    public function scopeRegular($query)
    {
        return $query->where('is_special_event', false);
    }

    /**
     * Scope for special events
     */
    public function scopeSpecial($query)
    {
        return $query->where('is_special_event', true);
    }

    /**
     * Scope for services within a date range
     */
    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    /**
     * Scope for services on a specific date
     */
    public function scopeOnDate($query, $date)
    {
        return $query->where('date', $date);
    }

    /**
     * Get the week identifier for this service (ISO week format)
     */
    public function getWeekIdentifierAttribute(): string
    {
        return $this->date->format('o-\WW');
    }
}
