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
        Schema::create('user_redeems', function (Blueprint $table) {
            $table->id();

            // User information
            $table->unsignedBigInteger('user_id');
            $table->string('user_name');
            $table->string('user_identifier'); // email or phone

            // Redeem item information (snapshot at redeem time)
            $table->unsignedInteger('redeem_item_id');
            $table->string('redeem_item_name');
            $table->text('redeem_item_description')->nullable();

            // Points and status
            $table->decimal('point_used', 10, 2);
            $table->enum('status', ['waiting', 'approved', 'rejected', 'cancelled'])->default('waiting');

            // Admin action tracking
            $table->unsignedBigInteger('status_changed_by')->nullable();
            $table->string('status_changed_by_name')->nullable();
            $table->timestamp('status_changed_at')->nullable();

            // Proof and additional info
            $table->string('reward_proof_url')->nullable();
            $table->json('info_json')->nullable(); // Stores user delivery info, etc.
            $table->text('notes')->nullable(); // Admin notes or rejection reason

            $table->timestamps();

            // Indexes for performance
            $table->index('user_id');
            $table->index('redeem_item_id');
            $table->index('status');
            $table->index(['user_id', 'status']);
            $table->index(['redeem_item_id', 'status']);
            $table->index('created_at');

            // Foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            // Note: redeem_items foreign key removed - table created manually without migration
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_redeems');
    }
};
