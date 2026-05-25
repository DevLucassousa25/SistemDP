<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('manager_evaluation_entries', function (Blueprint $table) {
            // Nota calibrada pelo DP/RH (null = sem calibração, usa score original)
            $table->unsignedSmallInteger('calibrated_score')->nullable()->after('score');
            $table->text('calibration_note')->nullable()->after('calibrated_score');
            $table->foreignId('calibrated_by')->nullable()->constrained('users')->nullOnDelete()->after('calibration_note');
            $table->timestamp('calibrated_at')->nullable()->after('calibrated_by');
        });

        Schema::table('manager_evaluations', function (Blueprint $table) {
            // Pontuação final calculada (média ponderada, considera calibração)
            $table->decimal('final_score', 5, 2)->nullable()->after('status');
            // Flag: o DP/RH liberou os resultados para visualização dos colaboradores
            $table->timestamp('results_published_at')->nullable()->after('completed_at');
        });

        Schema::table('evaluation_cycles', function (Blueprint $table) {
            // Quando o DP/RH publicou os resultados do ciclo globalmente
            $table->timestamp('results_published_at')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('manager_evaluation_entries', function (Blueprint $table) {
            $table->dropConstrainedForeignId('calibrated_by');
            $table->dropColumn(['calibrated_score', 'calibration_note', 'calibrated_at']);
        });

        Schema::table('manager_evaluations', function (Blueprint $table) {
            $table->dropColumn(['final_score', 'results_published_at']);
        });

        Schema::table('evaluation_cycles', function (Blueprint $table) {
            $table->dropColumn('results_published_at');
        });
    }
};
