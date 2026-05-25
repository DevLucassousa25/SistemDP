<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('okr_key_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('objective_id')->constrained('okr_objectives')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();

            // Tipo de medição
            $table->enum('type', ['numeric', 'percentage', 'boolean', 'currency'])->default('numeric');

            // Valores: inicial, alvo, atual
            $table->decimal('initial_value', 12, 2)->default(0);
            $table->decimal('target_value',  12, 2)->default(100);
            $table->decimal('current_value', 12, 2)->default(0);

            // Unidade exibida (ex: "usuários", "reais", "%")
            $table->string('unit', 50)->nullable();

            // Responsável pelo KR
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();

            $table->enum('status', ['not_started', 'on_track', 'at_risk', 'behind', 'completed'])
                  ->default('not_started');

            // Progresso 0-100 calculado automaticamente
            $table->decimal('progress', 5, 2)->default(0);

            $table->date('due_date')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('okr_key_results');
    }
};
