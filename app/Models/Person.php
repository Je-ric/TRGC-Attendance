<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    public const CATEGORIES = [
        'Kids',
        'Youth',
        'Adults',
        'Seniors',
        'Visitors',
    ];

    protected $fillable = [
        'first_name',
        'last_name',
        'birthdate',
        'category',
        'address',
        'contact_number',
        'family_id',
    ];

    protected $casts = [
        'birthdate' => 'date',
    ];

    // Full name helper
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    // Compute age automatically
    public function getAgeAttribute()
    {
        if (!$this->birthdate) {
            return null;
        }

        try {
            return $this->birthdate->age;
        } catch (\Exception $e) {
            return null;
        }
    }

    // Auto category based on age if not manually set
    public function getAutoCategoryAttribute()
    {
        if ($this->category) {
            return $this->category;
        }

        $age = $this->age;

        if ($age === null) {
            return 'Unknown';
        }
        if ($age <= 12) {
            return 'Kids';
        }
        if ($age <= 18) {
            return 'Youth';
        }
        if ($age <= 59) {
            return 'Adults';
        }
        return 'Seniors';
    }

    public function getEffectiveCategoryAttribute()
    {
        return $this->category ?: $this->auto_category;
    }

    public static function categories(): array
    {
        return self::CATEGORIES;
    }

    // Relation to family
    public function family()
    {
        return $this->belongsTo(Family::class);
    }
}
