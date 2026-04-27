<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('feedback_action_tasks', function (Blueprint $table) {
            // Fase do plano (30, 60 ou 90 dias)
            $table->string('phase')->nullable()->after('due_date');          // '30_dias'|'60_dias'|'90_dias'|null
            // Recursos que a empresa disponibiliza para esta tarefa
            $table->text('resources')->nullable()->after('phase');
            // Nota de validação inserida pelo gestor ao confirmar a conclusão
            $table->text('validation_note')->nullable()->after('completed_by');
        });
    }

    public function down(): void
    {
        Schema::table('feedback_action_tasks', function (Blueprint $table) {
            $table->dropColumn(['phase', 'resources', 'validation_note']);
        });
    }
};
