<?php

namespace Database\Seeders;

use App\Models\AccessProfile;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccessProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $profiles = [
            [
                'name'        => 'CEO',
                'slug'        => 'ceo',
                'description' => 'Diretor executivo — acesso completo ao sistema',
            ],
            [
                'name'        => 'Administrador',
                'slug'        => 'administrator',
                'description' => 'Acesso completo ao sistema',
            ],
            [
                'name'        => 'Gerente de RH',
                'slug'        => 'hr_manager',
                'description' => 'Gestão estratégica de pessoas e cultura organizacional',
            ],
            [
                'name'        => 'RH',
                'slug'        => 'hr',
                'description' => 'Acesso aos Recursos Humanos',
            ],
            [
                'name'        => 'Gerente',
                'slug'        => 'manager',
                'description' => 'Acesso à gestão de equipe',
            ],
            [
                'name'        => 'Funcionário',
                'slug'        => 'employee',
                'description' => 'Acesso básico ao sistema',
            ],
        ];

        foreach ($profiles as $profile) {
            AccessProfile::firstOrCreate(['slug' => $profile['slug']], $profile);
        }
    }
}
