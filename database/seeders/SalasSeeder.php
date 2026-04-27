<?php

namespace Database\Seeders;

use App\Models\room;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SalasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $rooms = [
            [
                'name'                  => 'Sala Inovação',
                'capacity'              => 10,
                'location'                 => '3º andar',
                'status'                => 'disponivel',
                'has_tv'                => true,
                'has_wifi'              => true,
                'has_video_conference'  => true,
                'has_projector'         => false,
                'has_coffee'            => false,
                'has_whiteboard'        => false,
            ],
            [
                'name'                  => 'Sala Estratégia',
                'capacity'              => 8,
                'location'                 => '3º andar',
                'status'                => 'ocupada',
                'has_tv'                => false,
                'has_wifi'              => true,
                'has_video_conference'  => false,
                'has_projector'         => true,
                'has_coffee'            => true,
                'has_whiteboard'        => true,
            ],
            [
                'name'                  => 'Sala Criatividade',
                'capacity'              => 6,
                'location'                 => '2º andar',
                'status'                => 'disponivel',
                'has_tv'                => true,
                'has_wifi'              => true,
                'has_video_conference'  => false,
                'has_projector'         => false,
                'has_coffee'            => false,
                'has_whiteboard'        => false,
            ],
            [
                'name'                  => 'Sala Colaboração',
                'capacity'              => 12,
                'location'                 => '4º andar',
                'status'                => 'manutencao',
                'has_tv'                => true,
                'has_wifi'              => true,
                'has_video_conference'  => true,
                'has_projector'         => false,
                'has_coffee'            => true,
                'has_whiteboard'        => false,
            ],
            [
                'name'                  => 'Sala Focus',
                'capacity'              => 4,
                'location'                 => '2º andar',
                'status'                => 'disponivel',
                'has_tv'                => false,
                'has_wifi'              => true,
                'has_video_conference'  => true,
                'has_projector'         => false,
                'has_coffee'            => false,
                'has_whiteboard'        => false,
            ],
            [
                'name'                  => 'Sala Executiva',
                'capacity'              => 20,
                'location'                 => '5º andar',
                'status'                => 'disponivel',
                'has_tv'                => true,
                'has_wifi'              => true,
                'has_video_conference'  => true,
                'has_projector'         => true,
                'has_coffee'            => true,
                'has_whiteboard'        => true,
            ],
            [
                'name'                  => 'Sala Treinamento',
                'capacity'              => 30,
                'location'                 => '1º andar',
                'status'                => 'ocupada',
                'has_tv'                => true,
                'has_wifi'              => true,
                'has_video_conference'  => false,
                'has_projector'         => true,
                'has_coffee'            => true,
                'has_whiteboard'        => true,
            ],
            [
                'name'                  => 'Sala Brainstorm',
                'capacity'              => 8,
                'location'                 => '2º andar',
                'status'                => 'disponivel',
                'has_tv'                => false,
                'has_wifi'              => true,
                'has_video_conference'  => false,
                'has_projector'         => false,
                'has_coffee'            => true,
                'has_whiteboard'        => true,
            ],
            [
                'name'                  => 'Sala Diretoria',
                'capacity'              => 16,
                'location'                 => '6º andar',
                'status'                => 'ocupada',
                'has_tv'                => true,
                'has_wifi'              => true,
                'has_video_conference'  => true,
                'has_projector'         => true,
                'has_coffee'            => true,
                'has_whiteboard'        => false,
            ],
            [
                'name'                  => 'Sala Ágil',
                'capacity'              => 6,
                'location'                 => '1º andar',
                'status'                => 'disponivel',
                'has_tv'                => false,
                'has_wifi'              => true,
                'has_video_conference'  => false,
                'has_projector'         => false,
                'has_coffee'            => false,
                'has_whiteboard'        => true,
            ],
            [
                'name'                  => 'Sala Projetos',
                'capacity'              => 14,
                'location'                 => '4º andar',
                'status'                => 'disponivel',
                'has_tv'                => true,
                'has_wifi'              => true,
                'has_video_conference'  => true,
                'has_projector'         => true,
                'has_coffee'            => false,
                'has_whiteboard'        => true,
            ],
            [
                'name'                  => 'Sala Reunião Geral',
                'capacity'              => 40,
                'location'                 => 'Térreo',
                'status'                => 'manutencao',
                'has_tv'                => true,
                'has_wifi'              => true,
                'has_video_conference'  => true,
                'has_projector'         => true,
                'has_coffee'            => true,
                'has_whiteboard'        => true,
            ],
        ];

        foreach ($rooms as $dadosSala) {
            room::create($dadosSala);
        }

        $this->command->info('✅ 12 salas cadastradas com sucesso!');
    }
}
