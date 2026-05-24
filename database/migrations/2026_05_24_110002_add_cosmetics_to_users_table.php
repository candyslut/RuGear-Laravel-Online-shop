<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('cosmetic_border')->nullable()->after('avatar');
            $table->string('cosmetic_font')->nullable()->after('cosmetic_border');
            $table->string('cosmetic_nickname_color')->nullable()->after('cosmetic_font');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['cosmetic_border', 'cosmetic_font', 'cosmetic_nickname_color']);
        });
    }
};
