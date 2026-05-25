<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rh_curriculos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // se vinculado a user interno
            $table->foreignId('candidato_id')->nullable()->constrained('rh_candidatos')->nullOnDelete();
            $table->string('nome');
            $table->string('cpf', 14)->nullable();
            $table->string('email')->nullable();
            $table->string('telefone', 20)->nullable();
            $table->date('data_nascimento')->nullable();
            $table->string('endereco')->nullable();
            $table->string('cidade')->nullable();
            $table->string('estado', 2)->nullable();
            $table->string('cep', 9)->nullable();
            $table->enum('escolaridade', ['fundamental','medio','tecnico','graduacao','pos_graduacao','mestrado','doutorado'])->nullable();
            $table->text('resumo_profissional')->nullable();
            $table->decimal('pretensao_salarial', 10, 2)->nullable();
            $table->string('area_interesse')->nullable();
            $table->string('arquivo_path')->nullable();
            $table->string('arquivo_original')->nullable();
            $table->string('arquivo_mime', 50)->nullable();
            $table->text('notas_internas')->nullable();
            $table->enum('status', ['ativo','inativo','banco_talentos','favorito'])->default('ativo');
            $table->unsignedSmallInteger('nota_media')->nullable(); // média dos testes
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rh_curriculos');
    }
};
