<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('manager_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_cycle_id')->constrained('evaluation_cycles')->cascadeOnDelete();
            $table->foreignId('manager_id')->constrained('users');
            $table->enum('status', ['not_started', 'in_progress', 'completed'])->default('not_started');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['evaluation_cycle_id', 'manager_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manager_evaluations');
    }
};
