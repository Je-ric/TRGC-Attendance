<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class AttendanceType extends Model
{
    protected $fillable = [
        'name',
        'description',
        'is_recurring',
        'day_of_week',
        'start_time',
        'location',
        'is_active',
    ];

    protected $casts = [
        'is_recurring' => 'boolean',
        'is_active'    => 'boolean',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function sessions()
    {
        return $this->hasMany(AttendanceSession::class);
    }

    public function getLatestSessionAttribute()
    {
        return $this->sessions()->orderByDesc('date')->first();
    }
}
