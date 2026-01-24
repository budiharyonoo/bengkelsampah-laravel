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
        Schema::create('blast_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('body');
            $table->string('topic'); // broadcast or broadcast-gocap
            $table->unsignedBigInteger('sent_by'); // admin ID
            $table->string('sent_by_name'); // admin name
            $table->string('sent_by_role'); // admin or cabang
            $table->enum('status', ['success', 'failed'])->default('success');
            $table->text('error_message')->nullable();
            $table->json('fcm_response')->nullable(); // Store full FCM response
            $table->timestamps();

            $table->foreign('sent_by')->references('id')->on('admins')->onDelete('cascade');
            $table->index(['topic', 'status']);
            $table->index('sent_by');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blast_notifications');
    }
};
