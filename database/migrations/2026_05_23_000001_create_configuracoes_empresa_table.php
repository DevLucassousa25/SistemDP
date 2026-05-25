<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('configuracoes_empresa', function (Blueprint $table) {
            $table->id();
            $table->string('nome_empresa')->nullable();
            $table->string('email_dominio')->nullable()->comment('Ex: empresa.com.br');
            $table->string('cnpj', 20)->nullable();
            $table->string('telefone', 20)->nullable();
            $table->string('site')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('configuracoes_empresa');
    }
};
