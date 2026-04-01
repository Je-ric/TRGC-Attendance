<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    public const CATEGORIES = ['Kids', 'Youth', 'Adults', 'Seniors', 'Visitors'];

    public const MEMBERSHIP_STATUSES = ['Member', 'Regular Attendee', 'Visitor', 'Inactive'];

    public const GENDERS = ['Male', 'Female'];

    public const CIVIL_STATUSES = ['Single', 'Married', 'Widowed', 'Separated'];

    protected $fillable = [
        'family_id',
        'first_name',
        'last_name',
        'birthdate',
        'gender',
        'civil_status',
        'category',
        'membership_status',
        'joined_date',
        'date_of_baptism',
        'address',
        'contact_number',
        'email',
        'notes',
    ];

    protected $casts = [
        'birthdate'   => 'date',
        'joined_date' => 'date',
        'date_of_baptism' => 'date',
    ];

    // ── Accessors ──────────────────────────────────────────────────────

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getAgeAttribute(): ?int
    {
        return $this->birthdate?->age;
    }

    public function getAutoCategoryAttribute(): string
    {
        if ($this->category) return $this->category;

        $age = $this->age;
        if ($age === null) return 'Unknown';
        if ($age <= 12)    return 'Kids';
        if ($age <= 18)    return 'Youth';
        if ($age <= 59)    return 'Adults';
        return 'Seniors';
    }

    public function getEffectiveCategoryAttribute(): string
    {
        return $this->category ?: $this->auto_category;
    }

    // ── Static helpers ─────────────────────────────────────────────────

    public static function categories(): array
    {
        return self::CATEGORIES;
    }

    public static function membershipStatuses(): array
    {
        return self::MEMBERSHIP_STATUSES;
    }

    // ── Relations ──────────────────────────────────────────────────────

    public function family()
    {
        return $this->belongsTo(Family::class);
    }

    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class);
    }
}
