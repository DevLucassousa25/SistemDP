<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adiciona campos de controle de auto-encerramento por manifestação.
     * Permitem sobrescrever a configuração global individualmente.
     */
    public function up(): void
    {
        Schema::table('manifestacoes', function (Blueprint $table) {
            // Prazo personalizado em horas (null = usa o da config global)
            $table->unsignedSmallInteger('prazo_personalizado_horas')
                  ->nullable()
                  ->after('concluido_em');

            // Desativa o auto-encerramento para esta manifestação específica
            $table->boolean('auto_encerramento_desativado')
                  ->default(false)
                  ->after('prazo_personalizado_horas');
        });
    }

    public function down(): void
    {
        Schema::table('manifestacoes', function (Blueprint $table) {
            $table->dropColumn(['prazo_personalizado_horas', 'auto_encerramento_desativado']);
        });
    }
};
