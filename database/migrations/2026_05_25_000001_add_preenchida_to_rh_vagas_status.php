<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Remove o CHECK constraint antigo e recria incluindo 'preenchida'
        DB::statement('ALTER TABLE rh_vagas DROP CONSTRAINT IF EXISTS rh_vagas_status_check');
        DB::statement("ALTER TABLE rh_vagas ADD CONSTRAINT rh_vagas_status_check CHECK (status IN ('rascunho','publicada','pausada','encerrada','preenchida'))");
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE rh_vagas DROP CONSTRAINT IF EXISTS rh_vagas_status_check');
        DB::statement("ALTER TABLE rh_vagas ADD CONSTRAINT rh_vagas_status_check CHECK (status IN ('rascunho','publicada','pausada','encerrada'))");
    }
};
