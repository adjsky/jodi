<?php

declare(strict_types=1);

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            EventAttendeeSeeder::class,
            EventSeeder::class,
            JournalEntrySeeder::class,
            MoodTrackerEntrySeeder::class,
            RecurrenceSeeder::class,
            RegistrationInvitationSeeder::class,
            TodoSeeder::class,
            UserLoginRequestsSeeder::class,
            UserSeeder::class,
        ]);
    }
}
