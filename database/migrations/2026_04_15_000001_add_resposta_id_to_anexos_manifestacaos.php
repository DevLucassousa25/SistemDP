<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adiciona vínculo opcional com uma resposta específica ao anexo.
     * Quando preenchido, indica que o arquivo foi enviado junto a uma resposta,
     * e não na criação da manifestação original.
     */
    public function up(): void
    {
        Schema::table('anexos_manifestacaos', function (Blueprint $table) {
            $table->foreignUuid('resposta_manifestacao_id')
                  ->nullable()
                  ->after('manifestacao_id')
                  ->constrained('respostas_manifestacaos')
                  ->nullOnDelete();

            // Usuário que fez o upload (rastreabilidade)
            $table->foreignId('uploaded_by')
                  ->nullable()
                  ->after('resposta_manifestacao_id')
                  ->constrained('users')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('anexos_manifestacaos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('uploaded_by');
            $table->dropForeign(['resposta_manifestacao_id']);
            $table->dropColumn('resposta_manifestacao_id');
        });
    }
};
