<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_quests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // The day this quest belongs to (one set of three per calendar day).
            $table->date('quest_date');
            // Which catalog template (App\Support\Quests) this row instantiates.
            $table->string('slug');
            // Progress toward the goal, plus a snapshot of the goal/reward so a
            // later tweak to the catalog never changes an in-flight quest.
            $table->unsignedInteger('progress')->default(0);
            $table->unsignedInteger('goal');
            $table->unsignedInteger('reward_coins');
            $table->unsignedInteger('reward_xp');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            // One row per quest per day per user; fast lookup of "today's set".
            $table->unique(['user_id', 'quest_date', 'slug']);
            $table->index(['user_id', 'quest_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_quests');
    }
};
