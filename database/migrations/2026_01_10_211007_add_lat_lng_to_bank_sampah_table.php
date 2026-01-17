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
        Schema::table('bank_sampah', function (Blueprint $table) {
            if (!Schema::hasColumn('bank_sampah', 'latitude')) {
                $table->decimal('latitude', 10, 8)->nullable()->after('gmaps_link');
            }
            if (!Schema::hasColumn('bank_sampah', 'longitude')) {
                $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bank_sampah', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude']);
        });
    }
};
