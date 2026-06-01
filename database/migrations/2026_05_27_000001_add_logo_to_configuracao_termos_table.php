<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('configuracao_termos', function (Blueprint $table) {
            $table->string('logo')->nullable()->after('rodape_texto');
            $table->string('logo_posicao', 20)->default('esquerda')->after('logo'); // esquerda | centro | direita | nenhum
        });
    }

    public function down(): void
    {
        Schema::table('configuracao_termos', function (Blueprint $table) {
            $table->dropColumn(['logo', 'logo_posicao']);
        });
    }
};
