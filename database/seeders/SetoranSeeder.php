<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\BankSampah;
use App\Models\Setoran;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SetoranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing users, bank sampah, and addresses
        $users = User::all();
        $bankSampahs = BankSampah::all();
        $addresses = Address::all();

        if ($users->isEmpty() || $bankSampahs->isEmpty() || $addresses->isEmpty()) {
            $this->command->warn('Skipping SetoranSeeder: No users, bank sampah, or addresses found');

            return;
        }

        $statuses = ['dikonfirmasi', 'diproses', 'dijemput', 'selesai', 'batal'];
        $tipeSetors = ['jual', 'sedekah', 'tabung'];

        // Create sample setoran data
        for ($i = 1; $i <= 20; $i++) {
            $user = $users->random();
            $bankSampah = $bankSampahs->random();
            $address = $addresses->random();
            $status = $statuses[array_rand($statuses)];
            $tipeSetor = $tipeSetors[array_rand($tipeSetors)];

            // Sample items data
            $items = [
                [
                    'sampah_id' => 1,
                    'estimasi_berat' => rand(5, 20),
                    'harga_per_satuan' => 2000.0,
                    'sampah_nama' => 'Botol Plastik',
                    'sampah_satuan' => 'KG',
                    'aktual_berat' => $status === 'selesai' ? rand(4, 22) : null,
                    'aktual_total' => $status === 'selesai' ? null : null, // Will be calculated
                    'status' => $status === 'selesai' ? '' : null,
                ],
                [
                    'sampah_id' => 2,
                    'estimasi_berat' => rand(3, 15),
                    'harga_per_satuan' => 1500.0,
                    'sampah_nama' => 'Kertas Bekas',
                    'sampah_satuan' => 'KG',
                    'aktual_berat' => $status === 'selesai' ? rand(2, 17) : null,
                    'aktual_total' => $status === 'selesai' ? null : null, // Will be calculated
                    'status' => $status === 'selesai' ? '' : null,
                ],
                [
                    'sampah_id' => 3,
                    'estimasi_berat' => rand(2, 10),
                    'harga_per_satuan' => 3000.0,
                    'sampah_nama' => 'Kaleng Bekas',
                    'sampah_satuan' => 'KG',
                    'aktual_berat' => $status === 'selesai' ? rand(1, 12) : null,
                    'aktual_total' => $status === 'selesai' ? null : null, // Will be calculated
                    'status' => $status === 'selesai' ? '' : null,
                ],
                [
                    'sampah_id' => 4,
                    'estimasi_berat' => rand(1, 8),
                    'harga_per_satuan' => 2500.0,
                    'sampah_nama' => 'Botol Kaca',
                    'sampah_satuan' => 'KG',
                    'aktual_berat' => $status === 'selesai' ? rand(1, 10) : null,
                    'aktual_total' => $status === 'selesai' ? null : null, // Will be calculated
                    'status' => $status === 'selesai' ? '' : null,
                ],
                [
                    'sampah_id' => 5,
                    'estimasi_berat' => rand(2, 12),
                    'harga_per_satuan' => 1800.0,
                    'sampah_nama' => 'Plastik Kemasan',
                    'sampah_satuan' => 'KG',
                    'aktual_berat' => $status === 'selesai' ? rand(1, 14) : null,
                    'aktual_total' => $status === 'selesai' ? null : null, // Will be calculated
                    'status' => $status === 'selesai' ? '' : null,
                ],
            ];

            // Calculate actual totals for completed transactions
            if ($status === 'selesai') {
                foreach ($items as &$item) {
                    $item['aktual_total'] = $item['aktual_berat'] * $item['harga_per_satuan'];
                }
            }

            $estimasiTotal = array_sum(array_map(function ($item) {
                return $item['estimasi_berat'] * $item['harga_per_satuan'];
            }, $items));

            // Calculate actual total for completed transactions
            $aktualTotal = null;
            if ($status === 'selesai') {
                $aktualTotal = array_sum(array_map(function ($item) {
                    return $item['aktual_total'];
                }, $items));
            }

            $setoran = Setoran::create([
                'user_id' => $user->id,
                'user_name' => $user->name ?? 'User '.$user->id,
                'user_identifier' => $user->email ?? $user->phone ?? 'user_'.$user->id,
                'bank_sampah_id' => $bankSampah->id,
                'bank_sampah_name' => $bankSampah->nama_bank_sampah ?? 'Bank Sampah '.$bankSampah->id,
                'bank_sampah_code' => $bankSampah->kode_bank_sampah ?? 'BS'.str_pad($bankSampah->id, 3, '0', STR_PAD_LEFT),
                'bank_sampah_address' => $bankSampah->alamat_bank_sampah ?? 'Alamat Bank Sampah '.$bankSampah->id,
                'bank_sampah_phone' => $bankSampah->kontak_penanggung_jawab ?? '08'.rand(100000000, 999999999),
                'address_id' => $address->id,
                'address_name' => $address->label_alamat ?? 'Alamat '.$address->id,
                'address_phone' => $address->nomor_handphone ?? '08'.rand(100000000, 999999999),
                'address_full_address' => $address->label_alamat.', '.$address->detail_lain.', '.$address->provinsi.', '.$address->kota_kabupaten.', '.$address->kecamatan.', '.$address->kode_pos,
                'address_is_default' => $address->is_default ?? false,
                'tipe_setor' => $tipeSetor,
                'status' => $status,
                'items_json' => json_encode($items),
                'estimasi_total' => $estimasiTotal,
                'aktual_total' => $aktualTotal,
                'tanggal_penjemputan' => Carbon::now()->addDays(rand(1, 7)),
                'waktu_penjemputan' => Carbon::now()->addDays(rand(1, 7))->setTime(rand(8, 17), 0, 0),
                'petugas_nama' => $status !== 'dikonfirmasi' ? ('Petugas '.rand(1, 5)) : null,
                'petugas_contact' => $status !== 'dikonfirmasi' ? ('08'.rand(100000000, 999999999)) : null,
                'foto_sampah' => 'sample_photo_'.rand(1, 3).'.jpg',
                'alasan_pembatalan' => $status === 'batal' ? $this->getRandomCancellationReason() : null,
                'tanggal_selesai' => $status === 'selesai' ? Carbon::now()->subDays(rand(1, 30)) : null,
                'tipe_layanan' => ['jemput', 'tempat', 'keduanya'][array_rand(['jemput', 'tempat', 'keduanya'])],
                'created_at' => Carbon::now()->subDays(rand(1, 90)),
                'updated_at' => Carbon::now()->subDays(rand(1, 90)),
            ]);
        }

        $this->command->info('SetoranSeeder completed successfully!');
    }

    private function getRandomNote()
    {
        $notes = [
            'Sampah sudah dipilah dengan baik',
            'Mohon penjemputan di pagi hari',
            'Ada sampah elektronik yang perlu penanganan khusus',
            'Sampah dalam kondisi baik dan bersih',
            'Mohon konfirmasi sebelum penjemputan',
        ];

        return $notes[array_rand($notes)];
    }

    private function getRandomCancellationReason()
    {
        $reasons = [
            'User membatalkan karena jadwal berubah',
            'Bank sampah tidak dapat melayani di waktu tersebut',
            'Kondisi cuaca tidak memungkinkan',
            'User tidak dapat ditemui saat penjemputan',
            'Ada masalah teknis di bank sampah',
        ];

        return $reasons[array_rand($reasons)];
    }
}
