<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rh_questoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teste_id')->constrained('rh_testes')->cascadeOnDelete();
            $table->text('enunciado');
            $table->enum('tipo', ['objetiva','discursiva'])->default('objetiva');
            $table->unsignedTinyInteger('peso')->default(1);
            $table->unsignedSmallInteger('ordem')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rh_questoes');
    }
};
