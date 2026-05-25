<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rh_vagas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('users');
            $table->string('titulo');
            $table->string('cargo');
            $table->text('descricao')->nullable();
            $table->text('requisitos')->nullable();
            $table->text('competencias')->nullable();
            $table->text('beneficios')->nullable();
            $table->decimal('salario_min', 10, 2)->nullable();
            $table->decimal('salario_max', 10, 2)->nullable();
            $table->enum('modalidade', ['presencial','remoto','hibrido'])->default('presencial');
            $table->string('cidade')->nullable();
            $table->string('estado', 2)->nullable();
            $table->enum('status', ['rascunho','publicada','pausada','encerrada'])->default('rascunho');
            $table->unsignedTinyInteger('nota_minima_aprovacao')->default(60);
            $table->unsignedSmallInteger('sla_dias')->nullable();
            $table->unsignedSmallInteger('vagas_disponiveis')->default(1);
            $table->date('data_encerramento')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rh_vagas');
    }
};
