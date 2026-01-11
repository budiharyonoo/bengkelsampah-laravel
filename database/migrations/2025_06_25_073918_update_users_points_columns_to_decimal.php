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
        Schema::table('users', function (Blueprint $table) {
            // Change poin column to decimal
            $table->decimal('poin', 15, 2)->default(0)->change();

            // Change sampah column to decimal
            $table->decimal('sampah', 10, 2)->default(0)->change();

            // Change xp column to decimal
            $table->decimal('xp', 10, 2)->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Revert back to integer
            $table->integer('poin')->default(0)->change();
            $table->integer('sampah')->default(0)->change();
            $table->integer('xp')->default(0)->change();
        });
    }
};
