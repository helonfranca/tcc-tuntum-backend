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
        Schema::create('doador', function (Blueprint $table) {
            $table->id();
            $table->boolean('apto');
            $table->boolean('malaria');
            $table->boolean('hiv');
            $table->boolean('droga_ilicita');
            $table->boolean('hepatiteb');
            $table->boolean('hepatitec');
            $table->unsignedBigInteger('usuario_id');
            $table->unsignedBigInteger('tipo_sanguineo_id');
            $table->timestamps();

            $table->foreign('usuario_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('tipo_sanguineo_id')->references('id')->on('tipo_sanguineo')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doador');
    }
};
