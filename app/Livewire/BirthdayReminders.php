<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Person;
use Carbon\Carbon;

class BirthdayReminders extends Component
{
    public $daysAhead = 30;

    public function render()
    {
        $today = Carbon::today();
        $endDate = $today->copy()->addDays($this->daysAhead);

        $allPeople = Person::with('family')->whereNotNull('birthdate')->get();

        $upcomingBirthdays = $allPeople
            ->map(function ($person) use ($today) {
                $thisYear = $today->year;
                $birthdayThisYear = Carbon::parse($person->birthdate)->setYear($thisYear);

                if ($birthdayThisYear->lt($today)) {
                    $birthdayThisYear->addYear();
                }

                $daysUntil = $today->diffInDays($birthdayThisYear, false);

                // Only include if within the days ahead range
                if ($daysUntil >= 0 && $daysUntil <= $this->daysAhead) {
                    $person->days_until = $daysUntil;
                    $person->next_birthday = $birthdayThisYear;
                    $person->age_on_birthday = $birthdayThisYear->diffInYears(Carbon::parse($person->birthdate));
                    return $person;
                }

                return null;
            })
            ->filter()
            ->sortBy('days_until')
            ->values();

        return view('livewire.birthday-reminders', compact('upcomingBirthdays'));
    }
}

