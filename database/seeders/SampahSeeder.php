<?php

namespace Database\Seeders;

use App\Models\BankSampah;
use App\Models\Price;
use App\Models\Sampah;
use Illuminate\Database\Seeder;

class SampahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sampahItems = [
            [
                'nama' => 'Botol Plastik',
                'deskripsi' => 'Botol plastik bekas minuman kemasan',
                'satuan' => 'unit',
                'first_price' => 2000,
            ],
            [
                'nama' => 'Kertas HVS',
                'deskripsi' => 'Kertas HVS bekas yang masih bisa didaur ulang',
                'satuan' => 'kg',
                'first_price' => 3000,
            ],
            [
                'nama' => 'Kaleng Minuman',
                'deskripsi' => 'Kaleng bekas minuman ringan',
                'satuan' => 'unit',
                'first_price' => 1500,
            ],
            [
                'nama' => 'Kardus',
                'deskripsi' => 'Kardus bekas kemasan yang masih bagus',
                'satuan' => 'kg',
                'first_price' => 2500,
            ],
            [
                'nama' => 'Botol Kaca',
                'deskripsi' => 'Botol kaca bekas minuman atau makanan',
                'satuan' => 'unit',
                'first_price' => 1000,
            ],
            [
                'nama' => 'Sisa Makanan',
                'deskripsi' => 'Sisa makanan yang bisa dijadikan kompos',
                'satuan' => 'kg',
                'first_price' => 1000,
            ],
            [
                'nama' => 'Kulit Buah',
                'deskripsi' => 'Kulit buah yang bisa dijadikan kompos',
                'satuan' => 'kg',
                'first_price' => 800,
            ],
            [
                'nama' => 'Baterai Bekas',
                'deskripsi' => 'Baterai bekas yang perlu penanganan khusus',
                'satuan' => 'unit',
                'first_price' => 500,
            ],
            [
                'nama' => 'Kantong Plastik',
                'deskripsi' => 'Kantong plastik bekas belanja',
                'satuan' => 'kg',
                'first_price' => 1500,
            ],
            [
                'nama' => 'Koran Bekas',
                'deskripsi' => 'Koran bekas yang masih bisa didaur ulang',
                'satuan' => 'kg',
                'first_price' => 2000,
            ],
            [
                'nama' => 'Sedotan Plastik',
                'deskripsi' => 'Sedotan plastik bekas minuman',
                'satuan' => 'kg',
                'first_price' => 1200,
            ],
            [
                'nama' => 'Pakaian Bekas',
                'deskripsi' => 'Pakaian bekas yang masih layak pakai',
                'satuan' => 'kg',
                'first_price' => 3000,
            ],
            [
                'nama' => 'Kulit Telur',
                'deskripsi' => 'Kulit telur yang bisa dijadikan kompos',
                'satuan' => 'kg',
                'first_price' => 500,
            ],
            [
                'nama' => 'Wadah Makanan',
                'deskripsi' => 'Wadah makanan plastik bekas',
                'satuan' => 'unit',
                'first_price' => 1000,
            ],
            [
                'nama' => 'Buku Bekas',
                'deskripsi' => 'Buku bekas yang masih bisa dibaca',
                'satuan' => 'kg',
                'first_price' => 2500,
            ],
        ];

        $bankSampah = BankSampah::all();

        if ($bankSampah->isEmpty()) {
            $this->command->warn('No bank sampah found. Please run BankSampahSeeder first.');

            return;
        }

        foreach ($sampahItems as $sampahData) {
            $firstPrice = $sampahData['first_price'];
            unset($sampahData['first_price']);

            $sampah = Sampah::create($sampahData);

            // Create price entries for all bank sampah
            foreach ($bankSampah as $bank) {
                Price::create([
                    'sampah_id' => $sampah->id,
                    'bank_sampah_id' => $bank->id,
                    'harga' => $firstPrice,
                ]);
            }
        }

        $this->command->info('Sampah data seeded successfully!');
    }
}
