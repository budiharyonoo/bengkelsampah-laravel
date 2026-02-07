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
        Schema::create('waste_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('kode_transaksi')->unique();
            $table->foreignId('bank_sampah_id')->constrained('bank_sampah')->cascadeOnDelete();
            $table->foreignId('offtaker_id')->constrained('offtakers')->cascadeOnDelete();
            $table->enum('type', ['sale', 'processing']);
            $table->json('items_json')->comment('Array of sold/processed items with details');
            $table->decimal('total_quantity', 10, 2);
            $table->decimal('total_value', 15, 2)->comment('Sale price from offtaker');
            $table->decimal('harga_beli_total', 15, 2)->comment('Total purchase cost for profit calculation');
            $table->string('struk')->nullable()->comment('Receipt image path');
            $table->string('metode_pengolahan')->nullable()->comment('Processing method (for processing type)');
            $table->text('hasil_pengolahan')->nullable()->comment('Processing output notes');
            $table->text('notes')->nullable();
            $table->foreignId('admin_id')->constrained('admins')->cascadeOnDelete();
            $table->string('admin_name')->comment('Denormalized admin name for history');
            $table->date('tanggal_transaksi');
            $table->timestamps();

            // Composite indexes for dashboard queries
            $table->index(['bank_sampah_id', 'type', 'tanggal_transaksi'], 'waste_transactions_dashboard_index');
            $table->index(['offtaker_id', 'tanggal_transaksi']);
            $table->index('tanggal_transaksi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('waste_transactions');
    }
};
