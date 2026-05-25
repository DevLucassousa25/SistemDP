<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\AccessProfile;

return new class extends Migration
{
    public function up(): void
    {
        $profiles = [
            [
                'name'        => 'CEO',
                'slug'        => 'ceo',
                'description' => 'Diretor executivo — acesso completo ao sistema',
            ],
            [
                'name'        => 'Gerente de RH',
                'slug'        => 'hr_manager',
                'description' => 'Gestão estratégica de pessoas e cultura organizacional',
            ],
        ];

        foreach ($profiles as $profile) {
            AccessProfile::firstOrCreate(['slug' => $profile['slug']], $profile);
        }
    }

    public function down(): void
    {
        AccessProfile::whereIn('slug', ['ceo', 'hr_manager'])->delete();
    }
};
