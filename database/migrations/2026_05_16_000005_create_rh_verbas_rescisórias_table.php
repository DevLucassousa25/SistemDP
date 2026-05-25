<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rh_verbas_rescisórias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('desligamento_id')->constrained('rh_desligamentos')->cascadeOnDelete();
            // Dados base
            $table->decimal('salario_base', 10, 2);
            $table->date('data_admissao');
            $table->date('data_demissao');
            $table->unsignedInteger('meses_trabalhados')->default(0);
            // Verbas calculadas
            $table->decimal('saldo_salario', 10, 2)->default(0);
            $table->decimal('ferias_proporcionais', 10, 2)->default(0);
            $table->decimal('ferias_vencidas', 10, 2)->default(0);
            $table->decimal('um_terco_ferias', 10, 2)->default(0);
            $table->decimal('decimo_terceiro', 10, 2)->default(0);
            $table->decimal('aviso_previo_valor', 10, 2)->default(0);
            $table->decimal('multa_fgts', 10, 2)->default(0);
            $table->decimal('outros_creditos', 10, 2)->default(0);
            $table->decimal('descontos', 10, 2)->default(0);
            $table->decimal('total_bruto', 10, 2)->default(0);
            $table->decimal('total_liquido', 10, 2)->default(0);
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rh_verbas_rescisórias');
    }
};
