<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('configuracao_termos', function (Blueprint $table) {
            $table->id();

            // Cabeçalho
            $table->string('titulo', 300)->nullable();
            $table->string('subtitulo', 300)->nullable();
            $table->text('intro_texto')->nullable();

            // Cláusulas: [{texto: string}]
            $table->json('clausulas')->nullable();

            // Campos visíveis na seção Equipamento/Funcionário
            // Array de strings: 'numero_serie','codigo_patrimonio','marca','modelo',
            //                   'condicao','data_entrega','garantia','local','valor',
            //                   'departamento','cargo','email','observacoes'
            $table->json('campos_visiveis')->nullable();

            // Assinaturas: [{label: string, papel: string}]
            $table->json('assinaturas')->nullable();

            // Rodapé customizado
            $table->string('rodape_texto', 300)->nullable();

            // Quem fez a última edição
            $table->foreignId('atualizado_por')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('configuracao_termos');
    }
};
