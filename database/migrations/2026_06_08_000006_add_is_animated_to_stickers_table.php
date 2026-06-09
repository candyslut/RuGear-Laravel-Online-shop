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
        Schema::table('stickers', function (Blueprint $table) {
            // Animated stickers are Lottie JSON (rendered on a <canvas>);
            // static ones are images such as Telegram .webp (rendered as <img>).
            $table->boolean('is_animated')->default(true)->after('file_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stickers', function (Blueprint $table) {
            $table->dropColumn('is_animated');
        });
    }
};
