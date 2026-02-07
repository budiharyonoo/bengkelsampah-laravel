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
        Schema::table('artikels', function (Blueprint $table) {
            // Check if column doesn't exist before adding
            if (! Schema::hasColumn('artikels', 'bank_sampah_id')) {
                // Add nullable bank_sampah_id column after kategori_id
                $table->unsignedBigInteger('bank_sampah_id')->nullable()->after('kategori_id');
            }

            // Add foreign key constraint with set null on delete
            $table->foreign('bank_sampah_id')
                ->references('id')
                ->on('bank_sampah')
                ->onDelete('set null');

            // Add composite index for query performance
            $table->index(['bank_sampah_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('artikels', function (Blueprint $table) {
            // Drop foreign key and index before dropping column
            $table->dropForeign(['bank_sampah_id']);
            $table->dropIndex(['bank_sampah_id', 'created_at']);
            $table->dropColumn('bank_sampah_id');
        });
    }
};
