<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('manager_evaluation_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('manager_evaluation_id')->constrained('manager_evaluations')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('users');
            $table->foreignId('criterion_id')->constrained('evaluation_criteria');
            $table->unsignedTinyInteger('score'); // 1–5
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->unique(['manager_evaluation_id', 'employee_id', 'criterion_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manager_evaluation_entries');
    }
};
