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
        if (!Schema::hasColumn('bank_sampah', 'gmaps_link')) {
            Schema::table('bank_sampah', function (Blueprint $table) {
                $table->string('gmaps_link')->nullable()->after('foto');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bank_sampah', function (Blueprint $table) {
            $table->dropColumn('gmaps_link');
        });
    }
};
