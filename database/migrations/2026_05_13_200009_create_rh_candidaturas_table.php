<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rh_candidaturas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('curriculo_id')->constrained('rh_curriculos')->cascadeOnDelete();
            $table->foreignId('vaga_id')->constrained('rh_vagas')->cascadeOnDelete();
            $table->foreignId('etapa_id')->nullable()->constrained('rh_vaga_etapas')->nullOnDelete();
            $table->enum('status', ['ativo','aprovado','reprovado','banco_talentos','desistiu'])->default('ativo');
            $table->decimal('nota_final', 5, 2)->nullable();
            $table->unsignedSmallInteger('ranking_posicao')->nullable();
            $table->timestamp('aprovado_at')->nullable();
            $table->timestamps();
            $table->unique(['curriculo_id', 'vaga_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rh_candidaturas');
    }
};
