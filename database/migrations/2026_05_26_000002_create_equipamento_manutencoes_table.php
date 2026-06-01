<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipamento_manutencoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipamento_id')->constrained('equipamentos')->cascadeOnDelete();
            $table->foreignId('registrado_por')->constrained('users');

            $table->string('titulo', 200);
            $table->text('descricao')->nullable();          // problema relatado
            $table->string('tipo', 50)->default('corretiva'); // corretiva | preventiva | calibracao
            $table->string('status', 50)->default('aberta'); // aberta | em_andamento | concluida | cancelada

            $table->date('data_entrada');
            $table->date('data_previsao')->nullable();
            $table->date('data_conclusao')->nullable();

            $table->string('fornecedor', 200)->nullable();
            $table->decimal('custo_estimado', 10, 2)->nullable();
            $table->decimal('custo_real', 10, 2)->nullable();

            $table->text('resolucao')->nullable();          // o que foi feito
            $table->text('observacoes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipamento_manutencoes');
    }
};
