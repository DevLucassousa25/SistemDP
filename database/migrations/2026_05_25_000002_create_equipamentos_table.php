<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipamentos', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('categoria'); // notebook, cracha, epi, monitor, teclado, mouse, headset, cadeira, outros
            $table->text('descricao')->nullable();
            $table->string('numero_serie')->nullable()->unique();
            $table->string('codigo_patrimonio')->nullable()->unique();
            $table->string('marca')->nullable();
            $table->string('modelo')->nullable();
            $table->string('status')->default('disponivel'); // disponivel, em_uso, manutencao, descartado
            $table->date('data_aquisicao')->nullable();
            $table->decimal('valor', 10, 2)->nullable();
            $table->text('observacoes')->nullable();
            $table->foreignId('cadastrado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipamentos');
    }
};
