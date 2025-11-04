<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RefereeSeeder extends Seeder
{
    /**
     * Seed the application's database with referee users.
     */
    public function run(): void
    {
        $this->command->info('Creating referee user...');

        User::create([
            'name' => 'Referee Admin',
            'email' => 'referee@pourtest.local',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        $this->command->info('Referee user created successfully!');
        $this->command->info('  Email: referee@pourtest.local');
        $this->command->info('  Password: password');
    }
}
