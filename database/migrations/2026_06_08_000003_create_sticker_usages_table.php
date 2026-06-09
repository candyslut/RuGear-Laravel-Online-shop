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
        Schema::create('sticker_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('sticker_id')->constrained()->onDelete('cascade');
            $table->timestamp('used_at')->useCurrent();

            // One row per user+sticker; we bump used_at on re-use to drive the "Recent" tab.
            $table->unique(['user_id', 'sticker_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sticker_usages');
    }
};
