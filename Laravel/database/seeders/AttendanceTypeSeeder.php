<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use Carbon\Carbon;

class AttendanceTypeSeeder extends Seeder
{
    public function run(): void
    {
        // Create some sample services for testing
        $services = [
            [
                'name' => 'Sunday Morning Service',
                'date' => Carbon::now()->next(Carbon::SUNDAY)->format('Y-m-d'),
                'time' => '09:00',
                'location' => 'Main Sanctuary',
                'is_special_event' => false,
                'service_category' => 'Sunday Morning',
                'notes' => 'Regular Sunday morning service',
            ],
            [
                'name' => 'Sunday Afternoon Youth Event',
                'date' => Carbon::now()->next(Carbon::SUNDAY)->format('Y-m-d'),
                'time' => '14:00',
                'location' => 'Youth Room',
                'is_special_event' => true,
                'service_category' => 'Sunday Afternoon',
                'notes' => 'Special youth event',
            ],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}
