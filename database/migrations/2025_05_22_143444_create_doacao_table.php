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
        Schema::create('doacao', function (Blueprint $table) {
            $table->id();
            $table->date('data_doacao');
            $table->float('quantidade');
            $table->foreignId('doador_id')->constrained('doador');
            $table->enum('status', ['pendente', 'confirmada'])->default('pendente');
            $table->foreignId('demanda_id')->constrained('demanda');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doacao');
    }
};
