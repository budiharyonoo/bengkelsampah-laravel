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
        Schema::create('xp_reset_history', function (Blueprint $table) {
            $table->id();
            $table->timestamp('reset_at');
            $table->unsignedBigInteger('admin_id');
            $table->string('admin_name');
            $table->unsignedBigInteger('top1_user_id')->nullable();
            $table->string('top1_name')->nullable();
            $table->decimal('top1_xp', 10, 2)->nullable();
            $table->unsignedBigInteger('top2_user_id')->nullable();
            $table->string('top2_name')->nullable();
            $table->decimal('top2_xp', 10, 2)->nullable();
            $table->unsignedBigInteger('top3_user_id')->nullable();
            $table->string('top3_name')->nullable();
            $table->decimal('top3_xp', 10, 2)->nullable();
            $table->timestamps();

            $table->index('reset_at');
            $table->index('admin_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('xp_reset_history');
    }
};
