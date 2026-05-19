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
        Schema::create('pad_specifications', function (Blueprint $table) {
            $table->id();
            $table->string('surface');       
            $table->string('material');       
            $table->string('base_material');   
            $table->string('dimensions');      
            $table->string('thickness');     
            $table->string('edges');           
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pad_specifications');
    }
};
