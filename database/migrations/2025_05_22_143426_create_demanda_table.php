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
        Schema::create('demanda', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tipo_sanguineo_id')->constrained('tipo_sanguineo')->OnDelete('cascade');;
            $table->foreignId('hemocentro_id')->constrained('hemocentro')->OnDelete('cascade');;
            $table->enum('status',['aberta','finalizada'])->default('aberta');
            $table->date('data_inicial');
            $table->date('data_final')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demanda');
    }
};
