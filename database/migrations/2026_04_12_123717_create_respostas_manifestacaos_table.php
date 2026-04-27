<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('respostas_manifestacaos', function (Blueprint $table) {
            $table->uuid('id')->primary(); // substitui o $table->id() padrão
            $table->foreignUuid('manifestacao_id')->constrained('manifestacoes')->cascadeOnDelete();
            $table->foreignId('respondente_id')->constrained('users');
            $table->text('conteudo');
            $table->boolean('is_interno')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('respostas_manifestacaos');
    }
};
