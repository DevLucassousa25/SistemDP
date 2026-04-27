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
        Schema::create('anexos_manifestacaos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('manifestacao_id')->constrained('manifestacoes')->cascadeOnDelete();
            $table->string('nome_arquivo');
            $table->string('caminho');
            $table->string('mime_type', 100);
            $table->unsignedInteger('tamanho_bytes');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anexos_manifestacaos');
    }
};
