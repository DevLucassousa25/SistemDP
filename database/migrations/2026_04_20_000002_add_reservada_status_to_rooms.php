<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Adiciona 'reservada' à lista de valores permitidos para rooms.status.
     *
     * "reservada" = a sala tem reserva confirmada futura (ainda não começou),
     * diferenciando-a de "ocupada" (reserva acontecendo nesse instante).
     */
    public function up(): void
    {
        // Laravel cria ENUMs em Postgres como VARCHAR + CHECK CONSTRAINT.
        // Para alterar, removemos o check e recriamos com o novo valor.
        DB::statement('ALTER TABLE rooms DROP CONSTRAINT IF EXISTS rooms_status_check');
        DB::statement("ALTER TABLE rooms ADD CONSTRAINT rooms_status_check
                       CHECK (status IN ('disponivel', 'ocupada', 'reservada', 'manutencao'))");
    }

    public function down(): void
    {
        // Se o banco contiver registros com status 'reservada', volta-os para 'disponivel'
        // antes de restaurar o check original para não violar a constraint.
        DB::table('rooms')->where('status', 'reservada')->update(['status' => 'disponivel']);

        DB::statement('ALTER TABLE rooms DROP CONSTRAINT IF EXISTS rooms_status_check');
        DB::statement("ALTER TABLE rooms ADD CONSTRAINT rooms_status_check
                       CHECK (status IN ('disponivel', 'ocupada', 'manutencao'))");
    }
};
