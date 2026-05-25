<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rh_candidato_respostas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidato_teste_id')->constrained('rh_candidato_testes')->cascadeOnDelete();
            $table->foreignId('questao_id')->constrained('rh_questoes')->cascadeOnDelete();
            $table->foreignId('opcao_id')->nullable()->constrained('rh_opcoes')->nullOnDelete();
            $table->text('resposta_discursiva')->nullable();
            $table->boolean('correta')->nullable();
            $table->decimal('nota_obtida', 5, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rh_candidato_respostas');
    }
};
