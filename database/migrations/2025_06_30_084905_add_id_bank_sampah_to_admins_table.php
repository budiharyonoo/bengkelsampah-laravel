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
        if (!Schema::hasColumn('admins', 'id_bank_sampah')) {
            Schema::table('admins', function (Blueprint $table) {
                $table->unsignedBigInteger('id_bank_sampah')->nullable()->after('role');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn('id_bank_sampah');
        });
    }
};
