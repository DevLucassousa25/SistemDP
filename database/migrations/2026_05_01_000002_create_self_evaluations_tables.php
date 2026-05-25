<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Cabeçalho da autoavaliação (1 por colaborador por ciclo)
        Schema::create('self_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_cycle_id')->constrained('evaluation_cycles')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['evaluation_cycle_id', 'employee_id']);
        });

        // Respostas da autoavaliação (1 por critério)
        Schema::create('self_evaluation_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('self_evaluation_id')->constrained('self_evaluations')->cascadeOnDelete();
            $table->foreignId('criterion_id')->constrained('evaluation_criteria')->cascadeOnDelete();
            // Score genérico: para scale 1-5, boolean 0/1 (0=Não, 1=Sim), multiple_choice = índice da opção (0-based)
            $table->unsignedSmallInteger('score');
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->unique(['self_evaluation_id', 'criterion_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('self_evaluation_entries');
        Schema::dropIfExists('self_evaluations');
    }
};
