<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Armazena o score consolidado de cada gerente por pesquisa.
 *
 * Recalculado a cada nova submissão de resposta de clima.
 * Usado para o ranking de gerentes exibido ao DP/RH.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('survey_manager_scores', function (Blueprint $table) {
            $table->id();

            $table->foreignId('survey_id')
                  ->constrained('surveys')
                  ->cascadeOnDelete();

            $table->foreignId('manager_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            // Média das notas recebidas nas perguntas com is_manager_evaluation = true
            $table->decimal('average_score', 5, 2)->nullable();

            // Quantidade de respostas consideradas no cálculo
            $table->unsignedInteger('total_responses')->default(0);

            // Momento do último recálculo
            $table->timestamp('calculated_at')->nullable();

            $table->timestamps();

            // Um único registro por (pesquisa × gerente)
            $table->unique(['survey_id', 'manager_id']);

            $table->index('manager_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_manager_scores');
    }
};
