<?php

namespace Database\Seeders;

use App\Models\Address;
use Illuminate\Database\Seeder;

class AddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $addresses = [
            [
                'user_id' => 1,
                'nama' => 'John Doe',
                'nomor_handphone' => '081234567890',
                'label_alamat' => 'Rumah',
                'provinsi' => 'DKI Jakarta',
                'kota_kabupaten' => 'Jakarta Selatan',
                'kecamatan' => 'Kebayoran Baru',
                'kode_pos' => '12120',
                'detail_lain' => 'Jl. Kebayoran Baru No. 123, RT 001/RW 002',
                'is_default' => true,
            ],
            [
                'user_id' => 1,
                'nama' => 'John Doe',
                'nomor_handphone' => '081234567890',
                'label_alamat' => 'Kantor',
                'provinsi' => 'DKI Jakarta',
                'kota_kabupaten' => 'Jakarta Pusat',
                'kecamatan' => 'Tanah Abang',
                'kode_pos' => '10250',
                'detail_lain' => 'Gedung Office Park Lt. 5, Jl. Sudirman Kav. 52-53',
                'is_default' => false,
            ],
            [
                'user_id' => 1,
                'nama' => 'John Doe',
                'nomor_handphone' => '081234567890',
                'label_alamat' => 'Kos',
                'provinsi' => 'DKI Jakarta',
                'kota_kabupaten' => 'Jakarta Selatan',
                'kecamatan' => 'Mampang Prapatan',
                'kode_pos' => '12790',
                'detail_lain' => 'Kos Putri Melati No. 45, Blok A',
                'is_default' => false,
            ],
        ];

        foreach ($addresses as $address) {
            Address::create($address);
        }
    }
}
