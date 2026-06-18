<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The public "Live Feed" stream. Each row is a denormalised snapshot of a
     * platform event (achievement / level-up / rank-up / order) so the feed can
     * be rendered without joins and reflects how the actor looked at the moment.
     */
    public function up(): void
    {
        Schema::create('feed_events', function (Blueprint $table) {
            $table->id();
            $table->string('type')->index();              // achievement|level_up|rank_up|order
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');                        // display-name snapshot
            $table->string('avatar')->nullable();          // avatar path snapshot
            $table->string('title')->nullable();           // achievement title / rank name / "Уровень 12"
            $table->string('color')->nullable();           // hex accent (rarity / rank tier)
            $table->timestamps();

            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feed_events');
    }
};
