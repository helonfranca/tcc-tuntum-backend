<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('endereco', function (Blueprint $table) {
            $table->id();
            $table->string('cep', 45)->nullable();
            $table->string('rua', 255)->nullable();
            $table->string('bairro', 45)->nullable();
            $table->string('estado', 45)->nullable();
            $table->string('municipio', 45)->nullable();
            $table->integer('numero')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('endereco');
    }
};
