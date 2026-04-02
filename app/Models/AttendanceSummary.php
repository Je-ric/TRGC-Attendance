<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceSummary extends Model
{
    protected $fillable = [
        'person_id',
        'total_present',
        'total_sessions',
        'attendance_rate',
        'streak',
        'last_attended_at',
    ];

    protected $casts = [
        'last_attended_at' => 'date',
        'attendance_rate'  => 'decimal:2',
    ];

    public function person()
    {
        return $this->belongsTo(Person::class);
    }
}
