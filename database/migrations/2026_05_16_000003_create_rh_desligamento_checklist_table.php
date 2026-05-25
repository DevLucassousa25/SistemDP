<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rh_desligamento_checklist', function (Blueprint $table) {
            $table->id();
            $table->foreignId('desligamento_id')->constrained('rh_desligamentos')->cascadeOnDelete();
            $table->string('titulo');
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
        Schema::dropIfExists('rh_desligamento_checklist');
    }
};
