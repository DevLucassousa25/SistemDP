<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Limpa dados existentes (mudança estrutural incompatível)
        DB::table('dpi_actions')->truncate();
        DB::table('dpi_goals')->truncate();

        // ── dpi_goals → Mapeamento de Competências ────────────────────
        Schema::table('dpi_goals', function (Blueprint $table) {
            $table->dropColumn(['title', 'description', 'category', 'target_date', 'status', 'progress']);
        });

        Schema::table('dpi_goals', function (Blueprint $table) {
            $table->string('name')->after('dpi_plan_id');
            $table->enum('priority', ['alta', 'media', 'baixa'])->default('media')->after('name');
            $table->unsignedTinyInteger('nivel_atual')->default(1)->after('priority'); // 1–5
            $table->unsignedTinyInteger('nivel_meta')->default(3)->after('nivel_atual');  // 1–5
        });

        // ── dpi_actions → Ações de Desenvolvimento ───────────────────
        Schema::table('dpi_actions', function (Blueprint $table) {
            $table->dropColumn(['provider', 'estimated_hours', 'completed_at', 'notes']);
        });

        Schema::table('dpi_actions', function (Blueprint $table) {
            $table->text('description')->nullable()->after('title');
        });
    }

    public function down(): void
    {
        DB::table('dpi_actions')->truncate();
        DB::table('dpi_goals')->truncate();

        Schema::table('dpi_goals', function (Blueprint $table) {
            $table->dropColumn(['name', 'priority', 'nivel_atual', 'nivel_meta']);
        });
        Schema::table('dpi_goals', function (Blueprint $table) {
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('category', ['tecnica', 'comportamental', 'lideranca', 'outros'])->default('tecnica');
            $table->date('target_date')->nullable();
            $table->enum('status', ['pendente', 'em_andamento', 'concluido'])->default('pendente');
            $table->unsignedTinyInteger('progress')->default(0);
        });

        Schema::table('dpi_actions', function (Blueprint $table) {
            $table->dropColumn(['description']);
        });
        Schema::table('dpi_actions', function (Blueprint $table) {
            $table->string('provider')->nullable();
            $table->unsignedSmallInteger('estimated_hours')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();
        });
    }
};
