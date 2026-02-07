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
        Schema::create('points', function (Blueprint $table) {
            $table->id();

            // User data (stored as text to avoid foreign key issues)
            $table->unsignedBigInteger('user_id');
            $table->string('user_name');
            $table->string('user_identifier'); // email or phone

            // Point transaction details
            $table->enum('type', ['setor', 'redeem']);
            $table->date('tanggal');
            $table->integer('jumlah_point');

            // Reference to setoran (if type is 'setor')
            $table->unsignedBigInteger('setoran_id')->nullable();

            // Description
            $table->text('keterangan')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('points');
    }
};
