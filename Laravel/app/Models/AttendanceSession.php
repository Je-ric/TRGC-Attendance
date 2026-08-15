<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceSession extends Model
{
    protected $fillable = [
        'attendance_type_id',
        'date',
        'notes',
        'service_name',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function attendanceType()
    {
        return $this->belongsTo(AttendanceType::class);
    }

    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class);
    }
}
