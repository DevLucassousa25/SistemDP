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
                'name' => 'Administrador',
                'slug' => 'administrator',
                'description' => 'Acesso completo ao sistema',
            ],
            [
                'name' => 'RH',
                'slug' => 'hr',
                'description' => 'Acesso aos Recursos Humanos',
            ],
            [
                'name' => 'Gerente',
                'slug' => 'manager',
                'description' => 'Acesso à gestão de equipe',
            ],
            [
                'name' => 'Funcionário',
                'slug' => 'employee',
                'description' => 'Acesso básico ao sistema',
            ],
        ];

        foreach ($profiles as $profile) {
            AccessProfile::create($profile);
        }
    }
}
