<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calendario_eventos', function (Blueprint $table) {
            $table->id();
            $table->date('data');
            $table->string('titulo');
            $table->enum('tipo', [
                'feriado_nacional',
                'data_comemorativa',
                'evento_empresa',
            ])->default('evento_empresa');
            $table->text('descricao')->nullable();
            $table->string('cor', 7)->default('#3B82F6')
                  ->comment('Hex color, ex: #3B82F6');
            $table->boolean('recorrente_anual')->default(false)
                  ->comment('Se true, repete todo ano na mesma data M-D');
            $table->foreignId('created_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->timestamps();

            $table->index('data');
            $table->index('tipo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calendario_eventos');
    }
};
