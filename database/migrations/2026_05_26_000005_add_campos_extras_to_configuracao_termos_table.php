<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('configuracao_termos', function (Blueprint $table) {
            // Campos extras que o usuário preenche antes de gerar o PDF
            // Ex: [{id:'cpf', label:'CPF', ativo:true, tipo:'predefinido'}, ...]
            $table->json('campos_extras')->nullable()->after('campos_visiveis');
        });
    }

    public function down(): void
    {
        Schema::table('configuracao_termos', function (Blueprint $table) {
            $table->dropColumn('campos_extras');
        });
    }
};
