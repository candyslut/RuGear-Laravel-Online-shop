<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Виден ли другим пользователям полный игровой профиль. Когда
            // false — чужой профиль показывает заглушку (рекорды в лидербордах
            // остаются видны, это только агрегат).
            $table->boolean('stats_public')->default(true)->after('redline_best_distance');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('stats_public');
        });
    }
};
