<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Person;
use App\Models\Family;

class PersonController extends Controller
{
    public function index()
    {
        $categories = Person::categories();

        $categoryCounts = [];
        foreach ($categories as $category) {
            $categoryCounts[$category] = 0;
        }

        $people = Person::with('family')->get();
        foreach ($people as $person) {
            $category = $person->effective_category;
            if (in_array($category, $categories, true)) {
                $categoryCounts[$category] = ($categoryCounts[$category] ?? 0) + 1;
            }
        }

        $familyCounts = Family::withCount('people')
            ->orderBy('family_name')
            ->get();

        $totalPeople = $people->count();

        return view('people.index', compact('categories', 'categoryCounts', 'familyCounts', 'totalPeople'));
    }
}
