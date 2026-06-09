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
        Schema::table('commentaries', function (Blueprint $table) {
            // A comment may now be a sticker instead of (or in addition to) text.
            $table->foreignId('sticker_id')->nullable()->after('product_id')
                ->constrained()->nullOnDelete();

            // Sticker-only comments carry no text.
            $table->text('content')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('commentaries', function (Blueprint $table) {
            $table->dropConstrainedForeignId('sticker_id');
            $table->text('content')->nullable(false)->change();
        });
    }
};
