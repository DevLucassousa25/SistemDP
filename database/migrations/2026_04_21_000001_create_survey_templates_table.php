<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('survey_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();

            // nps | clima | feedback | rh | custom
            $table->string('category', 30)->default('custom');

            // Array de perguntas: [{question, type, options, required, order}]
            $table->json('questions');

            // null = template do sistema
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            // true = template padrão do sistema (visível para todos)
            $table->boolean('is_system')->default(false);

            $table->timestamps();

            $table->index('category');
            $table->index('is_system');
            $table->index('created_by');
        });

        DB::statement("ALTER TABLE survey_templates DROP CONSTRAINT IF EXISTS survey_templates_category_check");
        DB::statement("ALTER TABLE survey_templates ADD CONSTRAINT survey_templates_category_check
                       CHECK (category IN ('nps', 'clima', 'feedback', 'rh', 'custom'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_templates');
    }
};
