<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── 1. Cursos ───────────────────────────────────────────────────────
        Schema::create('treinamentos', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('descricao')->nullable();
            $table->string('capa')->nullable();              // path da imagem de capa
            $table->enum('tipo', ['interno', 'externo'])->default('interno');
            $table->string('categoria')->nullable();         // ex: Liderança, Técnico, Compliance…
            $table->string('instrutor')->nullable();
            $table->unsignedInteger('carga_horaria')->default(0); // em minutos
            $table->enum('nivel', ['basico', 'intermediario', 'avancado'])->default('basico');
            $table->enum('status', ['rascunho', 'ativo', 'arquivado'])->default('rascunho');
            $table->boolean('certificado_habilitado')->default(true);
            $table->unsignedTinyInteger('nota_minima_aprovacao')->default(70); // %
            $table->boolean('inscricao_aberta')->default(true);
            $table->date('data_inicio')->nullable();
            $table->date('data_fim')->nullable();
            $table->foreignId('criado_por')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        // ── 2. Videoaulas ───────────────────────────────────────────────────
        Schema::create('treinamento_aulas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('treinamento_id')->constrained('treinamentos')->cascadeOnDelete();
            $table->string('titulo');
            $table->text('descricao')->nullable();
            $table->string('video_url')->nullable();         // YouTube, Vimeo ou upload
            $table->string('video_arquivo')->nullable();     // arquivo local
            $table->string('material_pdf')->nullable();      // material complementar
            $table->unsignedInteger('duracao')->default(0);  // em minutos
            $table->unsignedSmallInteger('ordem')->default(0);
            $table->boolean('obrigatoria')->default(true);
            $table->timestamps();
        });

        // ── 3. Testes ──────────────────────────────────────────────────────
        Schema::create('treinamento_testes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('treinamento_id')->constrained('treinamentos')->cascadeOnDelete();
            $table->string('titulo');
            $table->text('descricao')->nullable();
            $table->unsignedTinyInteger('nota_minima')->default(70);   // %
            $table->unsignedTinyInteger('tentativas_maximas')->default(3);
            $table->unsignedSmallInteger('tempo_limite')->nullable();  // minutos, null = sem limite
            $table->boolean('embaralhar_questoes')->default(true);
            $table->timestamps();
        });

        // ── 4. Questões ────────────────────────────────────────────────────
        Schema::create('treinamento_questoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teste_id')->constrained('treinamento_testes')->cascadeOnDelete();
            $table->text('enunciado');
            $table->enum('tipo', ['multipla_escolha', 'verdadeiro_falso'])->default('multipla_escolha');
            $table->unsignedSmallInteger('ordem')->default(0);
            $table->unsignedTinyInteger('pontos')->default(1);
            $table->timestamps();
        });

        // ── 5. Opções das questões ─────────────────────────────────────────
        Schema::create('treinamento_questao_opcoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('questao_id')->constrained('treinamento_questoes')->cascadeOnDelete();
            $table->string('texto');
            $table->boolean('correta')->default(false);
            $table->unsignedSmallInteger('ordem')->default(0);
            $table->timestamps();
        });

        // ── 6. Inscrições ──────────────────────────────────────────────────
        Schema::create('treinamento_inscricoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('treinamento_id')->constrained('treinamentos')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['inscrito', 'em_andamento', 'concluido', 'reprovado'])->default('inscrito');
            $table->unsignedTinyInteger('progresso')->default(0);   // %
            $table->timestamp('concluido_em')->nullable();
            $table->timestamps();
            $table->unique(['treinamento_id', 'user_id']);
        });

        // ── 7. Progresso por aula ──────────────────────────────────────────
        Schema::create('treinamento_aula_progresso', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inscricao_id')->constrained('treinamento_inscricoes')->cascadeOnDelete();
            $table->foreignId('aula_id')->constrained('treinamento_aulas')->cascadeOnDelete();
            $table->boolean('concluida')->default(false);
            $table->unsignedSmallInteger('segundos_assistidos')->default(0);
            $table->timestamp('concluida_em')->nullable();
            $table->timestamps();
            $table->unique(['inscricao_id', 'aula_id']);
        });

        // ── 8. Tentativas de teste ─────────────────────────────────────────
        Schema::create('treinamento_tentativas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inscricao_id')->constrained('treinamento_inscricoes')->cascadeOnDelete();
            $table->foreignId('teste_id')->constrained('treinamento_testes')->cascadeOnDelete();
            $table->unsignedTinyInteger('numero');           // 1, 2, 3…
            $table->decimal('nota', 5, 2)->nullable();       // ex: 85.50
            $table->boolean('aprovado')->default(false);
            $table->timestamp('iniciada_em')->nullable();
            $table->timestamp('finalizada_em')->nullable();
            $table->timestamps();
        });

        // ── 9. Respostas das tentativas ────────────────────────────────────
        Schema::create('treinamento_respostas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tentativa_id')->constrained('treinamento_tentativas')->cascadeOnDelete();
            $table->foreignId('questao_id')->constrained('treinamento_questoes')->cascadeOnDelete();
            $table->foreignId('opcao_id')->nullable()->constrained('treinamento_questao_opcoes')->nullOnDelete();
            $table->boolean('correta')->default(false);
            $table->timestamps();
        });

        // ── 10. Certificados ───────────────────────────────────────────────
        Schema::create('treinamento_certificados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inscricao_id')->constrained('treinamento_inscricoes')->cascadeOnDelete();
            $table->string('codigo')->unique();              // código de validação
            $table->string('arquivo')->nullable();           // PDF gerado
            $table->timestamp('emitido_em');
            $table->timestamps();
        });

        // ── 11. Dúvidas ────────────────────────────────────────────────────
        Schema::create('treinamento_duvidas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('treinamento_id')->constrained('treinamentos')->cascadeOnDelete();
            $table->foreignId('aula_id')->nullable()->constrained('treinamento_aulas')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('pergunta');
            $table->enum('status', ['aberta', 'respondida'])->default('aberta');
            $table->timestamps();
        });

        // ── 12. Respostas às dúvidas ───────────────────────────────────────
        Schema::create('treinamento_duvida_respostas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('duvida_id')->constrained('treinamento_duvidas')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('resposta');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('treinamento_duvida_respostas');
        Schema::dropIfExists('treinamento_duvidas');
        Schema::dropIfExists('treinamento_certificados');
        Schema::dropIfExists('treinamento_respostas');
        Schema::dropIfExists('treinamento_tentativas');
        Schema::dropIfExists('treinamento_aula_progresso');
        Schema::dropIfExists('treinamento_inscricoes');
        Schema::dropIfExists('treinamento_questao_opcoes');
        Schema::dropIfExists('treinamento_questoes');
        Schema::dropIfExists('treinamento_testes');
        Schema::dropIfExists('treinamento_aulas');
        Schema::dropIfExists('treinamentos');
    }
};
