<?php

namespace Database\Seeders;

use App\Models\EventParticipant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DummyUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create dummy users
        $dummyUsers = [
            [
                'name' => 'Akun Testing Google',
                'identifier' => 'testing@bengkelsampah.com',
                'password' => Hash::make('BengkelSampah25'),
            ],
            [
                'name' => 'Ahmad Rizki',
                'identifier' => '081234567890',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Siti Nurhaliza',
                'identifier' => '081234567891',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Budi Santoso',
                'identifier' => '081234567892',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Dewi Sartika',
                'identifier' => '081234567893',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Muhammad Fajar',
                'identifier' => '081234567894',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Nina Safitri',
                'identifier' => '081234567895',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Rudi Hermawan',
                'identifier' => '081234567896',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Lina Marlina',
                'identifier' => '081234567897',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Agus Setiawan',
                'identifier' => '081234567898',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Yuni Safitri',
                'identifier' => '081234567899',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Dedi Kurniawan',
                'identifier' => '081234567800',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Rina Marlina',
                'identifier' => '081234567801',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Eko Prasetyo',
                'identifier' => '081234567802',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Sari Indah',
                'identifier' => '081234567803',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Joko Widodo',
                'identifier' => '081234567804',
                'password' => Hash::make('password'),
            ],
        ];

        $createdUsers = [];
        foreach ($dummyUsers as $userData) {
            $user = User::create($userData);
            $createdUsers[] = $user;
        }

        // Get existing events from database
        $events = \App\Models\Event::where('status', 'active')->take(3)->get();

        if ($events->isEmpty()) {
            $this->command->warn('No active events found. Please run EventSeeder first.');

            return;
        }

        $joinTimes = [
            now()->subDays(5)->setTime(10, 30),
            now()->subDays(4)->setTime(14, 15),
            now()->subDays(3)->setTime(9, 45),
            now()->subDays(2)->setTime(16, 20),
            now()->subDays(1)->setTime(11, 10),
            now()->subDays(1)->setTime(13, 25),
            now()->subDays(1)->setTime(15, 40),
            now()->subDays(1)->setTime(17, 55),
            now()->setTime(8, 30),
            now()->setTime(10, 45),
            now()->setTime(12, 20),
            now()->setTime(14, 35),
            now()->setTime(16, 50),
            now()->setTime(18, 15),
            now()->setTime(19, 30),
        ];

        // Add participants to each event
        foreach ($events as $eventIndex => $event) {
            $participantsPerEvent = 5; // Add 5 participants per event
            $startIndex = $eventIndex * $participantsPerEvent;

            for ($i = 0; $i < $participantsPerEvent; $i++) {
                $userIndex = $startIndex + $i;
                if (isset($createdUsers[$userIndex])) {
                    $user = $createdUsers[$userIndex];
                    EventParticipant::create([
                        'event_id' => $event->id,
                        'user_id' => $user->id,
                        'user_name' => $user->name,
                        'user_identifier' => $user->identifier,
                        'join_datetime' => $joinTimes[$userIndex] ?? now(),
                    ]);
                }
            }
        }

        $this->command->info('Created '.count($createdUsers).' dummy users and added participants to '.$events->count().' events');
    }
}
