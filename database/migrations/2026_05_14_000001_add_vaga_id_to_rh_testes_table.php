<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rh_testes', function (Blueprint $table) {
            $table->foreignId('vaga_id')->nullable()->after('created_by')
                  ->constrained('rh_vagas')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('rh_testes', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\RhVaga::class, 'vaga_id');
            $table->dropColumn('vaga_id');
        });
    }
};
