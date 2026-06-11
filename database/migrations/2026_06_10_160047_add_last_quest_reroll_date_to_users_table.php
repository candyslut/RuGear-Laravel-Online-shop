<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Реролл ежедневных заданий доступен один раз в сутки;
            // здесь хранится дата последнего использования.
            $table->date('last_quest_reroll_date')->nullable()->after('last_active_date');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('last_quest_reroll_date');
        });
    }
};
