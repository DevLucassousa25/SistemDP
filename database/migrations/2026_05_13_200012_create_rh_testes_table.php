<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rh_testes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('users');
            $table->string('titulo');
            $table->text('descricao')->nullable();
            $table->text('instrucoes')->nullable();
            $table->unsignedSmallInteger('tempo_limite_minutos')->default(60);
            $table->boolean('randomizar_questoes')->default(false);
            $table->boolean('randomizar_opcoes')->default(false);
            $table->unsignedTinyInteger('nota_aprovacao')->default(60);
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rh_testes');
    }
};
