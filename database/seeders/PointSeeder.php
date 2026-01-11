<?php

namespace Database\Seeders;

use App\Models\Point;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PointSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing users
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->warn('Skipping PointSeeder: No users found');

            return;
        }

        $redeemReasons = [
            'Redeem Voucher Belanja',
            'Redeem Pulsa',
            'Redeem GoPay',
            'Redeem OVO',
            'Redeem DANA',
            'Redeem ShopeePay',
            'Redeem Tokopedia',
            'Redeem GrabPay',
            'Redeem Voucher GoFood',
            'Redeem Voucher GrabFood',
            'Redeem Voucher ShopeeFood',
            'Redeem Voucher Tokopedia',
            'Redeem Voucher Cinema',
            'Redeem Voucher Coffee Shop',
            'Redeem Voucher Restaurant',
            'Redeem Voucher Mall',
            'Redeem Voucher Transport',
            'Redeem Voucher Entertainment',
            'Redeem Voucher Education',
            'Redeem Voucher Health',
        ];

        $redeemAmounts = [
            1000, 2000, 5000, 10000, 15000, 20000, 25000, 30000, 50000, 75000, 100000,
        ];

        // Create sample redeem data (negative points for redeem)
        for ($i = 1; $i <= 50; $i++) {
            $user = $users->random();
            $redeemReason = $redeemReasons[array_rand($redeemReasons)];
            $redeemAmount = $redeemAmounts[array_rand($redeemAmounts)];

            // Random date within last 6 months
            $randomDate = Carbon::now()->subDays(rand(1, 180));

            $point = Point::create([
                'user_id' => $user->id,
                'user_name' => $user->name ?? 'User '.$user->id,
                'user_identifier' => $user->email ?? $user->phone ?? 'user_'.$user->id,
                'type' => 'redeem',
                'tanggal' => $randomDate->format('Y-m-d'),
                'jumlah_point' => -$redeemAmount, // Negative for redeem
                'setoran_id' => null,
                'keterangan' => $redeemReason,
                'bukti_redeem' => $this->getRandomProofImage(),
                'created_at' => $randomDate,
                'updated_at' => $randomDate,
            ]);
        }

        // Create some positive points from setoran (earning points)
        for ($i = 1; $i <= 30; $i++) {
            $user = $users->random();
            $earnAmount = rand(100, 5000); // Smaller amounts for earning

            // Random date within last 6 months
            $randomDate = Carbon::now()->subDays(rand(1, 180));

            $point = Point::create([
                'user_id' => $user->id,
                'user_name' => $user->name ?? 'User '.$user->id,
                'user_identifier' => $user->email ?? $user->phone ?? 'user_'.$user->id,
                'type' => 'setor',
                'tanggal' => $randomDate->format('Y-m-d'),
                'jumlah_point' => $earnAmount, // Positive for earning
                'setoran_id' => rand(1, 20), // Random setoran ID
                'keterangan' => 'Poin dari setoran sampah',
                'bukti_redeem' => null,
                'created_at' => $randomDate,
                'updated_at' => $randomDate,
            ]);
        }

        $this->command->info('PointSeeder completed successfully! Created 80 point records (50 redeems + 30 earnings)');
    }

    private function getRandomProofImage()
    {
        $proofImages = [
            'uploads/redeem/redeem_1750845405_Screenshot 2025-06-25 at 10.21.28.png',
            'uploads/redeem/redeem_1750845557_Screenshot 2025-06-25 at 10.21.28.png',
            'uploads/redeem/redeem_1750904953_Screenshot 2025-06-26 at 05.50.43.png',
            'uploads/redeem/proof_1.png',
            'uploads/redeem/proof_2.png',
            'uploads/redeem/proof_3.png',
            'uploads/redeem/proof_4.png',
            'uploads/redeem/proof_5.png',
        ];

        return $proofImages[array_rand($proofImages)];
    }
}
