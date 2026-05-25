<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rh_curriculos', function (Blueprint $table) {
            $table->text('texto_ocr')->nullable()->after('arquivo_mime');
            $table->timestamp('ocr_processado_at')->nullable()->after('texto_ocr');
        });
    }

    public function down(): void
    {
        Schema::table('rh_curriculos', function (Blueprint $table) {
            $table->dropColumn(['texto_ocr', 'ocr_processado_at']);
        });
    }
};
