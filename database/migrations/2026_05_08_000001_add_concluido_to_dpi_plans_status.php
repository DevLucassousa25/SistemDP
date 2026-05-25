<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * O status "concluido" foi adicionado ao fluxo do DPI do Gestor,
     * mas o CHECK constraint original da tabela não o incluía.
     * Esta migration remove e recria o constraint com o novo valor.
     */
    public function up(): void
    {
        // Remove o constraint antigo (nome gerado pelo Laravel para enum)
        DB::statement('ALTER TABLE dpi_plans DROP CONSTRAINT IF EXISTS dpi_plans_status_check');

        // Recria o constraint com os cinco valores válidos
        DB::statement("
            ALTER TABLE dpi_plans
            ADD CONSTRAINT dpi_plans_status_check
            CHECK (status IN ('rascunho', 'enviado', 'aprovado', 'reprovado', 'concluido'))
        ");
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE dpi_plans DROP CONSTRAINT IF EXISTS dpi_plans_status_check');

        DB::statement("
            ALTER TABLE dpi_plans
            ADD CONSTRAINT dpi_plans_status_check
            CHECK (status IN ('rascunho', 'enviado', 'aprovado', 'reprovado'))
        ");
    }
};
