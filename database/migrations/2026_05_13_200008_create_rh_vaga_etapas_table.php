<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rh_vaga_etapas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vaga_id')->constrained('rh_vagas')->cascadeOnDelete();
            $table->string('nome', 80);
            $table->unsignedTinyInteger('ordem')->default(0);
            $table->string('cor', 20)->default('bg-slate-400');
            $table->boolean('is_aprovado')->default(false);
            $table->boolean('is_reprovado')->default(false);
            $table->boolean('is_banco_talentos')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rh_vaga_etapas');
    }
};
