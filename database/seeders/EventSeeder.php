<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Event;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get first admin or create one if none exists
        $admin = Admin::first();
        if (! $admin) {
            $admin = Admin::create([
                'name' => 'Admin Bengkel Sampah',
                'email' => 'admin@bengkelsampah.com',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ]);
        }

        // Create sample events
        Event::create([
            'title' => 'Kutip Sampah Ramai-ramai di Taman Kota',
            'description' => 'Ayo bergabung dalam kegiatan kutip sampah ramai-ramai di Taman Kota. Kegiatan ini bertujuan untuk membersihkan taman dari sampah dan meningkatkan kesadaran masyarakat akan pentingnya menjaga kebersihan lingkungan.',
            'cover' => 'events/taman-kota-cover.jpg',
            'start_datetime' => now()->addDays(7)->setTime(8, 0),
            'end_datetime' => now()->addDays(7)->setTime(12, 0),
            'location' => 'Taman Kota, Jl. Sudirman No. 123',
            'max_participants' => 50,
            'status' => 'active',
            'admin_name' => $admin->name,
        ]);

        Event::create([
            'title' => 'Kampanye Zero Waste di Mall',
            'description' => 'Kampanye zero waste untuk mengedukasi pengunjung mall tentang cara mengurangi sampah plastik dan menerapkan gaya hidup ramah lingkungan.',
            'cover' => 'events/zero-waste-mall.jpg',
            'start_datetime' => now()->addDays(14)->setTime(10, 0),
            'end_datetime' => now()->addDays(14)->setTime(16, 0),
            'location' => 'Mall Central, Lantai 1',
            'max_participants' => 30,
            'status' => 'active',
            'admin_name' => $admin->name,
        ]);

        Event::create([
            'title' => 'Bersih-bersih Pantai',
            'description' => 'Kegiatan membersihkan pantai dari sampah plastik dan sampah lainnya. Mari kita jaga keindahan pantai kita bersama-sama.',
            'cover' => 'events/bersih-pantai.jpg',
            'start_datetime' => now()->addDays(21)->setTime(7, 0),
            'end_datetime' => now()->addDays(21)->setTime(11, 0),
            'location' => 'Pantai Indah, Jl. Pantai No. 45',
            'max_participants' => 100,
            'status' => 'active',
            'admin_name' => $admin->name,
        ]);

        Event::create([
            'title' => 'Workshop Daur Ulang Sampah',
            'description' => 'Workshop mengajarkan cara mendaur ulang sampah menjadi barang yang berguna. Peserta akan diajarkan membuat kerajinan dari sampah plastik dan kertas.',
            'cover' => 'events/workshop-daur-ulang.jpg',
            'start_datetime' => now()->addDays(28)->setTime(9, 0),
            'end_datetime' => now()->addDays(28)->setTime(15, 0),
            'location' => 'Balai Kota, Ruang Meeting',
            'max_participants' => 25,
            'status' => 'active',
            'admin_name' => $admin->name,
        ]);

        // Create a completed event
        Event::create([
            'title' => 'Kutip Sampah di Sekolah',
            'description' => 'Kegiatan kutip sampah yang sudah selesai dilaksanakan di beberapa sekolah dasar untuk mengedukasi anak-anak tentang pentingnya menjaga kebersihan.',
            'cover' => 'events/kutip-sekolah.jpg',
            'start_datetime' => now()->subDays(7)->setTime(8, 0),
            'end_datetime' => now()->subDays(7)->setTime(12, 0),
            'location' => 'SDN 01, SDN 02, SDN 03',
            'max_participants' => 40,
            'status' => 'completed',
            'admin_name' => $admin->name,
        ]);

        // Tambahkan 25 dummy event
        $faker = \Faker\Factory::create('id_ID');
        $statuses = ['active', 'completed', 'cancelled'];
        for ($i = 0; $i < 25; $i++) {
            $start = $faker->dateTimeBetween('-2 months', '+2 months');
            $end = (clone $start)->modify('+'.rand(2, 8).' hours');
            Event::create([
                'title' => $faker->sentence(4),
                'description' => $faker->paragraph(3),
                'cover' => 'https://picsum.photos/seed/event'.$i.'/400/200',
                'start_datetime' => $start,
                'end_datetime' => $end,
                'location' => $faker->address,
                'max_participants' => $faker->numberBetween(10, 200),
                'status' => $statuses[array_rand($statuses)],
                'admin_name' => $admin->name,
            ]);
        }

        // Create sample events
        Event::create([
            'title' => 'Bersih-bersih Pantai Kuta',
            'description' => 'Kegiatan bersih-bersih pantai untuk menjaga kebersihan lingkungan pantai Kuta. Mari bergabung untuk membuat pantai kita lebih bersih dan indah.',
            'cover' => null,
            'start_datetime' => now()->addDays(7),
            'end_datetime' => now()->addDays(7)->addHours(3),
            'location' => 'Pantai Kuta, Bali',
            'max_participants' => 50,
            'status' => 'active',
            'admin_name' => $admin->name,
        ]);

        Event::create([
            'title' => 'Kampanye Daur Ulang Sampah Plastik',
            'description' => 'Kampanye edukasi tentang pentingnya daur ulang sampah plastik. Akan ada workshop cara membuat kerajinan dari sampah plastik.',
            'cover' => null,
            'start_datetime' => now()->addDays(14),
            'end_datetime' => now()->addDays(14)->addHours(4),
            'location' => 'Taman Kota Denpasar',
            'max_participants' => 30,
            'status' => 'active',
            'admin_name' => $admin->name,
        ]);

        Event::create([
            'title' => 'Bersih-bersih Sungai Ayung',
            'description' => 'Kegiatan membersihkan sungai Ayung dari sampah yang mengalir. Penting untuk menjaga ekosistem sungai tetap sehat.',
            'cover' => null,
            'start_datetime' => now()->subDays(5),
            'end_datetime' => now()->subDays(5)->addHours(2),
            'location' => 'Sungai Ayung, Gianyar',
            'max_participants' => 40,
            'status' => 'completed',
            'admin_name' => $admin->name,
            'result_description' => 'Kegiatan berhasil membersihkan sungai dari sampah plastik dan organik. Total sampah terkumpul mencapai 150 kg.',
            'saved_waste_amount' => 150.50,
            'actual_participants' => 35,
            'result_submitted_at' => now()->subDays(4),
            'result_submitted_by_name' => $admin->name,
        ]);

        Event::create([
            'title' => 'Workshop Kompos Organik',
            'description' => 'Workshop membuat kompos dari sampah organik rumah tangga. Peserta akan diajarkan teknik composting yang benar.',
            'cover' => null,
            'start_datetime' => now()->subDays(10),
            'end_datetime' => now()->subDays(10)->addHours(3),
            'location' => 'Kantor Desa Sanur',
            'max_participants' => 25,
            'status' => 'completed',
            'admin_name' => $admin->name,
            'result_description' => 'Workshop berhasil mengedukasi 20 peserta tentang teknik composting. Peserta berhasil membuat kompos dari sampah organik.',
            'saved_waste_amount' => 25.00,
            'actual_participants' => 20,
            'result_submitted_at' => now()->subDays(9),
            'result_submitted_by_name' => $admin->name,
        ]);
    }
}
