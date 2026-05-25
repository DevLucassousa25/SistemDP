<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adiciona manager_id em survey_answers.
 *
 * Quando uma pergunta tem is_manager_evaluation = true, armazenamos aqui o ID
 * do gerente do departamento do respondente — necessário para calcular o score
 * do gestor no ranking de pesquisa de clima.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('survey_answers', function (Blueprint $table) {
            $table->foreignId('manager_id')
                  ->nullable()
                  ->after('value_text')
                  ->constrained('users')
                  ->nullOnDelete()
                  ->comment('Gerente avaliado (preenchido quando a pergunta tem is_manager_evaluation = true)');
        });
    }

    public function down(): void
    {
        Schema::table('survey_answers', function (Blueprint $table) {
            $table->dropForeignIfExists(['manager_id']);
            $table->dropColumn('manager_id');
        });
    }
};
