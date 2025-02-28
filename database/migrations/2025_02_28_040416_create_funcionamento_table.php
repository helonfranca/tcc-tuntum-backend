<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up():void
    {
        Schema::create('funcionamento', function (Blueprint $table) {
            $table->id();
            $table->time('hora_abertura');
            $table->time('hora_fechamento');
            $table->foreignId('hemocentro_id')->constrained('hemocentro')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('funcionamento');
    }
};
