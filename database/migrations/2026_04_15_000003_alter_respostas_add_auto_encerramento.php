<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * - Torna respondente_id nullable para suportar respostas automáticas do sistema.
     * - Adiciona is_automatica para identificar respostas geradas pelo auto-encerramento.
     */
    public function up(): void
    {
        Schema::table('respostas_manifestacaos', function (Blueprint $table) {
            // Torna nullable para permitir respostas do sistema (sem respondente humano)
            $table->foreignId('respondente_id')
                  ->nullable()
                  ->change();

            // Marca respostas geradas automaticamente pelo sistema
            $table->boolean('is_automatica')->default(false)->after('is_interno');
        });
    }

    public function down(): void
    {
        Schema::table('respostas_manifestacaos', function (Blueprint $table) {
            $table->dropColumn('is_automatica');
            $table->foreignId('respondente_id')->nullable(false)->change();
        });
    }
};
