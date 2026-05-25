<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('okr_checkins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('key_result_id')->constrained('okr_key_results')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // Snapshot de valores no momento do check-in
            $table->decimal('previous_value', 12, 2);
            $table->decimal('new_value',      12, 2);

            // Confiança (1-10, opcional)
            $table->tinyInteger('confidence')->nullable();

            $table->text('comment')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('okr_checkins');
    }
};
