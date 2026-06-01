<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipamento_inventarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('criado_por')->constrained('users');
            $table->string('titulo', 200);
            $table->string('status', 50)->default('em_andamento'); // em_andamento | concluido | cancelado
            $table->date('data_inicio');
            $table->date('data_conclusao')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });

        Schema::create('equipamento_inventario_itens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventario_id')->constrained('equipamento_inventarios')->cascadeOnDelete();
            $table->foreignId('equipamento_id')->constrained('equipamentos')->cascadeOnDelete();
            $table->foreignId('conferido_por')->nullable()->constrained('users');

            // Status: pendente | encontrado | nao_encontrado | divergencia
            $table->string('status', 50)->default('pendente');
            $table->datetime('conferido_at')->nullable();
            $table->text('observacao')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipamento_inventario_itens');
        Schema::dropIfExists('equipamento_inventarios');
    }
};
