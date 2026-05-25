<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dpi_goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dpi_plan_id')->constrained('dpi_plans')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('category', ['tecnica', 'comportamental', 'lideranca', 'outros'])->default('tecnica');
            $table->date('target_date')->nullable();
            $table->enum('status', ['pendente', 'em_andamento', 'concluido'])->default('pendente');
            $table->unsignedTinyInteger('progress')->default(0); // 0-100
            $table->unsignedSmallInteger('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dpi_goals');
    }
};
