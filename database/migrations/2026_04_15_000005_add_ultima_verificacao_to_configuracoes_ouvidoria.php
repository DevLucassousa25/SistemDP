<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Registra quando foi a última vez que o auto-encerramento foi verificado.
     * Usado para throttle da verificação lazy (sem necessidade de cron).
     */
    public function up(): void
    {
        Schema::table('configuracoes_ouvidoria', function (Blueprint $table) {
            $table->timestamp('ultima_verificacao_em')->nullable()->after('mensagem_auto_encerramento');
        });
    }

    public function down(): void
    {
        Schema::table('configuracoes_ouvidoria', function (Blueprint $table) {
            $table->dropColumn('ultima_verificacao_em');
        });
    }
};
