<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rh_solicitacoes', function (Blueprint $table) {
            $table->id();

            // Quem solicitou
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // Tipo do documento/serviço solicitado
            $table->enum('tipo', [
                'declaracao_emprego',
                'declaracao_salario',
                'declaracao_ferias',
                'declaracao_quitacao',
                'comprovante_pagamento',
                'comprovante_fgts',
                'informe_rendimentos',
                'segunda_via_cracha',
                'outros',
            ])->default('outros');

            // Detalhes adicionais do funcionário
            $table->text('descricao')->nullable();

            // Gerenciamento pelo RH
            $table->enum('status', ['pendente', 'em_andamento', 'concluida', 'cancelada'])->default('pendente');
            $table->date('prazo')->nullable();
            $table->foreignId('rh_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('observacao_rh')->nullable();
            $table->string('arquivo_url')->nullable();
            $table->timestamp('concluida_em')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rh_solicitacoes');
    }
};
