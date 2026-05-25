<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dpi_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dpi_goal_id')->constrained('dpi_goals')->cascadeOnDelete();
            $table->string('title');
            $table->enum('type', ['curso', 'certificacao', 'leitura', 'mentoria', 'projeto', 'workshop', 'outro'])->default('curso');
            $table->string('provider')->nullable();
            $table->unsignedSmallInteger('estimated_hours')->nullable();
            $table->date('target_date')->nullable();
            $table->enum('status', ['pendente', 'em_andamento', 'concluido'])->default('pendente');
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedSmallInteger('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dpi_actions');
    }
};
