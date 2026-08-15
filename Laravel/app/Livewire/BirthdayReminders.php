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
        $today = Carbon::today(config('app.timezone'));

        $allPeople = Person::with('family')->whereNotNull('birthdate')->get();

        $upcomingBirthdays = $allPeople
            ->map(function ($person) use ($today) {
                $birthdate = $person->birthdate instanceof Carbon
                    ? $person->birthdate->copy()
                    : Carbon::parse($person->birthdate);

                $birthday = $this->buildBirthdayForYear($birthdate, $today->year);
                if ($birthday->lt($today)) {
                    $birthday = $this->buildBirthdayForYear($birthdate, $today->year + 1);
                }

                $daysUntil = $today->diffInDays($birthday, false);

                // Only include if within the days ahead range
                if ($daysUntil >= 0 && $daysUntil <= $this->daysAhead) {
                    $person->days_until = $daysUntil;
                    $person->next_birthday = $birthday;
                    $person->age_on_birthday = $birthdate->diffInYears($birthday);
                    return $person;
                }

                return null;
            })
            ->filter()
            ->sortBy('days_until')
            ->values();

        return view('livewire.birthday-reminders', compact('upcomingBirthdays'));
    }

    private function buildBirthdayForYear(Carbon $birthdate, int $year): Carbon
    {
        $month = (int) $birthdate->month;
        $day = (int) $birthdate->day;

        // Handle Feb 29 birthdays on non-leap years.
        if ($month === 2 && $day === 29 && !Carbon::create($year, 1, 1)->isLeapYear()) {
            $day = 28;
        }

        return Carbon::create(
            $year,
            $month,
            $day,
            0,
            0,
            0,
            config('app.timezone')
        );
    }
}
