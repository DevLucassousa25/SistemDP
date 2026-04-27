<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surveys', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();

            // rascunho | ativa | encerrada
            $table->string('status', 20)->default('rascunho');

            // todos | departamentos (quando 'departamentos', target_department_ids contém os IDs)
            $table->string('target_audience', 30)->default('todos');
            $table->json('target_department_ids')->nullable();

            $table->boolean('is_anonymous')->default(true);

            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();

            $table->timestamps();

            $table->index('status');
            $table->index(['start_date', 'end_date']);
        });

        DB::statement("ALTER TABLE surveys DROP CONSTRAINT IF EXISTS surveys_status_check");
        DB::statement("ALTER TABLE surveys ADD CONSTRAINT surveys_status_check
                       CHECK (status IN ('rascunho', 'ativa', 'encerrada'))");

        DB::statement("ALTER TABLE surveys DROP CONSTRAINT IF EXISTS surveys_target_audience_check");
        DB::statement("ALTER TABLE surveys ADD CONSTRAINT surveys_target_audience_check
                       CHECK (target_audience IN ('todos', 'departamentos'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('surveys');
    }
};
