<?php

namespace Database\Seeders;

use App\Models\Reservations;
use App\Models\room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReservationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $room = room::first();
        $user = User::first();

        Reservations::create([
            'room_id'         => $room->id,
            'user_id'         => $user->id,
            'title'           => 'Reunião de Planejamento',
            'description'     => 'Discussão sobre metas da semana.',
            'start_time'      => Carbon::now()->setTime(9, 0),
            'end_time'        => Carbon::now()->setTime(10, 0),
            'attendees_count' => 5,
            'status'          => 'confirmada',
        ]);

        Reservations::create([
            'room_id'         => $room->id,
            'user_id'         => $user->id,
            'title'           => 'Daily Meeting',
            'description'     => 'Alinhamento rápido da equipe.',
            'start_time'      => Carbon::now()->setTime(11, 0),
            'end_time'        => Carbon::now()->setTime(11, 30),
            'attendees_count' => 3,
            'status'          => 'confirmada',
        ]);
    }
}
