<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceType extends Model
{
    protected $fillable = [
        'name',
        'is_recurring',
        'day_of_week',
    ];

    public function sessions()
    {
        return $this->hasMany(AttendanceSession::class);
    }
}
