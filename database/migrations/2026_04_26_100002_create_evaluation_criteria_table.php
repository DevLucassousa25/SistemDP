<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluation_criteria', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->unsignedSmallInteger('order')->default(0);
            $table->timestamps();
        });

        // Seed default criteria
        $now = now();
        DB::table('evaluation_criteria')->insert([
            ['name' => 'Competências Técnicas',  'description' => 'Conhecimento e aplicação das habilidades técnicas exigidas pelo cargo.',          'is_active' => true, 'is_default' => true, 'order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Comunicação',             'description' => 'Clareza, objetividade e efetividade na comunicação verbal e escrita.',             'is_active' => true, 'is_default' => true, 'order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Trabalho em Equipe',      'description' => 'Colaboração, respeito e contribuição com colegas de trabalho.',                   'is_active' => true, 'is_default' => true, 'order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Proatividade',            'description' => 'Iniciativa para identificar e resolver problemas sem ser solicitado.',             'is_active' => true, 'is_default' => true, 'order' => 4, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Liderança',               'description' => 'Capacidade de influenciar, motivar e guiar outras pessoas.',                      'is_active' => true, 'is_default' => true, 'order' => 5, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Gestão de Tempo',         'description' => 'Organização, priorização de tarefas e cumprimento de prazos.',                    'is_active' => true, 'is_default' => true, 'order' => 6, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluation_criteria');
    }
};
