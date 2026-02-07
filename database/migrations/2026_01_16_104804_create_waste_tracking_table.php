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
        Schema::create('waste_tracking', function (Blueprint $table) {
            $table->id();
            $table->foreignId('setoran_id')->constrained('setorans')->cascadeOnDelete();
            $table->foreignId('bank_sampah_id')->constrained('bank_sampah')->cascadeOnDelete();
            $table->foreignId('sampah_id')->constrained('sampah')->cascadeOnDelete();
            $table->decimal('quantity', 10, 2);
            $table->string('unit', 20)->default('kg');
            $table->decimal('harga_beli', 10, 2)->comment('Purchase price from user');
            $table->enum('status', ['stored', 'sold', 'processed'])->default('stored');
            $table->timestamp('sold_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->foreignId('waste_transaction_id')->nullable()->constrained('waste_transactions')->nullOnDelete();
            $table->timestamps();

            // Composite index for FIFO queries (get oldest stored items first)
            $table->index(['bank_sampah_id', 'sampah_id', 'status', 'created_at'], 'waste_tracking_fifo_index');
            // Index for tracking by setoran
            $table->index('setoran_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('waste_tracking');
    }
};
