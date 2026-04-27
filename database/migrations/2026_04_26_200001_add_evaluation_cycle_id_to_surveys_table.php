<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Vincula pesquisas a ciclos de avaliação de desempenho.
 *
 * Quando uma Survey possui evaluation_cycle_id, ela é considerada uma
 * "pesquisa de avaliação de gestor" para aquele ciclo, e seus resultados
 * aparecem no módulo de Avaliação de Desempenho.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surveys', function (Blueprint $table) {
            $table->foreignId('evaluation_cycle_id')
                  ->nullable()
                  ->after('created_by')
                  ->constrained('evaluation_cycles')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('surveys', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\EvaluationCycle::class, 'evaluation_cycle_id');
            $table->dropColumn('evaluation_cycle_id');
        });
    }
};
