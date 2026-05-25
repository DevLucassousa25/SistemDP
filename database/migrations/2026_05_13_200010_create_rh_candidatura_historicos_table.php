<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rh_candidatura_historicos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidatura_id')->constrained('rh_candidaturas')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('etapa_anterior')->nullable();
            $table->string('etapa_nova')->nullable();
            $table->string('acao', 80);
            $table->text('descricao')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rh_candidatura_historicos');
    }
};
