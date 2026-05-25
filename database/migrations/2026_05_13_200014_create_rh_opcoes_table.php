<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rh_opcoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('questao_id')->constrained('rh_questoes')->cascadeOnDelete();
            $table->string('texto');
            $table->boolean('correta')->default(false);
            $table->unsignedTinyInteger('ordem')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rh_opcoes');
    }
};
