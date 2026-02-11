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
        Schema::table('sk_bank_sampah', function (Blueprint $table) {
            $table->enum('tipe_jabatan', ['desa', 'lurah'])
                ->default('desa')
                ->after('nama_kepala_desa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sk_bank_sampah', function (Blueprint $table) {
            $table->dropColumn('tipe_jabatan');
        });
    }
};
