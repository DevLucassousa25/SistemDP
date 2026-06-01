<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dpi_actions', function (Blueprint $table) {
            $table->foreignId('treinamento_id')
                  ->nullable()
                  ->after('task_id')
                  ->constrained('treinamentos')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('dpi_actions', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\Treinamento::class);
            $table->dropColumn('treinamento_id');
        });
    }
};
