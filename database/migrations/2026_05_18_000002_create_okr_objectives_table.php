<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('okr_objectives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cycle_id')->constrained('okr_cycles')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();

            // Nível: empresa, departamento ou individual
            $table->enum('level', ['company', 'department', 'individual'])->default('individual');

            // Dono do objetivo
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();

            // Departamento (para level=department)
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();

            // Alinhamento hierárquico (KR de nível acima → Objetivo deste nível)
            $table->foreignId('parent_id')->nullable()->constrained('okr_objectives')->nullOnDelete();

            $table->enum('status', ['not_started', 'on_track', 'at_risk', 'behind', 'completed'])
                  ->default('not_started');

            // Progresso 0-100, recalculado a cada check-in
            $table->decimal('progress', 5, 2)->default(0);

            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('okr_objectives');
    }
};
