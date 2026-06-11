<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Один забег мини-игры = одна строка. Источник правды для всей
        // детальной статистики (графики по дням, рекорды, средние, тренды).
        // Пишется на завершении забега (смерть/закрытие в Redline, game over
        // в Buzzword). Награды за монеты/XP идут отдельными эндпоинтами —
        // здесь хранится только снимок результата сессии.
        Schema::create('game_plays', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // Ключ игры из реестра App\Support\GameStats (buzzword|redline).
            $table->string('game', 32);
            // Очки забега: метры в Redline, очки в Buzzword.
            $table->unsignedInteger('score')->default(0);
            // Уровень Buzzword / число взятых рубежей Redline (опционально).
            $table->unsignedSmallInteger('level')->nullable();
            $table->unsignedInteger('duration_ms')->default(0);
            $table->unsignedSmallInteger('coins_earned')->default(0);
            $table->unsignedSmallInteger('xp_earned')->default(0);
            // Запас под игро-специфичные детали (без миграций под каждую игру).
            $table->json('meta')->nullable();
            $table->timestamps();

            // Профиль игрока и лидерборды читают по (user, game) и по рекорду.
            $table->index(['user_id', 'game', 'created_at']);
            $table->index(['game', 'score']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_plays');
    }
};
