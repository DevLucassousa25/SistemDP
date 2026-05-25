<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rh_verbas_rescisórias', function (Blueprint $table) {
            // Avos (regra dos 15 dias — CLT)
            $table->unsignedTinyInteger('avos_ferias')->default(0)->after('meses_trabalhados');
            $table->unsignedTinyInteger('avos_decimo')->default(0)->after('avos_ferias');
            $table->unsignedSmallInteger('dias_aviso')->default(0)->after('avos_decimo');

            // Férias vencidas: flag para saber se havia período não gozado
            $table->boolean('tem_ferias_vencidas')->default(false)->after('ferias_vencidas');

            // Descontos legais
            $table->decimal('inss', 10, 2)->default(0)->after('aviso_previo_valor');
            $table->decimal('irrf', 10, 2)->default(0)->after('inss');
            $table->decimal('total_descontos', 10, 2)->default(0)->after('descontos');

            // FGTS
            $table->decimal('saldo_fgts', 10, 2)->default(0)->after('multa_fgts');
            $table->decimal('fgts_disponivel', 10, 2)->default(0)->after('saldo_fgts');

            // Dados usados no cálculo
            $table->unsignedTinyInteger('num_dependentes')->default(0)->after('observacoes');
        });
    }

    public function down(): void
    {
        Schema::table('rh_verbas_rescisórias', function (Blueprint $table) {
            $table->dropColumn([
                'avos_ferias', 'avos_decimo', 'dias_aviso',
                'tem_ferias_vencidas',
                'inss', 'irrf', 'total_descontos',
                'saldo_fgts', 'fgts_disponivel',
                'num_dependentes',
            ]);
        });
    }
};
