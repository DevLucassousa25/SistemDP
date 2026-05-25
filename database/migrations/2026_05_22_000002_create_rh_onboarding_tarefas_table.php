<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rh_onboarding_tarefas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('onboarding_id')->constrained('rh_onboardings')->cascadeOnDelete();
            $table->string('titulo', 255);
            $table->text('descricao')->nullable();
            $table->enum('responsavel', ['rh', 'ti', 'gestao', 'financeiro'])->default('rh');
            $table->enum('status', ['pendente', 'concluido'])->default('pendente');
            $table->foreignId('concluido_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('concluido_at')->nullable();
            $table->unsignedSmallInteger('ordem')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rh_onboarding_tarefas');
    }
};
