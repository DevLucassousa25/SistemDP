<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('manifestacoes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('protocolo')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_anonimo')->default(false);
            $table->enum('categoria', ['sugestao', 'reclamacao', 'denuncia', 'elogio']);
            $table->string('assunto');
            $table->text('descricao');
            $table->enum('status', ['em_analise', 'em_andamento', 'respondido', 'concluido'])
                ->default('em_analise');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'created_at']);
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manifestacoes');
    }
};
