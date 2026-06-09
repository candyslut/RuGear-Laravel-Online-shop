<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Current daily-login streak, its all-time best, and the last day the
            // user was counted as active (one streak tick per calendar day).
            $table->unsignedInteger('streak')->default(0)->after('coins');
            $table->unsignedInteger('best_streak')->default(0)->after('streak');
            $table->date('last_active_date')->nullable()->after('best_streak');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['streak', 'best_streak', 'last_active_date']);
        });
    }
};
