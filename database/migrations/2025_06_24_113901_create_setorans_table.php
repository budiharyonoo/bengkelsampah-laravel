<?php

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
        Schema::create('setorans', function (Blueprint $table) {
            $table->id();

            // User data (stored as text to avoid foreign key issues)
            $table->unsignedBigInteger('user_id');
            $table->string('user_name');
            $table->string('user_identifier'); // email or phone

            // Bank Sampah data (stored as text to avoid foreign key issues)
            $table->unsignedBigInteger('bank_sampah_id');
            $table->string('bank_sampah_name');
            $table->string('bank_sampah_code');
            $table->string('bank_sampah_address');
            $table->string('bank_sampah_phone');

            // Address data (stored as text to avoid foreign key issues)
            $table->unsignedBigInteger('address_id');
            $table->string('address_name');
            $table->string('address_phone');
            $table->text('address_full_address');
            $table->boolean('address_is_default');

            // Deposit details
            $table->enum('tipe_setor', ['jual', 'sedekah', 'tabung']);
            $table->enum('status', ['dikonfirmasi', 'diproses', 'dijemput', 'selesai', 'batal'])->default('dikonfirmasi');
            $table->text('items_json'); // JSON array of items with details
            $table->decimal('estimasi_total', 10, 2); // Estimated total from user
            $table->decimal('aktual_total', 10, 2)->nullable(); // Actual total from bank

            // Schedule and pickup info
            $table->date('tanggal_penjemputan')->nullable();
            $table->time('waktu_penjemputan')->nullable();
            $table->string('petugas_nama')->nullable(); // Officer name for pickup
            $table->string('petugas_contact')->nullable(); // Officer contact for pickup

            // Photo
            $table->string('foto_sampah')->nullable(); // Path to photo

            // Notes and completion
            $table->text('notes')->nullable(); // General notes
            $table->text('alasan_pembatalan')->nullable(); // Cancellation reason
            $table->text('perubahan_data')->nullable(); // Data changes notes
            $table->timestamp('tanggal_selesai')->nullable(); // Completion date

            // Service type
            $table->enum('tipe_layanan', ['jemput', 'tempat', 'keduanya']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setorans');
    }
};
