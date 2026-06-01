<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── 1. Prazo de conclusão na inscrição ─────────────────────────────
        Schema::table('treinamento_inscricoes', function (Blueprint $table) {
            $table->date('prazo_conclusao')->nullable()->after('concluido_em');
        });

        // ── 2. Inscrições obrigatórias ─────────────────────────────────────
        Schema::create('treinamento_obrigatorios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('treinamento_id')->constrained('treinamentos')->cascadeOnDelete();
            $table->enum('tipo_alvo', ['usuario', 'departamento']);
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->date('prazo')->nullable();
            $table->foreignId('criado_por')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        // ── 3. Avaliações de reação ────────────────────────────────────────
        Schema::create('treinamento_avaliacoes_reacao', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inscricao_id')->constrained('treinamento_inscricoes')->cascadeOnDelete();
            $table->unsignedTinyInteger('nota');  // 1–5 estrelas
            $table->text('comentario')->nullable();
            $table->timestamps();
            $table->unique('inscricao_id'); // 1 avaliação por inscrição
        });

        // ── 4. Trilhas de aprendizado ──────────────────────────────────────
        Schema::create('trilhas', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('descricao')->nullable();
            $table->string('capa')->nullable();
            $table->enum('status', ['rascunho', 'ativa', 'arquivada'])->default('rascunho');
            $table->string('categoria')->nullable();
            $table->foreignId('criado_por')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        // ── 5. Cursos de uma trilha ────────────────────────────────────────
        Schema::create('trilha_cursos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trilha_id')->constrained('trilhas')->cascadeOnDelete();
            $table->foreignId('treinamento_id')->constrained('treinamentos')->cascadeOnDelete();
            $table->unsignedSmallInteger('ordem')->default(0);
            $table->boolean('obrigatorio')->default(true);
            $table->timestamps();
            $table->unique(['trilha_id', 'treinamento_id']);
        });

        // ── 6. Inscrições em trilhas ───────────────────────────────────────
        Schema::create('trilha_inscricoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trilha_id')->constrained('trilhas')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['em_andamento', 'concluida'])->default('em_andamento');
            $table->unsignedTinyInteger('progresso')->default(0); // %
            $table->timestamp('concluida_em')->nullable();
            $table->timestamps();
            $table->unique(['trilha_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trilha_inscricoes');
        Schema::dropIfExists('trilha_cursos');
        Schema::dropIfExists('trilhas');
        Schema::dropIfExists('treinamento_avaliacoes_reacao');
        Schema::dropIfExists('treinamento_obrigatorios');
        Schema::table('treinamento_inscricoes', function (Blueprint $table) {
            $table->dropColumn('prazo_conclusao');
        });
    }
};
