<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedbacks', function (Blueprint $table) {
            $table->id();

            // Quem está sendo avaliado
            $table->foreignId('employee_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Quem avalia (null quando anônimo)
            $table->foreignId('evaluator_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->enum('type', ['reconhecimento', 'sugestao', 'alerta']);

            $table->enum('category', [
                'comportamento',
                'desempenho',
                'pontualidade',
                'trabalho_em_equipe',
                'comunicacao',
                'lideranca',
                'outros',
            ]);

            // Apenas para type = 'alerta'
            $table->enum('severity', ['baixo', 'medio', 'alto', 'critico'])->nullable();

            // Nota 1–5 (opcional)
            $table->tinyInteger('rating')->unsigned()->nullable();

            $table->date('occurred_at');
            $table->text('message');
            $table->text('action_plan')->nullable();

            $table->enum('status', [
                'aberto',
                'em_analise',
                'aguardando_plano',
                'plano_em_andamento',
                'resolvido',
                'arquivado',
            ])->default('aberto');

            $table->boolean('is_anonymous')->default(false);

            // Array de {name, path, size} para arquivos anexados
            $table->json('attachments')->nullable();

            $table->timestamps();

            $table->index(['employee_id', 'status']);
            $table->index(['type', 'severity']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedbacks');
    }
};
