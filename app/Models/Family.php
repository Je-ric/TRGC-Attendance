<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Family extends Model
{
    protected $fillable = [
        'family_name',
        'address',
        'contact_person',
    ];

    public function people()
    {
        return $this->hasMany(Person::class);
    }
}
