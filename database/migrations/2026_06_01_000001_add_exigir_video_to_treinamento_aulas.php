<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('treinamento_aulas', function (Blueprint $table) {
            $table->boolean('exigir_video')->default(false)->after('obrigatoria');
        });
    }

    public function down(): void
    {
        Schema::table('treinamento_aulas', function (Blueprint $table) {
            $table->dropColumn('exigir_video');
        });
    }
};
