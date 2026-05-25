<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rh_curriculo_formacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('curriculo_id')->constrained('rh_curriculos')->cascadeOnDelete();
            $table->string('instituicao');
            $table->string('curso');
            $table->enum('nivel', ['ensino_medio','tecnico','graduacao','pos_graduacao','mestrado','doutorado','curso_livre']);
            $table->year('ano_inicio')->nullable();
            $table->year('ano_conclusao')->nullable();
            $table->boolean('em_andamento')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rh_curriculo_formacoes');
    }
};
