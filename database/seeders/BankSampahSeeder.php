<?php

namespace Database\Seeders;

use App\Models\BankSampah;
use Illuminate\Database\Seeder;

class BankSampahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bankSampahList = [
            [
                'kode_bank_sampah' => 'BS-001',
                'nama_bank_sampah' => 'Bank Sampah Hijau Bersih',
                'alamat_bank_sampah' => 'Jl. Sudirman No. 123, Jakarta Selatan',
                'nama_penanggung_jawab' => 'Ahmad Rizki',
                'kontak_penanggung_jawab' => '021-5550123',
                'tipe_layanan' => 'keduanya',
            ],
            [
                'kode_bank_sampah' => 'BS-002',
                'nama_bank_sampah' => 'Bank Sampah Eco Green',
                'alamat_bank_sampah' => 'Jl. Thamrin No. 45, Jakarta Pusat',
                'nama_penanggung_jawab' => 'Siti Nurhaliza',
                'kontak_penanggung_jawab' => '021-5550456',
                'tipe_layanan' => 'jemput',
            ],
            [
                'kode_bank_sampah' => 'BS-003',
                'nama_bank_sampah' => 'Bank Sampah Bersih Lingkungan',
                'alamat_bank_sampah' => 'Jl. Gatot Subroto No. 67, Jakarta Selatan',
                'nama_penanggung_jawab' => 'Budi Santoso',
                'kontak_penanggung_jawab' => '021-5550789',
                'tipe_layanan' => 'tempat',
            ],
            [
                'kode_bank_sampah' => 'BS-004',
                'nama_bank_sampah' => 'Bank Sampah Hijau Masa Depan',
                'alamat_bank_sampah' => 'Jl. Rasuna Said No. 89, Jakarta Selatan',
                'nama_penanggung_jawab' => 'Dewi Sartika',
                'kontak_penanggung_jawab' => '021-5550112',
                'tipe_layanan' => 'keduanya',
            ],
            [
                'kode_bank_sampah' => 'BS-005',
                'nama_bank_sampah' => 'Bank Sampah Lestari Alam',
                'alamat_bank_sampah' => 'Jl. Kuningan No. 12, Jakarta Selatan',
                'nama_penanggung_jawab' => 'Muhammad Fajar',
                'kontak_penanggung_jawab' => '021-5550145',
                'tipe_layanan' => 'jemput',
            ],
            [
                'kode_bank_sampah' => 'BS-006',
                'nama_bank_sampah' => 'Bank Sampah Sejahtera',
                'alamat_bank_sampah' => 'Jl. Senayan No. 34, Jakarta Pusat',
                'nama_penanggung_jawab' => 'Nina Safitri',
                'kontak_penanggung_jawab' => '021-5550178',
                'tipe_layanan' => 'tempat',
            ],
            [
                'kode_bank_sampah' => 'BS-007',
                'nama_bank_sampah' => 'Bank Sampah Mandiri',
                'alamat_bank_sampah' => 'Jl. Kebayoran Baru No. 56, Jakarta Selatan',
                'nama_penanggung_jawab' => 'Rudi Hermawan',
                'kontak_penanggung_jawab' => '021-5550210',
                'tipe_layanan' => 'keduanya',
            ],
            [
                'kode_bank_sampah' => 'BS-008',
                'nama_bank_sampah' => 'Bank Sampah Cinta Lingkungan',
                'alamat_bank_sampah' => 'Jl. Blok M No. 78, Jakarta Selatan',
                'nama_penanggung_jawab' => 'Lina Marlina',
                'kontak_penanggung_jawab' => '021-5550243',
                'tipe_layanan' => 'jemput',
            ],
            [
                'kode_bank_sampah' => 'BS-009',
                'nama_bank_sampah' => 'Bank Sampah Hijau Nusantara',
                'alamat_bank_sampah' => 'Jl. Pondok Indah No. 90, Jakarta Selatan',
                'nama_penanggung_jawab' => 'Agus Setiawan',
                'kontak_penanggung_jawab' => '021-5550276',
                'tipe_layanan' => 'tempat',
            ],
            [
                'kode_bank_sampah' => 'BS-010',
                'nama_bank_sampah' => 'Bank Sampah Ramah Lingkungan',
                'alamat_bank_sampah' => 'Jl. Cilandak No. 23, Jakarta Selatan',
                'nama_penanggung_jawab' => 'Yuni Safitri',
                'kontak_penanggung_jawab' => '021-5550309',
                'tipe_layanan' => 'keduanya',
            ],
        ];

        foreach ($bankSampahList as $bankData) {
            BankSampah::updateOrCreate(
                ['kode_bank_sampah' => $bankData['kode_bank_sampah']],
                $bankData
            );
        }

        $this->command->info('Bank Sampah data seeded successfully!');
    }
}
