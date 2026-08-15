<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@trgc.org'],
            [
                'name'              => 'TRGC Admin',
                'email'             => 'admin@trgc.org',
                'password'          => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Admin user created: admin@trgc.org / password123');
    }
}
