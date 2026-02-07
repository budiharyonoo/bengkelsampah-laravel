<?php

namespace Database\Seeders;

use App\Models\AppVersion;
use Illuminate\Database\Seeder;

class AppVersionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AppVersion::create([
            'platform' => 'android',
            'version' => '1.0.0',
            'version_code' => 1,
            'is_required' => false,
            'update_message' => 'Versi terbaru tersedia dengan fitur baru dan perbaikan bug.',
            'store_url' => 'https://play.google.com/store/apps/details?id=com.bengkelsampah.app',
            'is_active' => true,
        ]);

        AppVersion::create([
            'platform' => 'android',
            'version' => '1.1.0',
            'version_code' => 2,
            'is_required' => false,
            'update_message' => 'Update penting dengan perbaikan keamanan dan performa.',
            'store_url' => 'https://play.google.com/store/apps/details?id=com.bengkelsampah.app',
            'is_active' => true,
        ]);

        AppVersion::create([
            'platform' => 'android',
            'version' => '1.2.0',
            'version_code' => 3,
            'is_required' => true,
            'update_message' => 'Update wajib! Versi ini memperbaiki masalah keamanan penting.',
            'store_url' => 'https://play.google.com/store/apps/details?id=com.bengkelsampah.app',
            'is_active' => true,
        ]);
    }
}
