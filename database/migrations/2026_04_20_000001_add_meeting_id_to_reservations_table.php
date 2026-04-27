<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            // Link opcional entre uma reserva e a reunião que a gerou.
            // Quando a reunião é excluída, a reserva correspondente também é removida.
            $table->foreignId('meeting_id')
                  ->nullable()
                  ->after('room_id')
                  ->constrained('meetings')
                  ->cascadeOnDelete();

            $table->index('meeting_id');
        });

        // ── Backfill ──────────────────────────────────────────────────────────
        // Cria reservas na tela de Salas para cada reunião presencial já existente
        // que ainda não tem reserva vinculada.
        $meetings = DB::table('meetings')
            ->where('location_type', 'presencial')
            ->whereNotNull('room_id')
            ->where('status', '!=', 'cancelada')
            ->get();

        foreach ($meetings as $m) {
            $alreadyLinked = DB::table('reservations')
                ->where('meeting_id', $m->id)
                ->exists();

            if ($alreadyLinked) continue;

            $attendees = DB::table('meeting_participants')
                ->where('meeting_id', $m->id)
                ->count();

            DB::table('reservations')->insert([
                'room_id'         => $m->room_id,
                'meeting_id'      => $m->id,
                'user_id'         => $m->organizer_id,
                'title'           => (string) $m->title,
                'description'     => (string) ($m->description ?? ''),
                'start_time'      => $m->start_time,
                'end_time'        => $m->end_time,
                'attendees_count' => max(1, $attendees + 1),
                'status'          => 'confirmada',
                'is_maintenance'  => false,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
        }
    }

    public function down(): void
    {
        // Remove reservas que foram criadas automaticamente a partir de reuniões.
        DB::table('reservations')->whereNotNull('meeting_id')->delete();

        Schema::table('reservations', function (Blueprint $table) {
            $table->dropForeign(['meeting_id']);
            $table->dropIndex(['meeting_id']);
            $table->dropColumn('meeting_id');
        });
    }
};
