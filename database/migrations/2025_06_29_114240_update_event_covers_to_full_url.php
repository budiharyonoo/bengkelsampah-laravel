<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update event covers from relative paths to full URLs
        DB::table('events')
            ->whereNotNull('cover')
            ->where('cover', 'not like', 'http%')
            ->update([
                'cover' => DB::raw("CONCAT('".env('APP_URL')."/uploads/', cover)"),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to relative paths
        DB::table('events')
            ->whereNotNull('cover')
            ->where('cover', 'like', env('APP_URL').'/uploads/%')
            ->update([
                'cover' => DB::raw("REPLACE(cover, '".env('APP_URL')."/uploads/', '')"),
            ]);
    }
};
