<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Family extends Model
{
    protected $fillable = [
        'family_name',
        'address',
        'barangay',
        'contact_person',
        'contact_number',
        'notes',
    ];

    public function people()
    {
        return $this->hasMany(Person::class);
    }

    public function getMemberCountAttribute(): int
    {
        return $this->people()->count();
    }
}
