<?php

namespace Database\Seeders;

use App\Models\Level;
use Illuminate\Database\Seeder;

class LevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $levels = [
            [
                'nama' => 'Pemula',
                'xp' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Pecinta Lingkungan',
                'xp' => 100,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Pahlawan Sampah',
                'xp' => 500,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Master Daur Ulang',
                'xp' => 1000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Legenda Lingkungan',
                'xp' => 2000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($levels as $level) {
            Level::create($level);
        }
    }
}
