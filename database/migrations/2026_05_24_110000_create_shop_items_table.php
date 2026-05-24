<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shop_items', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('category'); // font | border | nickname_color
            $table->string('name');
            $table->string('description');
            $table->unsignedInteger('price');
            $table->string('css_value'); // CSS value to apply, or 'rainbow' for special
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_items');
    }
};
