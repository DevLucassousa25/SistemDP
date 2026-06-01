<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('configuracao_termos', function (Blueprint $table) {
            $table->string('nome', 150)->default('Padrão')->after('id');
            $table->boolean('padrao')->default(false)->after('nome');
        });

        // O primeiro registro existente (singleton) vira o template padrão
        DB::table('configuracao_termos')->whereNull('nome')->orWhere('nome', '')->update([
            'nome'   => 'Padrão',
            'padrao' => true,
        ]);

        // Se não havia nenhum registro, garante que o primeiro seja o padrão
        if (DB::table('configuracao_termos')->where('padrao', true)->count() === 0) {
            DB::table('configuracao_termos')->orderBy('id')->limit(1)->update(['padrao' => true]);
        }
    }

    public function down(): void
    {
        Schema::table('configuracao_termos', function (Blueprint $table) {
            $table->dropColumn(['nome', 'padrao']);
        });
    }
};
