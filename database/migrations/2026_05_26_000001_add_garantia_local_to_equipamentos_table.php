<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('equipamentos', function (Blueprint $table) {
            $table->date('data_garantia')->nullable()->after('data_aquisicao');
            $table->string('local', 100)->nullable()->after('data_garantia'); // sala, andar, filial
            $table->decimal('taxa_depreciacao', 5, 2)->nullable()->after('valor'); // % ao ano
        });
    }

    public function down(): void
    {
        Schema::table('equipamentos', function (Blueprint $table) {
            $table->dropColumn(['data_garantia', 'local', 'taxa_depreciacao']);
        });
    }
};
