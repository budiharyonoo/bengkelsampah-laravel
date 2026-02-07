<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sk_bank_sampah', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_sampah_id')
                ->unique()
                ->constrained('bank_sampah')
                ->onDelete('cascade');

            // Data Bank Sampah (disimpan terpisah, tidak referensi dari bank_sampah)
            $table->string('nama_bank_sampah', 255);
            $table->text('alamat_bank_sampah');

            // Data SK
            $table->string('nomor_surat', 100);
            $table->date('tanggal_ditetapkan');
            $table->date('tanggal_rapat_musyawarah');

            // Data Wilayah
            $table->string('nama_desa_kelurahan', 100);
            $table->string('kecamatan', 100);
            $table->string('kabupaten', 100);
            $table->string('provinsi', 100);
            $table->string('kode_pos', 10)->nullable();

            // Masa Bakti
            $table->year('masa_bakti_mulai');
            $table->year('masa_bakti_selesai');

            // Pengesahan
            $table->string('nama_kepala_desa', 255);

            // Image paths (pengurus sebagai gambar)
            $table->string('pengurus_image_path', 500)->nullable();
            $table->string('struktur_organisasi_path', 500)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sk_bank_sampah');
    }
};
