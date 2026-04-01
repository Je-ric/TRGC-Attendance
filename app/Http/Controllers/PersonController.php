<?php

namespace App\Http\Controllers;

use App\Models\Person;
use App\Models\Family;

class PersonController extends Controller
{
    public function index()
    {
        $people     = Person::with('family')->get();
        $categories = Person::CATEGORIES;

        $categoryCounts = array_fill_keys($categories, 0);
        foreach ($people as $person) {
            $cat = $person->effective_category;
            if (isset($categoryCounts[$cat])) $categoryCounts[$cat]++;
        }

        $membershipCounts = array_fill_keys(Person::MEMBERSHIP_STATUSES, 0);
        foreach ($people as $person) {
            $ms = $person->membership_status ?? 'Regular Attendee';
            if (isset($membershipCounts[$ms])) $membershipCounts[$ms]++;
        }

        $totalPeople  = $people->count();
        $totalMembers = $membershipCounts['Member'] ?? 0;

        return view('people.index', compact(
            'categories', 'categoryCounts',
            'membershipCounts', 'totalPeople', 'totalMembers'
        ));
    }
}
