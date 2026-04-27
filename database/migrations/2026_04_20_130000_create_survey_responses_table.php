<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('survey_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')
                ->constrained('surveys')
                ->cascadeOnDelete();
            // Sempre armazenamos o user_id internamente para evitar duplas respostas.
            // O campo is_anonymous na pesquisa controla apenas a exibição nos relatórios.
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->timestamp('completed_at')->nullable(); // null = iniciou mas não concluiu
            $table->timestamps();

            $table->unique(['survey_id', 'user_id']); // impede dupla resposta
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_responses');
    }
};
