<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rh_candidato_testes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('curriculo_id')->constrained('rh_curriculos')->cascadeOnDelete();
            $table->foreignId('teste_id')->constrained('rh_testes')->cascadeOnDelete();
            $table->foreignId('vaga_id')->nullable()->constrained('rh_vagas')->nullOnDelete();
            $table->string('token', 64)->unique()->index();
            $table->enum('status', ['pendente','em_andamento','concluido','expirado'])->default('pendente');
            $table->timestamp('iniciado_at')->nullable();
            $table->timestamp('concluido_at')->nullable();
            $table->timestamp('expira_at')->nullable();
            $table->decimal('nota', 5, 2)->nullable();
            $table->boolean('aprovado')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rh_candidato_testes');
    }
};
