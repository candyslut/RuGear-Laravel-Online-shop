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
        Schema::create('mouse_specifications', function (Blueprint $table) {
            $table->id();
            $table->string('sensor');          
            $table->integer('max_dpi');        
            $table->integer('polling_rate');  
            $table->string('switches');        
            $table->string('connection');     
            $table->integer('battery_life')->nullable(); 
            $table->integer('weight');         
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mouse_specifications');
    }
};
