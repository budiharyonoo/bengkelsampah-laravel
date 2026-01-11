<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Sampah;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get sampah IDs by name
        $sampahIds = [];
        $sampahList = Sampah::all();
        foreach ($sampahList as $sampah) {
            $sampahIds[$sampah->nama] = $sampah->id;
        }

        $categories = [
            [
                'nama' => 'Sampah Organik',
                'sampah' => [
                    $sampahIds['Sisa Makanan'] ?? null,
                    $sampahIds['Kulit Buah'] ?? null,
                    $sampahIds['Kulit Telur'] ?? null,
                ],
            ],
            [
                'nama' => 'Sampah Plastik',
                'sampah' => [
                    $sampahIds['Botol Plastik'] ?? null,
                    $sampahIds['Kantong Plastik'] ?? null,
                    $sampahIds['Sedotan Plastik'] ?? null,
                    $sampahIds['Wadah Makanan'] ?? null,
                ],
            ],
            [
                'nama' => 'Sampah Kertas',
                'sampah' => [
                    $sampahIds['Kertas HVS'] ?? null,
                    $sampahIds['Kardus'] ?? null,
                    $sampahIds['Koran Bekas'] ?? null,
                    $sampahIds['Buku Bekas'] ?? null,
                ],
            ],
            [
                'nama' => 'Sampah Logam',
                'sampah' => [
                    $sampahIds['Kaleng Minuman'] ?? null,
                ],
            ],
            [
                'nama' => 'Sampah Kaca',
                'sampah' => [
                    $sampahIds['Botol Kaca'] ?? null,
                ],
            ],
            [
                'nama' => 'Sampah Elektronik',
                'sampah' => [
                    $sampahIds['Baterai Bekas'] ?? null,
                ],
            ],
            [
                'nama' => 'Sampah Tekstil',
                'sampah' => [
                    $sampahIds['Pakaian Bekas'] ?? null,
                ],
            ],
        ];

        foreach ($categories as $categoryData) {
            // Filter out null values from sampah array
            $categoryData['sampah'] = array_filter($categoryData['sampah'], function ($value) {
                return $value !== null;
            });

            // Only create category if it has sampah items
            if (! empty($categoryData['sampah'])) {
                Category::create($categoryData);
            }
        }

        $this->command->info('Category data seeded successfully!');
    }
}
