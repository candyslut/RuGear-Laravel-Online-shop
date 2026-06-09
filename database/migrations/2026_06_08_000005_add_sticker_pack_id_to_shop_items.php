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
        Schema::table('shop_items', function (Blueprint $table) {
            // Links a shop item (category 'sticker_pack') to the pack it unlocks.
            $table->foreignId('sticker_pack_id')->nullable()->after('category')
                ->constrained()->nullOnDelete();

            // Sticker packs carry no CSS cosmetic value.
            $table->string('css_value')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shop_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('sticker_pack_id');
            $table->string('css_value')->nullable(false)->change();
        });
    }
};
