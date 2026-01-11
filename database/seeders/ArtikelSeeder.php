<?php

namespace Database\Seeders;

use App\Models\Artikel;
use App\Models\KategoriArtikel;
use Illuminate\Database\Seeder;

class ArtikelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategoriIds = KategoriArtikel::pluck('id')->toArray();

        $artikels = [
            [
                'title' => '5 Tips Memilah Sampah dengan Benar',
                'content' => 'Memilah sampah adalah langkah awal yang penting dalam pengelolaan sampah yang baik. Berikut 5 tips untuk memilah sampah dengan benar:

1. Pisahkan sampah organik dan anorganik
2. Bersihkan sampah sebelum dibuang
3. Gunakan tempat sampah terpisah
4. Edukasi keluarga tentang pemilahan sampah
5. Konsisten dalam memilah sampah

Dengan menerapkan tips-tips ini, kita bisa membantu mengurangi beban TPA dan mendukung program daur ulang.',
                'cover' => 'https://example.com/images/tips-memilah-sampah.jpg',
                'kategori_id' => $kategoriIds[0], // Tips & Trik
                'creator' => 'Admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Kampanye "Zero Waste" di Jakarta',
                'content' => 'Jakarta meluncurkan kampanye "Zero Waste" untuk mengurangi sampah di ibu kota. Program ini melibatkan berbagai pihak termasuk pemerintah, swasta, dan masyarakat.

Target utama kampanye ini adalah:
- Mengurangi 30% sampah di TPA
- Meningkatkan kesadaran masyarakat
- Mendorong inovasi pengelolaan sampah

Mari dukung kampanye ini untuk Jakarta yang lebih bersih!',
                'cover' => 'https://example.com/images/zero-waste-jakarta.jpg',
                'kategori_id' => $kategoriIds[3], // Kampanye
                'creator' => 'Admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Mengenal Jenis-jenis Plastik',
                'content' => 'Plastik memiliki berbagai jenis dengan karakteristik yang berbeda. Mari kita kenali:

1. PET (Polyethylene Terephthalate)
   - Biasanya untuk botol minuman
   - Mudah didaur ulang

2. HDPE (High-Density Polyethylene)
   - Untuk botol susu dan deterjen
   - Tahan panas dan bahan kimia

3. PVC (Polyvinyl Chloride)
   - Untuk pipa dan mainan
   - Sulit didaur ulang

4. LDPE (Low-Density Polyethylene)
   - Untuk kantong plastik
   - Fleksibel dan tahan air

5. PP (Polypropylene)
   - Untuk wadah makanan
   - Tahan panas

6. PS (Polystyrene)
   - Untuk gelas dan piring sekali pakai
   - Sulit terurai

7. Other (Campuran)
   - Untuk berbagai keperluan
   - Sulit didaur ulang',
                'cover' => 'https://example.com/images/jenis-plastik.jpg',
                'kategori_id' => $kategoriIds[2], // Edukasi
                'creator' => 'Admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Inovasi Baru dalam Daur Ulang Sampah',
                'content' => 'Perusahaan teknologi lokal meluncurkan inovasi baru dalam sistem daur ulang sampah. Teknologi ini menggunakan AI untuk memilah sampah secara otomatis dan meningkatkan efisiensi proses daur ulang.

Keunggulan sistem ini:
- Akurasi pemilahan 95%
- Kapasitas pemrosesan tinggi
- Ramah lingkungan
- Hemat energi

Inovasi ini diharapkan dapat meningkatkan tingkat daur ulang di Indonesia.',
                'cover' => 'https://example.com/images/daur-ulang-ai.jpg',
                'kategori_id' => $kategoriIds[1], // Berita
                'creator' => 'Admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Cara Membuat Kompos dari Sampah Dapur',
                'content' => 'Membuat kompos dari sampah dapur adalah cara mudah untuk mengolah sampah organik. Berikut langkah-langkahnya:

1. Siapkan wadah kompos
2. Kumpulkan sampah organik
3. Tambahkan bahan kering
4. Aduk secara berkala
5. Tunggu 2-3 bulan

Keuntungan membuat kompos:
- Mengurangi sampah
- Menghasilkan pupuk alami
- Baik untuk tanaman
- Ramah lingkungan',
                'cover' => 'https://example.com/images/kompos-dapur.jpg',
                'kategori_id' => $kategoriIds[0], // Tips & Trik
                'creator' => 'Admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($artikels as $artikel) {
            Artikel::create($artikel);
        }
    }
}
