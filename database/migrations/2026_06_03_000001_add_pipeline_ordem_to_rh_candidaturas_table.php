<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rh_candidaturas', function (Blueprint $table) {
            $table->unsignedInteger('pipeline_ordem')->default(0)->after('etapa_id');
        });
    }

    public function down(): void
    {
        Schema::table('rh_candidaturas', function (Blueprint $table) {
            $table->dropColumn('pipeline_ordem');
        });
    }
};
