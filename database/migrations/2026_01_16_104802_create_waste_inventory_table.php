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
        Schema::create('waste_inventory', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_sampah_id')->constrained('bank_sampah')->cascadeOnDelete();
            $table->foreignId('sampah_id')->constrained('sampah')->cascadeOnDelete();
            $table->decimal('quantity', 10, 2)->default(0);
            $table->string('unit', 20)->default('kg');
            $table->timestamps();

            // Unique constraint - one inventory record per bank_sampah + sampah combination
            $table->unique(['bank_sampah_id', 'sampah_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('waste_inventory');
    }
};
