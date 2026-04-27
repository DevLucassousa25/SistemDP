<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Marca perguntas da pesquisa de clima como sendo referentes ao gestor do setor.
 *
 * Quando is_manager_evaluation = true, as respostas a essa pergunta são usadas
 * pelo módulo de Avaliação de Desempenho para calcular a nota do gestor.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('survey_questions', function (Blueprint $table) {
            $table->boolean('is_manager_evaluation')
                  ->default(false)
                  ->after('order')
                  ->comment('Indica se esta pergunta avalia o gestor do departamento');
        });
    }

    public function down(): void
    {
        Schema::table('survey_questions', function (Blueprint $table) {
            $table->dropColumn('is_manager_evaluation');
        });
    }
};
