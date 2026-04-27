<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabela de configuração global do auto-encerramento de ouvidorias.
     * Mantém sempre um único registro (singleton).
     */
    public function up(): void
    {
        Schema::create('configuracoes_ouvidoria', function (Blueprint $table) {
            $table->id();

            // Liga/desliga o recurso de auto-encerramento
            $table->boolean('auto_encerramento_ativo')->default(false);

            // Prazo máximo de inatividade antes do auto-encerramento (em horas)
            $table->unsignedSmallInteger('prazo_horas')->default(72); // 3 dias

            // Mensagem enviada automaticamente ao encerrar
            $table->text('mensagem_auto_encerramento')->default(
                'Esta manifestação foi encerrada automaticamente por inatividade. ' .
                'Caso necessite de atendimento adicional, abra uma nova manifestação. ' .
                'Agradecemos o contato.'
            );

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('configuracoes_ouvidoria');
    }
};
