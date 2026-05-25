<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── dpi_plans: quem criou o plano ────────────────────────────
        Schema::table('dpi_plans', function (Blueprint $table) {
            $table->foreignId('created_by')
                ->nullable()
                ->after('user_id')
                ->constrained('users')
                ->nullOnDelete();
        });

        // ── dpi_actions: evidências (upload) e validação pelo gerente ─
        Schema::table('dpi_actions', function (Blueprint $table) {
            $table->string('attachment_path')->nullable()->after('description');
            $table->string('attachment_name')->nullable()->after('attachment_path');
            $table->foreignId('validated_by')->nullable()->after('status')
                ->constrained('users')->nullOnDelete();
            $table->timestamp('validated_at')->nullable()->after('validated_by');
        });
    }

    public function down(): void
    {
        Schema::table('dpi_plans', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn('created_by');
        });

        Schema::table('dpi_actions', function (Blueprint $table) {
            $table->dropForeign(['validated_by']);
            $table->dropColumn(['attachment_path', 'attachment_name', 'validated_by', 'validated_at']);
        });
    }
};
