<?php

namespace Database\Seeders;

use App\Models\KategoriArtikel;
use Illuminate\Database\Seeder;

class KategoriArtikelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategoris = [
            [
                'nama' => 'Tips & Trik',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Berita',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Edukasi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Kampanye',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($kategoris as $kategori) {
            KategoriArtikel::create($kategori);
        }
    }
}
