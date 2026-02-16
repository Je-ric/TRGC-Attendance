<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AttendanceType;

class AttendanceTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            [
                'name' => 'Sunday Service',
                'is_recurring' => true,
                'day_of_week' => 'Sunday',
            ],
            // [
            //     'name' => 'Sunday Service',
            //     'is_recurring' => true,
            //     'day_of_week' => 'Sunday',
            // ],
            // [
            //     'name' => 'Youth Fellowship',
            //     'is_recurring' => true,
            //     'day_of_week' => 'Saturday',
            // ],
            // [
            //     'name' => 'Special Event',
            //     'is_recurring' => false,
            //     'day_of_week' => null,
            // ],
        ];

        foreach ($types as $type) {
            AttendanceType::create($type);
        }
    }
}
