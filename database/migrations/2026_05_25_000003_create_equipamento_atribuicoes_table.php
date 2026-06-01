<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipamento_atribuicoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipamento_id')->constrained('equipamentos')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // funcionário
            $table->foreignId('responsavel_id')->nullable()->constrained('users')->nullOnDelete(); // quem entregou/recolheu
            $table->string('tipo')->default('entrega'); // entrega, devolucao
            $table->date('data_entrega');
            $table->date('data_devolucao')->nullable();
            $table->string('condicao_entrega')->nullable(); // novo, bom, regular, danificado
            $table->string('condicao_devolucao')->nullable();
            $table->text('observacoes')->nullable();
            $table->boolean('assinado_funcionario')->default(false);
            $table->timestamp('assinado_em')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipamento_atribuicoes');
    }
};
