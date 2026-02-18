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
        $prefix = env('APP_URL').'/uploads/';
        $driver = DB::getDriverName();

        $expression = $driver === 'sqlite'
            ? "'{$prefix}' || cover"
            : "CONCAT('{$prefix}', cover)";

        DB::table('events')
            ->whereNotNull('cover')
            ->where('cover', 'not like', 'http%')
            ->update([
                'cover' => DB::raw($expression),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to relative paths
        $prefix = env('APP_URL').'/uploads/';

        DB::table('events')
            ->whereNotNull('cover')
            ->where('cover', 'like', $prefix.'%')
            ->update([
                'cover' => DB::raw("REPLACE(cover, '{$prefix}', '')"),
            ]);
    }
};
