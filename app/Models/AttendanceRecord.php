<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceRecord extends Model
{
    protected $fillable = [
        'service_id',
        'person_id',
        'status',
        'check_in_time',
        'check_out_time',
        'remarks',
    ];

    protected $casts = [
        'check_in_time' => 'datetime:H:i:s',
        'check_out_time' => 'datetime:H:i:s',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function person()
    {
        return $this->belongsTo(Person::class);
    }
}

