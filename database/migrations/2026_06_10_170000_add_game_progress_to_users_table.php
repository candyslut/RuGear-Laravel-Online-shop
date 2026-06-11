<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Прогресс мини-игр: открывает покупку легендарной косметики
            // (см. User::legendaryUnlocked()).
            $table->unsignedInteger('buzzword_levels')->default(0)->after('coins');
            $table->unsignedInteger('redline_best_distance')->default(0)->after('buzzword_levels');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['buzzword_levels', 'redline_best_distance']);
        });
    }
};
