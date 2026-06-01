<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('checklist_itens', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('descricao')->nullable();
            $table->string('tipo'); // onboarding, offboarding
            $table->string('categoria')->nullable(); // documentos, equipamentos, acesso_sistemas, treinamentos, outros
            $table->string('responsavel_padrao')->nullable(); // rh, ti, gestor, funcionario
            $table->integer('ordem')->default(0);
            $table->boolean('obrigatorio')->default(true);
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });

        // Seed com itens padrão de onboarding
        $this->seedDefaults();
    }

    private function seedDefaults(): void
    {
        $itens = [
            // Onboarding
            ['titulo' => 'Entregar notebook/computador', 'tipo' => 'onboarding', 'categoria' => 'equipamentos', 'responsavel_padrao' => 'ti', 'ordem' => 1],
            ['titulo' => 'Entregar crachá de identificação', 'tipo' => 'onboarding', 'categoria' => 'equipamentos', 'responsavel_padrao' => 'rh', 'ordem' => 2],
            ['titulo' => 'Entregar EPIs necessários', 'tipo' => 'onboarding', 'categoria' => 'equipamentos', 'responsavel_padrao' => 'rh', 'ordem' => 3],
            ['titulo' => 'Criar acesso ao e-mail corporativo', 'tipo' => 'onboarding', 'categoria' => 'acesso_sistemas', 'responsavel_padrao' => 'ti', 'ordem' => 4],
            ['titulo' => 'Liberar acesso ao sistema interno', 'tipo' => 'onboarding', 'categoria' => 'acesso_sistemas', 'responsavel_padrao' => 'ti', 'ordem' => 5],
            ['titulo' => 'Assinar contrato de trabalho', 'tipo' => 'onboarding', 'categoria' => 'documentos', 'responsavel_padrao' => 'rh', 'ordem' => 6],
            ['titulo' => 'Preencher ficha de admissão', 'tipo' => 'onboarding', 'categoria' => 'documentos', 'responsavel_padrao' => 'rh', 'ordem' => 7],
            ['titulo' => 'Apresentar ao time e gestor', 'tipo' => 'onboarding', 'categoria' => 'treinamentos', 'responsavel_padrao' => 'gestor', 'ordem' => 8],
            ['titulo' => 'Realizar treinamento de integração', 'tipo' => 'onboarding', 'categoria' => 'treinamentos', 'responsavel_padrao' => 'rh', 'ordem' => 9],
            ['titulo' => 'Explicar políticas internas e benefícios', 'tipo' => 'onboarding', 'categoria' => 'treinamentos', 'responsavel_padrao' => 'rh', 'ordem' => 10],

            // Offboarding
            ['titulo' => 'Recolher notebook/computador', 'tipo' => 'offboarding', 'categoria' => 'equipamentos', 'responsavel_padrao' => 'ti', 'ordem' => 1],
            ['titulo' => 'Recolher crachá de identificação', 'tipo' => 'offboarding', 'categoria' => 'equipamentos', 'responsavel_padrao' => 'rh', 'ordem' => 2],
            ['titulo' => 'Recolher EPIs entregues', 'tipo' => 'offboarding', 'categoria' => 'equipamentos', 'responsavel_padrao' => 'rh', 'ordem' => 3],
            ['titulo' => 'Revogar acesso ao e-mail corporativo', 'tipo' => 'offboarding', 'categoria' => 'acesso_sistemas', 'responsavel_padrao' => 'ti', 'ordem' => 4],
            ['titulo' => 'Revogar acesso ao sistema interno', 'tipo' => 'offboarding', 'categoria' => 'acesso_sistemas', 'responsavel_padrao' => 'ti', 'ordem' => 5],
            ['titulo' => 'Assinar Termo de Desligamento', 'tipo' => 'offboarding', 'categoria' => 'documentos', 'responsavel_padrao' => 'rh', 'ordem' => 6],
            ['titulo' => 'Realizar entrevista de desligamento', 'tipo' => 'offboarding', 'categoria' => 'documentos', 'responsavel_padrao' => 'rh', 'ordem' => 7],
            ['titulo' => 'Realizar exame demissional', 'tipo' => 'offboarding', 'categoria' => 'documentos', 'responsavel_padrao' => 'funcionario', 'ordem' => 8],
            ['titulo' => 'Quitar verbas rescisórias', 'tipo' => 'offboarding', 'categoria' => 'documentos', 'responsavel_padrao' => 'rh', 'ordem' => 9],
            ['titulo' => 'Backup e transferência de dados', 'tipo' => 'offboarding', 'categoria' => 'acesso_sistemas', 'responsavel_padrao' => 'ti', 'ordem' => 10],
        ];

        foreach ($itens as $item) {
            \DB::table('checklist_itens')->insert(array_merge($item, [
                'obrigatorio' => true,
                'ativo'       => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]));
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('checklist_itens');
    }
};
