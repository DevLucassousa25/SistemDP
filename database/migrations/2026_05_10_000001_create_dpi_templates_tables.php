<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Templates de plano DPI ─────────────────────────────────────
        Schema::create('dpi_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        // ── Metas (Goals) do template ──────────────────────────────────
        Schema::create('dpi_template_goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dpi_template_id')->constrained('dpi_templates')->cascadeOnDelete();
            $table->string('name');
            $table->enum('priority', ['alta', 'media', 'baixa'])->default('media');
            $table->unsignedTinyInteger('nivel_atual')->default(1);
            $table->unsignedTinyInteger('nivel_meta')->default(3);
            $table->unsignedSmallInteger('order')->default(0);
            $table->timestamps();
        });

        // ── Ações do template ──────────────────────────────────────────
        Schema::create('dpi_template_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dpi_template_goal_id')->constrained('dpi_template_goals')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['curso', 'certificacao', 'leitura', 'mentoria', 'projeto', 'workshop', 'outro'])->default('curso');
            $table->unsignedSmallInteger('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dpi_template_actions');
        Schema::dropIfExists('dpi_template_goals');
        Schema::dropIfExists('dpi_templates');
    }
};
