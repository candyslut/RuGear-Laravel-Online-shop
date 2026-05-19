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
        Schema::create('headphone_specifications', function (Blueprint $table) {
            $table->id();
            $table->string('sound_type');      
            $table->string('drivers');         
            $table->string('frequency');       
            $table->string('impedance');       
            $table->string('connection');     
            $table->string('microphone');      
            $table->integer('battery_life')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('headphones_specifications');
    }
};
