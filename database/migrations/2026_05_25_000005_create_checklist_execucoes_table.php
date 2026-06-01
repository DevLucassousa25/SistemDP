<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('checklist_execucoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('checklist_item_id')->constrained('checklist_itens')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // funcionário alvo
            $table->foreignId('executado_por')->nullable()->constrained('users')->nullOnDelete(); // quem marcou como concluído
            $table->string('tipo'); // onboarding, offboarding
            $table->boolean('concluido')->default(false);
            $table->timestamp('data_conclusao')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();

            // Garante que cada item seja único por funcionário + tipo
            $table->unique(['checklist_item_id', 'user_id', 'tipo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('checklist_execucoes');
    }
};
