<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rh_entrevista_desligamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('desligamento_id')->constrained('rh_desligamentos')->cascadeOnDelete();
            $table->string('token', 64)->unique();
            $table->timestamp('enviado_at')->nullable();
            $table->timestamp('respondido_at')->nullable();
            // Perguntas quantitativas (1-5)
            $table->unsignedTinyInteger('satisfacao_gestao')->nullable();
            $table->unsignedTinyInteger('satisfacao_cultura')->nullable();
            $table->unsignedTinyInteger('satisfacao_remuneracao')->nullable();
            $table->unsignedTinyInteger('satisfacao_crescimento')->nullable();
            $table->unsignedTinyInteger('satisfacao_equilibrio')->nullable(); // work-life balance
            $table->boolean('recomendaria_empresa')->nullable();
            // Perguntas qualitativas
            $table->string('motivo_principal')->nullable();   // select
            $table->text('pontos_positivos')->nullable();
            $table->text('pontos_melhoria')->nullable();
            $table->text('outros_comentarios')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rh_entrevista_desligamentos');
    }
};
