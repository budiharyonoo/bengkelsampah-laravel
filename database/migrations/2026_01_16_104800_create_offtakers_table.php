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
        Schema::create('offtakers', function (Blueprint $table) {
            $table->id();
            $table->string('kode_offtaker')->unique();
            $table->string('nama');
            $table->enum('tipe', ['buyer', 'processor', 'both']);
            $table->string('nama_pic');
            $table->string('kontak_pic');
            $table->text('alamat')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // Index for filtering active offtakers
            $table->index(['is_active', 'tipe']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offtakers');
    }
};
