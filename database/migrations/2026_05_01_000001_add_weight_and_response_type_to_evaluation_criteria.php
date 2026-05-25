<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('evaluation_criteria', function (Blueprint $table) {
            // Peso do critério para cálculo de média ponderada (padrão 1.0 = sem peso diferenciado)
            $table->decimal('weight', 4, 2)->default(1.00)->after('order');

            // Tipo de resposta: scale (1-5), boolean (sim/não), multiple_choice
            $table->enum('response_type', ['scale', 'boolean', 'multiple_choice'])
                  ->default('scale')
                  ->after('weight');

            // Opções para múltipla escolha: JSON array de strings
            // Ex: ["Nunca", "Raramente", "Às vezes", "Frequentemente", "Sempre"]
            $table->json('options')->nullable()->after('response_type');
        });
    }

    public function down(): void
    {
        Schema::table('evaluation_criteria', function (Blueprint $table) {
            $table->dropColumn(['weight', 'response_type', 'options']);
        });
    }
};
