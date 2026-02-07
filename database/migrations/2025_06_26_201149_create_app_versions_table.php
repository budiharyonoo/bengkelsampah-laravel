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
        if (Schema::hasTable('app_versions')) {
            return;
        }
        Schema::create('app_versions', function (Blueprint $table) {
            $table->id();
            $table->string('platform'); // android, ios
            $table->string('version'); // e.g., "1.0.0"
            $table->integer('version_code'); // e.g., 1, 2, 3
            $table->boolean('is_required')->default(false); // force update or not
            $table->text('update_message')->nullable(); // message to show to user
            $table->string('store_url')->nullable(); // play store/app store url
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['platform', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_versions');
    }
};
