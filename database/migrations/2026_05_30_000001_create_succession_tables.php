<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Posições Críticas ──────────────────────────────────────────
        Schema::create('succession_positions', function (Blueprint $table) {
            $table->id();
            $table->string('title');                               // Ex: "Gerente de TI"
            $table->text('description')->nullable();
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('current_holder_id')->nullable()    // Titular atual
                  ->constrained('users')->nullOnDelete();
            $table->enum('risk_level', ['critico', 'alto', 'medio'])->default('alto');
            $table->unsignedSmallInteger('time_to_fill_months')->default(6); // Prazo estimado de reposição
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });

        // ── Candidatos por Posição ─────────────────────────────────────
        Schema::create('succession_candidates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('position_id')->constrained('succession_positions')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('readiness', ['pronto_agora', '6_a_12_meses', '1_a_3_anos'])->default('1_a_3_anos');
            $table->unsignedTinyInteger('priority')->default(1); // 1=principal, 2=secundário, 3=reserva
            $table->foreignId('dpi_plan_id')->nullable()         // Vínculo ao PDI
                  ->constrained('dpi_plans')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            $table->unique(['position_id', 'user_id']); // Um candidato por posição
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('succession_candidates');
        Schema::dropIfExists('succession_positions');
    }
};
