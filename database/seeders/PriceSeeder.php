<?php

namespace Database\Seeders;

use App\Models\BankSampah;
use App\Models\Price;
use App\Models\Sampah;
use Illuminate\Database\Seeder;

class PriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sampahList = Sampah::all();
        $bankSampahList = BankSampah::all();

        foreach ($sampahList as $sampah) {
            foreach ($bankSampahList as $bankSampah) {
                // Check if price already exists
                $existingPrice = Price::where('sampah_id', $sampah->id)
                    ->where('bank_sampah_id', $bankSampah->id)
                    ->first();

                if (! $existingPrice) {
                    // Generate random price between 1000 and 10000
                    $harga = rand(1000, 10000);

                    Price::create([
                        'sampah_id' => $sampah->id,
                        'bank_sampah_id' => $bankSampah->id,
                        'harga' => $harga,
                    ]);
                }
            }
        }

        $this->command->info('Price data seeded successfully!');
    }
}
