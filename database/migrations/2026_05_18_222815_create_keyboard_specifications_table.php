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
        Schema::create('keyboard_specifications', function (Blueprint $table) {
            $table->id();
            $table->string('switch_type');     
            $table->string('form_factor');     
            $table->string('keycap_material'); 
            $table->string('hotswap');         
            $table->string('connection');      
            $table->string('illumination');    
            $table->string('construction');    
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keyboard_specifications');
    }
};
