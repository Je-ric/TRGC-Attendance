<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Family;
use App\Models\Person;

class FamilyController extends Controller
{
    public function index()
    {
        return view('families.index');
    }
}

